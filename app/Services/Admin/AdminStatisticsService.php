<?php

namespace App\Services\Admin;

use App\Models\Invitation;
use App\Models\PaymentInvoice;
use App\Models\RsvpResponse;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminStatisticsService
{
    public function dashboard(): array
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $weekAgo = $now->copy()->subDays(7);
        $monthAgo = $now->copy()->subDays(30);

        $rsvpAttending = RsvpResponse::query()->where('is_attending', true)->count();
        $rsvpDeclined = RsvpResponse::query()->where('is_attending', false)->count();
        $totalGuests = (int) RsvpResponse::query()
            ->where('is_attending', true)
            ->selectRaw('SUM(adults_count + children_count) as total')
            ->value('total');

        $paidRevenue = (int) PaymentInvoice::query()
            ->where('status', PaymentInvoice::STATUS_PAID)
            ->sum('amount');

        $revenueToday = (int) PaymentInvoice::query()
            ->where('status', PaymentInvoice::STATUS_PAID)
            ->where('paid_at', '>=', $today)
            ->sum('amount');

        $revenueMonth = (int) PaymentInvoice::query()
            ->where('status', PaymentInvoice::STATUS_PAID)
            ->where('paid_at', '>=', $monthAgo)
            ->sum('amount');

        return [
            'users_total' => User::query()->where('role', '!=', User::ROLE_ADMIN)->count(),
            'users_today' => User::query()->where('role', '!=', User::ROLE_ADMIN)->where('created_at', '>=', $today)->count(),
            'users_week' => User::query()->where('role', '!=', User::ROLE_ADMIN)->where('created_at', '>=', $weekAgo)->count(),
            'invitations_total' => Invitation::query()->count(),
            'invitations_active' => Invitation::query()->where('status', Invitation::STATUS_ACTIVE)->count(),
            'invitations_draft' => Invitation::query()->where('status', Invitation::STATUS_DRAFT)->count(),
            'invitations_expired' => Invitation::query()->where('status', Invitation::STATUS_EXPIRED)->count(),
            'invitations_today' => Invitation::query()->where('created_at', '>=', $today)->count(),
            'rsvp_total' => RsvpResponse::query()->count(),
            'rsvp_attending' => $rsvpAttending,
            'rsvp_declined' => $rsvpDeclined,
            'rsvp_guests' => $totalGuests,
            'rsvp_today' => RsvpResponse::query()->where('created_at', '>=', $today)->count(),
            'payments_total' => PaymentInvoice::query()->count(),
            'payments_paid' => PaymentInvoice::query()->where('status', PaymentInvoice::STATUS_PAID)->count(),
            'payments_pending' => PaymentInvoice::query()->whereIn('status', [
                PaymentInvoice::STATUS_PENDING,
                PaymentInvoice::STATUS_PREPARED,
            ])->count(),
            'revenue_total' => $paidRevenue,
            'revenue_today' => $revenueToday,
            'revenue_month' => $revenueMonth,
            'telegram_linked' => User::query()->whereNotNull('telegram_chat_id')->count(),
            'top_templates' => $this->topTemplates(),
            'revenue_by_provider' => $this->revenueByProvider(),
            'recent_users' => $this->recentUsers(),
            'recent_invitations' => $this->recentInvitations(),
            'recent_rsvps' => $this->recentRsvps(),
            'recent_payments' => $this->recentPayments(),
            'chart' => $this->activityChart(14),
            'fetched_at' => $now->timezone('Asia/Tashkent')->toIso8601String(),
        ];
    }

    public function formatMoney(int $amount): string
    {
        return number_format($amount, 0, '.', ' ').' '.__('landing.currency');
    }

    private function topTemplates(): array
    {
        return Invitation::query()
            ->select('template_slug', DB::raw('COUNT(*) as total'))
            ->whereNotNull('template_slug')
            ->groupBy('template_slug')
            ->orderByDesc('total')
            ->limit(6)
            ->get()
            ->map(fn ($row) => [
                'slug' => $row->template_slug,
                'total' => (int) $row->total,
            ])
            ->all();
    }

    private function revenueByProvider(): array
    {
        return PaymentInvoice::query()
            ->select('provider', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('status', PaymentInvoice::STATUS_PAID)
            ->groupBy('provider')
            ->get()
            ->map(fn ($row) => [
                'provider' => $row->provider,
                'total' => (int) $row->total,
                'count' => (int) $row->count,
            ])
            ->all();
    }

    private function recentUsers(): array
    {
        return User::query()
            ->where('role', '!=', User::ROLE_ADMIN)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'invitations_count' => $user->invitations()->count(),
                'created_at' => $user->created_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i'),
            ])
            ->all();
    }

    private function recentInvitations(): array
    {
        return Invitation::query()
            ->with('user:id,name,email')
            ->withCount('rsvpResponses')
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (Invitation $invitation) => [
                'id' => $invitation->id,
                'title' => $invitation->displayTitle(),
                'event_type' => $invitation->event_type,
                'status' => $invitation->status,
                'status_label' => $invitation->statusLabel(),
                'owner' => $invitation->user?->name ?? '—',
                'rsvp_count' => $invitation->rsvp_responses_count,
                'slug' => $invitation->slug,
                'created_at' => $invitation->created_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i'),
            ])
            ->all();
    }

    private function recentRsvps(): array
    {
        return RsvpResponse::query()
            ->with('invitation:id,slug,event_type,groom_name,bride_name,profile_meta')
            ->latest()
            ->limit(10)
            ->get()
            ->map(fn (RsvpResponse $response) => [
                'id' => $response->id,
                'guest_name' => $response->guest_name,
                'guest_summary' => $response->guestSummary(),
                'invitation_title' => $response->invitation?->displayTitle() ?? '—',
                'created_at' => $response->created_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i'),
            ])
            ->all();
    }

    private function recentPayments(): array
    {
        return PaymentInvoice::query()
            ->with(['user:id,name,email', 'invitation:id,slug,event_type'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(fn (PaymentInvoice $invoice) => [
                'id' => $invoice->id,
                'uuid' => $invoice->uuid,
                'provider' => strtoupper($invoice->provider),
                'amount' => $this->formatMoney((int) $invoice->amount),
                'status' => $invoice->status,
                'owner' => $invoice->user?->name ?? '—',
                'invitation' => $invoice->invitation?->displayTitle() ?? '—',
                'paid_at' => $invoice->paid_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i'),
                'created_at' => $invoice->created_at?->timezone('Asia/Tashkent')->format('d.m.Y H:i'),
            ])
            ->all();
    }

    private function activityChart(int $days): array
    {
        $start = now()->subDays($days - 1)->startOfDay();
        $labels = [];
        $users = [];
        $invitations = [];
        $rsvps = [];
        $revenue = [];

        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $next = $day->copy()->endOfDay();
            $labels[] = $day->format('d.m');

            $users[] = User::query()
                ->where('role', '!=', User::ROLE_ADMIN)
                ->whereBetween('created_at', [$day, $next])
                ->count();

            $invitations[] = Invitation::query()
                ->whereBetween('created_at', [$day, $next])
                ->count();

            $rsvps[] = RsvpResponse::query()
                ->whereBetween('created_at', [$day, $next])
                ->count();

            $revenue[] = (int) PaymentInvoice::query()
                ->where('status', PaymentInvoice::STATUS_PAID)
                ->whereBetween('paid_at', [$day, $next])
                ->sum('amount');
        }

        return compact('labels', 'users', 'invitations', 'rsvps', 'revenue');
    }
}

<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\Rsvp\RsvpDashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly RsvpDashboardService $rsvpDashboardService,
    ) {}

    public function __invoke(): View
    {
        $user = auth()->user();

        $invitations = $user->invitations()
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'orders_total' => $user->invitations()->count(),
            'orders_published' => $user->invitations()->where('status', Invitation::STATUS_ACTIVE)->count(),
            'orders_draft' => $user->invitations()->where('status', Invitation::STATUS_DRAFT)->count(),
            'favorites_total' => $user->favorites()->count(),
            'rsvp_total' => $user->invitations()
                ->withCount('rsvpResponses')
                ->get()
                ->sum('rsvp_responses_count'),
        ];

        $liveInvitation = $user->invitations()
            ->where('status', Invitation::STATUS_ACTIVE)
            ->where('rsvp_enabled', true)
            ->whereNotNull('published_at')
            ->latest('published_at')
            ->first();

        return view('account.dashboard', [
            'title' => 'Kabinet — Taklifnoma',
            'stats' => $stats,
            'recentOrders' => $invitations,
            'liveInvitation' => $liveInvitation,
            'rsvpSnapshot' => $liveInvitation
                ? $this->rsvpDashboardService->snapshot($liveInvitation)
                : null,
        ]);
    }
}

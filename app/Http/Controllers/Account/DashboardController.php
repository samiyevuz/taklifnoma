<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $invitations = $user->invitations()
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'orders_total' => $user->invitations()->count(),
            'orders_published' => $user->invitations()->where('status', Invitation::STATUS_PUBLISHED)->count(),
            'orders_draft' => $user->invitations()->where('status', Invitation::STATUS_DRAFT)->count(),
            'favorites_total' => $user->favorites()->count(),
            'rsvp_total' => $user->invitations()
                ->withCount('rsvpResponses')
                ->get()
                ->sum('rsvp_responses_count'),
        ];

        return view('account.dashboard', [
            'title' => 'Kabinet — Taklifnoma',
            'stats' => $stats,
            'recentOrders' => $invitations,
        ]);
    }
}

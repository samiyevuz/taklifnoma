<?php

namespace App\Services\Rsvp;

use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Support\Collection;

class RsvpDashboardService
{
    public function snapshot(Invitation $invitation, int $recentLimit = 12): array
    {
        $stats = $invitation->rsvpStats();
        $totalResponses = $stats['attending'] + $stats['declined'];
        $confirmationRate = $totalResponses > 0
            ? (int) round(($stats['attending'] / $totalResponses) * 100)
            : 0;

        return [
            'invitation_id' => $invitation->id,
            'event_title' => $invitation->displayTitle().' — '.$invitation->event_type,
            'attending' => $stats['attending'],
            'declined' => $stats['declined'],
            'total_guests' => (int) $stats['total_guests'],
            'total_responses' => $totalResponses,
            'confirmation_rate' => $confirmationRate,
            'recent' => $this->recentResponses($invitation, $recentLimit),
            'fetched_at' => now()->timezone('Asia/Tashkent')->toIso8601String(),
        ];
    }

    public function recentResponses(Invitation $invitation, int $limit = 12): Collection
    {
        return $invitation->rsvpResponses()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (RsvpResponse $response) => $this->formatResponse($response));
    }

    public function formatResponse(RsvpResponse $response): array
    {
        return [
            'id' => $response->id,
            'guest_name' => $response->guest_name,
            'is_attending' => $response->is_attending,
            'status' => $response->status,
            'status_label' => $response->statusLabel(),
            'guest_summary' => $response->guestSummary(),
            'adults_count' => $response->adults_count,
            'children_count' => $response->children_count,
            'created_at' => $response->created_at?->timezone('Asia/Tashkent')->toIso8601String(),
            'created_at_label' => $response->formattedTimestamp(),
        ];
    }
}

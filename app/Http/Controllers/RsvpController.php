<?php

namespace App\Http\Controllers;

use App\Events\RsvpResponseSubmitted;
use App\Http\Requests\StoreRsvpRequest;
use App\Models\RsvpResponse;
use App\Support\CustomDomainResolver;
use App\Support\InvitationResolver;
use App\Models\Invitation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function store(StoreRsvpRequest $request, string $slug): JsonResponse
    {
        return $this->storeForInvitation($request, InvitationResolver::findPublic($slug));
    }

    public function storeFromDomain(StoreRsvpRequest $request): JsonResponse
    {
        $invitation = CustomDomainResolver::findForRequest($request);

        if (! $invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Taklifnoma topilmadi.',
            ], 404);
        }

        return $this->storeForInvitation($request, $invitation);
    }

    private function storeForInvitation(StoreRsvpRequest $request, Invitation $invitation): JsonResponse
    {

        if (! $invitation->rsvp_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'RSVP ushbu taklifnoma uchun o\'chirilgan.',
            ], 403);
        }

        $validated = $request->validated();
        $isAttending = $validated['status'] === RsvpResponse::STATUS_ATTENDING;

        if ($isAttending) {
            $adults = (int) ($validated['adults_count'] ?? 1);
            $children = (int) ($validated['children_count'] ?? 0);

            if (! $invitation->canAcceptGuestCount($adults, $children)) {
                $limit = $invitation->resolvedGuestLimit();
                $remaining = $invitation->remainingGuestSlots();

                return response()->json([
                    'success' => false,
                    'message' => $remaining === 0
                        ? "Mehmon limiti to'ldi ({$limit} ta). Taklif egasi Premium tarifga o'tishi mumkin."
                        : "Faqat {$remaining} ta mehmon qabul qilinadi (limit: {$limit} ta).",
                    'data' => [
                        'guest_limit' => $limit,
                        'remaining_slots' => $remaining,
                        'current_guests' => $invitation->currentGuestCount(),
                    ],
                ], 422);
            }
        }

        $response = $invitation->rsvpResponses()->create([
            'guest_name' => $validated['guest_name'],
            'is_attending' => $isAttending,
            'status' => $validated['status'],
            'adults_count' => $validated['status'] === RsvpResponse::STATUS_ATTENDING
                ? ($validated['adults_count'] ?? 1)
                : 0,
            'children_count' => $validated['status'] === RsvpResponse::STATUS_ATTENDING
                ? ($validated['children_count'] ?? 0)
                : 0,
            'ip_hash' => hash('sha256', $request->ip().$request->userAgent()),
        ]);

        RsvpResponseSubmitted::dispatch($response)->afterCommit();

        $message = $validated['status'] === RsvpResponse::STATUS_ATTENDING
            ? "Rahmat, {$response->guest_name}! Javobingiz qabul qilindi."
            : "Rahmat, {$response->guest_name}. Yaxshi tilaklar tilaymiz.";

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => $response->id,
                'status' => $response->status,
            ],
        ], 201);
    }
}

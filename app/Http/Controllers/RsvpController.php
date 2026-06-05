<?php

namespace App\Http\Controllers;

use App\Events\RsvpResponseSubmitted;
use App\Http\Requests\StoreRsvpRequest;
use App\Models\RsvpResponse;
use App\Support\InvitationResolver;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    public function store(StoreRsvpRequest $request, string $slug): JsonResponse
    {
        $invitation = InvitationResolver::findPublic($slug);

        if (! $invitation->rsvp_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'RSVP ushbu taklifnoma uchun o\'chirilgan.',
            ], 403);
        }

        $validated = $request->validated();
        $isAttending = $validated['status'] === RsvpResponse::STATUS_ATTENDING;

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

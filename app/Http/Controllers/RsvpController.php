<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRsvpRequest;
use App\Models\Invitation;
use App\Models\RsvpResponse;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    public function store(StoreRsvpRequest $request, string $slug): JsonResponse
    {
        $invitation = Invitation::query()
            ->where('slug', $slug)
            ->where('status', Invitation::STATUS_PUBLISHED)
            ->firstOrFail();

        $validated = $request->validated();

        $response = $invitation->rsvpResponses()->create([
            'guest_name' => $validated['guest_name'],
            'status' => $validated['status'],
            'adults_count' => $validated['status'] === RsvpResponse::STATUS_ATTENDING
                ? ($validated['adults_count'] ?? 1)
                : 0,
            'children_count' => $validated['status'] === RsvpResponse::STATUS_ATTENDING
                ? ($validated['children_count'] ?? 0)
                : 0,
            'ip_hash' => hash('sha256', $request->ip().$request->userAgent()),
        ]);

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

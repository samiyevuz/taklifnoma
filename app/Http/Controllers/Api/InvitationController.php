<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreInvitationRequest;
use App\Http\Requests\Api\UpdateInvitationRequest;
use App\Http\Resources\InvitationResource;
use App\Models\Invitation;
use App\Services\InvitationApiService;
use App\Support\InvitationResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class InvitationController extends Controller
{
    public function __construct(
        private readonly InvitationApiService $invitationApiService,
    ) {}

    public function store(StoreInvitationRequest $request): JsonResponse
    {
        $invitation = $this->invitationApiService->create(
            $request->user()->id,
            $request->validated()
        );

        return (new InvitationResource($invitation))
            ->response()
            ->setStatusCode(201);
    }

    public function update(UpdateInvitationRequest $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('update', $invitation);

        $updated = $this->invitationApiService->update(
            $invitation,
            $request->validated()
        );

        Cache::forget($this->publicCacheKey($updated->custom_slug));
        Cache::forget($this->publicCacheKey($updated->uuid));

        return (new InvitationResource($updated))->response();
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $cacheKey = $this->publicCacheKey($slug);
        $ttl = now()->addMinutes(5);

        $payload = Cache::remember($cacheKey, $ttl, function () use ($slug) {
            $invitation = InvitationResolver::findPublic($slug);

            return (new InvitationResource($invitation))->resolve();
        });

        return response()
            ->json(['data' => $payload])
            ->header('Cache-Control', 'public, max-age=300, stale-while-revalidate=60');
    }

    private function publicCacheKey(string $slug): string
    {
        return 'invitation.public.'.$slug;
    }
}

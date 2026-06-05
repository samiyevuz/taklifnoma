<?php

namespace App\Services;

use App\Models\Invitation;
use App\Support\BuilderEventProfile;
use App\Support\InvitationEventData;
use App\Support\TemplateCatalog;
use Illuminate\Support\Str;

class InvitationApiService
{
    public function __construct(
        private readonly InvitationService $invitationService,
    ) {}

    public function create(int $userId, array $payload): Invitation
    {
        $templateSlug = $payload['template_slug'];
        $template = TemplateCatalog::find($templateSlug);
        $eventData = $payload['event_data'];
        $unpacked = InvitationEventData::unpack($eventData);
        $normalized = BuilderEventProfile::normalizeForStorage($templateSlug, [
            'profile' => $eventData['profile'] ?? [],
        ]);

        $publish = ($payload['status'] ?? 'draft') === Invitation::STATUS_ACTIVE;
        $displaySeed = BuilderEventProfile::displayTitle([
            'template' => $template['template'] ?? 'nikoh-premium',
            'profile_meta' => $normalized['profile_meta'],
            'groom_name' => $normalized['groom_name'],
            'bride_name' => $normalized['bride_name'],
        ]);

        $customSlug = $payload['custom_slug']
            ?? $this->invitationService->generateSlug($normalized['groom_name'], $normalized['bride_name']);

        return $this->invitationService->create([
            'user_id' => $userId,
            'uuid' => (string) Str::uuid(),
            'template_slug' => $templateSlug,
            'template' => $template['template'] ?? 'nikoh-premium',
            'event_type' => $payload['event_type'],
            'custom_slug' => $customSlug,
            'groom_name' => $normalized['groom_name'],
            'bride_name' => $normalized['bride_name'],
            'profile_meta' => $normalized['profile_meta'],
            'event_at' => $unpacked['event_at'],
            'event_city' => $unpacked['event_city'],
            'venue_name' => $unpacked['venue_name'],
            'venue_address' => $unpacked['venue_address'],
            'map_lat' => $unpacked['map_lat'],
            'map_lng' => $unpacked['map_lng'],
            'invitation_text_1' => $unpacked['invitation_text_1'],
            'invitation_text_2' => $unpacked['invitation_text_2'],
            'family_signature' => $unpacked['family_signature'],
            'music_url' => $unpacked['music_url'],
            'dress_colors' => $unpacked['dress_colors'],
            'rsvp_enabled' => $unpacked['rsvp_enabled'],
            'event_data' => $eventData,
        ], $publish);
    }

    public function update(Invitation $invitation, array $payload): Invitation
    {
        $templateSlug = $payload['template_slug'] ?? $invitation->template_slug ?? 'nikoh';
        $template = TemplateCatalog::find($templateSlug);
        $eventData = $payload['event_data'];
        $unpacked = InvitationEventData::unpack($eventData);
        $normalized = BuilderEventProfile::normalizeForStorage($templateSlug, [
            'profile' => $eventData['profile'] ?? [],
        ]);

        $publish = match ($payload['status'] ?? null) {
            Invitation::STATUS_ACTIVE => true,
            Invitation::STATUS_DRAFT => false,
            default => null,
        };

        $attributes = InvitationEventData::mergeIntoAttributes([
            'template_slug' => $templateSlug,
            'template' => $template['template'] ?? $invitation->template,
            'event_type' => $payload['event_type'] ?? $invitation->event_type,
            'custom_slug' => $payload['custom_slug'] ?? $invitation->custom_slug,
            'groom_name' => $normalized['groom_name'],
            'bride_name' => $normalized['bride_name'],
            'profile_meta' => $normalized['profile_meta'],
            'event_at' => $unpacked['event_at'],
            'event_city' => $unpacked['event_city'],
            'venue_name' => $unpacked['venue_name'],
            'venue_address' => $unpacked['venue_address'],
            'map_lat' => $unpacked['map_lat'],
            'map_lng' => $unpacked['map_lng'],
            'invitation_text_1' => $unpacked['invitation_text_1'],
            'invitation_text_2' => $unpacked['invitation_text_2'],
            'family_signature' => $unpacked['family_signature'],
            'music_url' => $unpacked['music_url'],
            'dress_colors' => $unpacked['dress_colors'],
            'rsvp_enabled' => $unpacked['rsvp_enabled'],
            'event_data' => $eventData,
        ]);

        if (isset($attributes['custom_slug'])) {
            $attributes['slug'] = $attributes['custom_slug'];
        }

        if (($payload['status'] ?? null) === Invitation::STATUS_EXPIRED) {
            $attributes['status'] = Invitation::STATUS_EXPIRED;
            $attributes['expires_at'] = now();
            $publish = false;
        }

        return $this->invitationService->update($invitation, $attributes, $publish);
    }
}

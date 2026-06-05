<?php

namespace App\Support;

use App\Models\Invitation;
use Illuminate\Support\Arr;

class InvitationEventData
{
    public static function pack(array $payload): array
    {
        $profile = $payload['profile'] ?? $payload['profile_meta'] ?? [];

        return [
            'profile' => is_array($profile) ? $profile : [],
            'schedule' => [
                'event_at' => $payload['event_at'] ?? null,
                'event_city' => $payload['event_city'] ?? null,
            ],
            'venue' => [
                'name' => $payload['venue_name'] ?? null,
                'address' => $payload['venue_address'] ?? null,
                'map_lat' => $payload['map_lat'] ?? null,
                'map_lng' => $payload['map_lng'] ?? null,
            ],
            'copy' => [
                'invitation_text_1' => $payload['invitation_text_1'] ?? null,
                'invitation_text_2' => $payload['invitation_text_2'] ?? null,
                'family_signature' => $payload['family_signature'] ?? null,
            ],
            'media' => [
                'music_url' => $payload['music_url'] ?? null,
                'cover_image' => $payload['cover_image'] ?? null,
                'template_blade' => $payload['template'] ?? null,
                'template_variant' => $payload['template_variant'] ?? null,
            ],
            'dress_colors' => $payload['dress_colors'] ?? [],
            'rsvp_enabled' => (bool) ($payload['rsvp_enabled'] ?? true),
        ];
    }

    public static function unpack(?array $eventData): array
    {
        $eventData ??= [];

        return [
            'profile_meta' => $eventData['profile'] ?? [],
            'event_at' => Arr::get($eventData, 'schedule.event_at'),
            'event_city' => Arr::get($eventData, 'schedule.event_city'),
            'venue_name' => Arr::get($eventData, 'venue.name'),
            'venue_address' => Arr::get($eventData, 'venue.address'),
            'map_lat' => Arr::get($eventData, 'venue.map_lat'),
            'map_lng' => Arr::get($eventData, 'venue.map_lng'),
            'invitation_text_1' => Arr::get($eventData, 'copy.invitation_text_1'),
            'invitation_text_2' => Arr::get($eventData, 'copy.invitation_text_2'),
            'family_signature' => Arr::get($eventData, 'copy.family_signature'),
            'music_url' => Arr::get($eventData, 'media.music_url'),
            'cover_image' => Arr::get($eventData, 'media.cover_image'),
            'template' => Arr::get($eventData, 'media.template_blade'),
            'template_variant' => Arr::get($eventData, 'media.template_variant'),
            'dress_colors' => $eventData['dress_colors'] ?? [],
            'rsvp_enabled' => (bool) ($eventData['rsvp_enabled'] ?? true),
        ];
    }

    public static function fromInvitation(Invitation $invitation): array
    {
        if (is_array($invitation->event_data) && $invitation->event_data !== []) {
            return $invitation->event_data;
        }

        return self::pack([
            'profile_meta' => $invitation->profile_meta,
            'event_at' => $invitation->event_at,
            'event_city' => $invitation->event_city,
            'venue_name' => $invitation->venue_name,
            'venue_address' => $invitation->venue_address,
            'map_lat' => $invitation->map_lat,
            'map_lng' => $invitation->map_lng,
            'invitation_text_1' => $invitation->invitation_text_1,
            'invitation_text_2' => $invitation->invitation_text_2,
            'family_signature' => $invitation->family_signature,
            'music_url' => $invitation->music_url,
            'cover_image' => $invitation->cover_image,
            'template' => $invitation->template,
            'template_variant' => $invitation->template_variant,
            'dress_colors' => $invitation->dress_colors,
            'rsvp_enabled' => $invitation->rsvp_enabled,
        ]);
    }

    public static function mergeIntoAttributes(array $attributes): array
    {
        $eventData = self::pack($attributes);
        $attributes['event_data'] = $eventData;

        $unpacked = self::unpack($eventData);

        foreach ($unpacked as $key => $value) {
            if ($value !== null || in_array($key, ['profile_meta', 'dress_colors', 'rsvp_enabled'], true)) {
                $attributes[$key] = $value;
            }
        }

        return $attributes;
    }
}

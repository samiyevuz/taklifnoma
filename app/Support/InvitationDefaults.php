<?php

namespace App\Support;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationDefaults
{
    public static function dressColors(): array
    {
        return [
            ['name' => __('builder.defaults.dress_beige'), 'hex' => '#E8DFD0', 'note' => __('builder.defaults.dress_beige_note')],
            ['name' => __('builder.defaults.dress_pink'), 'hex' => '#E8B4B8', 'note' => __('builder.defaults.dress_pink_note')],
            ['name' => __('builder.defaults.dress_gold'), 'hex' => '#C9A227', 'note' => __('builder.defaults.dress_gold_note')],
            ['name' => __('builder.defaults.dress_emerald'), 'hex' => '#0D6B5C', 'note' => __('builder.defaults.dress_emerald_note')],
        ];
    }

    public static function musicPresets(): array
    {
        return [
            [
                'id' => 'romantic',
                'label' => __('builder.defaults.music_romantic'),
                'url' => asset('audio/romantic-wedding.mp3'),
            ],
            [
                'id' => 'classic',
                'label' => __('builder.defaults.music_classic'),
                'url' => asset('audio/romantic-wedding.mp3'),
            ],
            [
                'id' => 'custom',
                'label' => __('builder.defaults.music_custom'),
                'url' => '',
            ],
            [
                'id' => 'upload',
                'label' => __('builder.defaults.music_upload'),
                'url' => '',
            ],
        ];
    }

    public static function demoAttributes(): array
    {
        return [
            'slug' => 'farhod-shirin',
            'template' => 'nikoh-premium',
            'status' => 'active',
            'groom_name' => 'Farhod',
            'bride_name' => 'Shirin',
            'event_type' => 'Nikoh To\'yi',
            'event_at' => '2026-09-22 18:00:00',
            'event_city' => 'Toshkent',
            'venue_name' => 'Muhtasham To\'yxonasi',
            'venue_address' => 'Toshkent sh., Amir Temur ko\'chasi 108',
            'map_lat' => 41.311081,
            'map_lng' => 69.240562,
            'invitation_text_1' => 'Hurmatli mehmon! Hayotimizning eng muborak kunida — nikoh to\'yimizda bizni sharaf bilan quvvatlashishingizni chin qalbdan so\'raymiz.',
            'invitation_text_2' => 'Sizning iliq tilaklaringiz va qatnashuvingiz biz uchun eng katta baxt. Marosimimizda sizni kutib qolamiz.',
            'family_signature' => 'Farhod & Shirin oilalari',
            'music_url' => asset('audio/romantic-wedding.mp3'),
            'dress_colors' => self::dressColors(),
            'rsvp_enabled' => true,
            'published_at' => now(),
        ];
    }

    public static function demoForTemplate(string $slug): array
    {
        $catalog = TemplateCatalog::find($slug) ?? TemplateCatalog::find('nikoh');
        $profile = BuilderEventProfile::demoProfile($slug);
        $normalized = BuilderEventProfile::normalizeForStorage($slug, ['profile' => $profile]);
        $trans = __("landing.templates.{$slug}");
        $eventType = is_array($trans) ? ($trans['title'] ?? 'Nikoh To\'yi') : 'Nikoh To\'yi';

        return array_merge(self::demoAttributes(), [
            'slug' => 'preview-'.$slug,
            'custom_slug' => 'preview-'.$slug,
            'template' => $catalog['template'] ?? 'nikoh-premium',
            'template_slug' => $slug,
            'event_type' => $eventType,
            'groom_name' => $normalized['groom_name'],
            'bride_name' => $normalized['bride_name'],
            'profile_meta' => $normalized['profile_meta'],
            'status' => Invitation::STATUS_ACTIVE,
            'published_at' => now(),
            'rsvp_enabled' => false,
        ]);
    }

    public static function demoInvitation(string $slug): Invitation
    {
        $attributes = self::demoForTemplate($slug);
        $invitation = new Invitation($attributes);
        $invitation->uuid = (string) Str::uuid();
        $invitation->event_data = InvitationEventData::fromInvitation($invitation);

        return $invitation;
    }
}

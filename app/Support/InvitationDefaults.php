<?php

namespace App\Support;

use App\Models\Invitation;
use Illuminate\Support\Str;

class InvitationDefaults
{
    public static function dressColors(): array
    {
        return [
            ['name' => 'Bej', 'hex' => '#E8DFD0', 'note' => 'Bej va krem tonlari — klassik va nafis tanlov.'],
            ['name' => 'Pushti', 'hex' => '#E8B4B8', 'note' => 'Pastel pushti — romantik va yumshoq ranglar.'],
            ['name' => 'Oltin', 'hex' => '#C9A227', 'note' => 'Oltin aksentlar — hashamatli ko\'rinish uchun.'],
            ['name' => 'Zumrad', 'hex' => '#0D6B5C', 'note' => 'Zumrad yashil — tabiat ilhomidagi nafis ton.'],
        ];
    }

    public static function musicPresets(): array
    {
        return [
            [
                'id' => 'romantic',
                'label' => 'Romantik pianino',
                'url' => asset('audio/romantic-wedding.mp3'),
            ],
            [
                'id' => 'classic',
                'label' => 'Klassik to\'y marosimi',
                'url' => asset('audio/romantic-wedding.mp3'),
            ],
            [
                'id' => 'custom',
                'label' => 'O\'z musiqam (MP3 havola)',
                'url' => '',
            ],
            [
                'id' => 'upload',
                'label' => 'Kompyuterdan yuklash (MP3)',
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

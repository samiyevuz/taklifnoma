<?php

namespace App\Support;

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

    public static function demoAttributes(): array
    {
        return [
            'slug' => 'ali-vali',
            'template' => 'nikoh-premium',
            'status' => 'published',
            'groom_name' => 'Ali',
            'bride_name' => 'Vali',
            'event_type' => 'Nikoh To\'yi',
            'event_at' => '2026-09-22 18:00:00',
            'event_city' => 'Toshkent',
            'venue_name' => 'Muhtasham To\'yxonasi',
            'venue_address' => 'Toshkent sh., Amir Temur ko\'chasi 108',
            'map_lat' => 41.311081,
            'map_lng' => 69.240562,
            'invitation_text_1' => 'Hurmatli mehmon! Hayotimizning eng muborak kunida — nikoh to\'yimizda bizni sharaf bilan quvvatlashishingizni chin qalbdan so\'raymiz.',
            'invitation_text_2' => 'Sizning iliq tilaklaringiz va qatnashuvingiz biz uchun eng katta baxt. Marosimimizda sizni kutib qolamiz.',
            'family_signature' => 'Ali & Vali oilalari',
            'dress_colors' => self::dressColors(),
            'published_at' => now(),
        ];
    }
}

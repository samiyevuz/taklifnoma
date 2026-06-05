<?php

namespace App\Support;

class TemplateCatalog
{
    public static function all(): array
    {
        return [
            [
                'slug' => 'nikoh',
                'template' => 'nikoh-premium',
                'visual' => 'template-visual--nikoh',
                'title' => 'Nikoh To\'yi',
                'desc' => 'Oltin naqshlar, klassik serif va romantik fon — to\'yingiz uchun eng hashamatli shablon.',
                'price' => '89 000 so\'m',
                'price_amount' => 89000,
                'tag' => 'Eng mashhur',
                'preview_route' => 'invitation.show',
                'preview_param' => 'farhod-shirin',
            ],
            [
                'slug' => 'qiz',
                'template' => 'qiz-uzatish',
                'visual' => 'template-visual--qiz',
                'title' => 'Qiz Uzatish',
                'desc' => 'Nozik pushti va binafsha tonlar — an\'anaviy marosim uchun zamonaviy nafosat.',
                'price' => '79 000 so\'m',
                'price_amount' => 79000,
                'tag' => 'Yangi',
                'preview_route' => null,
                'preview_param' => null,
            ],
            [
                'slug' => 'sunnat',
                'template' => 'sunnat-toyi',
                'visual' => 'template-visual--sunnat',
                'title' => 'Sunnat To\'yi',
                'desc' => 'Yashil va marvarid palitrasi — oilaviy bayram uchun iliq va yorqin dizayn.',
                'price' => '69 000 so\'m',
                'price_amount' => 69000,
                'tag' => null,
                'preview_route' => null,
                'preview_param' => null,
            ],
            [
                'slug' => 'birthday',
                'template' => 'birthday-premium',
                'visual' => 'template-visual--birthday',
                'title' => 'Tug\'ilgan Kun',
                'desc' => 'Shampan oltin va iliq krem — har qanday yosh uchun zamonaviy premium taklif.',
                'price' => '59 000 so\'m',
                'price_amount' => 59000,
                'tag' => 'Chegirma',
                'preview_route' => null,
                'preview_param' => null,
            ],
        ];
    }

    public static function find(string $slug): ?array
    {
        foreach (self::all() as $template) {
            if ($template['slug'] === $slug) {
                return $template;
            }
        }

        return null;
    }

    public static function slugs(): array
    {
        return array_column(self::all(), 'slug');
    }
}

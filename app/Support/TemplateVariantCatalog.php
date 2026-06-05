<?php

namespace App\Support;

class TemplateVariantCatalog
{
    public static function definitions(): array
    {
        return [
            'nikoh' => [
                [
                    'id' => 'nikoh-classic',
                    'title' => 'Nikoh Classic',
                    'subtitle' => 'Sodda va nafis dizayn',
                    'price_amount' => 69000,
                    'blade' => 'nikoh-premium',
                    'theme' => 'classic',
                    'cover_image' => 'images/templates/nikoh.jpg',
                    'badge' => null,
                ],
                [
                    'id' => 'nikoh-premium',
                    'title' => 'Nikoh Premium',
                    'subtitle' => 'Oltin naqshlar va romantik fon',
                    'price_amount' => 89000,
                    'blade' => 'nikoh-premium',
                    'theme' => 'premium',
                    'cover_image' => 'images/templates/nikoh.jpg',
                    'badge' => 'Eng mashhur',
                ],
                [
                    'id' => 'nikoh-luxury',
                    'title' => 'Nikoh Luxury',
                    'subtitle' => 'Kinematik animatsiya va musiqa',
                    'price_amount' => 119000,
                    'blade' => 'nikoh-premium',
                    'theme' => 'luxury',
                    'cover_image' => 'images/templates/nikoh.jpg',
                    'badge' => 'Yangi',
                ],
                [
                    'id' => 'nikoh-royal',
                    'title' => 'Nikoh Royal',
                    'subtitle' => 'VIP effektlar va maxsus domen',
                    'price_amount' => 149000,
                    'blade' => 'nikoh-premium',
                    'theme' => 'royal',
                    'cover_image' => 'images/templates/nikoh.jpg',
                    'badge' => 'VIP',
                ],
            ],
            'qiz' => [
                [
                    'id' => 'qiz-standard',
                    'title' => 'Qiz Uzatish Standard',
                    'subtitle' => 'Nozik pushti palitra',
                    'price_amount' => 69000,
                    'blade' => 'qiz-uzatish',
                    'theme' => 'classic',
                    'cover_image' => 'images/templates/qiz.jpg',
                    'badge' => null,
                ],
                [
                    'id' => 'qiz-premium',
                    'title' => 'Qiz Uzatish Premium',
                    'subtitle' => 'Zamonaviy guldasta uslubi',
                    'price_amount' => 79000,
                    'blade' => 'qiz-uzatish',
                    'theme' => 'premium',
                    'cover_image' => 'images/templates/qiz.jpg',
                    'badge' => 'Eng mashhur',
                ],
                [
                    'id' => 'qiz-luxury',
                    'title' => 'Qiz Uzatish Luxury',
                    'subtitle' => 'Premium fon musiqasi bilan',
                    'price_amount' => 99000,
                    'blade' => 'qiz-uzatish',
                    'theme' => 'luxury',
                    'cover_image' => 'images/templates/qiz.jpg',
                    'badge' => null,
                ],
            ],
            'sunnat' => [
                [
                    'id' => 'sunnat-standard',
                    'title' => 'Sunnat Standard',
                    'subtitle' => 'Iliq oltin tonlar',
                    'price_amount' => 59000,
                    'blade' => 'sunnat-toyi',
                    'theme' => 'classic',
                    'cover_image' => 'images/templates/sunnat.jpg',
                    'badge' => null,
                ],
                [
                    'id' => 'sunnat-premium',
                    'title' => 'Sunnat Premium',
                    'subtitle' => 'Yorqin bolalar motivlari',
                    'price_amount' => 69000,
                    'blade' => 'sunnat-toyi',
                    'theme' => 'premium',
                    'cover_image' => 'images/templates/sunnat.jpg',
                    'badge' => 'Eng mashhur',
                ],
                [
                    'id' => 'sunnat-luxury',
                    'title' => 'Sunnat Luxury',
                    'subtitle' => 'Animatsiya va RSVP panel',
                    'price_amount' => 89000,
                    'blade' => 'sunnat-toyi',
                    'theme' => 'luxury',
                    'cover_image' => 'images/templates/sunnat.jpg',
                    'badge' => null,
                ],
            ],
        ];
    }

    public static function forFamily(string $familySlug): array
    {
        $variants = self::definitions()[$familySlug] ?? null;

        if (is_array($variants) && $variants !== []) {
            return array_map(fn (array $variant) => self::enrich($variant, $familySlug), $variants);
        }

        $template = TemplateCatalog::find($familySlug);

        if (! $template) {
            return [];
        }

        return [self::enrich([
            'id' => $familySlug.'-standard',
            'title' => $template['title'],
            'subtitle' => $template['desc'] ?? '',
            'price_amount' => $template['price_amount'],
            'blade' => $template['template'],
            'theme' => 'premium',
            'cover_image' => $template['cover_image'] ?? null,
            'badge' => $template['tag'] ?? null,
        ], $familySlug)];
    }

    public static function find(string $familySlug, ?string $variantId): ?array
    {
        if (blank($variantId)) {
            return self::defaultForFamily($familySlug);
        }

        foreach (self::forFamily($familySlug) as $variant) {
            if ($variant['id'] === $variantId) {
                return $variant;
            }
        }

        return self::defaultForFamily($familySlug);
    }

    public static function defaultForFamily(string $familySlug): ?array
    {
        $variants = self::forFamily($familySlug);

        if ($variants === []) {
            return null;
        }

        foreach ($variants as $variant) {
            if (($variant['badge'] ?? null) === 'Eng mashhur') {
                return $variant;
            }
        }

        return $variants[0];
    }

    public static function resolvePrice(string $familySlug, ?string $variantId): int
    {
        $variant = self::find($familySlug, $variantId);
        if ($variant) {
            return (int) $variant['price_amount'];
        }

        $template = TemplateCatalog::find($familySlug);

        return (int) ($template['price_amount'] ?? 89000);
    }

    private static function enrich(array $variant, string $familySlug): array
    {
        $variant['family_slug'] = $familySlug;
        $variant['price'] = number_format($variant['price_amount'], 0, '.', ' ').' '.__('landing.currency');
        $variant['cover_url'] = isset($variant['cover_image'])
            ? asset($variant['cover_image'])
            : null;

        return $variant;
    }
}

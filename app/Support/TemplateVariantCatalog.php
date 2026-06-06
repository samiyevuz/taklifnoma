<?php

namespace App\Support;

use App\Models\EventTemplate;
use App\Models\EventTemplateVariant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class TemplateVariantCatalog
{
    public static function staticDefinitions(): array
    {
        return [
            'nikoh' => self::tiers('nikoh', 'Nikoh', 'nikoh-premium', 'images/templates/nikoh.jpg', [
                ['suffix' => 'Classic', 'subtitle' => 'Sodda va nafis dizayn', 'price' => 69000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Oltin naqshlar va romantik fon', 'price' => 89000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Kinematik animatsiya va musiqa', 'price' => 119000, 'theme' => 'luxury', 'badge' => 'Yangi'],
                ['suffix' => 'Royal', 'subtitle' => 'VIP effektlar va maxsus domen', 'price' => 149000, 'theme' => 'royal', 'badge' => 'VIP'],
            ]),
            'qiz' => self::tiers('qiz', 'Qiz Uzatish', 'qiz-uzatish', 'images/templates/qiz.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Nozik pushti palitra', 'price' => 69000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Zamonaviy guldasta uslubi', 'price' => 79000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Premium fon musiqasi bilan', 'price' => 99000, 'theme' => 'luxury'],
            ]),
            'sunnat' => self::tiers('sunnat', 'Sunnat To\'yi', 'sunnat-toyi', 'images/templates/sunnat.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Iliq oltin tonlar', 'price' => 59000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Yorqin bolalar motivlari', 'price' => 69000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Animatsiya va RSVP panel', 'price' => 89000, 'theme' => 'luxury'],
            ]),
            'beshik' => self::tiers('beshik', 'Beshik To\'yi', 'beshik-toyi', 'images/templates/beshik.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Yumshoq yashil tabiat tonlari', 'price' => 59000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Nafis chaqaloq motivlari', 'price' => 75000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Animatsiya va maxsus muqova', 'price' => 95000, 'theme' => 'luxury'],
            ]),
            'yubiley' => self::tiers('yubiley', 'Yubiley', 'yubiley-premium', 'images/templates/yubiley.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Pushti va oltin aksentlar', 'price' => 55000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => '50 yoki 60 yubiley uchun hashamat', 'price' => 69000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Kinematik slayd va musiqa', 'price' => 89000, 'theme' => 'luxury'],
            ]),
            'nahor' => self::tiers('nahor', 'Nahor Oshi', 'nahor-oshi', 'images/templates/nahor.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Moviy va lavanda ertalabki uslub', 'price' => 65000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Zamonaviy dasturxon taklifi', 'price' => 79000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Premium fon va RSVP panel', 'price' => 99000, 'theme' => 'luxury'],
            ]),
            'fotiha' => self::tiers('fotiha', 'Fotiha To\'yi', 'fotiha-toyi', 'images/templates/fotiha.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Romantik pushti va oltin naqsh', 'price' => 69000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Unashtiruv uchun premium dizayn', 'price' => 85000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Hashamatli animatsiya va musiqa', 'price' => 109000, 'theme' => 'luxury'],
            ]),
            'birthday' => self::tiers('birthday', 'Tug\'ilgan Kun', 'birthday-premium', 'images/templates/birthday.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Iliq krem va shampan oltin', 'price' => 49000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Har qanday yosh uchun zamonaviy', 'price' => 59000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Premium effektlar va fon musiqasi', 'price' => 79000, 'theme' => 'luxury'],
            ]),
            'iftorlik' => self::tiers('iftorlik', 'Iftorlik', 'iftorlik-premium', 'images/templates/iftorlik.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'To\'q yashil Ramazon uslubi', 'price' => 59000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Iftor marosimi uchun nafis taklif', 'price' => 72000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Oltin naqshlar va premium musiqa', 'price' => 92000, 'theme' => 'luxury'],
            ]),
            'aqiyqa' => self::tiers('aqiyqa', 'Aqiyqa', 'aqiyqa-toyi', 'images/templates/aqiyqa.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'Krem va bej samimiy tonlar', 'price' => 55000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Yangi chaqaloq uchun iliq dizayn', 'price' => 68000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Premium muqova va fon musiqasi', 'price' => 88000, 'theme' => 'luxury'],
            ]),
            'bitiruv' => self::tiers('bitiruv', 'Bitiruv Oqshomi', 'bitiruv-oqshomi', 'images/templates/bitiruv.jpg', [
                ['suffix' => 'Standard', 'subtitle' => 'To\'q ko\'k rasmiy uslub', 'price' => 49000, 'theme' => 'classic'],
                ['suffix' => 'Premium', 'subtitle' => 'Maktab yoki universitet bitiruvi', 'price' => 64000, 'theme' => 'premium', 'badge' => 'Eng mashhur'],
                ['suffix' => 'Luxury', 'subtitle' => 'Oltin aksentlar va RSVP panel', 'price' => 84000, 'theme' => 'luxury'],
            ]),
        ];
    }

    /** @deprecated Use staticDefinitions() */
    public static function definitions(): array
    {
        return self::staticDefinitions();
    }

    public static function forFamily(string $familySlug): array
    {
        if (self::usesDatabase()) {
            return Cache::remember("landing.variants.{$familySlug}", 3600, function () use ($familySlug) {
                $template = EventTemplate::query()->where('slug', $familySlug)->first();

                if (! $template) {
                    return [];
                }

                return $template->activeVariants
                    ->map(fn (EventTemplateVariant $variant) => self::enrich($variant->toCatalogArray($familySlug), $familySlug))
                    ->all();
            });
        }

        $variants = self::staticDefinitions()[$familySlug] ?? null;

        if (is_array($variants) && $variants !== []) {
            return array_map(fn (array $variant) => self::enrich($variant, $familySlug), $variants);
        }

        $template = TemplateCatalog::find($familySlug);

        if (! $template) {
            return [];
        }

        return array_map(
            fn (array $variant) => self::enrich($variant, $familySlug),
            self::generatedTiers(
                $familySlug,
                $template['title'] ?? $familySlug,
                $template['template'] ?? 'nikoh-premium',
                (int) ($template['price_amount'] ?? 69000),
                $template['cover_image'] ?? null,
                $template['desc'] ?? ''
            )
        );
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
            if (! empty($variant['is_default'])) {
                return $variant;
            }
        }

        foreach ($variants as $variant) {
            if (($variant['badge'] ?? null) === 'Eng mashhur') {
                return $variant;
            }
        }

        return $variants[(int) floor(count($variants) / 2)] ?? $variants[0];
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

    public static function clearCache(): void
    {
        if (! Schema::hasTable('event_templates')) {
            return;
        }

        foreach (EventTemplate::query()->pluck('slug') as $slug) {
            Cache::forget("landing.variants.{$slug}");
        }
    }

    private static function usesDatabase(): bool
    {
        return Schema::hasTable('event_template_variants')
            && EventTemplateVariant::query()->where('is_active', true)->exists();
    }

    private static function tiers(
        string $slug,
        string $titlePrefix,
        string $blade,
        string $coverImage,
        array $levels,
    ): array {
        return array_map(function (array $level) use ($slug, $titlePrefix, $blade, $coverImage) {
            $suffix = $level['suffix'];
            $idSuffix = strtolower(str_replace(' ', '-', $suffix));

            return [
                'id' => "{$slug}-{$idSuffix}",
                'title' => trim("{$titlePrefix} {$suffix}"),
                'subtitle' => $level['subtitle'],
                'price_amount' => (int) $level['price'],
                'blade' => $blade,
                'theme' => $level['theme'] ?? 'premium',
                'cover_image' => $coverImage,
                'badge' => $level['badge'] ?? null,
            ];
        }, $levels);
    }

    private static function generatedTiers(
        string $slug,
        string $title,
        string $blade,
        int $premiumPrice,
        ?string $coverImage,
        string $desc,
    ): array {
        $classicPrice = max(39000, self::roundPrice($premiumPrice - 20000));
        $luxuryPrice = self::roundPrice($premiumPrice + 20000);

        return self::tiers($slug, $title, $blade, $coverImage ?? '', [
            [
                'suffix' => 'Classic',
                'subtitle' => $desc ?: 'Sodda va zamonaviy dizayn',
                'price' => $classicPrice,
                'theme' => 'classic',
            ],
            [
                'suffix' => 'Premium',
                'subtitle' => $desc ?: 'Eng mashhur variant',
                'price' => $premiumPrice,
                'theme' => 'premium',
                'badge' => 'Eng mashhur',
            ],
            [
                'suffix' => 'Luxury',
                'subtitle' => 'Premium effektlar va fon musiqasi',
                'price' => $luxuryPrice,
                'theme' => 'luxury',
            ],
        ]);
    }

    private static function roundPrice(int $amount): int
    {
        return (int) (round($amount / 1000) * 1000);
    }

    private static function enrich(array $variant, string $familySlug): array
    {
        $theme = $variant['theme'] ?? 'premium';

        $variant['family_slug'] = $familySlug;
        $variant['price'] = number_format($variant['price_amount'], 0, '.', ' ').' '.__('landing.currency');
        $variant['cover_url'] = isset($variant['cover_image']) && $variant['cover_image'] !== ''
            ? asset($variant['cover_image'])
            : null;
        $plan = PlanEntitlements::forTheme($theme);
        $guestLimit = array_key_exists('guest_limit', $variant) && $variant['guest_limit'] !== null
            ? (int) $variant['guest_limit']
            : $plan['guest_limit'];

        $variant['animation'] = $plan['animation'];
        $variant['tier_level'] = self::tierLevelForTheme($theme);
        $variant['cover_focus'] = self::coverFocusForTheme($theme);
        $variant['entitlements'] = array_merge($plan, ['guest_limit' => $guestLimit]);
        $variant['features'] = PlanEntitlements::featureLabels($theme);
        $variant['guest_limit'] = $guestLimit;
        $variant['guest_limit_label'] = PlanEntitlements::guestLimitLabel($guestLimit);

        return $variant;
    }

    private static function tierLevelForTheme(string $theme): int
    {
        return match ($theme) {
            'classic' => 1,
            'premium' => 2,
            'luxury' => 3,
            'royal' => 4,
            default => 2,
        };
    }

    private static function coverFocusForTheme(string $theme): string
    {
        return match ($theme) {
            'classic' => 'center 25%',
            'premium' => 'center 40%',
            'luxury' => 'center 15%',
            'royal' => 'center 30%',
            default => 'center 40%',
        };
    }
}

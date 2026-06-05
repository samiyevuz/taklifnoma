<?php

namespace App\Support;

use App\Models\EventTemplate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class TemplateCatalog
{
    public static function fallbackDefinitions(): array
    {
        return [
            [
                'slug' => 'nikoh',
                'template' => 'nikoh-premium',
                'visual' => 'template-visual--nikoh',
                'cover_image' => 'images/templates/nikoh.jpg',
                'price_amount' => 89000,
                'preview_route' => 'template.preview',
                'preview_param' => 'nikoh',
            ],
            [
                'slug' => 'qiz',
                'template' => 'qiz-uzatish',
                'visual' => 'template-visual--qiz',
                'cover_image' => 'images/templates/qiz.jpg',
                'price_amount' => 79000,
                'preview_route' => 'template.preview',
                'preview_param' => 'qiz',
            ],
            [
                'slug' => 'sunnat',
                'template' => 'sunnat-toyi',
                'visual' => 'template-visual--sunnat',
                'cover_image' => 'images/templates/sunnat.jpg',
                'price_amount' => 69000,
                'preview_route' => 'template.preview',
                'preview_param' => 'sunnat',
            ],
            [
                'slug' => 'beshik',
                'template' => 'beshik-toyi',
                'visual' => 'template-visual--beshik',
                'cover_image' => 'images/templates/beshik.jpg',
                'price_amount' => 75000,
                'preview_route' => 'template.preview',
                'preview_param' => 'beshik',
            ],
            [
                'slug' => 'yubiley',
                'template' => 'yubiley-premium',
                'visual' => 'template-visual--yubiley',
                'cover_image' => 'images/templates/yubiley.jpg',
                'price_amount' => 69000,
                'preview_route' => 'template.preview',
                'preview_param' => 'yubiley',
            ],
            [
                'slug' => 'nahor',
                'template' => 'nahor-oshi',
                'visual' => 'template-visual--nahor',
                'cover_image' => 'images/templates/nahor.jpg',
                'price_amount' => 79000,
                'preview_route' => 'template.preview',
                'preview_param' => 'nahor',
            ],
            [
                'slug' => 'fotiha',
                'template' => 'fotiha-toyi',
                'visual' => 'template-visual--fotiha',
                'cover_image' => 'images/templates/fotiha.jpg',
                'price_amount' => 85000,
                'preview_route' => 'template.preview',
                'preview_param' => 'fotiha',
            ],
            [
                'slug' => 'birthday',
                'template' => 'birthday-premium',
                'visual' => 'template-visual--birthday',
                'cover_image' => 'images/templates/birthday.jpg',
                'price_amount' => 59000,
                'preview_route' => 'template.preview',
                'preview_param' => 'birthday',
            ],
            [
                'slug' => 'iftorlik',
                'template' => 'iftorlik-premium',
                'visual' => 'template-visual--iftorlik',
                'cover_image' => 'images/templates/iftorlik.jpg',
                'price_amount' => 72000,
                'preview_route' => 'template.preview',
                'preview_param' => 'iftorlik',
            ],
            [
                'slug' => 'aqiyqa',
                'template' => 'aqiyqa-toyi',
                'visual' => 'template-visual--aqiyqa',
                'cover_image' => 'images/templates/aqiyqa.jpg',
                'price_amount' => 68000,
                'preview_route' => 'template.preview',
                'preview_param' => 'aqiyqa',
            ],
            [
                'slug' => 'bitiruv',
                'template' => 'bitiruv-oqshomi',
                'visual' => 'template-visual--bitiruv',
                'cover_image' => 'images/templates/bitiruv.jpg',
                'price_amount' => 64000,
                'preview_route' => 'template.preview',
                'preview_param' => 'bitiruv',
            ],
        ];
    }

    public static function definitions(): array
    {
        if (! self::usesDatabase()) {
            return self::fallbackDefinitions();
        }

        return Cache::remember('landing.templates.definitions', 3600, function () {
            return EventTemplate::query()
                ->active()
                ->ordered()
                ->get()
                ->map(fn (EventTemplate $template) => [
                    'slug' => $template->slug,
                    'template' => $template->blade,
                    'visual' => $template->visual,
                    'cover_image' => $template->cover_path,
                    'price_amount' => $template->price_amount,
                    'preview_route' => $template->preview_route,
                    'preview_param' => $template->preview_param,
                ])
                ->all();
        });
    }

    public static function all(): array
    {
        if (! self::usesDatabase()) {
            return array_map(function (array $item) {
                return self::enrichDefinition($item);
            }, self::fallbackDefinitions());
        }

        $locale = app()->getLocale();

        return Cache::remember("landing.templates.all.{$locale}", 3600, function () use ($locale) {
            return EventTemplate::query()
                ->active()
                ->ordered()
                ->get()
                ->map(fn (EventTemplate $template) => $template->toCatalogArray($locale))
                ->all();
        });
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

    public static function findByBlade(string $blade): ?array
    {
        foreach (self::definitions() as $item) {
            if ($item['template'] === $blade) {
                return self::find($item['slug']);
            }
        }

        return null;
    }

    public static function slugs(): array
    {
        return array_column(self::definitions(), 'slug');
    }

    public static function clearCache(): void
    {
        Cache::forget('landing.templates.definitions');

        foreach (['uz', 'en', 'ru'] as $locale) {
            Cache::forget("landing.templates.all.{$locale}");
        }
    }

    private static function usesDatabase(): bool
    {
        return Schema::hasTable('event_templates')
            && EventTemplate::query()->active()->exists();
    }

    private static function enrichDefinition(array $item): array
    {
        $slug = $item['slug'];
        $trans = __("landing.templates.{$slug}");

        $item['title'] = is_array($trans) ? ($trans['title'] ?? $slug) : $slug;
        $item['desc'] = is_array($trans) ? ($trans['desc'] ?? '') : '';
        $item['tag'] = is_array($trans) ? ($trans['tag'] ?? null) : null;
        $item['price'] = number_format($item['price_amount'], 0, '.', ' ').' '.__('landing.currency');
        $item['cover_url'] = isset($item['cover_image'])
            ? asset($item['cover_image'])
            : null;

        return $item;
    }
}

<?php

namespace App\Support;

class TemplateCatalog
{
    public static function definitions(): array
    {
        return [
            [
                'slug' => 'nikoh',
                'template' => 'nikoh-premium',
                'visual' => 'template-visual--nikoh',
                'price_amount' => 89000,
                'preview_route' => 'invitation.show',
                'preview_param' => 'farhod-shirin',
            ],
            [
                'slug' => 'qiz',
                'template' => 'qiz-uzatish',
                'visual' => 'template-visual--qiz',
                'price_amount' => 79000,
                'preview_route' => null,
                'preview_param' => null,
            ],
            [
                'slug' => 'sunnat',
                'template' => 'sunnat-toyi',
                'visual' => 'template-visual--sunnat',
                'price_amount' => 69000,
                'preview_route' => null,
                'preview_param' => null,
            ],
            [
                'slug' => 'birthday',
                'template' => 'birthday-premium',
                'visual' => 'template-visual--birthday',
                'price_amount' => 59000,
                'preview_route' => null,
                'preview_param' => null,
            ],
        ];
    }

    public static function all(): array
    {
        return array_map(function (array $item) {
            $slug = $item['slug'];
            $trans = __("landing.templates.{$slug}");

            $item['title'] = is_array($trans) ? ($trans['title'] ?? $slug) : $slug;
            $item['desc'] = is_array($trans) ? ($trans['desc'] ?? '') : '';
            $item['tag'] = is_array($trans) ? ($trans['tag'] ?? null) : null;
            $item['price'] = number_format($item['price_amount'], 0, '.', ' ').' '.__('landing.currency');

            return $item;
        }, self::definitions());
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
        return array_column(self::definitions(), 'slug');
    }
}

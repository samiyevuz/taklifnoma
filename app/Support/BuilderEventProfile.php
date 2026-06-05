<?php

namespace App\Support;

class BuilderEventProfile
{
    public const LAYOUT_COUPLE = 'couple';

    public const LAYOUT_COUPLE_BRIDE_FIRST = 'couple_bride_first';

    public const LAYOUT_CHILD = 'child';

    public const LAYOUT_CELEBRANT = 'celebrant';

    public const LAYOUT_GRADUATION = 'graduation';

    public const LAYOUT_GENERAL = 'general';

    private const SLUG_LAYOUT_MAP = [
        'nikoh' => self::LAYOUT_COUPLE,
        'fotiha' => self::LAYOUT_COUPLE,
        'qiz' => self::LAYOUT_COUPLE_BRIDE_FIRST,
        'sunnat' => self::LAYOUT_CHILD,
        'beshik' => self::LAYOUT_CHILD,
        'birthday' => self::LAYOUT_CELEBRANT,
        'yubiley' => self::LAYOUT_CELEBRANT,
        'bitiruv' => self::LAYOUT_GRADUATION,
        'nahor' => self::LAYOUT_GENERAL,
        'iftorlik' => self::LAYOUT_GENERAL,
        'aqiyqa' => self::LAYOUT_GENERAL,
    ];

    public static function resolveLayout(string $slug): string
    {
        return self::SLUG_LAYOUT_MAP[$slug] ?? self::LAYOUT_COUPLE;
    }

    public static function resolveSlug(?string $slug, ?string $blade = null): string
    {
        if ($slug && isset(self::SLUG_LAYOUT_MAP[$slug])) {
            return $slug;
        }

        if ($blade) {
            $template = TemplateCatalog::findByBlade($blade);

            return $template['slug'] ?? 'nikoh';
        }

        return 'nikoh';
    }

    public static function fieldsForLayout(string $layout): array
    {
        return match ($layout) {
            self::LAYOUT_COUPLE, self::LAYOUT_COUPLE_BRIDE_FIRST => [
                ['key' => 'groom_name', 'column' => 'groom_name', 'label' => 'builder.groom_name', 'placeholder' => 'builder.placeholders.groom_name', 'required' => true, 'preview' => 'primary'],
                ['key' => 'bride_name', 'column' => 'bride_name', 'label' => 'builder.bride_name', 'placeholder' => 'builder.placeholders.bride_name', 'required' => true, 'preview' => 'secondary'],
            ],
            self::LAYOUT_CHILD => [
                ['key' => 'child_name', 'column' => 'groom_name', 'label' => 'builder.child_name', 'placeholder' => 'builder.placeholders.child_name', 'required' => true, 'preview' => 'primary'],
                ['key' => 'hosts', 'column' => 'bride_name', 'label' => 'builder.hosts', 'placeholder' => 'builder.placeholders.hosts', 'required' => false, 'preview' => 'secondary'],
            ],
            self::LAYOUT_CELEBRANT => [
                ['key' => 'celebrant_name', 'column' => 'groom_name', 'label' => 'builder.celebrant_name', 'placeholder' => 'builder.placeholders.celebrant_name', 'required' => true, 'preview' => 'primary'],
                ['key' => 'milestone', 'column' => 'bride_name', 'label' => 'builder.milestone', 'placeholder' => 'builder.placeholders.milestone', 'required' => true, 'preview' => 'secondary'],
            ],
            self::LAYOUT_GRADUATION => [
                ['key' => 'school_name', 'column' => 'groom_name', 'label' => 'builder.school_name', 'placeholder' => 'builder.placeholders.school_name', 'required' => true, 'preview' => 'primary'],
                ['key' => 'class_name', 'column' => 'bride_name', 'label' => 'builder.class_name', 'placeholder' => 'builder.placeholders.class_name', 'required' => true, 'preview' => 'secondary'],
            ],
            self::LAYOUT_GENERAL => [
                ['key' => 'primary_name', 'column' => 'groom_name', 'label' => 'builder.primary_name', 'placeholder' => 'builder.placeholders.primary_name', 'required' => true, 'preview' => 'primary'],
                ['key' => 'secondary_name', 'column' => 'bride_name', 'label' => 'builder.secondary_name', 'placeholder' => 'builder.placeholders.secondary_name', 'required' => false, 'preview' => 'secondary'],
            ],
            default => self::fieldsForLayout(self::LAYOUT_COUPLE),
        };
    }

    public static function fieldsForSlug(string $slug): array
    {
        return self::fieldsForLayout(self::resolveLayout($slug));
    }

    public static function previewConfig(string $slug): array
    {
        $layout = self::resolveLayout($slug);

        $taglines = [
            'sunnat' => __('builder.preview.sunnat_tagline'),
            'beshik' => __('builder.preview.beshik_tagline'),
            'birthday' => __('builder.preview.birthday_tagline'),
            'yubiley' => __('builder.preview.yubiley_tagline'),
            'bitiruv' => __('builder.preview.bitiruv_tagline'),
            'nahor' => __('builder.preview.nahor_tagline'),
            'iftorlik' => __('builder.preview.iftorlik_tagline'),
            'aqiyqa' => __('builder.preview.aqiyqa_tagline'),
            'fotiha' => __('builder.preview.fotiha_tagline'),
            'qiz' => __('builder.preview.qiz_tagline'),
        ];

        $placeholders = [
            self::LAYOUT_COUPLE => [
                'primary' => __('builder.placeholders.groom_name'),
                'secondary' => __('builder.placeholders.bride_name'),
            ],
            self::LAYOUT_COUPLE_BRIDE_FIRST => [
                'primary' => __('builder.placeholders.bride_name'),
                'secondary' => __('builder.placeholders.groom_name'),
            ],
            self::LAYOUT_CHILD => [
                'primary' => __('builder.placeholders.child_name'),
                'secondary' => __('builder.placeholders.hosts_optional'),
            ],
            self::LAYOUT_CELEBRANT => [
                'primary' => __('builder.placeholders.celebrant_name'),
                'secondary' => __('builder.placeholders.milestone'),
            ],
            self::LAYOUT_GRADUATION => [
                'primary' => __('builder.placeholders.school_name'),
                'secondary' => __('builder.placeholders.class_name'),
            ],
            self::LAYOUT_GENERAL => [
                'primary' => __('builder.placeholders.primary_name'),
                'secondary' => __('builder.placeholders.secondary_name'),
            ],
        ];

        return [
            'layout' => $layout,
            'tagline' => $taglines[$slug] ?? '',
            'placeholders' => $placeholders[$layout] ?? $placeholders[self::LAYOUT_COUPLE],
            'review_label' => match ($layout) {
                self::LAYOUT_CHILD => __('builder.review.child'),
                self::LAYOUT_CELEBRANT => __('builder.review.celebrant'),
                self::LAYOUT_GRADUATION => __('builder.review.graduation'),
                self::LAYOUT_GENERAL => __('builder.review.host'),
                default => __('builder.review.couple'),
            },
            'show_connector' => in_array($layout, [self::LAYOUT_COUPLE, self::LAYOUT_COUPLE_BRIDE_FIRST], true),
            'connector' => $layout === self::LAYOUT_COUPLE_BRIDE_FIRST ? '&' : '&',
            'hero_mode' => match ($layout) {
                self::LAYOUT_CHILD, self::LAYOUT_CELEBRANT => 'single',
                self::LAYOUT_GRADUATION => 'stacked',
                default => 'dual',
            },
        ];
    }

    public static function bootstrapSchema(string $slug, object|array $source): array
    {
        $layout = self::resolveLayout($slug);
        $fields = self::fieldsForLayout($layout);
        $values = self::extractProfile($slug, $source);
        $preview = self::previewConfig($slug);

        return [
            'slug' => $slug,
            'layout' => $layout,
            'fields' => array_map(function (array $field) use ($values) {
                return [
                    'key' => $field['key'],
                    'name' => "profile[{$field['key']}]",
                    'label' => __($field['label']),
                    'placeholder' => __($field['placeholder']),
                    'required' => $field['required'],
                    'preview' => $field['preview'],
                    'value' => $values[$field['key']] ?? '',
                ];
            }, $fields),
            'preview' => $preview,
            'values' => $values,
        ];
    }

    public static function extractProfile(string $slug, object|array $source): array
    {
        $layout = self::resolveLayout($slug);
        $fields = self::fieldsForLayout($layout);
        $meta = self::readMeta($source);
        $values = [];

        if (! empty($meta['layout']) && $meta['layout'] === $layout) {
            foreach ($fields as $field) {
                $values[$field['key']] = (string) ($meta[$field['key']] ?? '');
            }

            if (self::hasAnyValue($values)) {
                return $values;
            }
        }

        return self::fallbackFromColumns($layout, $source);
    }

    public static function normalizeForStorage(string $slug, array $input): array
    {
        $layout = self::resolveLayout($slug);
        $fields = self::fieldsForLayout($layout);
        $profile = is_array($input['profile'] ?? null) ? $input['profile'] : [];

        $groom = '';
        $bride = '';
        $meta = ['layout' => $layout];

        foreach ($fields as $field) {
            $value = trim((string) ($profile[$field['key']] ?? ''));
            $meta[$field['key']] = $value;

            if ($field['column'] === 'groom_name') {
                $groom = $value;
            }

            if ($field['column'] === 'bride_name') {
                $bride = $value;
            }
        }

        return [
            'groom_name' => $groom !== '' ? $groom : __('builder.fallback_primary'),
            'bride_name' => $bride,
            'profile_meta' => $meta,
        ];
    }

    public static function displayTitle(object|array $source): string
    {
        $slug = self::resolveSlug(
            is_array($source) ? ($source['template_slug'] ?? null) : null,
            is_array($source) ? ($source['template'] ?? null) : ($source->template ?? null)
        );

        $layout = self::resolveLayout($slug);
        $values = self::extractProfile($slug, $source);

        return match ($layout) {
            self::LAYOUT_COUPLE => self::joinNames($values['groom_name'] ?? '', $values['bride_name'] ?? ''),
            self::LAYOUT_COUPLE_BRIDE_FIRST => self::joinNames($values['bride_name'] ?? '', $values['groom_name'] ?? ''),
            self::LAYOUT_CHILD => trim((string) ($values['child_name'] ?? '')),
            self::LAYOUT_CELEBRANT => self::joinNames(
                $values['celebrant_name'] ?? '',
                $values['milestone'] ?? '',
                ' · '
            ),
            self::LAYOUT_GRADUATION => self::joinNames(
                $values['school_name'] ?? '',
                $values['class_name'] ?? '',
                ' · '
            ),
            self::LAYOUT_GENERAL => self::joinNames(
                $values['primary_name'] ?? '',
                $values['secondary_name'] ?? ''
            ),
            default => self::joinNames(
                is_array($source) ? ($source['groom_name'] ?? '') : ($source->groom_name ?? ''),
                is_array($source) ? ($source['bride_name'] ?? '') : ($source->bride_name ?? '')
            ),
        };
    }

    public static function demoProfile(string $slug): array
    {
        return match ($slug) {
            'qiz' => ['groom_name' => 'Bobur', 'bride_name' => 'Nilufar'],
            'sunnat' => ['child_name' => 'Muhammadali', 'hosts' => 'Karimovlar oilasi'],
            'beshik' => ['child_name' => 'Madina', 'hosts' => 'Saidovlar oilasi'],
            'birthday' => ['celebrant_name' => 'Dilnoza', 'milestone' => '25 yosh'],
            'yubiley' => ['celebrant_name' => 'Rustam aka', 'milestone' => '50 yillik yubiley'],
            'bitiruv' => ['school_name' => '15-sonli maktab', 'class_name' => '11-A sinf'],
            'nahor' => ['primary_name' => 'Alisher aka', 'secondary_name' => 'Karimovlar oilasi'],
            'iftorlik' => ['primary_name' => 'Mahalla 12', 'secondary_name' => ''],
            'aqiyqa' => ['primary_name' => 'Yusuf', 'secondary_name' => 'Toshmatovlar oilasi'],
            'fotiha' => ['groom_name' => 'Jasur', 'bride_name' => 'Maftuna'],
            default => ['groom_name' => 'Farhod', 'bride_name' => 'Shirin'],
        };
    }

    private static function readMeta(object|array $source): array
    {
        $meta = is_array($source) ? ($source['profile_meta'] ?? []) : ($source->profile_meta ?? []);

        return is_array($meta) ? $meta : [];
    }

    private static function fallbackFromColumns(string $layout, object|array $source): array
    {
        $groom = is_array($source) ? ($source['groom_name'] ?? '') : ($source->groom_name ?? '');
        $bride = is_array($source) ? ($source['bride_name'] ?? '') : ($source->bride_name ?? '');

        return match ($layout) {
            self::LAYOUT_CHILD => ['child_name' => $groom, 'hosts' => $bride],
            self::LAYOUT_CELEBRANT => ['celebrant_name' => $groom, 'milestone' => $bride],
            self::LAYOUT_GRADUATION => ['school_name' => $groom, 'class_name' => $bride],
            self::LAYOUT_GENERAL => ['primary_name' => $groom, 'secondary_name' => $bride],
            self::LAYOUT_COUPLE_BRIDE_FIRST => ['groom_name' => $groom, 'bride_name' => $bride],
            default => ['groom_name' => $groom, 'bride_name' => $bride],
        };
    }

    private static function hasAnyValue(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }

    private static function joinNames(string $first, string $second, string $separator = ' & '): string
    {
        $parts = array_values(array_filter([trim($first), trim($second)]));

        return implode($separator, $parts);
    }
}

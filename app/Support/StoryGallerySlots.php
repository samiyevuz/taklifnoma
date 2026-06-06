<?php

namespace App\Support;

class StoryGallerySlots
{
    public const MOMENT_COUNT = 12;

    public const MAX_TOTAL_IMAGES = 14;

    /**
     * @return array<int, string>
     */
    public static function slotKeys(): array
    {
        $keys = ['primary', 'secondary'];

        for ($index = 0; $index < self::MOMENT_COUNT; $index++) {
            $keys[] = "moment_{$index}";
        }

        return $keys;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public static function uploadFieldRules(): array
    {
        $rules = [];
        $imageRule = ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'];
        $captionRule = ['nullable', 'string', 'max:120'];

        foreach (self::slotKeys() as $key) {
            $rules["story_image_{$key}"] = $imageRule;

            if (str_starts_with($key, 'moment_')) {
                $rules["story_caption_{$key}"] = $captionRule;
            }
        }

        return $rules;
    }

    public static function slotsForSlug(string $slug): array
    {
        $fields = BuilderEventProfile::fieldsForSlug($slug);
        $primary = $fields[0] ?? null;
        $secondary = $fields[1] ?? null;

        $slots = [
            [
                'key' => 'primary',
                'type' => 'portrait',
                'label' => $primary ? __($primary['label']) : __('builder.primary_name'),
                'hint' => __('builder.story_portrait_hint'),
                'caption' => false,
            ],
        ];

        if ($secondary) {
            $slots[] = [
                'key' => 'secondary',
                'type' => 'portrait',
                'label' => __($secondary['label']),
                'hint' => __('builder.story_portrait_hint'),
                'caption' => false,
            ];
        }

        for ($index = 0; $index < self::MOMENT_COUNT; $index++) {
            $slots[] = [
                'key' => "moment_{$index}",
                'type' => 'moment',
                'label' => __('builder.story_moment_label', ['num' => $index + 1]),
                'hint' => __('builder.story_moment_hint'),
                'caption' => true,
            ];
        }

        return $slots;
    }

    public static function sectionTitle(string $slug): string
    {
        return match (BuilderEventProfile::resolveLayout($slug)) {
            BuilderEventProfile::LAYOUT_COUPLE, BuilderEventProfile::LAYOUT_COUPLE_BRIDE_FIRST => __('invitation.story_title_couple'),
            BuilderEventProfile::LAYOUT_CHILD => __('invitation.story_title_child'),
            BuilderEventProfile::LAYOUT_CELEBRANT => __('invitation.story_title_celebrant'),
            BuilderEventProfile::LAYOUT_GRADUATION => __('invitation.story_title_graduation'),
            default => __('invitation.story_title_general'),
        };
    }

    public static function sectionSubtitle(string $slug): string
    {
        return match (BuilderEventProfile::resolveLayout($slug)) {
            BuilderEventProfile::LAYOUT_COUPLE, BuilderEventProfile::LAYOUT_COUPLE_BRIDE_FIRST => __('invitation.story_subtitle_couple'),
            BuilderEventProfile::LAYOUT_CHILD => __('invitation.story_subtitle_child'),
            BuilderEventProfile::LAYOUT_CELEBRANT => __('invitation.story_subtitle_celebrant'),
            BuilderEventProfile::LAYOUT_GRADUATION => __('invitation.story_subtitle_graduation'),
            default => __('invitation.story_subtitle_general'),
        };
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $stored
     * @return array<int, array<string, mixed>>
     */
    public static function hydrate(?array $stored, string $slug): array
    {
        $stored = collect($stored ?? [])->keyBy('slot');
        $hydrated = [];

        foreach (self::slotsForSlug($slug) as $slot) {
            $item = $stored->get($slot['key'], []);
            $path = is_array($item) ? ($item['path'] ?? null) : null;
            $caption = is_array($item) ? trim((string) ($item['caption'] ?? '')) : '';

            $hydrated[] = [
                'slot' => $slot['key'],
                'type' => $slot['type'],
                'label' => $slot['label'],
                'path' => filled($path) ? $path : null,
                'url' => filled($path) ? asset($path) : null,
                'caption' => $caption,
            ];
        }

        return $hydrated;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function compactForStorage(array $items): array
    {
        return collect($items)
            ->filter(fn (array $item) => filled($item['path'] ?? null))
            ->map(fn (array $item) => [
                'slot' => $item['slot'],
                'path' => $item['path'],
                'caption' => trim((string) ($item['caption'] ?? '')),
            ])
            ->values()
            ->all();
    }
}

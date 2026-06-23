<?php

namespace App\Support;

use App\Models\Invitation;

class PlanEntitlements
{
    public const TIER_CLASSIC = 'classic';

    public const TIER_PREMIUM = 'premium';

    public const TIER_LUXURY = 'luxury';

    public const TIER_ROYAL = 'royal';

    public static function definitions(): array
    {
        return [
            self::TIER_CLASSIC => [
                'tier' => self::TIER_CLASSIC,
                'label' => 'Classic',
                'guest_limit' => 30,
                'music_enabled' => false,
                'music_upload' => false,
                'cover_upload' => true,
                'story_gallery' => false,
                'custom_slug' => false,
                'custom_domain' => false,
                'rsvp_enabled' => true,
                'map_enabled' => true,
                'telegram_notifications' => false,
                'excel_export' => false,
                'animation' => 'basic',
            ],
            self::TIER_PREMIUM => [
                'tier' => self::TIER_PREMIUM,
                'label' => 'Premium',
                'guest_limit' => 200,
                'music_enabled' => true,
                'music_upload' => true,
                'cover_upload' => true,
                'story_gallery' => false,
                'custom_slug' => true,
                'custom_domain' => false,
                'rsvp_enabled' => true,
                'map_enabled' => true,
                'telegram_notifications' => true,
                'excel_export' => true,
                'animation' => 'enhanced',
            ],
            self::TIER_LUXURY => [
                'tier' => self::TIER_LUXURY,
                'label' => 'Luxury',
                'guest_limit' => 500,
                'music_enabled' => true,
                'music_upload' => true,
                'cover_upload' => true,
                'story_gallery' => true,
                'custom_slug' => true,
                'custom_domain' => false,
                'rsvp_enabled' => true,
                'map_enabled' => true,
                'telegram_notifications' => true,
                'excel_export' => true,
                'animation' => 'cinematic',
            ],
            self::TIER_ROYAL => [
                'tier' => self::TIER_ROYAL,
                'label' => 'Royal VIP',
                'guest_limit' => null,
                'music_enabled' => true,
                'music_upload' => true,
                'cover_upload' => true,
                'story_gallery' => true,
                'custom_slug' => true,
                'custom_domain' => false,
                'rsvp_enabled' => true,
                'map_enabled' => true,
                'telegram_notifications' => true,
                'excel_export' => true,
                'animation' => 'vip',
            ],
        ];
    }

    public static function forTheme(?string $theme): array
    {
        $definitions = self::definitions();

        return $definitions[$theme] ?? $definitions[self::TIER_PREMIUM];
    }

    public static function forVariant(?string $familySlug, ?string $variantId): array
    {
        $variant = TemplateVariantCatalog::find($familySlug ?? 'nikoh', $variantId);

        return self::forTheme($variant['theme'] ?? self::TIER_PREMIUM);
    }

    public static function forInvitation(Invitation $invitation): array
    {
        if (filled($invitation->plan_tier)) {
            return self::forTheme($invitation->plan_tier);
        }

        return self::forVariant($invitation->template_slug, $invitation->template_variant);
    }

    public static function featureLabels(string $theme): array
    {
        $plan = self::forTheme($theme);
        $labels = [];

        if ($plan['guest_limit'] === null) {
            $labels[] = __('builder.plan_features.unlimited_guests');
        } else {
            $labels[] = __('builder.plan_features.guests', ['count' => $plan['guest_limit']]);
        }

        if ($plan['music_enabled']) {
            $labels[] = __('builder.plan_features.music');
        }

        if ($plan['custom_slug']) {
            $labels[] = __('builder.plan_features.custom_slug');
        }

        if ($plan['telegram_notifications']) {
            $labels[] = __('builder.plan_features.telegram');
        }

        if ($plan['animation'] === 'cinematic') {
            $labels[] = __('builder.plan_features.cinematic');
        }

        if ($plan['animation'] === 'vip') {
            $labels[] = __('builder.plan_features.vip_animation');
        }

        if ($plan['story_gallery']) {
            $labels[] = __('builder.plan_features.story_gallery');
        }

        return $labels;
    }

    public static function guestLimitLabel(?int $limit): string
    {
        return $limit === null
            ? __('builder.plan_features.unlimited_guests')
            : __('builder.plan_features.guests_up_to', ['count' => $limit]);
    }

    public static function applyPlanToPayload(array $data): array
    {
        $familySlug = $data['template_slug'] ?? 'nikoh';
        $variant = TemplateVariantCatalog::find($familySlug, $data['template_variant'] ?? null);
        $plan = self::forTheme($variant['theme'] ?? self::TIER_PREMIUM);

        $data['plan_tier'] = $plan['tier'];
        $data['guest_limit'] = $plan['guest_limit'];

        $data['custom_domain'] = null;

        if (! $plan['music_enabled']) {
            $data['music_url'] = null;
            unset($data['music_file']);
        } elseif (blank($data['music_url'] ?? null) && empty($data['music_file'])) {
            $data['music_url'] = asset('audio/romantic-wedding.mp3');
        }

        if (! $plan['rsvp_enabled']) {
            $data['rsvp_enabled'] = false;
        }

        if (! $plan['story_gallery']) {
            $data['story_images'] = null;
        }

        return $data;
    }

    public static function validatePayload(array $data, ?Invitation $invitation = null): array
    {
        $familySlug = $data['template_slug']
            ?? $invitation?->template_slug
            ?? 'nikoh';
        $variantId = $data['template_variant'] ?? $invitation?->template_variant;
        $plan = self::forVariant($familySlug, $variantId);
        $errors = [];

        if (! $plan['custom_slug'] && isset($data['slug']) && $invitation && $data['slug'] !== $invitation->slug) {
            $errors['slug'] = __('builder.plan_errors.slug_not_allowed');
        }

        if (! $plan['music_enabled'] && (filled($data['music_url'] ?? null) || ! empty($data['music_file']))) {
            $errors['music_url'] = __('builder.plan_errors.music_not_allowed');
        }

        if (! $plan['cover_upload'] && ! empty($data['cover_image'])) {
            $errors['cover_image'] = __('builder.plan_errors.cover_not_allowed');
        }

        return $errors;
    }
}

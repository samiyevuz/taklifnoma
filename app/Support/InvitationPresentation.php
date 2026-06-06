<?php

namespace App\Support;

use App\Models\Invitation;

class InvitationPresentation
{
    public static function for(Invitation $invitation): array
    {
        $familySlug = $invitation->template_slug ?? 'nikoh';
        $variant = TemplateVariantCatalog::find($familySlug, $invitation->template_variant);
        $entitlements = PlanEntitlements::forInvitation($invitation);
        $theme = $variant['theme'] ?? $entitlements['tier'] ?? PlanEntitlements::TIER_PREMIUM;
        $animation = $entitlements['animation'] ?? 'enhanced';

        return [
            'theme' => $theme,
            'animation' => $animation,
            'theme_class' => 'inv-theme--'.$theme,
            'animation_class' => 'inv-anim--'.$animation,
            'ribbon' => match ($animation) {
                'vip' => 'VIP',
                'cinematic' => 'LUXURY',
                default => null,
            },
            'cover_url' => self::resolveCoverUrl($invitation, $variant),
            'cover_focus' => $variant['cover_focus'] ?? 'center 40%',
        ];
    }

    private static function resolveCoverUrl(Invitation $invitation, ?array $variant): ?string
    {
        if (filled($invitation->cover_image)) {
            return asset($invitation->cover_image);
        }

        return $variant['cover_url'] ?? null;
    }
}

<?php

namespace App\Services;

use App\Models\Invitation;
use App\Support\InvitationEventData;
use App\Support\TemplateCatalog;
use App\Support\PlanEntitlements;
use App\Support\TemplateVariantCatalog;
use Illuminate\Support\Str;

class InvitationService
{
    public function create(array $data, bool $publish = false): Invitation
    {
        unset($data['publish'], $data['dress_colors_json']);

        $data = InvitationEventData::mergeIntoAttributes($data);

        if (empty($data['template_slug']) && ! empty($data['template'])) {
            $catalog = TemplateCatalog::findByBlade($data['template']);
            $data['template_slug'] = $catalog['slug'] ?? 'nikoh';
        }

        $data = $this->resolveTemplateVariant($data);

        $slugSeed = $data['custom_slug'] ?? $data['slug'] ?? $this->generateSlug($data['groom_name'], $data['bride_name']);
        $data['custom_slug'] = $this->resolveUniqueSlug($slugSeed);
        $data['slug'] = $data['custom_slug'];

        $data['dress_colors'] = $data['dress_colors'] ?? [];
        $data['template'] = $data['template'] ?? Invitation::TEMPLATE_NIKOH_PREMIUM;

        if ($publish) {
            $data['status'] = Invitation::STATUS_ACTIVE;
            $data['published_at'] = now();
        } else {
            $data['status'] = Invitation::STATUS_DRAFT;
        }

        return Invitation::create($data);
    }

    public function update(Invitation $invitation, array $data, ?bool $publish = null): Invitation
    {
        unset($data['publish'], $data['dress_colors_json']);

        $data = $this->resolveTemplateVariant($data);

        if (isset($data['event_data'])) {
            $data = array_merge($data, array_filter(
                InvitationEventData::unpack($data['event_data']),
                fn ($value) => $value !== null
            ));
        } else {
            $data['event_data'] = InvitationEventData::pack(array_merge(
                InvitationEventData::unpack($invitation->event_data),
                $data
            ));
        }

        if (isset($data['custom_slug']) && $data['custom_slug'] !== $invitation->custom_slug) {
            $data['custom_slug'] = $this->resolveUniqueSlug($data['custom_slug'], $invitation->id);
            $data['slug'] = $data['custom_slug'];
        }

        if ($publish === true) {
            $data['status'] = Invitation::STATUS_ACTIVE;
            $data['published_at'] = $invitation->published_at ?? now();
        } elseif ($publish === false) {
            $data['status'] = Invitation::STATUS_DRAFT;
            $data['published_at'] = null;
        }

        $invitation->update($data);

        return $invitation->fresh();
    }

    public function generateSlug(string $groom, string $bride): string
    {
        $base = Str::slug(trim($groom.'-'.$bride, '-'), '-', 'uz');

        return $this->resolveUniqueSlug($base ?: 'taklifnoma');
    }

    public function resolveUniqueSlug(string $slug, ?int $exceptId = null): string
    {
        $slug = Str::slug($slug, '-', 'uz') ?: 'taklifnoma';
        $original = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $exceptId)) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function resolveTemplateVariant(array $data): array
    {
        $familySlug = $data['template_slug'] ?? null;

        if (! $familySlug && ! empty($data['template'])) {
            $catalog = TemplateCatalog::findByBlade($data['template']);
            $familySlug = $catalog['slug'] ?? 'nikoh';
            $data['template_slug'] = $familySlug;
        }

        if (! $familySlug) {
            return $data;
        }

        $variant = TemplateVariantCatalog::find($familySlug, $data['template_variant'] ?? null);

        if ($variant) {
            $data['template_variant'] = $variant['id'];
            $data['template'] = $variant['blade'];
        }

        return PlanEntitlements::applyPlanToPayload($data);
    }

    private function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = Invitation::query()
            ->where(function ($builder) use ($slug) {
                $builder->where('slug', $slug)
                    ->orWhere('custom_slug', $slug);
            });

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}

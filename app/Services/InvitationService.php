<?php

namespace App\Services;

use App\Models\Invitation;
use App\Support\InvitationDefaults;
use Illuminate\Support\Str;

class InvitationService
{
    public function create(array $data, bool $publish = false): Invitation
    {
        unset($data['publish']);

        $data['slug'] = $this->resolveUniqueSlug(
            $data['slug'] ?? $this->generateSlug($data['groom_name'], $data['bride_name'])
        );

        $data['dress_colors'] = $data['dress_colors'] ?? InvitationDefaults::dressColors();
        $data['template'] = $data['template'] ?? Invitation::TEMPLATE_NIKOH_PREMIUM;

        if ($publish) {
            $data['status'] = Invitation::STATUS_PUBLISHED;
            $data['published_at'] = now();
        } else {
            $data['status'] = Invitation::STATUS_DRAFT;
        }

        return Invitation::create($data);
    }

    public function update(Invitation $invitation, array $data, ?bool $publish = null): Invitation
    {
        unset($data['publish']);

        if (isset($data['slug']) && $data['slug'] !== $invitation->slug) {
            $data['slug'] = $this->resolveUniqueSlug($data['slug'], $invitation->id);
        }

        if ($publish === true) {
            $data['status'] = Invitation::STATUS_PUBLISHED;
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
        $base = Str::slug($groom.'-'.$bride, '-', 'uz');

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

    private function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $query = Invitation::query()->where('slug', $slug);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }
}

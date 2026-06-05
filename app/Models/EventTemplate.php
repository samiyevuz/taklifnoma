<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventTemplate extends Model
{
    protected $fillable = [
        'slug',
        'blade',
        'visual',
        'cover_path',
        'price_amount',
        'preview_route',
        'preview_param',
        'translations',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
            'price_amount' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function translation(string $locale, string $field, ?string $fallback = null): ?string
    {
        $value = $this->translations[$locale][$field] ?? null;

        if (filled($value)) {
            return $value;
        }

        if ($fallback !== null) {
            return $fallback;
        }

        foreach (['uz', 'en', 'ru'] as $code) {
            $alt = $this->translations[$code][$field] ?? null;
            if (filled($alt)) {
                return $alt;
            }
        }

        return null;
    }

    public function localizedTitle(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translation($locale, 'title', $this->slug) ?? $this->slug;
    }

    public function localizedDesc(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translation($locale, 'desc') ?? '';
    }

    public function localizedBadge(?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $badge = $this->translation($locale, 'badge');

        return filled($badge) ? $badge : null;
    }

    public function coverUrl(): ?string
    {
        return filled($this->cover_path) ? asset($this->cover_path) : null;
    }

    public function formattedPrice(): string
    {
        return number_format($this->price_amount, 0, '.', ' ').' '.__('landing.currency');
    }

    public function toCatalogArray(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return [
            'slug' => $this->slug,
            'template' => $this->blade,
            'visual' => $this->visual,
            'cover_image' => $this->cover_path,
            'cover_url' => $this->coverUrl(),
            'price_amount' => $this->price_amount,
            'price' => $this->formattedPrice(),
            'preview_route' => $this->preview_route,
            'preview_param' => $this->preview_param,
            'title' => $this->localizedTitle($locale),
            'desc' => $this->localizedDesc($locale),
            'tag' => $this->localizedBadge($locale),
        ];
    }
}

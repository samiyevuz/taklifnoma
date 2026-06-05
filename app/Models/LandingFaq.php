<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingFaq extends Model
{
    protected $fillable = [
        'translations',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'translations' => 'array',
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

    public function localizedQuestion(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translation($locale, 'q') ?? '';
    }

    public function localizedAnswer(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translation($locale, 'a') ?? '';
    }

    public function toFaqArray(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();

        return [
            'q' => $this->localizedQuestion($locale),
            'a' => $this->localizedAnswer($locale),
        ];
    }
}

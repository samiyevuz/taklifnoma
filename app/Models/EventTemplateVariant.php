<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTemplateVariant extends Model
{
    public const THEMES = ['classic', 'premium', 'luxury', 'royal'];

    protected $fillable = [
        'event_template_id',
        'variant_key',
        'title',
        'subtitle',
        'price_amount',
        'theme',
        'blade',
        'cover_path',
        'badge',
        'guest_limit',
        'sort_order',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_amount' => 'integer',
            'guest_limit' => 'integer',
            'sort_order' => 'integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function eventTemplate(): BelongsTo
    {
        return $this->belongsTo(EventTemplate::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function coverUrl(): ?string
    {
        if (filled($this->cover_path)) {
            return asset($this->cover_path);
        }

        return $this->eventTemplate?->coverUrl();
    }

    public function formattedPrice(): string
    {
        return number_format($this->price_amount, 0, '.', ' ').' '.__('landing.currency');
    }

    public function themeLabel(): string
    {
        return match ($this->theme) {
            'classic' => 'Classic',
            'premium' => 'Premium',
            'luxury' => 'Luxury',
            'royal' => 'Royal VIP',
            default => ucfirst($this->theme),
        };
    }

    public function toCatalogArray(string $familySlug): array
    {
        return [
            'id' => $this->variant_key,
            'title' => $this->title,
            'subtitle' => $this->subtitle ?? '',
            'price_amount' => $this->price_amount,
            'blade' => $this->blade ?: ($this->eventTemplate?->blade ?? 'nikoh-premium'),
            'theme' => $this->theme,
            'cover_image' => $this->cover_path ?: ($this->eventTemplate?->cover_path),
            'badge' => $this->badge,
            'guest_limit' => $this->guest_limit,
            'family_slug' => $familySlug,
            'is_default' => $this->is_default,
        ];
    }
}

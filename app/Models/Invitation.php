<?php

namespace App\Models;

use App\Support\MusicUrlNormalizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Invitation extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const TEMPLATE_NIKOH_PREMIUM = 'nikoh-premium';

    protected $fillable = [
        'user_id',
        'slug',
        'template',
        'status',
        'groom_name',
        'bride_name',
        'event_type',
        'event_at',
        'event_city',
        'venue_name',
        'venue_address',
        'map_lat',
        'map_lng',
        'invitation_text_1',
        'invitation_text_2',
        'family_signature',
        'dress_colors',
        'music_url',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'event_at' => 'datetime',
            'dress_colors' => 'array',
            'map_lat' => 'float',
            'map_lng' => 'float',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rsvpResponses(): HasMany
    {
        return $this->hasMany(RsvpResponse::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => __('account.status_published'),
            default => __('account.status_draft'),
        };
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED && $this->published_at !== null;
    }

    public function coupleTitle(): string
    {
        return "{$this->groom_name} & {$this->bride_name}";
    }

    public function defaultMusicUrl(): string
    {
        return asset('audio/romantic-wedding.mp3');
    }

    public function resolvedMusicUrl(): string
    {
        return MusicUrlNormalizer::normalize($this->music_url) ?? $this->defaultMusicUrl();
    }

    public function hasCustomMusic(): bool
    {
        return filled($this->music_url);
    }

    public function eventIsoString(): string
    {
        return $this->event_at->timezone('Asia/Tashkent')->toIso8601String();
    }

    public function formattedEventDate(): string
    {
        $months = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'Iyun', 7 => 'Iyul', 8 => 'Avgust',
            9 => 'Sentabr', 10 => 'Oktabr', 11 => 'Noyabr', 12 => 'Dekabr',
        ];

        $date = $this->event_at->timezone('Asia/Tashkent');

        return $date->format('j').' '.$months[(int) $date->format('n')].' '.$date->format('Y');
    }

    public function formattedEventTime(): string
    {
        return 'Soat '.$this->event_at->timezone('Asia/Tashkent')->format('H:i');
    }

    public function welcomeSubtitle(): string
    {
        $parts = array_filter([
            $this->formattedEventDate(),
            $this->event_city,
        ]);

        return implode(' · ', $parts);
    }

    public function googleMapsUrl(): ?string
    {
        if ($this->map_lat === null || $this->map_lng === null) {
            return null;
        }

        return 'https://www.google.com/maps/search/?api=1&query='.$this->map_lat.','.$this->map_lng;
    }

    public function yandexMapsUrl(): ?string
    {
        if ($this->map_lat === null || $this->map_lng === null) {
            return null;
        }

        return 'https://yandex.com/maps/?pt='.$this->map_lng.','.$this->map_lat.'&z=16&l=map';
    }

    public function publish(): void
    {
        $this->update([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => Carbon::now(),
        ]);
    }

    public function rsvpStats(): array
    {
        $responses = $this->rsvpResponses();

        return [
            'attending' => (clone $responses)->where('status', RsvpResponse::STATUS_ATTENDING)->count(),
            'declined' => (clone $responses)->where('status', RsvpResponse::STATUS_DECLINED)->count(),
            'total_guests' => (clone $responses)->where('status', RsvpResponse::STATUS_ATTENDING)
                ->selectRaw('SUM(adults_count + children_count) as total')
                ->value('total') ?? 0,
        ];
    }
}

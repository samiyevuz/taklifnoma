<?php

namespace App\Models;

use App\Models\PaymentInvoice;
use App\Support\BuilderEventProfile;
use App\Support\InvitationEventData;
use App\Support\MusicUrlNormalizer;
use App\Support\PlanEntitlements;
use App\Support\TemplateCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Invitation extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    /** @deprecated Use STATUS_ACTIVE */
    public const STATUS_PUBLISHED = 'active';

    public const STATUS_EXPIRED = 'expired';

    public const TEMPLATE_NIKOH_PREMIUM = 'nikoh-premium';

    protected $fillable = [
        'user_id',
        'uuid',
        'slug',
        'custom_slug',
        'template',
        'template_slug',
        'template_variant',
        'plan_tier',
        'guest_limit',
        'custom_domain',
        'status',
        'groom_name',
        'bride_name',
        'profile_meta',
        'event_data',
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
        'cover_image',
        'rsvp_enabled',
        'published_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'event_at' => 'datetime',
            'dress_colors' => 'array',
            'profile_meta' => 'array',
            'event_data' => 'array',
            'map_lat' => 'float',
            'map_lng' => 'float',
            'published_at' => 'datetime',
            'expires_at' => 'datetime',
            'rsvp_enabled' => 'boolean',
            'guest_limit' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            if (! $invitation->uuid) {
                $invitation->uuid = (string) Str::uuid();
            }

            if (! $invitation->template_slug && $invitation->template) {
                $catalog = TemplateCatalog::findByBlade($invitation->template);
                $invitation->template_slug = $catalog['slug'] ?? 'nikoh';
            }

            if (! $invitation->custom_slug && $invitation->slug) {
                $invitation->custom_slug = $invitation->slug;
            }

            if (! $invitation->event_data) {
                $invitation->event_data = InvitationEventData::fromInvitation($invitation);
            }
        });

        static::updating(function (Invitation $invitation) {
            if ($invitation->isDirty('custom_slug') && $invitation->custom_slug) {
                $invitation->slug = $invitation->custom_slug;
            }

            if ($invitation->isDirty([
                'profile_meta', 'event_at', 'event_city', 'venue_name', 'venue_address',
                'map_lat', 'map_lng', 'invitation_text_1', 'invitation_text_2',
                'family_signature', 'music_url', 'cover_image', 'dress_colors', 'rsvp_enabled', 'template', 'template_variant',
                'plan_tier', 'guest_limit', 'custom_domain',
            ])) {
                $invitation->event_data = InvitationEventData::fromInvitation($invitation);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rsvpResponses(): HasMany
    {
        return $this->hasMany(RsvpResponse::class);
    }

    public function paymentInvoices(): HasMany
    {
        return $this->hasMany(PaymentInvoice::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => __('account.status_published'),
            self::STATUS_EXPIRED => __('account.status_expired'),
            default => __('account.status_draft'),
        };
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->published_at !== null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function coupleTitle(): string
    {
        return $this->displayTitle();
    }

    public function displayTitle(): string
    {
        return BuilderEventProfile::displayTitle($this);
    }

    public function publicUrl(): string
    {
        $slug = $this->custom_slug ?: $this->slug;

        return url('/l/'.$slug);
    }

    public function customDomainUrl(): ?string
    {
        if (! filled($this->custom_domain) || ! $this->allowsCustomDomain()) {
            return null;
        }

        return 'https://'.strtolower($this->custom_domain);
    }

    public function primaryShareUrl(): string
    {
        return $this->customDomainUrl() ?? $this->publicUrl();
    }

    public function defaultMusicUrl(): string
    {
        return asset('audio/romantic-wedding.mp3');
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
            'status' => self::STATUS_ACTIVE,
            'published_at' => Carbon::now(),
        ]);
    }

    public function rsvpStats(): array
    {
        $responses = $this->rsvpResponses();

        return [
            'attending' => (clone $responses)->where('is_attending', true)->count(),
            'declined' => (clone $responses)->where('is_attending', false)->count(),
            'total_guests' => (int) ((clone $responses)->where('is_attending', true)
                ->selectRaw('SUM(adults_count + children_count) as total')
                ->value('total') ?? 0),
        ];
    }

    public function entitlements(): array
    {
        return PlanEntitlements::forInvitation($this);
    }

    public function resolvedGuestLimit(): ?int
    {
        if ($this->guest_limit !== null) {
            return (int) $this->guest_limit;
        }

        return PlanEntitlements::forInvitation($this)['guest_limit'];
    }

    public function currentGuestCount(): int
    {
        return (int) $this->rsvpStats()['total_guests'];
    }

    public function remainingGuestSlots(): ?int
    {
        $limit = $this->resolvedGuestLimit();

        if ($limit === null) {
            return null;
        }

        return max(0, $limit - $this->currentGuestCount());
    }

    public function canAcceptGuestCount(int $adults, int $children): bool
    {
        $limit = $this->resolvedGuestLimit();

        if ($limit === null) {
            return true;
        }

        $incoming = max(0, $adults) + max(0, $children);

        return ($this->currentGuestCount() + $incoming) <= $limit;
    }

    public function allowsMusic(): bool
    {
        return (bool) $this->entitlements()['music_enabled'];
    }

    public function allowsCustomDomain(): bool
    {
        return (bool) $this->entitlements()['custom_domain'];
    }

    public function resolvedMusicUrl(): string
    {
        if (! $this->allowsMusic()) {
            return '';
        }

        return MusicUrlNormalizer::normalize($this->music_url) ?? $this->defaultMusicUrl();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PaymentInvoice extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_PREPARED = 'prepared';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_FAILED = 'failed';

    public const PROVIDER_CLICK = 'click';

    public const PROVIDER_PAYME = 'payme';

    protected $fillable = [
        'uuid',
        'user_id',
        'invitation_id',
        'provider',
        'merchant_trans_id',
        'provider_trans_id',
        'amount',
        'amount_tiyin',
        'currency',
        'template_slug',
        'status',
        'provider_state',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'amount_tiyin' => 'integer',
            'provider_state' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PaymentInvoice $invoice) {
            if (! $invoice->uuid) {
                $invoice->uuid = (string) Str::uuid();
            }

            if (! $invoice->merchant_trans_id) {
                $invoice->merchant_trans_id = 'TN-'.strtoupper(Str::random(12));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function markPrepared(array $state = []): void
    {
        $this->update([
            'status' => self::STATUS_PREPARED,
            'provider_state' => array_merge($this->provider_state ?? [], $state),
        ]);
    }

    public function markPaid(?string $providerTransId = null, array $state = []): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'provider_trans_id' => $providerTransId ?? $this->provider_trans_id,
            'provider_state' => array_merge($this->provider_state ?? [], $state),
            'paid_at' => now(),
        ]);
    }

    public function markCancelled(array $state = []): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'provider_state' => array_merge($this->provider_state ?? [], $state),
        ]);
    }
}

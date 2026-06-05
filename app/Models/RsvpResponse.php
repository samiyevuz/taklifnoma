<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsvpResponse extends Model
{
    public const STATUS_ATTENDING = 'attending';

    public const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'invitation_id',
        'guest_name',
        'is_attending',
        'status',
        'adults_count',
        'children_count',
        'ip_hash',
    ];

    protected function casts(): array
    {
        return [
            'is_attending' => 'boolean',
            'adults_count' => 'integer',
            'children_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (RsvpResponse $response) {
            if ($response->isDirty('status') && ! $response->isDirty('is_attending')) {
                $response->is_attending = $response->status !== self::STATUS_DECLINED;
            }

            if ($response->isDirty('is_attending') && ! $response->isDirty('status')) {
                $response->status = $response->is_attending
                    ? self::STATUS_ATTENDING
                    : self::STATUS_DECLINED;
            }
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}

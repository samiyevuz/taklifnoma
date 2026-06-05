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

    public function isAttending(): bool
    {
        return $this->is_attending;
    }

    public function statusLabel(): string
    {
        return $this->is_attending ? 'Keladi' : 'Kelolmaydi';
    }

    public function guestSummary(): string
    {
        if (! $this->is_attending) {
            return 'Kelolmaydi';
        }

        $parts = [];

        if ($this->adults_count > 0) {
            $parts[] = $this->adults_count.' ta kattalar';
        }

        if ($this->children_count > 0) {
            $parts[] = $this->children_count.' ta bola';
        }

        if ($parts === []) {
            return 'Keladi';
        }

        return 'Keladi ('.implode(', ', $parts).')';
    }

    public function listLabel(): string
    {
        if (! $this->is_attending) {
            return $this->guest_name.' — Kelolmaydi';
        }

        $guestCount = $this->adults_count + $this->children_count;

        return $this->guest_name.' — Keladi ('.$guestCount.'ta mehmon)';
    }

    public function formattedTimestamp(): string
    {
        return $this->created_at
            ? $this->created_at->timezone('Asia/Tashkent')->format('d.m.Y H:i')
            : now()->timezone('Asia/Tashkent')->format('d.m.Y H:i');
    }
}

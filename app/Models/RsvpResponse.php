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
        'status',
        'adults_count',
        'children_count',
        'ip_hash',
    ];

    protected function casts(): array
    {
        return [
            'adults_count' => 'integer',
            'children_count' => 'integer',
        ];
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}

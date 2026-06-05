<?php

namespace App\Support;

use App\Models\Invitation;
use Illuminate\Database\Eloquent\Builder;

class InvitationResolver
{
    public static function publicQuery(): Builder
    {
        return Invitation::query()
            ->where('status', Invitation::STATUS_ACTIVE)
            ->whereNotNull('published_at')
            ->where(function (Builder $query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    public static function findPublic(string $identifier): Invitation
    {
        return self::publicQuery()
            ->where(function (Builder $query) use ($identifier) {
                $query->where('custom_slug', $identifier)
                    ->orWhere('uuid', $identifier)
                    ->orWhere('slug', $identifier);
            })
            ->firstOrFail();
    }

    public static function findOwnedByUuid(int $userId, string $uuid): Invitation
    {
        return Invitation::query()
            ->where('uuid', $uuid)
            ->where('user_id', $userId)
            ->firstOrFail();
    }
}

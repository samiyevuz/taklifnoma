<?php

namespace App\Support;

use App\Models\User;

class ComplimentaryAccess
{
    public static function emailMatches(?string $email): bool
    {
        if (blank($email)) {
            return false;
        }

        $normalized = strtolower(trim($email));

        return in_array($normalized, self::emails(), true);
    }

    public static function hasAccess(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->is_complimentary) {
            return true;
        }

        return self::emailMatches($user->email);
    }

    /** @return list<string> */
    public static function emails(): array
    {
        return config('complimentary.emails', []);
    }
}

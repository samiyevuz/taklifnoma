<?php

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;

class InvitationPolicy
{
    public function view(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function update(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $invitation->user_id === $user->id;
    }
}

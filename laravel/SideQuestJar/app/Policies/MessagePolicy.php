<?php

namespace App\Policies;

use App\Models\User;

class MessagePolicy
{
    /**
     * Determine if the given user can be messaged by the authenticated user.
     */
    public function send(User $authUser, User $targetUser): bool
    {
        return $authUser->friends()->where('users.id', $targetUser->id)->exists();
    }
}

<?php

namespace App\Policies;

use App\Models\Jar;
use App\Models\User;
use Illuminate\Auth\Access\Response;



class JarPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    /**
     * Any authenticated user can browse the jars listing.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    /**
     * A jar is viewable if it is public, the user created it,
     * or the user is a member of it.
     */
    public function view(User $user, Jar $jar): bool
    {
        if ($jar->type === 'public_jar') {
            return true;
        }

        return $user->id === $jar->created_by
            || $jar->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    /**
     * Any authenticated user may create a jar.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Jar $jar): bool
    {
        return $user->id == $jar->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Jar $jar): bool
    {
        return $user->id == $jar->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Jar $jar): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Jar $jar): bool
    {
        return false;
    }

    /**
     * Determine whether the user can invite others to the model.
     */
    public function invite(User $user, Jar $jar): bool
    {
        $isMember = $jar->users()->where('users.id', $user->id)->exists();
        $isCreator = $jar->created_by === $user->id;

        return $isMember || $isCreator;
    }


    /**
     * Admins bypass all policy checks and are granted full access.
     * Returning null falls through to the individual policy methods.
     */
    public function before(User $user): bool|null
    {
        if ($user->is_admin) {
            return true;
        }

        return null;
    }

    
}

<?php

namespace App\Policies;

use App\Models\JarQuest;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class JarQuestPolicy
{
    //Igazabol nincs hasznalva meg 
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, JarQuest $jarQuest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, JarQuest $jarQuest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, JarQuest $jarQuest): bool
    {
        return $user->id === $jarQuest->jar->created_by;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, JarQuest $jarQuest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, JarQuest $jarQuest): bool
    {
        return false;
    }

    public function before(User $user): bool|null {
        if ($user->is_admin){
            return true;
        }
        return null;
    }
}

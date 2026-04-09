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
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Jar $jar): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
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
        


        return $user->id === $jar->created_by;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Jar $jar): bool
    {
        return $user->id === $jar->created_by;
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


    //Minen elott megnezi hogy a user admin e ha igen -> enged mindent || ha nem akkor tovabb megy a tobbi ellenorzessel
    public function before(User $user): bool|null {
        if ($user->is_admin){
            return true;
        }
        return null;
    }

    
}

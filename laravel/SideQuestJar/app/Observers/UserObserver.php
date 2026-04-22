<?php

namespace App\Observers;

use App\Models\Jar;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user)
    {
        $todaysAdventureJar = Jar::where('name', "Today's Adventure")
            ->where('type', 'official_jar')
            ->first();

        if ($todaysAdventureJar) {
            $user->jars()->syncWithoutDetaching([
                $todaysAdventureJar->id => ['streak' => 0]
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Models\Jar;
use Illuminate\Support\Facades\Auth;

class JarObserver
{
    /**
     * Handle the Jar "created" event.
     */
    public function creating(Jar $jar): void
    {
        // Only auto-assign the creator when not explicitly provided
        if (!$jar->created_by && Auth::check()) {
            $jar->created_by = Auth::id();
        }
    }

    /**
     * Handle the Jar "updated" event.
     */
    public function updating(Jar $jar): void
    {
        //
    }

    /**
     * Handle the Jar "deleted" event.
     */
    public function deleted(Jar $jar): void
    {
        //
    }

    /**
     * Handle the Jar "restored" event.
     */
    public function restored(Jar $jar): void
    {
        //
    }

    /**
     * Handle the Jar "force deleted" event.
     */
    public function forceDeleted(Jar $jar): void
    {
        //
    }
}

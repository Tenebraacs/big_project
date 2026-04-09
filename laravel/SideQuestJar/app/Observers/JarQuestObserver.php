<?php

namespace App\Observers;

use App\Models\JarQuest;
use Illuminate\Support\Facades\Auth;


class JarQuestObserver
{
    /**
     * Handle the JarQuest "created" event.
     */
    public function creating(JarQuest $jarQuest): void
    {
        $jarQuest->created_by = Auth::id();
    }

    /**
     * Handle the JarQuest "updated" event.
     */
    public function updated(JarQuest $jarQuest): void
    {
        //
    }

    /**
     * Handle the JarQuest "deleted" event.
     */
    public function deleted(JarQuest $jarQuest): void
    {
        //
    }

    /**
     * Handle the JarQuest "restored" event.
     */
    public function restored(JarQuest $jarQuest): void
    {
        //
    }

    /**
     * Handle the JarQuest "force deleted" event.
     */
    public function forceDeleted(JarQuest $jarQuest): void
    {
        //
    }
}

<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    protected $listeners = [
        'profile-updated' => '$refresh',
    ];

    public function getUserProperty()
    {
        return Auth::user()?->fresh();
    }

    // You can tweak this anytime
    public function getXpPerLevelProperty(): int
    {
        return 500;
    }

    public function getTotalXpProperty(): int
    {
        return (int) ($this->user?->user_xp ?? 0);
    }

    public function getLevelProperty(): int
    {
        // Level 1 at 0 XP
        return intdiv($this->totalXp, $this->xpPerLevel) + 1;
    }

    public function getXpIntoLevelProperty(): int
    {
        return $this->totalXp % $this->xpPerLevel;
    }

    public function getXpToNextLevelProperty(): int
    {
        return $this->xpPerLevel - $this->xpIntoLevel;
    }

    public function getProgressPercentProperty(): int
    {
        if ($this->xpPerLevel <= 0) return 0;
        return (int) round(($this->xpIntoLevel / $this->xpPerLevel) * 100);
    }
};
?>

<div class="row">
    <div class="col-5 col-md-3">
        <div class="ratio ratio-1x1 m-2">
            <img src="{{ $this->user->profile_picture_url }}"
                 class="img-fluid rounded-circle"
                 alt="Profile picture">
        </div>
    </div>

    <div class="col-7 col-md-9">
        <h1 class="fw-bold jar-name display-5 m-2"
            style="font-size: clamp(1.5rem, 5vw, 3rem);">
            {{ $this->user->username }}
        </h1>

        <!-- Level -->
        <div class="rounded-4 m-2 d-flex justify-content-center align-items-center mx-auto"
             style="background-color: #A76F6F; max-height: 10vh; max-width: 5vw;">
            <p class="jar-name fs-2 m-0">{{ $this->level }}</p>
        </div>

        <!-- XP Bar -->
        <div class="progress m-1"
             role="progressbar"
             aria-label="XP Bar"
             aria-valuenow="{{ $this->progressPercent }}"
             aria-valuemin="0"
             aria-valuemax="100">
            <div class="progress-bar" style="width: {{ $this->progressPercent }}%"></div>
        </div>

        <!-- XP Numbers -->
        <p class="jar-name text-start m-1">
            {{ $this->xpIntoLevel }}/{{ $this->xpPerLevel }}
            <span class="opacity-75"> ({{ $this->xpToNextLevel }} to next level)</span>
        </p>
    </div>
</div>
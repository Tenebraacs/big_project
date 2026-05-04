<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

new class extends Component
{
    public ?User $user = null;

    protected $listeners = [
        'profile-updated' => '$refresh',
    ];

    public function mount(?User $user = null): void
    {
        // If a user is passed (users.show page) and exists, use it.
        // Otherwise fall back to the logged-in user (profile page).
        $this->user = ($user && $user->exists) ? $user : Auth::user();
    }

    // Make sure $this->user stays fresh when it's the auth user
    public function getUserProperty()
    {
        return $this->user?->id
            ? $this->user->fresh()
            : null;
    }

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

        <!-- Actions (Friend/Message) -->
        @if(auth()->check() && auth()->id() !== $this->user->id)
            <div class="d-flex mt-3 gap-2">
                @php
                    $friendStatus = \App\Models\Friend::where(function($q) {
                        $q->where('user_id', auth()->id())->where('friend_id', $this->user->id);
                    })->orWhere(function($q) {
                        $q->where('user_id', $this->user->id)->where('friend_id', auth()->id());
                    })->first();
                @endphp

                @if(!$friendStatus)
                    <form action="{{ route('friends.store', $this->user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Add Friend</button>
                    </form>
                @elseif($friendStatus->status === 'pending')
                    @if($friendStatus->user_id === auth()->id())
                        <button class="btn btn-sm btn-secondary rounded-pill px-3" disabled>Request Sent</button>
                    @else
                        <form action="{{ route('friends.accept', $this->user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Accept Request</button>
                        </form>
                        <form action="{{ route('friends.decline', $this->user) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Decline</button>
                        </form>
                    @endif
                @elseif($friendStatus->status === 'accepted')
                    <a href="{{ route('chat.show', $this->user) }}" class="btn btn-sm btn-info rounded-pill px-3 text-white">Message</a>
                    <form action="{{ route('friends.destroy', $this->user) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Remove</button>
                    </form>
                @endif
            </div>
        @endif
    </div>
</div>
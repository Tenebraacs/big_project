<?php

use Livewire\Component;
use App\Models\QuestCompletion;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public int $start = 0;
    public int $limit = 9;
    public bool $loadingMore = false;

    protected $listeners = [
        'open-login' => 'open',
        'refresh-likes' => '$refresh',
        'user-logged-in' => '$refresh',
        'proof-created' => '$refresh',
    ];

    public function loadMore()
    {
        $this->limit += 9;
    }

    public function getProofsProperty()
    {


        return QuestCompletion::whereNotNull('picture')
            ->where('type', 'public_post')
            ->whereDate('completion_date', today())
            ->latest()
            ->with('user', 'jarQuest')
            ->withCount('likes')
            ->withExists([
                'likes as is_liked' => function ($query) {
                    if (auth()->check()) {
                        $query->where('user_id', auth()->id());
                    } else {
                        $query->whereRaw('1 = 0'); // always false for guests
                    }
                }
            ])
            ->limit($this->limit)
            ->get();
    }

    public function getHasMoreProperty()
    {
        return QuestCompletion::whereNotNull('picture')
            ->where('type', 'public_post')
            ->whereDate('completion_date', today())
            ->count() > $this->limit;
    }

    public function getCanSlideProperty()
    {
        return $this->proofs->count() > $this->visibleCount;
    }

    public function next()
    {
        if (!$this->canSlide) return;

        $count = $this->proofs->count();

        
        $this->start = ($this->start + 1) % $count;

        
        $visible = $this->visibleCount;

        if (!$this->loadingMore && $this->start + $visible >= $count && $this->hasMore) {
            $this->loadingMore = true;

            $this->limit += 9;

            $this->loadingMore = false;
        }
    }

    public function previous()
    {
        if (!$this->canSlide) return;

        $count = $this->proofs->count();
        $this->start = ($this->start - 1 + $count) % $count;
    }

    public function getVisibleProofsProperty()
    {
        $count = $this->proofs->count();

        if ($count === 0) return collect();

        $visibleCount = min($this->visibleCount, $count);

        return collect(range(0, $visibleCount - 1))
            ->map(fn($i) => $this->proofs[($this->start + $i) % $count]);
    }

    public function getVisibleCountProperty()
    {
        if (request()->header('User-Agent') && str_contains(request()->header('User-Agent'), 'Mobile')) {
            return 1;
        }

        return 3;
    }

   public function toggleLike($proofId)
    {
        if (!auth()->check()) return;

        $userId = Auth::id();

        $proof = QuestCompletion::findOrFail($proofId);

        $like = $proof->likes()
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            $proof->likes()->create([
                'user_id' => $userId,
            ]);
        }

        // only refresh computed data
        unset($this->proofs);
    }

};
?>

<div class="container-fluid mt-5">

    <div class="row mb-3">
        <div class="col-12 text-center">
            <h2 class="fw-bold welcome-text">Today's Solutions</h2>
        </div>
    </div>

    <div class="row align-items-stretch">

        <!-- LEFT -->
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button wire:click="previous"
                class="btn blue-button rounded-circle fw-bold"
                style="color: #EAB2A0; background-color: #2D4356;"
                @if(!$this->canSlide) disabled style="opacity: 0.4;" @endif>
                ❮
            </button>
        </div>

        <!-- CARDS -->
        <div class="col-10">
            <div class="row g-3">

                @foreach ($this->visibleProofs as $loopIndex => $proof)
                    <div class="col-12 col-md-6 col-lg-4 mb-4
                        @if($loopIndex >= 1) d-none d-md-block @endif
                        @if($loopIndex >= 2) d-none d-lg-block @endif
                    ">
                        <div class="card bg-block-blue rounded-5 h-100 jar-card-border">

                            <!-- HEADER -->
                            <div class="card-header border-0 text-center">
                                <h5 class="fw-bold jar-name fs-3 text-truncate mt-2">
                                    {{ $proof->user->username }}
                                </h5>
                                <p class="quest-title mb-0 mt-1">
                                    {{ $proof->jarQuest->title ?? 'Quest' }}
                                </p>
                            </div>

                            <!-- IMAGE -->
                            <div class="card-body text-center">
                                <img src="{{ $proof->picture_url }}"
                                    class="img-fluid rounded-4"
                                    style="aspect-ratio: 1/1; object-fit: cover;">
                            </div>

                            <!-- FOOTER -->
                            <div class="card-footer border-0 text-center">
                                <p class="mb-1 small quest-description">
                                    {{ $proof->description }}
                                </p>
                                <div class="card-likes-bar">
                                    @auth
                                        <button
                                            wire:click="toggleLike({{ $proof->id }})"
                                            wire:key="like-{{ $proof->id }}-{{ $proof->is_liked }}"
                                            class="card-like-heart-btn{{ $proof->is_liked ? ' liked' : '' }}"
                                            aria-pressed="{{ $proof->is_liked ? 'true' : 'false' }}"
                                            title="{{ $proof->is_liked ? 'Unlike' : 'Like' }}"
                                        >
                                            @if($proof->is_liked)
                                                <i class="bi bi-heart-fill"></i>
                                            @else
                                                <i class="bi bi-heart"></i>
                                            @endif
                                        </button>
                                    @else
                                        <button
                                            wire:click="$dispatchTo('auth.login-modal', 'open-login')"
                                            class="card-like-heart-btn"
                                            title="Login to Like"
                                        >
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    @endauth
                                    <span class="likes-badge-count">
                                        {{ $proof->likes_count }} like{{ $proof->likes_count == 1 ? '' : 's' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button wire:click="next"
                class="btn blue-button rounded-circle fw-bold"
                style="color: #EAB2A0; background-color: #2D4356;"
                @if(!$this->canSlide) disabled style="opacity: 0.4;" @endif>
                ❯
            </button>
        </div>

    </div>
</div>
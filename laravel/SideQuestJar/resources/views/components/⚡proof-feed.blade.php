<?php

use Livewire\Component;
use App\Models\QuestCompletion;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public int $start = 0;
    public int $limit = 9;

    /**
     * Safe default for first paint (mobile).
     * JS will update to 2/3 on larger screens.
     */
    public int $visibleCount = 1;

    protected $listeners = [
        'open-login' => 'open',
        'refresh-likes' => '$refresh',
        'user-logged-in' => '$refresh',
        'proof-created' => '$refresh',
        'visible-count-changed' => 'setVisibleCount',
    ];

    public function loadMore(): void
    {
        $this->limit += 9;
        $this->clampStart();
    }

    public function getProofsProperty()
    {
        return QuestCompletion::query()
            ->whereNotNull('picture')
            ->where('picture', '!=', '')
            ->where('type', 'public_post')
            ->whereDate('completion_date', today())
            ->orderByDesc('completion_date')
            ->orderByDesc('id')
            ->with(['user', 'jarQuest'])
            ->withCount('likes')
            ->withExists([
                'likes as is_liked' => function ($query) {
                    if (auth()->check()) {
                        $query->where('user_id', auth()->id());
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                }
            ])
            ->limit($this->limit)
            ->get();
    }

    public function getCanGoPreviousProperty(): bool
    {
        return $this->start > 0;
    }

    public function getCanGoNextProperty(): bool
    {
        $count = $this->proofs->count();

        if (($this->start + $this->visibleCount) < $count) {
            return true;
        }

        return $this->hasMore;
    }

    public function getHasMoreProperty(): bool
    {
        return QuestCompletion::query()
            ->whereNotNull('picture')
            ->where('picture', '!=', '')
            ->where('type', 'public_post')
            ->whereDate('completion_date', today())
            ->orderByDesc('completion_date')
            ->orderByDesc('id')
            ->skip($this->limit)
            ->take(1)
            ->exists();
    }

    public function next(): void
    {
        $count = $this->proofs->count();
        if ($count === 0) return;

        if (($this->start + $this->visibleCount) < $count) {
            $this->start++;
            return;
        }

        if ($this->hasMore) {
            $this->limit += 9;
            $this->clampStart();
            return;
        }
    }

    public function previous(): void
    {
        if ($this->start <= 0) return;

        $this->start--;
        $this->clampStart();
    }

    public function getVisibleProofsProperty()
    {
        $proofs = $this->proofs;

        if ($proofs->isEmpty()) return collect();

        return $proofs->slice($this->start, $this->visibleCount)->values();
    }

    public function setVisibleCount(int $count): void
    {
        $this->visibleCount = max(1, min(3, $count));
        $this->clampStart();
    }

    private function clampStart(): void
    {
        $count = $this->proofs->count();
        $maxStart = max(0, $count - $this->visibleCount);

        if ($this->start > $maxStart) {
            $this->start = $maxStart;
        }
    }

    public function toggleLike(int $proofId): void
    {
        if (!auth()->check()) return;

        $userId = Auth::id();

        // Constrain to the same rules as "Today's Solutions"
        $proof = QuestCompletion::query()
            ->whereKey($proofId)
            ->whereNotNull('picture')
            ->where('picture', '!=', '')
            ->where('type', 'public_post')
            ->whereDate('completion_date', today())
            ->firstOrFail();

        $like = $proof->likes()
            ->where('user_id', $userId)
            ->first();

        if ($like) {
            $like->delete();
        } else {
            $proof->likes()->firstOrCreate([
                'user_id' => $userId,
            ]);
        }

        // Force proofs computed property to refresh
        unset($this->proofs);
        $this->clampStart();
    }
};
?>

<div class="container-fluid mt-5 todays-solutions-root" wire:init="$dispatch('request-visible-count')">
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h2 class="fw-bold welcome-text">Today's Solutions</h2>
        </div>

    </div>

    <div class="row align-items-stretch">
        <!-- LEFT arrow (md+) -->
        <div class="col-1 d-none d-md-flex justify-content-center align-items-center">
            <button
                wire:click="previous"
                class="btn blue-button rounded-circle fw-bold"
                style="color:#EAB2A0; background-color:#2D4356;"
                @if(!$this->canGoPrevious) disabled style="opacity:.4; cursor:not-allowed;" @endif
                aria-label="Previous solution"
            >
                ❮
            </button>
        </div>

        <!-- CARDS -->
        <div class="col-12 col-md-10">
            <div class="row g-3 justify-content-center">
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
                                <img
                                    src="{{ $proof->picture_url }}"
                                    class="img-fluid rounded-4"
                                    style="aspect-ratio: 1/1; object-fit: cover;"
                                    alt="Proof image"
                                >
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
                                            wire:key="like-{{ $proof->id }}"
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

            <!-- MOBILE arrows (under the card) -->
            <div class="d-flex d-md-none justify-content-between px-4 pb-3">
                <button
                    wire:click="previous"
                    class="btn blue-button rounded-circle fw-bold"
                    style="color:#EAB2A0; background-color:#2D4356;"
                    @if(!$this->canGoPrevious) disabled style="opacity:.4; cursor:not-allowed;" @endif
                    aria-label="Previous solution"
                >
                    ❮
                </button>

                <button
                    wire:click="next"
                    class="btn blue-button rounded-circle fw-bold"
                    style="color:#EAB2A0; background-color:#2D4356;"
                    @if(!$this->canGoNext) disabled style="opacity:.4; cursor:not-allowed;" @endif
                    aria-label="Next solution"
                >
                    ❯
                </button>
            </div>
        </div>

        <!-- RIGHT arrow (md+) -->
        <div class="col-1 d-none d-md-flex justify-content-center align-items-center">
            <button
                wire:click="next"
                class="btn blue-button rounded-circle fw-bold"
                style="color:#EAB2A0; background-color:#2D4356;"
                @if(!$this->canGoNext) disabled style="opacity:.4; cursor:not-allowed;" @endif
                aria-label="Next solution"
            >
                ❯
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const computeVisibleCount = () => {
        if (window.matchMedia('(min-width: 992px)').matches) return 3;
        if (window.matchMedia('(min-width: 768px)').matches) return 2;
        return 1;
    };

    // Livewire v4: use Livewire.on + dispatch with params ARRAY
    Livewire.on('request-visible-count', () => {
        Livewire.dispatch('visible-count-changed', [computeVisibleCount()]);
    });

    let t;
    window.addEventListener('resize', () => {
        clearTimeout(t);
        t = setTimeout(() => {
            Livewire.dispatch('visible-count-changed', [computeVisibleCount()]);
        }, 100);
    });
})();
</script>
@endpush
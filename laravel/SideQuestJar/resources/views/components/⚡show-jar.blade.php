<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Jar;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

new class extends Component
{
    use AuthorizesRequests;

    public Jar $jar;
    public ?int $quest = null;
    public $quests;
    public int $currentIndex = 0;
    public bool $liked = false;
    public bool $confirmingDeleteQuest = false;
    public bool $confirmingDeleteJar = false;
    public bool $joined = false;
    public int $likesCount = 0;
    public bool $questLiked = false;
    public int $questLikesCount = 0;

    // Kick user state
    public bool $confirmingKickUser = false;
    public ?int $userIdToKick = null;

    // Comments
    public string $newComment = '';

    public function mount(Jar $jar, ?int $quest = null)
    {
        $this->jar = $jar->load('users');
        $this->quest = $quest;
        $this->quests = $this->jar->quests()
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
            ->get()
            ->values();

        if (auth()->check()) {
            $this->liked = $this->jar->likes()->where('user_id', auth()->id())->exists();
            $this->joined = $this->jar->users()->where('user_id', auth()->id())->exists();
            $this->likesCount = $this->jar->likes()->count();
        }

        if ($this->quests->isEmpty()) {
            return;
        }

        if ($quest !== null) {
            $index = $this->quests->pluck('id')->search((int) $quest);

            if ($index !== false) {
                $this->currentIndex = $index;
            }
        }

        if ($this->currentQuest && auth()->check()) {
            $this->questLiked = $this->currentQuest->is_liked;
        }

        if ($this->currentQuest) {
            $this->questLikesCount = $this->currentQuest->likes_count;
        }
    }

    public function getCurrentQuestProperty()
    {
        if ($this->quests->isEmpty()) {
            return null;
        }

        return $this->quests[$this->currentIndex];
    }

    public function getNextQuestProperty()
    {
        if ($this->quests->isEmpty()) {
            return null;
        }

        $nextIndex = ($this->currentIndex + 1) % $this->quests->count();

        return $this->quests[$nextIndex];
    }

    public function getPreviousQuestProperty()
    {
        if ($this->quests->isEmpty()) {
            return null;
        }

        $previousIndex = ($this->currentIndex - 1 + $this->quests->count()) % $this->quests->count();

        return $this->quests[$previousIndex];
    }

    public function updateUrl()
    {
        if ($this->quests->isEmpty()) {
            return;
        }

        $questId = $this->currentQuest->id;

        $this->js("
            window.history.replaceState({}, '', '/jars/{$this->jar->id}/{$questId}');
        ");
    }

    public function updateQuestLikeState()
    {
        if (!$this->currentQuest) return;

        $freshQuest = $this->jar->quests()
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
            ->find($this->currentQuest->id);

        if (!$freshQuest) return;

        $this->quests[$this->currentIndex] = $freshQuest;
        $this->questLikesCount = $freshQuest->likes_count;
        $this->questLiked = $freshQuest->is_liked;
    }

    public function next()
    {
        if ($this->quests->isEmpty()) {
            return;
        }

        $this->currentIndex = ($this->currentIndex + 1) % $this->quests->count();
        $this->updateQuestLikeState();
        $this->updateUrl();
    }

    public function previous()
    {
        if ($this->quests->isEmpty()) {
            return;
        }

        $this->currentIndex = ($this->currentIndex - 1 + $this->quests->count()) % $this->quests->count();
        $this->updateQuestLikeState();
        $this->updateUrl();
    }

    public function toggleLike()
    {
        if (!auth()->check()) {
            return;
        }

        $existingLike = $this->jar->likes()->where('user_id', auth()->id())->first();

        if ($existingLike) {
            $existingLike->delete();
            $this->liked = false;
            $this->likesCount = $this->jar->likes()->count();
            return;
        }

        $this->jar->likes()->create([
            'user_id' => auth()->id(),
        ]);

        $this->liked = true;
        $this->likesCount = $this->jar->likes()->count();
    }

    public function toggleQuestLike()
    {
        if (!auth()->check() || !$this->currentQuest) return;

        $quest = $this->currentQuest;

        $like = $quest->likes()
            ->where('user_id', auth()->id())
            ->first();

        if ($like) {
            $like->delete();
        } else {
            $quest->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }

        $this->updateQuestLikeState();
    }

    public function joinJar()
    {
        if (!auth()->check() || $this->joined) {
            return;
        }

        $this->jar->users()->syncWithoutDetaching([
            auth()->id() => ['streak' => 0]
        ]);

        $this->jar = $this->jar->fresh()->load('users');
        $this->joined = true;
    }

    public function leaveJar()
    {
        if (!auth()->check() || !$this->joined) {
            return;
        }

        $this->jar->users()->detach(auth()->id());

        $this->jar = $this->jar->fresh()->load('users');
        $this->joined = false;
    }

    public function deleteJar()
    {
        $this->authorize('delete', $this->jar);

        $this->confirmingDeleteJar = false;

        $this->jar->delete();

        return $this->redirect(route('home'), navigate: true);
    }

    public function deleteCurrentQuest()
    {
        if (!$this->currentQuest) {
            return;
        }

        $this->authorize('update', $this->jar);

        $deletedIndex = $this->currentIndex;
        $questToDelete = $this->currentQuest;

        $questToDelete->delete();

        $this->jar = $this->jar->fresh()->load('users');
        $this->quests = $this->jar->quests()
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
            ->orderBy('id')
            ->get()
            ->values();

        $this->confirmingDeleteQuest = false;

        if ($this->quests->isEmpty()) {
            $this->currentIndex = 0;
            $this->quest = null;

            unset($this->currentQuest, $this->nextQuest, $this->previousQuest);

            return;
        }

        $this->currentIndex = min($deletedIndex, $this->quests->count() - 1);
        $this->quest = $this->quests[$this->currentIndex]->id;

        unset($this->currentQuest, $this->nextQuest, $this->previousQuest);

        $this->updateUrl();
    }

    public function confirmDeleteQuest()
    {
        if (!$this->currentQuest) {
            return;
        }

        $this->confirmingDeleteQuest = true;
    }

    public function confirmDeleteJar()
    {
        $this->confirmingDeleteJar = true;
    }

    public function closeDeleteModal()
    {
        $this->confirmingDeleteQuest = false;
        $this->confirmingDeleteJar = false;

        // also closes kick modal
        $this->confirmingKickUser = false;
        $this->userIdToKick = null;
    }

    public function confirmKickUser(int $userId)
    {
        if (!auth()->check() || !($this->jar->created_by === auth()->id() || auth()->user()->is_admin)) {
            return;
        }

        if ((int) $userId === (int) $this->jar->created_by) {
            return;
        }

        $this->userIdToKick = $userId;
        $this->confirmingKickUser = true;
    }

    public function kickUser()
    {
        if (!auth()->check() || !($this->jar->created_by === auth()->id() || auth()->user()->is_admin)) {
            return;
        }

        if (!$this->userIdToKick) {
            return;
        }

        if ((int) $this->userIdToKick === (int) $this->jar->created_by) {
            $this->confirmingKickUser = false;
            $this->userIdToKick = null;
            return;
        }

        $this->jar->users()->detach($this->userIdToKick);

        $this->jar = $this->jar->fresh()->load('users');

        if ((int) $this->userIdToKick === (int) auth()->id()) {
            $this->joined = false;
        }

        $this->confirmingKickUser = false;
        $this->userIdToKick = null;
    }

    public function closeKickModal()
    {
        $this->confirmingKickUser = false;
        $this->userIdToKick = null;
    }

    #[Computed]
    public function comments()
    {
        return $this->jar->comments()->with('user')->oldest()->get();
    }

    public function addComment()
    {
        if (!auth()->check() || (!$this->joined && $this->jar->created_by !== auth()->id() && !auth()->user()->is_admin)) {
            return;
        }

        $this->validate([
            'newComment' => 'required|string|max:1000',
        ]);

        $this->jar->comments()->create([
            'user_id' => auth()->id(),
            'content' => $this->newComment,
        ]);

        $this->newComment = '';
    }

};
?>

<div class="container-fluid">
    <div class="row align-items-start justify-content-center g-3">

        <!-- LEFT: MEMBERS -->
        <div class="col-12 col-lg-2 order-2 order-lg-1">
            <div class="card bg-block-blue rounded-5 h-100">
                <div class="card-header border-0 text-center">
                    <h3 class="jar-name fs-3 fw-bold mb-1">Members</h3>
                    <p class="mb-0">{{ $this->jar->users->count() }} joined</p>
                </div>

                <div class="card-body scrolling-bar m-2 overflow-auto" style="max-height: 70vh;">
                    @if ($this->jar->users->isNotEmpty())
                        <div class="d-flex flex-column gap-2">
                            @foreach ($this->jar->users->sortByDesc(fn($user) => $user->pivot->streak ?? 0) as $user)
                                <div wire:key="jar-user-{{ $this->jar->id }}-{{ $user->id }}" class="rounded-4 px-3 py-2" style="background-color: #EAB2A0;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                        <span class="fw-bold text-dark text-break">{{ $user->username }}</span>

                                        <div class="d-flex gap-1 align-items-center">
                                            @if ($user->id === $this->jar->created_by)
                                                <span class="badge text-bg-dark">Owner</span>
                                            @endif

                                            @auth
                                                @if ($user->id === auth()->id())
                                                    <span class="badge text-bg-primary">You</span>
                                                @endif

                                                {{-- KICK BUTTON (owner/admin only, cannot kick owner) --}}
                                                @if (($this->jar->created_by === auth()->id() || auth()->user()->is_admin) && $user->id !== $this->jar->created_by)
                                                    <button
                                                        type="button"
                                                        wire:click="confirmKickUser({{ $user->id }})"
                                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                                    >
                                                        Kick
                                                    </button>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>

                                    <div class="small text-muted">
                                        Streak: {{ $user->pivot->streak ?? 0 }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center mb-0">No members yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- CENTER: MAIN JAR / QUEST -->
        <div class="col-12 col-lg-7 order-1 order-lg-2">
            <div class="card text-center bg-block-blue rounded-5 h-100 d-flex flex-column show-jar-card">

                <div class="card-header border-0 position-relative pb-0">
                    @if ($this->jar)
                        <div class="jar-name-container text-center w-100" style="padding-right: 4rem;">
                            <h1 class="jar-name display-6 fw-bold mb-1">{{ $this->jar->name }}</h1>
                        </div>

                        @if($this->jar->description)
                            <p class="jar-name fs-5 overflow-auto scrolling-bar mb-0" style="max-height: 10vh; font-weight: normal;">
                                {{ $this->jar->description }}
                            </p>
                        @else
                            <p class="jar-name fs-6 mb-0 opacity-75 fw-normal">No current description found</p>
                        @endif

                        {{-- JAR LIKE BUTTON (top-right) --}}
                        <div class="position-absolute top-0 end-0 d-flex align-items-center gap-1 p-2">
                            @auth
                                <button
                                    wire:click="toggleLike"
                                    wire:key="jar-like-{{ $this->jar->id }}-{{ $this->liked }}"
                                    class="card-like-heart-btn{{ $this->liked ? ' liked' : '' }}"
                                    style="width: 2.2em; height: 2.2em; font-size: 1.3rem;"
                                >
                                    @if($this->liked)
                                        <i class="bi bi-heart-fill"></i>
                                    @else
                                        <i class="bi bi-heart"></i>
                                    @endif
                                </button>
                            @else
                                <button
                                    wire:click="$dispatchTo('auth.login-modal', 'open-login')"
                                    class="card-like-heart-btn"
                                    style="width: 2.2em; height: 2.2em; font-size: 1.3rem;"
                                >
                                    <i class="bi bi-heart"></i>
                                </button>
                            @endauth
                            <span class="likes-badge-count fs-5 text-light" style="text-shadow: none; font-weight: 700;">
                                {{ $this->likesCount }}
                            </span>
                        </div>
                    @else
                        <h1 class="jar-name fs-3 fw-bold mb-0">No Usable Adventure Found</h1>
                    @endif
                </div>

                <div class="card-body text-center flex-grow-1 d-flex flex-column show-jar-body p-2 p-md-3">

                    @if ($this->currentQuest)
                        <div class="quest-viewer mb-3">
                            <button
                                type="button"
                                wire:click="previous"
                                class="quest-arrow"
                                aria-label="Previous quest"
                            >
                                ❮
                            </button>

                            <div class="quest-image-frame position-relative">

                                <img
                                    src="{{ $this->currentQuest->picture_url }}"
                                    alt="Quest picture"
                                    class="quest-image"
                                >

                                <!-- LIKE BUTTON OVERLAY -->
                                <div class="position-absolute top-0 end-0 m-2 text-end">
                                    @auth
                                        <button
                                            wire:click="toggleQuestLike"
                                            wire:key="quest-like-{{ $this->currentQuest->id }}-{{ $this->questLiked }}"
                                            class="card-like-heart-btn{{ $this->questLiked ? ' liked' : '' }}"
                                        >
                                            @if($this->questLiked)
                                                <i class="bi bi-heart-fill"></i>
                                            @else
                                                <i class="bi bi-heart"></i>
                                            @endif
                                        </button>
                                    @else
                                        <button
                                            wire:click="$dispatchTo('auth.login-modal', 'open-login')"
                                            class="card-like-heart-btn"
                                        >
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    @endauth

                                    <span class="likes-badge-count">
                                        {{ $this->questLikesCount }}
                                    </span>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click="next"
                                class="quest-arrow"
                                aria-label="Next quest"
                            >
                                ❯
                            </button>
                        </div>

                        <!-- QUEST INFO CARD BELOW IMAGE -->
                        <div class="quest-info-card mx-auto w-100 p-3 text-start shadow-sm" style="max-width: 95%; background: linear-gradient(145deg, #EAB2A0, #D9A08E); border-radius: 1.2rem; border: 1px solid rgba(255,255,255,0.2);">
                            <h3 class="quest-name fs-4 fw-bold mb-1" style="color: #2D4356; letter-spacing: 0.5px;">{{ $this->currentQuest->title }}</h3>
                            
                            @if ($this->currentQuest->description)
                                <p class="quest-description scrolling-bar mb-0" style="color: #2D4356; font-size: 0.95rem; max-height: 80px; overflow-y: auto; opacity: 0.95; font-weight: 600;">
                                    {{ $this->currentQuest->description }}
                                </p>
                            @else
                                <p class="quest-description mb-0 opacity-75" style="color: #2D4356; font-style: italic; font-weight: normal;">
                                    No description yet so just figure it out.
                                </p>
                            @endif
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <div class="quest-info-card w-100">
                                <h3 class="quest-name fs-4 mb-0">No Quests inside the jar</h3>
                            </div>
                        </div>
                    @endif
                </div>

                @auth
                    <div class="card-footer p-0 border-0">
                        @if($this->jar->created_by === auth()->id() || auth()->user()->is_admin)

                            <a
                                href="{{ route('jars.quests.create', ['jar' => $this->jar->id]) }}"
                                class="btn fw-medium pink-button rounded-pill w-75 w-lg-50 fs-5 fs-md-4 px-3 py-2 m-1"
                            >
                                Add New Quest
                            </a>

                            @if($this->currentQuest && $this->jar->quests->isNotEmpty())
                                <a
                                    href="{{ route('jars.quests.edit', ['jar' => $this->jar->id, 'jarQuest' => $this->currentQuest->id]) }}"
                                    class="btn fw-medium pink-button rounded-pill w-75 w-lg-50 fs-5 fs-md-4 px-3 py-2 m-1"
                                >
                                    Edit Current Quest
                                </a>
                            @endif

                            @if($this->currentQuest)
                                <button
                                    type="button"
                                    wire:click="confirmDeleteQuest"
                                    class="border-0 btn btn-outline-danger rounded-pill w-75 w-lg-50 fs-5 fs-md-4 px-3 py-2 m-1"
                                >
                                    Delete Quest
                                </button>
                            @endif
                        @else
                            @if ($this->joined)
                                <button
                                    type="button"
                                    wire:click="leaveJar"
                                    class="btn fw-medium pink-button rounded-pill w-75 w-lg-50 fs-5 fs-md-4 px-3 py-2 m-1"
                                >
                                    Leave Jar
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="joinJar"
                                    class="btn fw-medium pink-button rounded-pill w-75 w-lg-50 fs-5 fs-md-4 px-3 py-2 m-1"
                                >
                                    Join Jar
                                </button>
                            @endif
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        <!-- RIGHT: COMMENTS / ADMIN -->
        <div class="col-12 col-lg-3 order-3 d-flex flex-column gap-3">
            @auth
                @if ($this->jar && ($this->joined || $this->jar->created_by === auth()->id() || (auth()->check() && auth()->user()->is_admin)))
                    <!-- Comments Card -->
                    <div class="card bg-block-blue rounded-5 text-center d-flex flex-column" style="height: 60vh; min-height: 400px;">
                        <div class="card-header border-0 d-flex justify-content-center align-items-center pt-3 pb-2 px-3">
                            <h5 class="fw-bold jar-name fs-4 m-0">Jar Chat</h5>
                        </div>
                        <div class="card-body overflow-auto chat-scroll d-flex flex-column gap-2 text-start p-2" wire:poll.3s>
                            @forelse($this->comments as $comment)
                                <div wire:key="comment-{{ $comment->id }}" class="message p-2 mb-1 text-dark shadow-sm rounded-4" style="background-color: {{ $comment->user_id === auth()->id() ? '#EAB2A0' : '#A76F6F' }}; max-width: 90%; {{ $comment->user_id === auth()->id() ? 'align-self: flex-end;' : 'align-self: flex-start; color: #fff !important;' }}">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <img src="{{ $comment->user->profile_picture_url }}" class="rounded-circle" style="width: 20px; height: 20px; object-fit: cover;">
                                        <small class="fw-bold d-flex align-items-center" style="font-size: 0.75rem;">
                                            {{ $comment->user->username }}
                                            @if(optional($comment->user)->is_admin)
                                                <span class="ms-1 px-2 py-0 rounded-pill bg-danger text-white fw-bold" style="font-size: 0.6rem; border: 1px solid rgba(255,255,255,0.2);">ADMIN</span>
                                            @endif
                                            @if($comment->user_id === $this->jar->created_by)
                                                <span class="ms-1 px-2 py-0 rounded-pill bg-warning text-dark fw-bold" style="font-size: 0.6rem; border: 1px solid rgba(0,0,0,0.1);">CREATOR</span>
                                            @endif
                                        </small>
                                    </div>
                                    <div style="font-size: 0.9rem; word-break: break-word;">
                                        {{ $comment->content }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-light mt-4">
                                    <small>No comments yet. Be the first to comment!</small>
                                </div>
                            @endforelse
                        </div>
                        <div class="card-footer border-0 p-2 pb-3">
                            <form wire:submit.prevent="addComment" class="d-flex gap-2">
                                <input type="text" wire:model="newComment" class="form-control rounded-pill border-0 px-3" placeholder="Message..." style="background-color: rgba(234, 178, 160, 0.2); color: #fff;" required>
                                <button type="submit" class="btn pink-button rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; min-width: 38px;">
                                    <span style="font-size: 1rem;">&#10148;</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                @if ($this->jar && ($this->jar->created_by === auth()->id() || (auth()->check() && auth()->user()->is_admin)))
                    <!-- Admin Card -->
                    <div class="card bg-block-blue rounded-5 text-center mt-auto">
                        <div class="card-body text-center p-3">
                            <div class="d-flex flex-column align-items-center gap-2">
                                <a
                                    href="{{ route('jars.edit', $this->jar) }}"
                                    class="btn fw-medium pink-button rounded-pill w-100 px-3 py-2 fs-6"
                                >
                                    Edit Jar
                                </a>

                                <button
                                    type="button"
                                    wire:click="confirmDeleteJar"
                                    class="w-100 border-0 btn btn-outline-danger rounded-pill px-3 py-2 fs-6"
                                >
                                    Delete Jar
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
        </div>

    </div>

    <div>
        @if ($this->confirmingDeleteQuest)
            <div
                class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                style="background: rgba(0,0,0,0.5); z-index: 9999;"
            >
                <div class="card bg-block-blue rounded-5 p-4 text-center" style="width: min(90vw, 420px);">
                    <h3 class="jar-name fs-3 mb-3">Delete quest?</h3>
                    <p class="mb-4">Are you sure you want to delete this quest?</p>

                    <div class="d-flex justify-content-center gap-2">
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="btn blue-button rounded-pill px-4"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            wire:click="deleteCurrentQuest"
                            class="btn btn-outline-danger rounded-pill px-4"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if ($this->confirmingDeleteJar)
            <div
                class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                style="background: rgba(0,0,0,0.5); z-index: 9999;"
            >
                <div class="card bg-block-blue rounded-5 p-4 text-center" style="width: min(90vw, 420px);">
                    <h3 class="jar-name fs-3 mb-3">Delete jar?</h3>
                    <p class="mb-4">This will delete the whole jar. Are you sure?</p>

                    <div class="d-flex justify-content-center gap-2">
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="btn blue-button rounded-pill px-4"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            wire:click="deleteJar"
                            class="btn btn-outline-danger rounded-pill px-4"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- KICK USER MODAL (ADDED) --}}
        @if ($this->confirmingKickUser)
            <div
                class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"
                style="background: rgba(0,0,0,0.5); z-index: 9999;"
            >
                <div class="card bg-block-blue rounded-5 p-4 text-center" style="width: min(90vw, 420px);">
                    <h3 class="jar-name fs-3 mb-3">Kick user?</h3>
                    <p class="kick-modal-text mb-4">Are you sure you want to remove this user from the jar?</p>

                    <div class="d-flex justify-content-center gap-2">
                        <button
                            type="button"
                            wire:click="closeDeleteModal"
                            class="btn blue-button rounded-pill px-4"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            wire:click="kickUser"
                            class="btn btn-outline-danger rounded-pill px-4"
                        >
                            Kick
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
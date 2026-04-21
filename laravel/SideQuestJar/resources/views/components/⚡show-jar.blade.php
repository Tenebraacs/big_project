<?php

use Livewire\Component;
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


    public function mount(Jar $jar, ?int $quest = null)
    {
        $this->jar = $jar->load('users');
        $this->quest = $quest;
        $this->quests = $this->jar->quests->values();

        if(auth()->check()) {
            $this->liked = $this->jar->likes()->where('user_id', auth()->id())->exists(); //when the page loads check if the logged-in user already liked this jar
            $this->joined = $this->jar->users()->where('user_id', auth()->id())->exists();
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

    public function next()
    {
        if ($this->quests->isEmpty()) {
            return;
        }

        $this->currentIndex = ($this->currentIndex + 1) % $this->quests->count();
        $this->updateUrl();
    }

    public function previous()
    {
        if ($this->quests->isEmpty()) {
            return;
        }

        $this->currentIndex = ($this->currentIndex - 1 + $this->quests->count()) % $this->quests->count();
        $this->updateUrl();
    }

    

    public function toggleLike()
    {
        if (! auth()->check()){
            return;
        }

        $existingLike = $this->jar->likes()->where('user_id', auth()->id())->first();

        if($existingLike) {
            $existingLike->delete();
            $this->liked = false;
            return;
        }

        $this->jar->likes()->create([
            'user_id' => auth()->id(),
        ]);
        
        $this->liked = true;
    }

    public function joinJar()
    {
        if(! auth()->check() || $this->joined) {
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
        if(! auth()->check() || ! $this->joined){
            return;
        }

        $this->jar->users()->detach(auth()->id());

        $this->jar = $this->jar->fresh()->load('users');
        $this->joined = false;
    }


    public function deleteJar(){
        $this->authorize('delete', $this->jar);

        $this->confirmingDeleteJar = false;

        $this->jar->delete();

        return $this->redirect(route('home'), navigate: true);
        
    }

    public function deleteCurrentQuest()
    {
        if (! $this->currentQuest) {
            return;
        }

        $this->authorize('update', $this->jar);

        $deletedIndex = $this->currentIndex;
        $questToDelete = $this->currentQuest;

        $questToDelete->delete();

        $this->jar = $this->jar->fresh()->load('users');
        $this->quests = $this->jar->quests()->orderBy('id')->get()->values();

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

    public function confirmDeleteQuest(){
        if(! $this->currentQuest){
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
    }

};
?>

<div class="container-fluid">
    <div class="row align-items-start justify-content-center g-3">

        <!-- LEFT: MEMBERS -->
        <div class="col-12 col-lg-3 order-2 order-lg-1">
            <div class="card bg-block-blue rounded-5 h-100">
                <div class="card-header border-0 text-center">
                    <h3 class="jar-name fs-3 fw-bold mb-1">Members</h3>
                    <p class="mb-0">{{ $this->jar->users->count() }} joined</p>
                </div>

                <div class="card-body overflow-auto" style="max-height: 70vh;">
                    @if ($this->jar->users->isNotEmpty())
                        <div class="d-flex flex-column gap-2">
                            @foreach ($this->jar->users->sortByDesc(fn($user) => $user->pivot->steak ?? 0) as $user)
                                <div class="rounded-4 px-3 py-2" style="background-color: #EAB2A0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-dark">{{ $user->username }}</span>

                                        <div class="d-flex gap-1">
                                            @if ($user->id === $this->jar->created_by)
                                                <span class="badge text-bg-dark">Owner</span>
                                            @endif

                                            @auth
                                                @if ($user->id === auth()->id())
                                                    <span class="badge text-bg-primary">You</span>
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
        <div class="col-12 col-lg-6 order-1 order-lg-2">
            <div class="card text-center bg-block-blue rounded-5 h-100 d-flex flex-column">

                <div class="card-header border-0">
                    @if ($this->jar)
                        <div class="d-flex">
                            <div class="flex-grow-1 text-center">
                                <h1 class="jar-name display-5 fw-bold">{{ $this->jar->name }}</h1>
                            </div>

                            <div class="text-start border-0">
                                <button
                                    type="button"
                                    wire:click="toggleLike"
                                    class="bg-transparent border-0 fs-2 bi {{ $this->liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"
                                    style="color: #EAB2A0;"
                                ></button>
                            </div>
                        </div>

                        @if($this->jar->description)
                            <h2 class="jar-name fs-2 overflow-auto scrolling-bar" style="max-height: 20vh;">
                                {{ $this->jar->description }}
                            </h2>
                        @else
                            <h2 class="jar-name fs-2">No current description found</h2>
                        @endif
                    @else
                        <h1 class="jar-name fs-1 fw-bold">No Usable Adventure Found</h1>
                    @endif
                </div>

                <div class="card-body text-center flex-grow-1 d-flex flex-column">
                    <div class="p-2 mb-3">
                        <div class="card-header shadow-black rounded-4" style="background-color: #EAB2A0">
                            @if($this->currentQuest)
                                <h3 class="quest-name fs-2">{{ $this->currentQuest->title }}</h3>

                                @if ($this->currentQuest->description)
                                    <p class="fs-5 quest-name overflow-auto scrolling-bar" style="max-height: 20vh">
                                        {{ $this->currentQuest->description }}
                                    </p>
                                @else
                                    <p class="fs-5 quest-name">No description yet so just figure it out</p>
                                @endif
                            @else
                                <h3 class="quest-name fs-5">No Quests inside the jar</h3>
                            @endif
                        </div>
                    </div>

                    @if ($this->currentQuest)
                        <div class="container-fluid">
                            <div class="row justify-content-center align-items-stretch">
                                <button
                                    type="button"
                                    wire:click="previous"
                                    class="blue-button fw-bold col-1 d-flex align-items-center justify-content-center fs-1 border-0"
                                    style="color: #EAB2A0; background-color: #2D4356;"
                                >
                                    ❮
                                </button>

                                <div class="col-10 p-0" style="aspect-ratio: 1/1;">
                                    <img
                                        src="{{ $this->currentQuest->picture_url }}"
                                        alt="Quest picture"
                                        class="w-100 h-100 rounded-4"
                                        style="object-fit: cover;"
                                    >
                                </div>

                                <button
                                    type="button"
                                    wire:click="next"
                                    class="fw-bold col-1 d-flex align-items-center justify-content-center fs-1 border-0"
                                    style="color: #EAB2A0; background-color: #2D4356;"
                                >
                                    ❯
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                @auth
                    <div class="card-footer p-0 border-0">
                        @if($this->jar->created_by === auth()->id() || auth()->user()->is_admin)

                            <a
                                href="{{ route('jars.quests.create', ['jar' => $this->jar->id]) }}"
                                class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1"
                            >
                                Add New Quest
                            </a>

                            @if($this->currentQuest && $this->jar->quests->isNotEmpty())
                                <a
                                    href="{{ route('jars.quests.edit', ['jar' => $this->jar->id, 'jarQuest' => $this->currentQuest->id]) }}"
                                    class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1"
                                >
                                    Edit Current Quest
                                </a>
                            @endif

                            @if($this->currentQuest)
                                <button
                                    type="button"
                                    wire:click="confirmDeleteQuest"
                                    class="w-50 border-0 btn btn-outline-danger rounded-pill fs-4 p-0 m-1"
                                >
                                    Delete Quest
                                </button>
                            @endif
                        @else
                            @if ($this->joined)
                                <button
                                    type="button"
                                    wire:click="leaveJar"
                                    class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1"
                                >
                                    Leave Jar
                                </button>
                            @else
                                <button
                                    type="button"
                                    wire:click="joinJar"
                                    class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1"
                                >
                                    Join Jar
                                </button>
                            @endif
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        <!-- RIGHT: PREVIEW / ADMIN -->
        <div class="col-12 col-lg-3 order-3">
            @auth
                @if ($this->jar && ($this->jar->created_by === auth()->id() || auth()->user()->is_admin))
                    <div class="card bg-block-blue rounded-5 h-100 text-center">

                        <div class="card-header border-0 text-center">
                            <h5 class="fw-bold jar-name fs-3 text-truncate mt-2">
                                {{ $this->jar->name }}
                            </h5>
                        </div>

                        <div class="card-body text-center">
                            @if ($this->jar->picture_url)
                                <img
                                    src="{{ $this->jar->picture_url }}"
                                    alt="{{ $this->jar->name }}"
                                    class="img-fluid rounded-4"
                                    style="aspect-ratio: 1/1; object-fit: cover; width: 100%;"
                                >
                            @else
                                <p>No image found.</p>
                            @endif
                        </div>

                        <div class="card-footer border-0 d-flex flex-column align-items-center gap-2 pb-3">

                            <a
                                href="{{ route('jars.edit', $this->jar) }}"
                                class="btn fw-medium pink-button rounded-pill w-75 fs-5 p-0"
                            >
                                Edit Jar
                            </a>

                            <button
                                type="button"
                                wire:click="confirmDeleteJar"
                                class="w-75 border-0 btn btn-outline-danger rounded-pill fs-5 p-0"
                            >
                                Delete Jar
                            </button>
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
    </div>
</div>

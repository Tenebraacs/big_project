<?php

use Livewire\Component;
use App\Models\Jar;


new class extends Component
{
    public Jar $jar;
    public ?int $quest = null;
    public $quests;
    public int $currentIndex = 0;
    public bool $liked = false;

    public function mount(Jar $jar, ?int $quest = null)
    {
        $this->jar = $jar;
        $this->quest = $quest;
        $this->quests = $jar->quests->values();

        if(auth()->check()) {
            $this->liked = $this->jar->likes()->where('user_id', auth()->id())->exists(); //when the page loads check if the logged-in user already liked this jar
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



};
?>

<div class="container-fluid">
    <div class="row align-items-end justify-content-end">
        <div class="col-4 col-md-8 col-lg-6 col-xl-4">
            <div class="card text-center bg-block-blue rounded-5 h-100 d-flex flex-column">


                <div class="card-header border-0">
                    @if ($jar)
                        <div class="d-flex">
                            <div class="flex-grow-1 text-center">
                                <h1 class="jar-name display-5 fw-bold">{{ $jar->name }}</h1>
                            </div>

                            <div class="text-start border-0">
                                <button
                                    type="button"
                                    wire:click="toggleLike"
                                    class="bg-transparent border-0 fs-2 bi {{ $liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"
                                    style="color: #EAB2A0;"
                                ></button>
                            </div>
                        </div>

                        @if($jar->description)
                            <h2 class="jar-name fs-2 overflow-auto scrolling-bar p-1" style="max-height: 20vh;">{{ $jar->description }}</h2>
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
                                    <p class="fs-5 quest-name overflow-auto scrolling-bar p-1" style="max-height: 20vh;">{{ $this->currentQuest->description }}</p>
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
                                        src="{{ $this->currentQuest->picture }}"
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
                        @if($jar->created_by === auth()->id() || auth()->user()->is_admin)

                            <a href="{{ route('jars.quests.create', ['jar' => $jar->id]) }}"
                               class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1">
                                Add New Quest
                            </a>

                            @if($this->currentQuest && $this->jar->quests->isNotEmpty())
                                <a href="{{ route('jars.quests.edit', ['jar' => $jar->id, 'jarQuest' => $this->currentQuest->id]) }}"
                                   class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1">
                                    Edit Current Quest
                                </a>
                            @endif

                            @if($this->currentQuest)
                                <form action="{{ route('jars.quests.destroy', ['jar' => $jar->id, 'jarQuest' => $this->currentQuest->id]) }}" method="POST">
                                    <!-- onsubmit="return confirm('Are you sure you want to delete this quest?');" ez gagyi de valami ilyesmi kene azert hogy ne lehessen veletlen torolni (agresszivan magamnak szol ez a komment) -->
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-50 border-0 btn btn-outline-danger rounded-pill fs-4 p-0 m-1">
                                    Delete Quest
                                    </button>
                                </form>
                            @endif
                        @else
                            @if ($jar->users()->where('user_id', auth()->id())->exists())
                                <form action="{{ route('jars.leave', $jar) }}" method="POST" class="w-100">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1">
                                        Leave Jar
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('jars.join', $jar) }}" method="POST" class="w-100">
                                    @csrf
                                    <button type="submit" class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1">
                                        Join Jar
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                @endauth
            </div>
        </div>
        
        <div class="col-4">
            @auth 
            @if ($jar && ($jar->created_by === auth()->id() || auth()->user()->is_admin))
            
            
            <div class="card text-center bg-block-blue h-25 w-75 rounded-5 d-flex flex-column alignt-items-center ms-auto justify-content-end">
            
                @if( $jar && ($jar->created_by === auth()->id() || auth()->user()->is_admin))
                    <a class="btn pink-button rounded-pill m-2 w-75 fs-3 p-0 fw-medium mx-auto" href="{{  route("jars.edit", $jar) }}">Edit Jar</a>
                @endif
                
                @if ($jar->created_by === auth()->id() || auth()->user()->is_admin)
                    <form action="{{ route('jars.destroy', $jar) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-50 border-0 btn btn-outline-danger rounded-pill fs-4 p-0 m-2">
                            Delete Jar
                        </button>
                    </form>
                @endif
           
            
            </div>
            @endif
            @endauth
        </div>
         
    </div>
</div>
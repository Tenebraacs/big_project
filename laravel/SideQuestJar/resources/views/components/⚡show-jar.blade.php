<?php

use Livewire\Component;
use App\Models\Jar;

new class extends Component
{
    public Jar $jar;
    public ?int $quest = null;
    public $quests;
    public int $currentIndex = 0;

    public function mount(Jar $jar, ?int $quest = null){

        

        $this->jar = $jar;
        $this->quest = $quest;

        $this->quests = $jar->quests->values();

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

    public function getNextQuestProperty(){

        if($this->quests->isEmpty()) {
            return null;
        }

        $nextIndex = ($this->currentIndex + 1) % $this->quests->count();

        return $this->quests[$nextIndex];


    }


    public function getPreviousQuestProperty(){

        if($this->quests->isEmpty()) {
            return null;
        }

        $previousIndex = ($this->currentIndex - 1 + $this->quests->count()) % $this->quests->count();

        return $this->quests[$previousIndex];


    }

    public function updateUrl()
    {
        if($this->quests->isEmpty()) {
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
        if($this->quests->isEmpty()) {
            return;
        }

        $this->currentIndex = ($this->currentIndex - 1 + $this->quests->count()) % $this->quests->count();
        $this->updateUrl();

    }

    public function getCurrentQuestProperty()
    {
        if($this->quests->isEmpty()) {
            return null;
        }

        return $this->quests[$this->currentIndex];
    }


    
};
?>

<div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                    
                    
    <div class=" rounded-3 p-2 mb-3 ">
        <div class="card-header rounded-3">
            @if($this->currentQuest)
            <h3>{{ $this->currentQuest->title }}</h3>
                @if ($this->currentQuest->description)
                    <p class="fs-5">{{ $this->currentQuest->description }}</p>
                @else
                    <p class="fs-5">No description yet so just figure it out</p>
                @endif
            @else
            <h3>No Quests inside the jar</h3>
            @endif
        </div>
    </div>
    @if ($this->currentQuest)
        <div class="container-fluid">
            <div class="row justify-content-center align-items-stretch">

                <!-- PREV -->
                <button
                    type="button"
                    wire:click="previous"
                    class="col-1 d-flex align-items-center justify-content-center text-dark fs-1 border-0 bg-transparent"
                >
                    ❮
                </button>

                <!-- IMAGE -->
                <div class="col-10 p-0" style="aspect-ratio: 1/1;">
                    <img 
                        src="{{ $this->currentQuest->picture }}" 
                        alt="Quest picture" 
                        class="w-100 h-100" 
                        style="object-fit: cover;">
                    

                    


                </div>

                @if ($this->nextQuest)
                    <img src="{{ $this->nextQuest->picture }}" alt="" class="d-none">
                @endif

                @if ($this->previousQuest)
                    <img src="{{ $this->previousQuest->picture }}" alt="" class="d-none">
                @endif

                <!-- NEXT -->
                <button
                    type="button"
                    wire:click="next"
                    class="col-1 d-flex align-items-center justify-content-center text-dark fs-1 border-0 bg-transparent"
                >
                    ❯
                </button>


            </div>

        </div>
    @endif
    
</div>
<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Jar;


new class extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public Jar $jar;
    public string $title = '';
    public string $description = '';
    public $picture = null;

    public function mount(Jar $jar){
        $this->jar = $jar;
    }

    public function save()
    {
        $this->authorize('update', $this->jar);

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        if ($this->picture) {
            $validated['picture'] = $this->picture->store('quests', 'public');
        }

        $quest = $this->jar->quests()->create($validated);

        return redirect()->route('jars.show', [
            'jar' => $this->jar->id,
            'quest' => $quest->id,
        ])->with('success', 'Quest created successfully.');
    }

};
?>



    <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form wire:submit="save">
            
                <div class="card text-center bg-block-blue rounded-5 h-100 d-flex flex-column" style="">
                    

                    <div class="card-header border-0">
                        
                        <h1 class="fs-1 jar-name">{{ $jar->name }}</h1>

                        @if($jar->description)
                            <h2 class="jar-name fs-3">{{ $jar->description }}</h2>
                        @else
                            <h2 class="jar-name fs-3">No current description found</h2>
                        @endif
                            
                        
                                    
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger m-2">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                        
                        
                        <div class="row p-1">
                            <label for="title" class="jar-name fs-4">Quest Title</label>
                        </div>
                        <div class="row justify-content-center">
                            <input 
                                wire:model="title"
                                class="text-center col-8 p-1 rounded-3"
                                style="background-color: #eab3a0d5;"
                                required
                                type="text"
                                id="title"
                                placeholder="QuestTitle"
                            >
                        </div>
                        @error('title')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror


                        <div class="row p-1">
                            <label for="description" class="jar-name fs-4">Quest Description</label>
                        </div>
                        <div class="row justify-content-center pb-2">
                            <textarea
                                wire:model="description" 
                                class=" text-center col-8 p-2 rounded-3"
                                style="background-color: #eab3a0d5;"
                                id="description"
                                placeholder="QuestDescription"
                            ></textarea>
                            
                        </div>
                        @error('description')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror
                        
                        <div class="row justify-content-center p-2">
                            <input
                                wire:model="picture"
                                type="file"
                                name="picture"
                                id="picture"
                                accept="image/*"
                                class="col-8"
                            >
                        </div>
                        @error('picture')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror

                        <div class="row justify-content-center align-items-stretch">
                            <div class="col-10 p-0 " style="aspect-ratio: 1/1;">
                                @if ($picture)
                                    <img
                                        src="{{ $picture->temporaryUrl() }}"
                                        alt="Quest preview"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                    >
                                @else
                                    <img
                                        src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png"
                                        alt="Quest picture"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                    >
                                @endif
                            </div>
                        </div>

                        
                        
                    </div>
                    
                    <div class="card-footer p-0 border-0">
                        
                        <div>
                            <button type="submit" class="btn pink-button rounded-pill w-50 h-100 fs-3 p-0 m-2">
                                Add Quest
                            </button>
                        </div>    
                                        
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>


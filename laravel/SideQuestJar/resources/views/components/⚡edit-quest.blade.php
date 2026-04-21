<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use App\Models\Jar;
use App\Models\JarQuest;

new class extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public Jar $jar;
    public JarQuest $jarQuest;

    public string $title = '';
    public string $description = '';
    public $picture = null;


    public function mount(Jar $jar, JarQuest $jarQuest)
    {
        abort_if($jarQuest->jar_id !== $jar->id, 404);

        $this->jar = $jar;
        $this->jarQuest = $jarQuest;

        $this->title = $jarQuest->title ?? '';
        $this->description = $jarQuest->description ?? '';
    }

    public function save()
    {
        

        abort_if($this->jarQuest->jar_id !== $this->jar->id, 404);

        $this->authorize('update', $this->jar);


        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($validated['picture']);

        if ($this->picture) {

            if ($this->jarQuest->picture && Storage::disk('public')->exists($this->jarQuest->picture)){
                Storage::disk('public')->delete($this->jarQuest->picture);
            }

            $validated['picture'] = $this->picture->store('quests', 'public');
        }
            
        

        $this->jarQuest->update($validated);

        return redirect()->route('jars.show', $this->jar->id)
            ->with('success', 'Quest updated successfully');
    }
};
?>


<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form wire:submit="save">

                <div class="card text-center h-100 d-flex flex-column bg-block-blue rounded-5" style="">
                    
                    <div class="card-header border-0">
                        
                        <h1 class="fs-1 jar-name">{{ $jar->name }}</h1>

                        @if($jar->description)
                            <h2 class="jar-name fs-3">{{ $jar->description }}</h2>
                        @else
                            <h2 class="jar-name fs-3">No current description found</h2>
                        @endif
                                   
                    </div>   

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

                        <div class="row">
                            <label for="picture" class="jar-name fs-4">Quest Picture</label>
                        </div>
                        <div class="row justify-content-center p-2">
                            <input 
                                wire:model="picture"    
                                type="file"
                                id="picture"
                                accept="image/*"
                                class="col-8"    
                            >
                        </div>
                        @error('picture')
                            <div class="text-danger mt-2">{{ $message }}</div>
                        @enderror

                        <div wire:loading wire:target="picture" class="text-light mt-2">
                            Uploading image...
                        </div>

                        <div class="row justify-content-center align-items-stretch">
                            <div class="col-10 p-0" style="aspect-ratio: 1/1;">
                                @if ($picture)
                                    <img
                                        src="{{ $picture->temporaryUrl() }}"
                                        alt="Quest preview"
                                        class="w-100 h-100"
                                        style="object-fit: cover;"
                                    >
                                @else
                                    <img
                                        src="{{ $jarQuest->picture_url }}"
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
                            <button 
                                type="submit"
                                class="btn w-50 h-100 pink-button rounded-pill fs-3 p-0 m-2"
                                wire:loading.attr="disabled"    
                            >
                                Update Quest
                            </button>
                        </div>    
                                        
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>


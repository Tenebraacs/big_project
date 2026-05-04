<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Jar;



new class extends Component
{
     use WithFileUploads, AuthorizesRequests;

     public string $name ='';
     public string $description ='';
     public string $type = 'public_jar';
     public $picture = null;

    public function save()
    {
        $this->authorize('create', Jar::class);

        $types = ['public_jar', 'private_jar', 'friends_only_jar', 'invite_only_jar'];
        if (auth()->check() && auth()->user()->is_admin) {
            $types[] = 'official_jar';
        }

        $validated = $this->validate([
            'name' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('jars')->where(fn ($query) => $query->where('created_by', auth()->id()))
            ],
            'description' => ['nullable', 'string'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'type' => ['required', \Illuminate\Validation\Rule::in($types)],
        ]);

        $validated['created_by'] = auth()->id();

        if ($this->picture) {
            $validated['picture'] = $this->picture->store('jars', 'public');
        }
        else{
            unset($validated['picture']);
        }

        $jar = Jar::create($validated);

        $jar->users()->syncWithoutDetaching([
            auth()->id() => ['streak' => 0]
        ]);

        return redirect()->route('jars.show', $jar)->with('success', 'New jar was added');


    }

};
?>


    <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form wire:submit="save">
            
                <div class="card bg-block-blue rounded-5 text-center h-100 d-flex flex-column" style="">
                    <div class="card-header border-0">
                        
                        <div class="row">
                            <label for="name" class="jar-name fs-4">Jar Name</label>
                        </div>
                        <div class="row justify-content-center">

                            <input 
                                wire:model="name"
                                class="text-center col-10 col-md-8 col-lg-6 rounded-2 p-1"
                                style="background-color: #eab3a0d5;"
                                required 
                                type="text" 
                                id="name"
                                placeholder="JarName"
                            >
                            @error('name')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror

                        </div>
                            
                        <div class="row mt-3">
                            <label for="type" class="jar-name fs-4">Visibility</label>
                        </div>
                        <div class="row justify-content-center p-2">
                            <select wire:model="type" id="type" class="text-center col-10 col-md-8 col-lg-6 rounded-2 p-1" style="background-color: #eab3a0d5;">
                                <option value="public_jar">Public</option>
                                <option value="private_jar">Private</option>
                                <option value="friends_only_jar">Friends Only</option>
                                @if(auth()->user()?->is_admin)
                                <option value="official_jar">Official Jar</option>
                                @endif
                            </select>
                            @error('type')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                                    
                    </div>

                    <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                        
                        
                        <div class="row">
                            <label for="description" class="jar-name fs-4">Jar Description</label>
                        </div>
                        <div class="row justify-content-center p-2">
                            <textarea wire:model="description"
                            class=" text-center col-10 col-md-8 p-2 rounded-3"
                            style="background-color: #eab3a0d5;"
                            id="description"
                            placeholder="JarDescription"></textarea>
                            @error('description')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <label for="picture" class="jar-name fs-4">Jar Picture</label>
                        </div>
                        <div class="row justify-content-center p-2">
                            <input
                                wire:model="picture"
                                type="file"
                                id="picture"
                                accept="image/*"
                                class="col-10 col-md-8 text-center"
                            >
                            @error('picture')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row justify-content-center align-items-stretch">
                            <div class="col-10 p-0 m-1" style="aspect-ratio: 1/1;">
                                @if ($picture)
                                <img 
                                    src="{{ $picture->temporaryUrl() }}"
                                    alt="Jar picture"
                                    class="w-100 h-100 rounded-5"
                                    style="object-fit: cover;"
                                >
                                @else
                                <img 
                                    src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png"
                                    alt="Jar picture"
                                    class="w-100 h-100 rounded-5"
                                    style="object-fit: cover;"
                                >
                                @endif
                            </div>
                        </div>

                        
                        
                    </div>
                    
                    <div class="card-footer p-0 border-0">
                        
                        <div>
                            <button type="submit" class="w-75 w-md-50 pink-button rounded-pill px-3 py-2 fs-5 fs-md-4 mb-3">
                                Create Jar
                            </button>
                        </div>    
                                        
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>



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
        } else {
            $validated['picture'] = 'https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png';
        }

        $quest = $this->jar->quests()->create($validated);

        return redirect()->route('jars.show', [
            'jar' => $this->jar->id,
            'quest' => $quest->id,
        ])->with('success', 'Quest created successfully.');
    }

};
?>

<style>
    .sqj-form input::placeholder,
    .sqj-form textarea::placeholder { color: rgba(255,255,255,0.4); }
    .sqj-form .form-control:focus { box-shadow: 0 0 0 3px rgba(234,178,160,0.3); background-color: rgba(234,178,160,0.25) !important; color: #fff; border-color: transparent; }
</style>

<div class="container py-4" style="max-width: 540px;">
    <form wire:submit="save" class="sqj-form">
        <div class="card bg-block-blue rounded-5 border-0 overflow-hidden shadow-lg">

            {{-- Header --}}
            <div class="card-header border-0 text-center pt-4 pb-3 px-4">
                <h1 class="jar-name display-5 fw-bold mb-1">Add Quest</h1>
                <p class="text-light opacity-50 small mb-0">{{ $jar->name }}</p>
            </div>

            @if ($errors->any())
                <div class="px-4 pt-2">
                    <div class="alert alert-danger rounded-4 py-2 small mb-0">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Body --}}
            <div class="card-body px-4 py-3 d-flex flex-column gap-3">

                {{-- Title --}}
                <div class="d-flex flex-column gap-1">
                    <label for="title" class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">Quest Title</label>
                    <input
                        wire:model="title"
                        type="text" id="title" required
                        class="form-control rounded-pill border-0 px-3 py-2"
                        style="background-color: rgba(234,178,160,0.18); color: #fff;"
                        placeholder="Name the quest…"
                    >
                    @error('title')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Description --}}
                <div class="d-flex flex-column gap-1">
                    <label for="description" class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">
                        Description <span class="opacity-50 fw-normal text-lowercase">(optional)</span>
                    </label>
                    <textarea
                        wire:model="description"
                        id="description" rows="3"
                        class="form-control rounded-4 border-0 px-3 py-2"
                        style="background-color: rgba(234,178,160,0.18); color: #fff; resize: none;"
                        placeholder="Describe what needs to be done…"
                    ></textarea>
                    @error('description')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Picture --}}
                <div class="d-flex flex-column gap-2">
                    <label class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">
                        Quest Picture <span class="opacity-50 fw-normal text-lowercase">(optional)</span>
                    </label>
                    <div class="rounded-4 overflow-hidden" style="aspect-ratio:1/1; max-height:260px;">
                        @if ($picture)
                            <img src="{{ $picture->temporaryUrl() }}" alt="Preview" class="w-100 h-100" style="object-fit:cover;">
                        @else
                            <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Placeholder" class="w-100 h-100" style="object-fit:cover; opacity:0.55;">
                        @endif
                    </div>
                    <div class="custom-upload-wrapper">
                        <label class="custom-upload-button">
                            <input type="file" wire:model="picture" accept="image/*" hidden>
                            <span><i class="bi bi-upload me-1"></i> Choose Image</span>
                        </label>
                        <span class="custom-upload-filename">
                            {{ $picture ? $picture->getClientOriginalName() : 'No file selected' }}
                        </span>
                    </div>
                    <div wire:loading wire:target="picture" class="text-center text-light small opacity-75">Uploading…</div>
                    @error('picture')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer border-0 px-4 pb-4 pt-2 text-center">
                <button type="submit"
                    class="btn pink-button rounded-pill px-5 py-2 fs-5 w-75"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Add Quest</span>
                    <span wire:loading>Adding…</span>
                </button>
            </div>

        </div>
    </form>
</div>

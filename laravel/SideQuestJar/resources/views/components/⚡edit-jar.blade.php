<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use App\Models\Jar;

new class extends Component
{
    use WithFileUploads, AuthorizesRequests;

    public Jar $jar;
    public string $name = '';
    public string $description = '';
    public string $type = 'public_jar';
    public $picture = null;

    public function mount(Jar $jar)
    {
        $this->jar = $jar;
        $this->name = $jar->name;
        $this->description = $jar->description ?? '';
        $this->type = $jar->type;
    }

    public function save(){

        $this->authorize('update', $this->jar);

        $types = ['public_jar', 'private_jar', 'friends_only_jar', 'invite_only_jar'];
        if (auth()->check() && auth()->user()->is_admin) {
            $types[] = 'official_jar';
        }

        $validated = $this->validate([
            'name' => [
                'required', 'string', 'max:255',
                \Illuminate\Validation\Rule::unique('jars')->where(fn ($query) => $query->where('created_by', auth()->id()))->ignore($this->jar)
            ],
            'description' => ['nullable', 'string'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'type' => ['required', \Illuminate\Validation\Rule::in($types)],
        ]);

        unset($validated['picture']);

        if ($this->picture) {

            if ($this->jar->picture && Storage::disk('public')->exists($this->jar->picture)){
                Storage::disk('public')->delete($this->jar->picture);
            }

            $validated['picture'] = $this->picture->store('jars', 'public');
        }

        $this->jar->update($validated);

        return redirect()->route('jars.show', $this->jar)->with('success', 'Jar updated successfully');

    }

};
?>

<style>
    .sqj-form input::placeholder,
    .sqj-form textarea::placeholder { color: rgba(255,255,255,0.4); }
    .sqj-form select option { background: #1e2a3a; color: #fff; }
    .sqj-form .form-control:focus,
    .sqj-form .form-select:focus { box-shadow: 0 0 0 3px rgba(234,178,160,0.3); background-color: rgba(234,178,160,0.25) !important; color: #fff; border-color: transparent; }
</style>

<div class="container py-4" style="max-width: 540px;">
    <form wire:submit="save" class="sqj-form">
        <div class="card bg-block-blue rounded-5 border-0 overflow-hidden shadow-lg">

            {{-- Header --}}
            <div class="card-header border-0 text-center pt-4 pb-3 px-4">
                <h1 class="jar-name display-5 fw-bold mb-1">Edit Jar</h1>
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

                {{-- Name --}}
                <div class="d-flex flex-column gap-1">
                    <label for="name" class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">Jar Name</label>
                    <input
                        wire:model="name"
                        type="text" id="name" required
                        class="form-control rounded-pill border-0 px-3 py-2"
                        style="background-color: rgba(234,178,160,0.18); color: #fff;"
                        placeholder="Give your jar a name…"
                    >
                    @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Visibility --}}
                <div class="d-flex flex-column gap-1">
                    <label for="type" class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">Visibility</label>
                    <select wire:model="type" id="type"
                        class="form-select rounded-pill border-0 px-3 py-2"
                        style="background-color: rgba(234,178,160,0.18); color: #fff;">
                        <option value="public_jar">🌍 Public</option>
                        <option value="private_jar">🔒 Private</option>
                        <option value="friends_only_jar">👥 Friends Only</option>
                        @if(auth()->user()?->is_admin)
                            <option value="official_jar">⭐ Official Jar</option>
                        @endif
                    </select>
                    @error('type')<span class="text-danger small">{{ $message }}</span>@enderror
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
                        placeholder="Describe what this jar is about…"
                    ></textarea>
                    @error('description')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Picture --}}
                <div class="d-flex flex-column gap-2">
                    <label class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">Jar Picture</label>
                    <div class="rounded-4 overflow-hidden" style="aspect-ratio:1/1; max-height:260px;">
                        @if ($picture)
                            <img src="{{ $picture->temporaryUrl() }}" alt="Preview" class="w-100 h-100" style="object-fit:cover;">
                        @else
                            <img src="{{ $jar->picture_url }}" alt="Current picture" class="w-100 h-100" style="object-fit:cover;">
                        @endif
                    </div>
                    <div class="custom-upload-wrapper">
                        <label class="custom-upload-button">
                            <input type="file" wire:model="picture" accept="image/*" hidden>
                            <span><i class="bi bi-upload me-1"></i> Change Image</span>
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
                    <span wire:loading.remove>Update Jar</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>

        </div>
    </form>
</div>
<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;
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
            'title' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:1500'],
            'picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($validated['picture']);

        if ($this->picture) {

            if ($this->jarQuest->picture && Storage::disk('public')->exists($this->jarQuest->picture)){
                Storage::disk('public')->delete($this->jarQuest->picture);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($this->picture->getRealPath());
            $image->scaleDown(1200, 1200);

            $filename = 'quests/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($filename, (string) $image->toWebp(80));

            $validated['picture'] = $filename;
        }

        $this->jarQuest->update($validated);

        return redirect()->route('jars.show', [
                'jar' => $this->jar->id,
                'quest' => $this->jarQuest->id,
            ])
            ->with('success', 'Quest updated successfully');
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
                <h1 class="jar-name display-5 fw-bold mb-1">Edit Quest</h1>
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
                        type="text" id="title" required maxlength="60"
                        class="form-control rounded-pill border-0 px-3 py-2"
                        style="background-color: rgba(234,178,160,0.18); color: #fff;"
                        placeholder="Name the quest…"
                    >
                    <div class="d-flex justify-content-end"><span class="text-light opacity-40" style="font-size:0.72rem;">{{ strlen($title) }}/60</span></div>
                    @error('title')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Description --}}
                <div class="d-flex flex-column gap-1">
                    <label for="description" class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">
                        Description <span class="opacity-50 fw-normal text-lowercase">(optional)</span>
                    </label>
                    <textarea
                        wire:model="description"
                        id="quest-description" rows="3"
                        class="form-control rounded-4 border-0 px-3 py-2"
                        style="background-color: rgba(234,178,160,0.18); color: #fff; resize: none;"
                        placeholder="Describe what needs to be done…"
                        maxlength="1500"
                        oninput="updateWordCount(this,'quest-desc-count')"
                    ></textarea>
                    <div class="d-flex justify-content-end">
                        <span id="quest-desc-count" class="text-light opacity-40" style="font-size:0.72rem;">0 / 250 words</span>
                    </div>
                    @error('description')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

                {{-- Picture --}}
                <div class="d-flex flex-column gap-2">
                    <label class="text-light opacity-75 small fw-semibold text-uppercase" style="letter-spacing:.06em;">Quest Picture</label>
                    <div class="rounded-4 overflow-hidden" style="aspect-ratio:1/1; max-height:260px;">
                        @if ($picture)
                            <img src="{{ $picture->temporaryUrl() }}" alt="Preview" class="w-100 h-100" style="object-fit:cover;">
                        @else
                            <img src="{{ $jarQuest->picture_url }}" alt="Current picture" class="w-100 h-100" style="object-fit:cover;">
                        @endif
                    </div>
                    <x-image-cropper property="picture" label="Change Image" />
                    <div wire:loading wire:target="picture" class="text-center text-light small opacity-75">Uploading…</div>
                    @error('picture')<span class="text-danger small">{{ $message }}</span>@enderror
                </div>

            </div>

            {{-- Footer --}}
            <div class="card-footer border-0 px-4 pb-4 pt-2 text-center">
                <button type="submit"
                    class="btn pink-button rounded-pill px-5 py-2 fs-5 w-75"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Quest</span>
                    <span wire:loading>Saving…</span>
                </button>
            </div>

        </div>
    </form>
</div>

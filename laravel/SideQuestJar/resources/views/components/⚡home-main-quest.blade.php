<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\QuestCompletion;
use App\Models\JarUser;
use Illuminate\Support\Facades\Auth;



new class extends Component
{
    use WithFileUploads;

    public $mainJar;
    public $mainQuest;

    public bool $showClaimModal = false;
    public $completionImage = null;
    public string $completionDescription = '';

    public function mount($mainJar, $mainQuest)
    {
        $this->mainJar = $mainJar;
        $this->mainQuest = $mainQuest;
    }

    protected function rules()
    {
        return [
            'completionImage' => 'required|image|max:2048',
            'completionDescription' => 'nullable|string|max:1000',
        ];
    }

    public function getAlreadyClaimedTodayProperty()
    {
        if (!Auth::check() || !$this->mainQuest) {
            return false;
        }

        return QuestCompletion::where('user_id', Auth::id())
            ->where('jar_quest_id', $this->mainQuest->id)
            ->whereDate('completion_date', now()->toDateString())
            ->exists();
    }

    public function openClaimModal()
    {
        if (!Auth::check() || !$this->mainQuest || $this->alreadyClaimedToday) {
            return;
        }

        $this->reset(['completionImage', 'completionDescription']);
        $this->resetValidation();

        $this->showClaimModal = true;
    }

    public function closeClaimModal()
    {
        $this->showClaimModal = false;
        $this->reset(['completionImage', 'completionDescription']);
        $this->resetValidation();
    }

    public function claimQuest()
    {
        if (!Auth::check() || !$this->mainQuest) {
            return;
        }

        if ($this->alreadyClaimedToday) {
            $this->closeClaimModal();
            return;
        }

        $this->validate();

        $imagePath = $this->completionImage->store('quest-completions', 'public');

        QuestCompletion::create([
            'completion_date' => now(),
            'picture' => $imagePath,
            'description' => $this->completionDescription,
            'type' => 'public_post',
            'user_id' => Auth::id(),
            'jar_quest_id' => $this->mainQuest->id,
        ]);

            $user = Auth::user();

        
        $user->jars()->syncWithoutDetaching([
            $this->mainJar->id => ['streak' => 0]
        ]);

        
        $pivotRow = JarUser::where('user_id', $user->id)
            ->where('jar_id', $this->mainJar->id)
            ->first();

        $currentStreak = $pivotRow?->streak ?? 0;

        
        $claimedYesterday = QuestCompletion::where('user_id', $user->id)
            ->where('jar_quest_id', '!=', $this->mainQuest->id) // optional, not required
            ->whereDate('completion_date', now()->subDay()->toDateString())
            ->whereHas('jarQuest', function ($query) {
                $query->where('jar_id', $this->mainJar->id);
            })
            ->exists();

        $newStreak = $claimedYesterday ? $currentStreak + 1 : 1;

        $user->jars()->updateExistingPivot($this->mainJar->id, [
            'streak' => $newStreak,
        ]);
        $this->dispatch('streak-updated');


        unset($this->alreadyClaimedToday);

        $this->closeClaimModal();
    }
};
?>

<div class="row justify-content-center mb-3">
    <div class="col-10">
        <div class="card rounded-5 bg-block-blue m-3">
            <div class="text-center card-header border-0">
                @if ($mainJar)
                    <h1 class="fw-bold display-3 welcome-text m-3">{{ $mainJar->name }}</h1>
                @else
                    <h1 class="fs-1 fw-bold welcome-text">No Usable Adventure Found</h1>
                @endif
            </div>
            
            <div class="card-body text-center">
                <div class="row">
                    @if ($mainQuest)
                        <div class="col-sm-8 text-center align-self-center d-flex justify-content-center">
                            <p class="jar-name display-6 text-center">{{ $mainQuest->description }}</p>
                        </div>

                        <div class="col-sm-4">
                            <div style="aspect-ratio: 1/1;">
                                <img src="{{ $mainQuest->picture_url }}" alt="Jar picture" class="img-fluid w-100 h-100 rounded-5" style="object-fit: cover;">
                            </div>
                        </div>
                    @endif
                </div>

                @auth
                    @if ($mainQuest)
                        <button
                            wire:click="openClaimModal"
                            type="button"
                            class="btn home-claim-button m-2 rounded-pill {{ $this->alreadyClaimedToday ? 'claim-button-disabled' : 'blue-button' }}"
                            @if($this->alreadyClaimedToday) disabled @endif
                        >
                            {{ $this->alreadyClaimedToday ? __('Already Claimed Today') : __('Claim') }}
                        </button>
                    @endif
                @endauth

            </div>
        </div>
    </div>

    @if ($showClaimModal)
        <div class="modal d-block claim-modal-backdrop" tabindex="-1" wire:click="closeClaimModal">
            <div class="modal-dialog modal-dialog-centered" wire:click.stop>
                <div class="modal-content claim-modal-content rounded-5 border-0">
                    <div class="modal-header claim-modal-header border-0">
                        <h5 class="modal-title claim-modal-title">Claim Quest</h5>
                        <button type="button" class="btn-close claim-modal-close" wire:click="closeClaimModal"></button>
                    </div>

                    <div class="modal-body claim-modal-body">
                        <div class="mb-3">
                            <label class="form-label claim-label">Upload an image</label>

                            <div class="custom-upload-wrapper">
                                <label class="custom-upload-button">
                                    <input type="file" wire:model="completionImage" hidden>
                                    <span><i class="bi bi-upload me-1"></i> Choose Image</span>
                                </label>

                                <span class="custom-upload-filename">
                                    {{ $completionImage ? $completionImage->getClientOriginalName() : 'No file selected' }}
                                </span>
                            </div>

                            @error('completionImage')
                                <div class="text-danger mt-2 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        <div wire:loading wire:target="completionImage" class="claim-uploading">
                            Uploading image...
                        </div>

                        <div class="mb-3 mt-4">
                            <label for="completionDescription" class="form-label claim-label">
                                Description <span class="opacity-75">(optional)</span>
                            </label>

                            <textarea
                                id="completionDescription"
                                wire:model.defer="completionDescription"
                                class="form-control claim-input claim-textarea"
                                rows="4"
                                maxlength="1000"
                                placeholder="Add a short description about this claim"
                            ></textarea>

                            @error('completionDescription')
                                <div class="text-danger mt-2 fw-bold">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($completionImage)
                            <div class="mt-4 text-center">
                                <p class="claim-preview-label mb-2">Preview</p>
                                <img src="{{ $completionImage->temporaryUrl() }}" class="img-fluid rounded-4 claim-preview-image">
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer claim-modal-footer border-0">
                        <button type="button" class="btn claim-cancel-button rounded-pill" wire:click="closeClaimModal">
                            Cancel
                        </button>
                        <button type="button" class="btn blue-button rounded-pill px-4" wire:click="claimQuest">
                            Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\QuestCompletion;
use App\Models\JarUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        if (!Auth::check() || !$this->mainQuest || !$this->mainJar) {
            return;
        }

        if ($this->alreadyClaimedToday) {
            $this->closeClaimModal();
            return;
        }

        $this->validate();

        DB::transaction(function () {
            $imagePath = $this->completionImage->store('quest-completions', 'public');

            // 1) Create completion
            QuestCompletion::create([
                'completion_date' => now(),
                'picture' => $imagePath,
                'description' => $this->completionDescription,
                'type' => 'public_post',
                'user_id' => Auth::id(),
                'jar_quest_id' => $this->mainQuest->id,
            ]);

            // 2) Increase quest completions count
            $this->mainQuest->increment('completions');

            // 3) Award XP (xp_gain on jar_quests, fallback to 25)
            $xpGained = (int) ($this->mainQuest->xp_gain ?? 25);

            $user = Auth::user()->fresh();
            $user->increment('user_xp', $xpGained);

            // 4) Streak logic (your original logic)
            $pivotRow = JarUser::where('user_id', $user->id)
                ->where('jar_id', $this->mainJar->id)
                ->first();

            if (!$pivotRow) {
                $user->jars()->attach($this->mainJar->id, ['streak' => 0]);

                $pivotRow = JarUser::where('user_id', $user->id)
                    ->where('jar_id', $this->mainJar->id)
                    ->first();
            }

            $currentStreak = $pivotRow?->streak ?? 0;

            $claimedYesterday = QuestCompletion::where('user_id', $user->id)
                ->whereDate('completion_date', now()->subDay()->toDateString())
                ->whereHas('jarQuest', function ($query) {
                    $query->where('jar_id', $this->mainJar->id);
                })
                ->exists();

            $newStreak = $claimedYesterday ? $currentStreak + 1 : 1;

            $user->jars()->updateExistingPivot($this->mainJar->id, [
                'streak' => $newStreak,
            ]);
        });

        // Refresh UI
        $this->dispatch('streak-updated');
        $this->dispatch('proof-created');
        $this->dispatch('profile-updated'); // ✅ updates navbar + profile header XP/level

        unset($this->alreadyClaimedToday);

        $this->closeClaimModal();
    }
};
?>

<div class="row justify-content-center mb-3">
    <div class="col-12 col-md-10 px-2 px-md-3">
        <div class="card rounded-5 bg-block-blue mb-3 mx-1 mx-md-3 mt-md-3 mainjar-border">
            <div class="text-center card-header border-0 px-4 px-md-5 pt-4 pb-0">
                @if ($mainJar)
                    <h1 class="fw-bold fs-1 display-md-3 welcome-text m-0">{{ $mainJar->name }}</h1>
                @else
                    <h1 class="fs-2 fs-md-1 fw-bold welcome-text m-0">No Usable Adventure Found</h1>
                @endif
            </div>

            <div class="card-body p-3 p-md-4 pt-1">
                <div class="row align-items-start g-3">
                    @if ($mainQuest)
                        <div class="col-12 col-md-7 text-center d-flex flex-column px-md-4 mt-0">
                            <h2 class="fw-bold welcome-text mb-5" style="font-size: clamp(1.4rem, 2.5vw, 2.2rem); opacity: 0.95;">{{ $mainQuest->title }}</h2>
                            <p class="jar-name mb-0" style="font-size: clamp(1.15rem, 2.2vw, 1.45rem); opacity: 1; line-height: 1.5; margin-top: 1rem;">{{ $mainQuest->description }}</p>
                        </div>

                        <div class="col-10 col-sm-8 col-md-5 mx-auto">
                            <div style="aspect-ratio: 1/1;">
                                <img src="{{ $mainQuest->picture_url }}" alt="Quest picture"
                                     class="img-fluid w-100 h-100 rounded-5"
                                     style="object-fit: cover; box-shadow: 0 0 28px rgba(234,178,160,0.25), 0 8px 24px rgba(0,0,0,0.35);">
                            </div>
                        </div>
                    @endif
                </div>

                @if ($mainQuest)
                    <div class="text-center mt-3">
                        @auth
                            <button
                                wire:click="openClaimModal"
                                type="button"
                                class="btn home-claim-button m-2 rounded-pill {{ $this->alreadyClaimedToday ? 'claim-button-disabled' : 'blue-button' }}"
                                @if($this->alreadyClaimedToday) disabled @endif
                            >
                                {{ $this->alreadyClaimedToday ? __('Already Claimed Today') : __('Claim') }}
                            </button>
                        @else
                            <button
                                type="button"
                                class="btn home-claim-button m-2 rounded-pill blue-button"
                                wire:click="$dispatchTo('auth.login-modal', 'open-login')"
                            >
                                Claim
                            </button>
                        @endauth
                    </div>
                @endif
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
                                class="form-control claim-input claim-textarea theme-scrollbar"
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
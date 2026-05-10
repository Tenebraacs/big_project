<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

new class extends Component
{
    use WithFileUploads;

    public string $username = '';
    public string $email = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public bool $editUsername = false;
    public bool $editEmail = false;
    public bool $editPassword = false;

    
    public bool $editPicture = false;
    public $profile_picture_upload = null; // TemporaryUploadedFile|null
    

    public function mount(): void
    {
        $user = Auth::user();

        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
    }

    public function startEdit(string $field): void
    {
        $this->editUsername = false;
        $this->editEmail = false;
        $this->editPassword = false;
        $this->editPicture = false;

        $this->resetValidation();

        // reset password inputs whenever we start editing anything
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        // reset file input when switching edits
        $this->profile_picture_upload = null;

        if ($field === 'username') $this->editUsername = true;
        if ($field === 'email') $this->editEmail = true;
        if ($field === 'password') $this->editPassword = true;
        if ($field === 'picture') $this->editPicture = true;
    }

    public function cancelEdit(): void
    {
        $user = Auth::user();

        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';

        $this->editUsername = false;
        $this->editEmail = false;
        $this->editPassword = false;
        $this->editPicture = false;

        $this->resetValidation();

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->profile_picture_upload = null;
    }

    public function saveUsername(): void
    {
        $user = Auth::user();

        $this->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
        ]);

        $user->username = $this->username;
        $user->save();

        $this->editUsername = false;
        session()->flash('profile_saved', 'Username updated.');

        $this->dispatch('profile-updated');
    }

    public function saveEmail(): void
    {
        $user = Auth::user();

        $this->validate([
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->email = $this->email;
        $user->save();

        $this->editEmail = false;
        session()->flash('profile_saved', 'E-mail updated.');
        $this->dispatch('profile-updated');
    }

    public function savePassword(): void
    {
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Current password is incorrect.');
            return;
        }

        // Your User model casts password => 'hashed'
        $user->password = $this->new_password;
        $user->save();

        $this->editPassword = false;
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('profile_saved', 'Password updated.');
        $this->dispatch('profile-updated');
    }

    // NEW: save profile picture
    public function savePicture(): void
    {
        $user = Auth::user();

        $this->validate([
            'profile_picture_upload' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $manager = new ImageManager(new Driver());
        $image = $manager->read($this->profile_picture_upload->getRealPath());
        $image->cover(400, 400);

        $filename = 'profile-pictures/' . Str::random(40) . '.webp';
        Storage::disk('public')->put($filename, (string) $image->toWebp(80));

        $user->profile_picture = $filename; 
        $user->save();

        $this->editPicture = false;
        $this->profile_picture_upload = null;

        session()->flash('profile_saved', 'Profile picture updated.');
        $this->dispatch('profile-updated');
    }
};
?>

<div>
    @if (session('profile_saved'))
        <div class="alert alert-success rounded-pill">
            {{ session('profile_saved') }}
        </div>
    @endif

    {{-- Profile Picture --}}
    <div class="mb-4">
        <label class="profile-label fs-4 d-flex align-self-start m-1 mb-0">Profile picture</label>

        @if ($editPicture)
            <div class="d-flex flex-column gap-2 align-items-start">
                <x-image-cropper property="profile_picture_upload" label="Choose Image" />

                @error('profile_picture_upload')
                    <div class="text-danger">{{ $message }}</div>
                @enderror

                {{-- preview --}}
                @if ($profile_picture_upload)
                    <img
                        src="{{ $profile_picture_upload->temporaryUrl() }}"
                        class="img-fluid rounded-4"
                        style="max-width: 220px; aspect-ratio: 1/1; object-fit: cover;"
                        alt="Preview"
                    >
                @endif

                <div class="d-flex gap-2">
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="savePicture" type="button">Save</button>
                    <button class="btn btn-secondary fw-medium rounded-pill" wire:click="cancelEdit" type="button">Cancel</button>
                </div>
            </div>
        @else
            <div class="d-flex gap-2 align-items-center">
                <button class="btn edit-button fw-medium rounded-pill" wire:click="startEdit('picture')" type="button">Edit</button>
            </div>
        @endif
    </div>

    {{-- Username --}}
    <div class="mb-3">
        <label class="profile-label fs-4 d-flex align-self-start m-1 mb-0">Username</label>

        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                @if ($editUsername)
                    <input type="text" class="form-control rounded-pill" wire:model.defer="username">
                    @error('username') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                @else
                    <p class="profile-label fs-5 fw-light text-white d-flex align-self-start m-2 mt-0">
                        {{ $username }}
                    </p>
                @endif
            </div>

            <div class="col-md-4 d-flex gap-2">
                @if ($editUsername)
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="saveUsername" type="button">Save</button>
                    <button class="btn btn-secondary fw-medium rounded-pill" wire:click="cancelEdit" type="button">Cancel</button>
                @else
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="startEdit('username')" type="button">Edit</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Email --}}
    <div class="mb-3">
        <label class="profile-label fs-4 d-flex align-self-start m-1 mb-0">E-mail</label>

        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                @if ($editEmail)
                    <input type="email" class="form-control rounded-pill" wire:model.defer="email">
                    @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                @else
                    <p class="profile-label fs-5 fw-light text-truncate text-white d-flex align-self-center m-2 mt-0">
                        {{ $email }}
                    </p>
                @endif
            </div>

            <div class="col-md-4 d-flex gap-2">
                @if ($editEmail)
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="saveEmail" type="button">Save</button>
                    <button class="btn btn-secondary fw-medium rounded-pill" wire:click="cancelEdit" type="button">Cancel</button>
                @else
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="startEdit('email')" type="button">Edit</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Password --}}
    <div class="mb-3">
        <label class="profile-label fs-4 d-flex align-self-start m-1 mb-0">Password</label>

        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                @if ($editPassword)
                    <input type="password" class="form-control rounded-pill mb-2"
                        placeholder="Current password" wire:model.defer="current_password">
                    @error('current_password') <div class="text-danger mb-2">{{ $message }}</div> @enderror

                    <input type="password" class="form-control rounded-pill mb-2"
                        placeholder="New password" wire:model.defer="new_password">
                    @error('new_password') <div class="text-danger mb-2">{{ $message }}</div> @enderror

                    <input type="password" class="form-control rounded-pill"
                        placeholder="Confirm new password" wire:model.defer="new_password_confirmation">
                @else
                    <p class="profile-label fs-5 fw-light text-white text-truncate d-flex align-self-start m-2 mt-0">
                        ***********
                    </p>
                @endif
            </div>

            <div class="col-md-4 d-flex gap-2">
                @if ($editPassword)
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="savePassword" type="button">Save</button>
                    <button class="btn btn-secondary fw-medium rounded-pill" wire:click="cancelEdit" type="button">Cancel</button>
                @else
                    <button class="btn edit-button fw-medium rounded-pill" wire:click="startEdit('password')" type="button">Edit</button>
                @endif
            </div>
        </div>
    </div>
</div>
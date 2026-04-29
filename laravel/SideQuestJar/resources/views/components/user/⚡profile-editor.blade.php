<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

new class extends Component
{
    public string $username = '';
    public string $email = '';

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public bool $editUsername = false;
    public bool $editEmail = false;
    public bool $editPassword = false;

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

        $this->resetValidation();

        // reset password inputs whenever we start editing anything
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        if ($field === 'username') $this->editUsername = true;
        if ($field === 'email') $this->editEmail = true;
        if ($field === 'password') $this->editPassword = true;
    }

    public function cancelEdit(): void
    {
        $user = Auth::user();

        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';

        $this->editUsername = false;
        $this->editEmail = false;
        $this->editPassword = false;

        $this->resetValidation();

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
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

        // Your User model casts password => 'hashed', so plain assignment is OK.
        $user->password = $this->new_password;
        $user->save();

        $this->editPassword = false;
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        session()->flash('profile_saved', 'Password updated.');
    }
};
?>

<div>
    @if (session('profile_saved'))
        <div class="alert alert-success rounded-pill">
            {{ session('profile_saved') }}
        </div>
    @endif

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
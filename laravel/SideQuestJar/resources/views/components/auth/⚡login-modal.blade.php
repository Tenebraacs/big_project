<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public bool $show = false;

    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected $listeners = [
        'open-login' => 'open',
    ];

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string',
        ];
    }

    public function open()
    {
        $this->reset(['email', 'password', 'remember']);
        $this->resetValidation();
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function login()
    {
        $this->validate();

        if (Auth::attempt([
            'email' => $this->email,
            'password' => $this->password
        ], $this->remember)) {
            session()->regenerate();

            $this->close();
            $this->dispatch('login-success');
        } else {
            $this->addError('email', 'Invalid credentials');
        }
    }
};
?>

<div x-data @login-success.window="window.location.reload()">
    @if ($show)
        <div class="modal d-block claim-modal-backdrop" wire:click="close" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" wire:click.stop>
                <div class="modal-content claim-modal-content rounded-5 border-0">

                    <div class="modal-header border-0 claim-modal-header">
                        <h5 class="modal-title claim-modal-title">Login</h5>

                        <button
                            type="button"
                            class="btn-close claim-modal-close"
                            wire:click="close"
                            aria-label="Close"
                        ></button>
                    </div>

                    <div class="modal-body claim-modal-body">
                        <div class="mb-3">
                            <label class="claim-label">Email</label>
                            <input
                                type="email"
                                wire:model.defer="email"
                                class="form-control claim-input"
                                placeholder="you@example.com"
                                autocomplete="email"
                            >
                            @error('email')
                                <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="claim-label">Password</label>
                            <input
                                type="password"
                                wire:model.defer="password"
                                class="form-control claim-input"
                                placeholder="••••••••"
                                autocomplete="current-password"
                            >
                            @error('password')
                                <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-check mb-2">
                            <input
                                id="rememberMe"
                                type="checkbox"
                                wire:model="remember"
                                class="form-check-input claim-check"
                            >
                            <label for="rememberMe" class="form-check-label claim-check-label">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer border-0 claim-modal-footer">
                        <button type="button" class="btn claim-cancel-button rounded-pill" wire:click="close">
                            Cancel
                        </button>

                        <button type="button" class="btn blue-button rounded-pill px-4" wire:click="login">
                            Login
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif
</div>
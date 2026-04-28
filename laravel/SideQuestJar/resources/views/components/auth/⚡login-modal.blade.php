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
        <div class="modal d-block claim-modal-backdrop" wire:click="close">
            <div class="modal-dialog modal-dialog-centered" wire:click.stop>
                <div class="modal-content claim-modal-content rounded-5 border-0">

                    <div class="modal-header border-0">
                        <h5 class="modal-title">Login</h5>
                        <button type="button" class="btn-close" wire:click="close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" wire:model.defer="email" class="form-control">
                            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" wire:model.defer="password" class="form-control">
                            @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" wire:model="remember" class="form-check-input">
                            <label class="form-check-label">Remember Me</label>
                        </div>

                    </div>

                    <div class="modal-footer border-0">
                        <button class="btn claim-cancel-button rounded-pill" wire:click="close">
                            Cancel
                        </button>
                        <button class="btn blue-button rounded-pill px-4" wire:click="login">
                            Login
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endif

</div>


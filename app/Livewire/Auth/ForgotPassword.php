<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public ?string $status = null;

    public function sendResetLink(): void
    {
        $this->validate();

        $result = Password::sendResetLink(['email' => $this->email]);

        $this->status = $result === Password::RESET_LINK_SENT
            ? 'Instruksi reset password telah dikirim ke email Anda.'
            : 'Kami tidak dapat menemukan akun dengan email tersebut.';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.guest');
    }
}

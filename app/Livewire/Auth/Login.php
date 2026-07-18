<?php

namespace App\Livewire\Auth;

use App\Models\ActivityLog;
use App\Models\ProfilKampus;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public int $captchaA = 0;

    public int $captchaB = 0;

    #[Validate('required')]
    public string $captchaAnswer = '';

    public int $failedAttempts = 0;

    public bool $isLockedOut = false;

    public ?string $errorMsg = null;

    public function mount(): void
    {
        $this->generateCaptcha();
        $this->failedAttempts = session('login_failed_attempts', 0);
        $this->isLockedOut = $this->failedAttempts >= 3;
    }

    public function generateCaptcha(): void
    {
        $this->captchaA = random_int(1, 10);
        $this->captchaB = random_int(1, 10);
        $this->captchaAnswer = '';
        session(['login_captcha_answer' => $this->captchaA + $this->captchaB]);
    }

    public function authenticate()
    {
        $this->errorMsg = null;
        $this->validate();

        if ((int) $this->captchaAnswer !== session('login_captcha_answer')) {
            $this->errorMsg = 'Kode CAPTCHA salah!';
            $this->generateCaptcha();

            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->failedAttempts++;
            session(['login_failed_attempts' => $this->failedAttempts]);
            $this->errorMsg = 'Email atau password salah.';
            $this->generateCaptcha();

            if ($this->failedAttempts >= 3) {
                $this->isLockedOut = true;
            }

            return;
        }

        session()->forget(['login_failed_attempts', 'login_captcha_answer']);
        request()->session()->regenerate();

        $user = Auth::user();
        ActivityLog::log((string) $user->id, $user->name, $user->role?->value, 'Berhasil login ke sistem');

        return redirect()->intended(route('dashboard'));
    }

    public function goToReset(): void
    {
        $this->isLockedOut = false;
        $this->errorMsg = null;
        session()->forget(['login_failed_attempts']);
        $this->failedAttempts = 0;
        $this->redirectRoute('forgot-password');
    }

    public function render()
    {
        return view('livewire.auth.login', [
            'profilKampus' => ProfilKampus::first(),
        ])->layout('layouts.guest');
    }
}

<?php

namespace App\Livewire\Public\Kontak;

use App\Models\ContactMessage;
use App\Models\ProfilKampus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.public')]
class Index extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    public string $subject = '';

    #[Validate('required|string')]
    public string $message = '';

    public bool $sent = false;

    public function send(): void
    {
        $this->validate();

        ContactMessage::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->reset(['name', 'email', 'subject', 'message']);
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.public.kontak.index', [
            'profil' => ProfilKampus::first(),
        ]);
    }
}

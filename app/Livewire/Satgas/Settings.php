<?php

namespace App\Livewire\Satgas;

use App\Models\ProfilKampus;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use WithFileUploads;

    public string $satgas_nama = '';

    public string $satgas_deskripsi = '';

    public string $satgas_email = '';

    public string $satgas_telepon = '';

    public ?string $satgas_logo_url = null;

    public $newLogo = null;

    public ?string $saveSuccess = null;

    public function mount(): void
    {
        $profil = ProfilKampus::first();

        $this->satgas_nama = $profil?->satgas_nama ?? 'Satgas PPKS';
        $this->satgas_deskripsi = $profil?->satgas_deskripsi ?? '';
        $this->satgas_email = $profil?->satgas_email ?? '';
        $this->satgas_telepon = $profil?->satgas_telepon ?? '';
        $this->satgas_logo_url = $profil?->satgas_logo_url;
    }

    public function updatedNewLogo(): void
    {
        $this->validate(['newLogo' => 'image|max:2048']);
        $this->satgas_logo_url = $this->newLogo->store('satgas', 'public');
    }

    public function save(): void
    {
        $this->validate([
            'satgas_nama' => 'required|string|max:255',
            'satgas_deskripsi' => 'nullable|string',
            'satgas_email' => 'nullable|email|max:255',
            'satgas_telepon' => 'nullable|string|max:50',
        ]);

        $profil = ProfilKampus::firstOrNew();
        $profil->fill([
            'satgas_nama' => $this->satgas_nama,
            'satgas_deskripsi' => $this->satgas_deskripsi,
            'satgas_email' => $this->satgas_email,
            'satgas_telepon' => $this->satgas_telepon,
            'satgas_logo_url' => $this->satgas_logo_url,
        ])->save();

        $this->saveSuccess = 'Pengaturan Satgas PPK berhasil disimpan.';
    }

    public function render()
    {
        return view('livewire.satgas.settings');
    }
}

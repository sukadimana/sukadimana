<?php

namespace App\Livewire\CampusProfile;

use App\Models\ProfilKampus;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    #[Validate('required|string|max:255')]
    public string $nama_kampus = '';

    #[Validate('required|string')]
    public string $alamat_kampus = '';

    #[Validate('required|string|max:50')]
    public string $telepon = '';

    #[Validate('required|email')]
    public string $email = '';

    public string $website = '';

    public string $petugas_pembayaran = '';

    public string $wa_api_url = '';

    public string $wa_api_key = '';

    public ?string $logo_url = null;

    public $newLogo = null;

    public ?string $saveSuccess = null;

    public function mount(): void
    {
        $profile = ProfilKampus::first();
        if ($profile) {
            $this->nama_kampus = $profile->nama_kampus;
            $this->alamat_kampus = $profile->alamat_kampus ?? '';
            $this->telepon = $profile->telepon ?? '';
            $this->email = $profile->email ?? '';
            $this->website = $profile->website ?? '';
            $this->petugas_pembayaran = $profile->petugas_pembayaran ?? '';
            $this->wa_api_url = $profile->wa_api_url ?? '';
            $this->wa_api_key = $profile->wa_api_key ?? '';
            $this->logo_url = $profile->logo_url;
        }
    }

    public function updatedNewLogo(): void
    {
        $this->validate(['newLogo' => 'image|max:2048']);
        $this->logo_url = 'data:'.$this->newLogo->getMimeType().';base64,'.base64_encode(file_get_contents($this->newLogo->getRealPath()));
    }

    public function save(): void
    {
        $this->validate();

        ProfilKampus::updateOrCreate([], [
            'nama_kampus' => $this->nama_kampus,
            'alamat_kampus' => $this->alamat_kampus,
            'telepon' => $this->telepon,
            'email' => $this->email,
            'website' => $this->website,
            'petugas_pembayaran' => $this->petugas_pembayaran,
            'logo_url' => $this->logo_url,
            'wa_api_url' => $this->wa_api_url,
            'wa_api_key' => $this->wa_api_key,
        ]);

        $this->newLogo = null;
        $this->saveSuccess = 'Profil kampus berhasil disimpan.';
    }

    public function render()
    {
        return view('livewire.campus-profile.index');
    }
}

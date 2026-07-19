<?php

namespace App\Livewire\Satgas\Public\Pengaduan;

use App\Models\PpksReport;
use App\Models\ProfilKampus;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.satgas')]
class Index extends Component
{
    public bool $is_anonymous = false;

    public string $reporter_name = '';

    public string $reporter_contact = '';

    #[Validate('required|string|min:20')]
    public string $description = '';

    public ?string $referenceCode = null;

    public function submit(): void
    {
        $this->validate();

        if (! $this->is_anonymous) {
            $this->validate([
                'reporter_name' => 'required|string|max:255',
                'reporter_contact' => 'required|string|max:255',
            ]);
        }

        $report = PpksReport::create([
            'reference_code' => PpksReport::generateReferenceCode(),
            'reporter_name' => $this->is_anonymous ? null : $this->reporter_name,
            'reporter_contact' => $this->is_anonymous ? null : $this->reporter_contact,
            'is_anonymous' => $this->is_anonymous,
            'description' => $this->description,
            'status' => 'BARU',
        ]);

        $this->referenceCode = $report->reference_code;
        $this->reset(['reporter_name', 'reporter_contact', 'description', 'is_anonymous']);
    }

    public function render()
    {
        return view('livewire.satgas.public.pengaduan.index', [
            'profil' => ProfilKampus::first(),
        ]);
    }
}

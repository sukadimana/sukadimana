<?php

namespace App\Livewire\Prodi;

use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public bool $isModalOpen = false;

    #[Validate('required|string|max:20')]
    public string $kode_prodi = '';

    #[Validate('required|string|max:255')]
    public string $nama_prodi = '';

    #[Validate('required|in:S1,S2')]
    public string $jenjang = 'S1';

    public function openModal(): void
    {
        $this->reset(['kode_prodi', 'nama_prodi', 'jenjang']);
        $this->jenjang = 'S1';
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'kode_prodi' => 'required|string|max:20|unique:prodis,kode_prodi',
            'nama_prodi' => 'required|string|max:255',
            'jenjang' => 'required|in:S1,S2',
        ]);

        Prodi::create([
            'kode_prodi' => strtoupper($this->kode_prodi),
            'nama_prodi' => $this->nama_prodi,
            'jenjang' => $this->jenjang,
        ]);

        $this->isModalOpen = false;
    }

    public function render()
    {
        $prodis = Prodi::query()
            ->when($this->search, fn ($q) => $q->where('nama_prodi', 'like', "%{$this->search}%")
                ->orWhere('kode_prodi', 'like', "%{$this->search}%"))
            ->orderBy('nama_prodi')
            ->get();

        return view('livewire.prodi.index', [
            'prodis' => $prodis,
            'isAdmin' => Auth::user()->hasRole('admin'),
        ]);
    }
}

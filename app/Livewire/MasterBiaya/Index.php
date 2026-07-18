<?php

namespace App\Livewire\MasterBiaya;

use App\Models\MasterBiaya;
use App\Models\Prodi;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public bool $isModalOpen = false;

    public bool $isEditing = false;

    public ?int $currentEditId = null;

    public ?int $itemToDeleteId = null;

    public string $itemToDeleteName = '';

    #[Validate('required|string|max:255')]
    public string $nama_biaya = '';

    #[Validate('required|string|max:20')]
    public string $angkatan = '';

    public string $prodi_id = '';

    #[Validate('required|numeric|min:0')]
    public $nominal_baku = 0;

    public bool $bisa_dicicil = true;

    public int $semester = 1;

    #[Validate('required|in:S1,S2,SEMUA')]
    public string $jenjang = 'SEMUA';

    public function mount(): void
    {
        $this->angkatan = (string) now()->year;
    }

    protected function resetForm(): void
    {
        $this->reset(['nama_biaya', 'prodi_id', 'nominal_baku', 'bisa_dicicil', 'semester', 'jenjang', 'isEditing', 'currentEditId']);
        $this->angkatan = (string) now()->year;
        $this->bisa_dicicil = true;
        $this->semester = 1;
        $this->jenjang = 'SEMUA';
        $this->resetErrorBag();
    }

    public function openAddModal(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $biaya = MasterBiaya::findOrFail($id);
        $this->nama_biaya = $biaya->nama_biaya;
        $this->angkatan = $biaya->angkatan;
        $this->prodi_id = $biaya->prodi_id ? (string) $biaya->prodi_id : '';
        $this->nominal_baku = (float) $biaya->nominal_baku;
        $this->bisa_dicicil = $biaya->bisa_dicicil;
        $this->semester = $biaya->semester ?? 1;
        $this->jenjang = $biaya->jenjang;
        $this->currentEditId = $biaya->id;
        $this->isEditing = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'nama_biaya' => $this->nama_biaya,
            'angkatan' => $this->angkatan,
            'prodi_id' => $this->prodi_id !== '' ? $this->prodi_id : null,
            'nominal_baku' => $this->nominal_baku,
            'bisa_dicicil' => $this->bisa_dicicil,
            'semester' => $this->semester,
            'jenjang' => $this->jenjang,
        ];

        if ($this->isEditing && $this->currentEditId) {
            MasterBiaya::findOrFail($this->currentEditId)->update($payload);
        } else {
            MasterBiaya::create($payload);
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->itemToDeleteId = $id;
        $this->itemToDeleteName = $name;
    }

    public function delete(): void
    {
        if ($this->itemToDeleteId) {
            MasterBiaya::destroy($this->itemToDeleteId);
        }
        $this->itemToDeleteId = null;
    }

    public function render()
    {
        $biayas = MasterBiaya::with('prodi')
            ->when($this->search, fn ($q) => $q->where('nama_biaya', 'like', "%{$this->search}%")
                ->orWhere('angkatan', 'like', "%{$this->search}%"))
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.master-biaya.index', [
            'biayas' => $biayas,
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
            'isAdmin' => Auth::user()->hasRole('admin'),
        ]);
    }
}

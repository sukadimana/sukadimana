<?php

namespace App\Livewire\MataKuliah;

use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public bool $isModalOpen = false;

    public ?int $currentEditId = null;

    #[Validate('required|string|max:20')]
    public string $kode_mk = '';

    #[Validate('required|string|max:255')]
    public string $nama_mk = '';

    #[Validate('required|integer|min:1|max:6')]
    public int $sks = 2;

    #[Validate('required|exists:prodis,id')]
    public string $prodi_id = '';

    #[Validate('required|integer|min:1|max:14')]
    public int $semester = 1;

    public function openCreate(): void
    {
        $this->reset(['kode_mk', 'nama_mk', 'prodi_id', 'currentEditId']);
        $this->sks = 2;
        $this->semester = 1;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $mk = MataKuliah::findOrFail($id);
        $this->currentEditId = $mk->id;
        $this->kode_mk = $mk->kode_mk;
        $this->nama_mk = $mk->nama_mk;
        $this->sks = $mk->sks;
        $this->prodi_id = (string) $mk->prodi_id;
        $this->semester = $mk->semester;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'kode_mk' => ['required', 'string', 'max:20', Rule::unique('mata_kuliahs', 'kode_mk')->ignore($this->currentEditId)],
            'nama_mk' => 'required|string|max:255',
            'sks' => 'required|integer|min:1|max:6',
            'prodi_id' => 'required|exists:prodis,id',
            'semester' => 'required|integer|min:1|max:14',
        ]);

        $payload = [
            'kode_mk' => strtoupper($this->kode_mk),
            'nama_mk' => $this->nama_mk,
            'sks' => $this->sks,
            'prodi_id' => $this->prodi_id,
            'semester' => $this->semester,
        ];

        if ($this->currentEditId) {
            MataKuliah::findOrFail($this->currentEditId)->update($payload);
        } else {
            MataKuliah::create($payload);
        }

        $this->isModalOpen = false;
    }

    public function delete(int $id): void
    {
        MataKuliah::destroy($id);
    }

    public function render()
    {
        $mataKuliahs = MataKuliah::with('prodi')
            ->when($this->search, fn ($q) => $q->where('nama_mk', 'like', "%{$this->search}%")
                ->orWhere('kode_mk', 'like', "%{$this->search}%"))
            ->orderBy('prodi_id')->orderBy('semester')->orderBy('nama_mk')
            ->get();

        return view('livewire.mata-kuliah.index', [
            'mataKuliahs' => $mataKuliahs,
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
        ]);
    }
}

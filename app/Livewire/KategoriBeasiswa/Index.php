<?php

namespace App\Livewire\KategoriBeasiswa;

use App\Models\KategoriBeasiswa;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public bool $isFormOpen = false;

    public ?int $currentEditId = null;

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|in:FULL,CUSTOM,NONE')]
    public string $tipe_potongan = 'NONE';

    public ?int $deleteConfirmId = null;

    public function openCreate(): void
    {
        $this->reset(['nama', 'tipe_potongan', 'currentEditId']);
        $this->tipe_potongan = 'NONE';
        $this->resetErrorBag();
        $this->isFormOpen = true;
    }

    public function edit(int $id): void
    {
        $k = KategoriBeasiswa::findOrFail($id);
        $this->currentEditId = $k->id;
        $this->nama = $k->nama;
        $this->tipe_potongan = $k->tipe_potongan;
        $this->resetErrorBag();
        $this->isFormOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        $payload = ['nama' => $this->nama, 'tipe_potongan' => $this->tipe_potongan];

        if ($this->currentEditId) {
            KategoriBeasiswa::findOrFail($this->currentEditId)->update($payload);
        } else {
            KategoriBeasiswa::create($payload);
        }

        $this->isFormOpen = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteConfirmId = $id;
    }

    public function delete(): void
    {
        if ($this->deleteConfirmId) {
            KategoriBeasiswa::destroy($this->deleteConfirmId);
        }
        $this->deleteConfirmId = null;
    }

    public function render()
    {
        return view('livewire.kategori-beasiswa.index', [
            'items' => KategoriBeasiswa::orderBy('nama')->get(),
        ]);
    }
}

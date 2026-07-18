<?php

namespace App\Livewire\TahunAkademik;

use App\Models\TahunAkademik;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    public bool $isModalOpen = false;

    public bool $isEditing = false;

    public ?int $currentId = null;

    #[Validate('required|string|max:255')]
    public string $nama = '';

    #[Validate('required|in:GANJIL,GENAP')]
    public string $semester_tipe = 'GANJIL';

    public bool $is_active = false;

    public function openAddModal(): void
    {
        $this->isEditing = false;
        $this->currentId = null;
        $this->reset(['nama', 'semester_tipe', 'is_active']);
        $this->semester_tipe = 'GANJIL';
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        $ta = TahunAkademik::findOrFail($id);
        $this->isEditing = true;
        $this->currentId = $ta->id;
        $this->nama = $ta->nama;
        $this->semester_tipe = $ta->semester_tipe;
        $this->is_active = $ta->is_active;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->is_active) {
            TahunAkademik::where('is_active', true)
                ->when($this->currentId, fn ($q) => $q->where('id', '!=', $this->currentId))
                ->update(['is_active' => false]);
        }

        if ($this->isEditing && $this->currentId) {
            TahunAkademik::findOrFail($this->currentId)->update([
                'nama' => $this->nama,
                'semester_tipe' => $this->semester_tipe,
                'is_active' => $this->is_active,
            ]);
        } else {
            TahunAkademik::create([
                'nama' => $this->nama,
                'semester_tipe' => $this->semester_tipe,
                'is_active' => $this->is_active,
            ]);
        }

        $this->isModalOpen = false;
    }

    public function setActive(int $id): void
    {
        TahunAkademik::where('is_active', true)->update(['is_active' => false]);
        TahunAkademik::findOrFail($id)->update(['is_active' => true]);
    }

    public function delete(int $id): void
    {
        $ta = TahunAkademik::findOrFail($id);

        if ($ta->is_active) {
            return;
        }

        $ta->delete();
    }

    public function render()
    {
        return view('livewire.tahun-akademik.index', [
            'tahunAkademiks' => TahunAkademik::orderByDesc('created_at')->get(),
            'canManage' => Auth::user()->hasRole('admin', 'akademik'),
        ]);
    }
}

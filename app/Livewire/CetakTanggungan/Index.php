<?php

namespace App\Livewire\CetakTanggungan;

use App\Models\Mahasiswa;
use App\Models\ProfilKampus;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public ?int $selectedStudentId = null;

    public bool $showCetakModal = false;

    public function selectStudent(int $id): void
    {
        $this->selectedStudentId = $id;
        $this->search = '';
    }

    public function clearSelection(): void
    {
        $this->selectedStudentId = null;
        $this->showCetakModal = false;
    }

    public function render()
    {
        $results = strlen($this->search) > 2
            ? Mahasiswa::with('prodi')
                ->where(fn ($q) => $q->where('nama_lengkap', 'like', "%{$this->search}%")
                    ->orWhere('nim', 'like', "%{$this->search}%"))
                ->limit(20)
                ->get()
            : collect();

        $selectedStudent = $this->selectedStudentId ? Mahasiswa::with('prodi')->find($this->selectedStudentId) : null;
        $studentTagihans = $selectedStudent ? Tagihan::where('mahasiswa_id', $selectedStudent->id)->get() : collect();
        $totalSisa = $studentTagihans->sum('sisa_tagihan');
        $isLunas = $studentTagihans->isNotEmpty() && $totalSisa <= 0;

        return view('livewire.cetak-tanggungan.index', [
            'results' => $results,
            'selectedStudent' => $selectedStudent,
            'studentTagihans' => $studentTagihans,
            'totalSisa' => $totalSisa,
            'isLunas' => $isLunas,
            'profilKampus' => ProfilKampus::first(),
            'petugas' => Auth::user()->name,
        ]);
    }
}

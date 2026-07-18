<?php

namespace App\Livewire\Khs;

use App\Models\Mahasiswa;
use App\Models\Nilai;
use App\Models\ProfilKampus;
use App\Models\TahunAkademik;
use Livewire\Component;

class Cetak extends Component
{
    public string $searchMahasiswa = '';

    public ?int $selectedMahasiswaId = null;

    public ?int $selectedTahunAkademikId = null;

    public function mount(): void
    {
        $this->selectedTahunAkademikId = TahunAkademik::where('is_active', true)->value('id');
    }

    public function selectMahasiswa(int $id): void
    {
        $this->selectedMahasiswaId = $id;
        $this->searchMahasiswa = '';
    }

    public function clearSelection(): void
    {
        $this->selectedMahasiswaId = null;
    }

    protected function hitungIp($nilais): array
    {
        $totalSks = $nilais->sum(fn ($n) => $n->mataKuliah->sks);
        $totalMutu = $nilais->sum(fn ($n) => $n->mataKuliah->sks * $n->bobot);

        return [
            'sks' => $totalSks,
            'ip' => $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0,
        ];
    }

    public function render()
    {
        $mahasiswaResults = $this->searchMahasiswa !== ''
            ? Mahasiswa::with('prodi')
                ->where(fn ($q) => $q->where('nama_lengkap', 'like', "%{$this->searchMahasiswa}%")
                    ->orWhere('nim', 'like', "%{$this->searchMahasiswa}%"))
                ->limit(10)
                ->get()
            : collect();

        $selectedMahasiswa = $this->selectedMahasiswaId ? Mahasiswa::with('prodi')->find($this->selectedMahasiswaId) : null;

        $nilaiSemester = collect();
        $ips = ['sks' => 0, 'ip' => 0];
        $ipk = ['sks' => 0, 'ip' => 0];

        if ($selectedMahasiswa && $this->selectedTahunAkademikId) {
            $nilaiSemester = Nilai::with('mataKuliah')
                ->where('mahasiswa_id', $selectedMahasiswa->id)
                ->where('tahun_akademik_id', $this->selectedTahunAkademikId)
                ->get()
                ->sortBy(fn ($n) => $n->mataKuliah->nama_mk);

            $ips = $this->hitungIp($nilaiSemester);

            $nilaiKumulatif = Nilai::with('mataKuliah')
                ->where('mahasiswa_id', $selectedMahasiswa->id)
                ->get();

            $ipk = $this->hitungIp($nilaiKumulatif);
        }

        return view('livewire.khs.cetak', [
            'mahasiswaResults' => $mahasiswaResults,
            'selectedMahasiswa' => $selectedMahasiswa,
            'nilaiSemester' => $nilaiSemester,
            'ips' => $ips,
            'ipk' => $ipk,
            'tahunAkademiks' => TahunAkademik::orderByDesc('created_at')->get(),
            'profilKampus' => ProfilKampus::first(),
        ])->layout('layouts.app');
    }
}

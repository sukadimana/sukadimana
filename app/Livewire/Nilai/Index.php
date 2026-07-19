<?php

namespace App\Livewire\Nilai;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Nilai;
use App\Models\TahunAkademik;
use Livewire\Component;

class Index extends Component
{
    public string $searchMahasiswa = '';

    public ?int $selectedMahasiswaId = null;

    public ?int $selectedTahunAkademikId = null;

    public ?int $filterSemester = null;

    public array $grades = [];

    public ?string $saveMessage = null;

    public function mount(): void
    {
        $this->selectedTahunAkademikId = TahunAkademik::where('is_active', true)->value('id');
    }

    public function selectMahasiswa(int $id): void
    {
        $this->selectedMahasiswaId = $id;
        $this->searchMahasiswa = '';
        $this->saveMessage = null;

        $mahasiswa = Mahasiswa::find($id);
        $this->filterSemester = $mahasiswa?->semester_saat_ini ?: 1;

        $this->loadGrades();
    }

    public function updatedSelectedTahunAkademikId(): void
    {
        $this->loadGrades();
    }

    public function updatedFilterSemester(): void
    {
        $this->loadGrades();
    }

    protected function loadGrades(): void
    {
        $this->grades = [];

        if (! $this->selectedMahasiswaId || ! $this->selectedTahunAkademikId) {
            return;
        }

        $mahasiswa = Mahasiswa::find($this->selectedMahasiswaId);
        if (! $mahasiswa) {
            return;
        }

        $mataKuliahs = MataKuliah::where('prodi_id', $mahasiswa->prodi_id)
            ->when($this->filterSemester, fn ($q) => $q->where('semester', $this->filterSemester))
            ->get();

        $existing = Nilai::where('mahasiswa_id', $this->selectedMahasiswaId)
            ->where('tahun_akademik_id', $this->selectedTahunAkademikId)
            ->pluck('nilai_huruf', 'mata_kuliah_id');

        foreach ($mataKuliahs as $mk) {
            $this->grades[$mk->id] = $existing->get($mk->id, '');
        }
    }

    public function clearSelection(): void
    {
        $this->selectedMahasiswaId = null;
        $this->grades = [];
        $this->saveMessage = null;
    }

    public function saveGrades(): void
    {
        if (! $this->selectedMahasiswaId || ! $this->selectedTahunAkademikId) {
            return;
        }

        foreach ($this->grades as $mataKuliahId => $huruf) {
            if ($huruf === '' || $huruf === null) {
                Nilai::where([
                    'mahasiswa_id' => $this->selectedMahasiswaId,
                    'mata_kuliah_id' => $mataKuliahId,
                    'tahun_akademik_id' => $this->selectedTahunAkademikId,
                ])->delete();

                continue;
            }

            Nilai::updateOrCreate(
                [
                    'mahasiswa_id' => $this->selectedMahasiswaId,
                    'mata_kuliah_id' => $mataKuliahId,
                    'tahun_akademik_id' => $this->selectedTahunAkademikId,
                ],
                [
                    'nilai_huruf' => $huruf,
                    'bobot' => Nilai::BOBOT[$huruf] ?? 0,
                ]
            );
        }

        $this->saveMessage = 'Nilai berhasil disimpan.';
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

        $mataKuliahs = collect();
        if ($selectedMahasiswa) {
            $mataKuliahs = MataKuliah::where('prodi_id', $selectedMahasiswa->prodi_id)
                ->when($this->filterSemester, fn ($q) => $q->where('semester', $this->filterSemester))
                ->orderBy('nama_mk')
                ->get();
        }

        return view('livewire.nilai.index', [
            'mahasiswaResults' => $mahasiswaResults,
            'selectedMahasiswa' => $selectedMahasiswa,
            'mataKuliahs' => $mataKuliahs,
            'tahunAkademiks' => TahunAkademik::orderByDesc('created_at')->get(),
            'nilaiOptions' => array_keys(Nilai::BOBOT),
        ]);
    }
}

<?php

namespace App\Livewire\Mahasiswa;

use App\Enums\StatusAkademik;
use App\Models\KategoriBeasiswa;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterProdi = 'all';

    public string $filterJenjang = 'all';

    public string $filterStatusAkademik = 'all';

    public bool $isModalOpen = false;

    public bool $isEditing = false;

    public ?int $currentStudentId = null;

    public ?int $studentToDeleteId = null;

    public string $studentToDeleteName = '';

    public bool $isPromoteModalOpen = false;

    public array $selectedForPromotion = [];

    #[Validate('required|string|max:30')]
    public string $nim = '';

    #[Validate('required|string|max:255')]
    public string $nama_lengkap = '';

    #[Validate('required')]
    public string $prodi_id = '';

    #[Validate('required|string|max:20')]
    public string $angkatan = '';

    #[Validate('required|string|max:10')]
    public string $tahun_masuk = '';

    public string $status_akademik = 'AKTIF';

    public string $kategori_beasiswa = 'NONE';

    public $beasiswa_potongan = 0;

    public string $jenjang = 'S1';

    public int $semester_saat_ini = 1;

    public string $jenis_kelamin = 'L';

    public function mount(): void
    {
        $this->angkatan = (string) now()->year;
        $this->tahun_masuk = (string) now()->year;
    }

    protected function resetForm(): void
    {
        $this->reset([
            'nim', 'nama_lengkap', 'prodi_id', 'status_akademik', 'kategori_beasiswa',
            'beasiswa_potongan', 'jenjang', 'semester_saat_ini', 'jenis_kelamin',
            'isEditing', 'currentStudentId',
        ]);
        $this->angkatan = (string) now()->year;
        $this->tahun_masuk = (string) now()->year;
        $this->status_akademik = 'AKTIF';
        $this->kategori_beasiswa = 'NONE';
        $this->jenjang = 'S1';
        $this->semester_saat_ini = 1;
        $this->jenis_kelamin = 'L';
        $this->resetErrorBag();
    }

    public function openAddModal(): void
    {
        $this->resetForm();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $student = Mahasiswa::findOrFail($id);
        $this->nim = $student->nim;
        $this->nama_lengkap = $student->nama_lengkap;
        $this->prodi_id = (string) $student->prodi_id;
        $this->angkatan = $student->angkatan;
        $this->tahun_masuk = $student->tahun_masuk ?? (string) now()->year;
        $this->status_akademik = $student->status_akademik;
        $this->kategori_beasiswa = $student->kategori_beasiswa ?? 'NONE';
        $this->beasiswa_potongan = (float) $student->beasiswa_potongan;
        $this->jenjang = $student->jenjang ?? 'S1';
        $this->semester_saat_ini = $student->semester_saat_ini ?? 1;
        $this->jenis_kelamin = $student->jenis_kelamin ?? 'L';
        $this->currentStudentId = $student->id;
        $this->isEditing = true;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'nim' => [
                'required', 'string', 'regex:/^\d+$/', 'min:5',
                Rule::unique('mahasiswas', 'nim')->ignore($this->currentStudentId),
            ],
            'nama_lengkap' => 'required|string|max:255',
            'prodi_id' => 'required|exists:prodis,id',
            'angkatan' => 'required|string|max:20',
            'tahun_masuk' => 'required|string|max:10',
        ], [
            'nim.regex' => 'NIM hanya boleh berisi angka',
            'nim.min' => 'NIM harus minimal 5 karakter',
            'nim.unique' => 'NIM sudah terdaftar',
            'prodi_id.required' => 'Program Studi wajib dipilih',
        ]);

        $activeTahunAkademik = TahunAkademik::where('is_active', true)->first();

        $payload = [
            'prodi_id' => $this->prodi_id,
            'nim' => $this->nim,
            'nama_lengkap' => $this->nama_lengkap,
            'angkatan' => $this->angkatan,
            'tahun_masuk' => $this->tahun_masuk,
            'status_akademik' => $this->status_akademik,
            'kategori_beasiswa' => $this->kategori_beasiswa ?: 'NONE',
            'beasiswa_potongan' => $this->beasiswa_potongan ?: 0,
            'jenjang' => $this->jenjang,
            'semester_saat_ini' => $this->semester_saat_ini,
            'jenis_kelamin' => $this->jenis_kelamin,
        ];

        if ($this->isEditing && $this->currentStudentId) {
            Mahasiswa::findOrFail($this->currentStudentId)->update($payload);
        } else {
            $payload['user_id'] = 'USR-'.random_int(1000, 10999);
            $payload['tahun_akademik_id_saat_ini'] = $activeTahunAkademik?->id;
            Mahasiswa::create($payload);
        }

        $this->isModalOpen = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->studentToDeleteId = $id;
        $this->studentToDeleteName = $name;
    }

    public function delete(): void
    {
        if ($this->studentToDeleteId) {
            Mahasiswa::destroy($this->studentToDeleteId);
        }
        $this->studentToDeleteId = null;
    }

    public function openPromoteModal(): void
    {
        $this->selectedForPromotion = Mahasiswa::where('status_akademik', StatusAkademik::AKTIF->value)->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->isPromoteModalOpen = true;
    }

    public function toggleSelectAllPromotion(): void
    {
        $activeIds = Mahasiswa::where('status_akademik', StatusAkademik::AKTIF->value)->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->selectedForPromotion = count($this->selectedForPromotion) === count($activeIds) ? [] : $activeIds;
    }

    public function promoteSemester(): void
    {
        if (empty($this->selectedForPromotion)) {
            return;
        }

        $activeTahunAkademik = TahunAkademik::where('is_active', true)->first();

        DB::transaction(function () use ($activeTahunAkademik) {
            foreach (Mahasiswa::whereIn('id', $this->selectedForPromotion)->get() as $student) {
                $currentSemester = (int) ($student->semester_saat_ini ?: 1);

                if ($currentSemester >= 8) {
                    $student->update(['status_akademik' => StatusAkademik::LULUS->value]);
                } else {
                    $student->update([
                        'semester_saat_ini' => $currentSemester + 1,
                        'tahun_akademik_id_saat_ini' => $activeTahunAkademik?->id ?? $student->tahun_akademik_id_saat_ini,
                    ]);
                }
            }
        });

        $this->isPromoteModalOpen = false;
        $this->selectedForPromotion = [];
    }

    protected function filteredQuery()
    {
        return Mahasiswa::query()
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('nama_lengkap', 'like', "%{$this->search}%")
                ->orWhere('nim', 'like', "%{$this->search}%")))
            ->when($this->filterProdi !== 'all', fn ($q) => $q->where('prodi_id', $this->filterProdi))
            ->when($this->filterJenjang !== 'all', fn ($q) => $q->where('jenjang', $this->filterJenjang))
            ->when($this->filterStatusAkademik !== 'all', fn ($q) => $q->where('status_akademik', $this->filterStatusAkademik));
    }

    public function render()
    {
        $students = $this->filteredQuery()
            ->with('prodi')
            ->orderByDesc('created_at')
            ->paginate(15);

        $totalFiltered = $this->filteredQuery()->count();
        $totalLaki = $this->filteredQuery()->where('jenis_kelamin', 'L')->count();
        $totalPerempuan = $this->filteredQuery()->where('jenis_kelamin', 'P')->count();

        $activeStudentsForPromotion = Mahasiswa::where('status_akademik', StatusAkademik::AKTIF->value)->get(['id', 'nim', 'nama_lengkap', 'semester_saat_ini']);

        return view('livewire.mahasiswa.index', [
            'students' => $students,
            'totalFiltered' => $totalFiltered,
            'totalLaki' => $totalLaki,
            'totalPerempuan' => $totalPerempuan,
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
            'kategoriBeasiswas' => KategoriBeasiswa::all(),
            'statusOptions' => array_column(StatusAkademik::cases(), 'value'),
            'activeStudentsForPromotion' => $activeStudentsForPromotion,
        ]);
    }
}

<?php

namespace App\Livewire\Billing;

use App\Enums\StatusAkademik;
use App\Enums\StatusTagihan;
use App\Models\KategoriBeasiswa;
use App\Models\Mahasiswa;
use App\Models\MasterBiaya;
use App\Models\Prodi;
use App\Models\Tagihan;
use App\Models\TahunAkademik;
use Livewire\Component;

class Index extends Component
{
    public string $activeTab = 'SINGLE';

    public string $search = '';

    public ?int $selectedStudentId = null;

    public string $jatuhTempoDate = '';

    public string $batchJenjang = 'SEMUA';

    public string $batchProdiId = 'SEMUA';

    public string $batchKategori = 'SEMUA';

    public string $batchSemester = 'SEMUA';

    public string $selectedBiayaForBatch = '';

    public ?string $successMessage = null;

    public ?string $errorMessage = null;

    public function selectStudent(int $id): void
    {
        $this->selectedStudentId = $id;
    }

    protected function generateForStudent(MasterBiaya $mb, Mahasiswa $student, ?TahunAkademik $activeTa): array
    {
        $blockedStatuses = [StatusAkademik::LULUS->value, StatusAkademik::NON_AKTIF->value, StatusAkademik::DO->value, StatusAkademik::CUTI->value];

        if (in_array($student->status_akademik, $blockedStatuses, true)) {
            return ['success' => false, 'message' => 'Mahasiswa sudah lulus atau tidak aktif'];
        }

        if ($activeTa && $student->tahun_akademik_id_saat_ini !== $activeTa->id) {
            return ['success' => false, 'message' => 'Mahasiswa belum dinaikkan/didaftarkan ke tahun akademik aktif saat ini'];
        }

        $exists = Tagihan::where('mahasiswa_id', $student->id)->where('master_biaya_id', $mb->id)->exists();
        if ($exists) {
            return ['success' => false, 'message' => 'Tagihan sudah ada'];
        }

        $potongan = 0;
        $kb = KategoriBeasiswa::find($student->kategori_beasiswa);
        if ($kb && $kb->tipe_potongan === 'FULL') {
            $potongan = $mb->nominal_baku;
        } elseif ($kb && $kb->tipe_potongan === 'CUSTOM') {
            $potongan = $student->beasiswa_potongan ?: 0;
        }

        $totalHarusBayar = max(0, $mb->nominal_baku - $potongan);

        Tagihan::create([
            'mahasiswa_id' => $student->id,
            'master_biaya_id' => $mb->id,
            'nominal_awal' => $mb->nominal_baku,
            'potongan_beasiswa' => $potongan,
            'total_harus_bayar' => $totalHarusBayar,
            'total_sudah_bayar' => 0,
            'sisa_tagihan' => $totalHarusBayar,
            'status' => $totalHarusBayar == 0 ? StatusTagihan::LUNAS->value : StatusTagihan::BELUM_LUNAS->value,
            'nama_tagihan_snapshot' => $mb->nama_biaya,
            'nama_mahasiswa_snapshot' => $student->nama_lengkap,
            'semester' => $mb->semester ?: 1,
            'kategori_snapshot' => $student->kategori_beasiswa,
            'jatuh_tempo' => $this->jatuhTempoDate ?: null,
            'tahun_akademik_id_snapshot' => $activeTa?->id,
        ]);

        return ['success' => true, 'message' => 'Tagihan berhasil dibuat.'];
    }

    public function generateSingle(int $masterBiayaId): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $student = Mahasiswa::find($this->selectedStudentId);
        $mb = MasterBiaya::find($masterBiayaId);

        if (! $student || ! $mb) {
            return;
        }

        $activeTa = TahunAkademik::where('is_active', true)->first();
        $result = $this->generateForStudent($mb, $student, $activeTa);

        if ($result['success']) {
            $this->successMessage = $result['message'];
        } else {
            $this->errorMessage = $result['message'];
        }
    }

    public function batchGenerate(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        $mb = MasterBiaya::find($this->selectedBiayaForBatch);
        if (! $mb) {
            $this->errorMessage = 'Pilih komponen biaya terlebih dahulu.';

            return;
        }

        $activeTa = TahunAkademik::where('is_active', true)->first();

        $targets = Mahasiswa::query()
            ->where('status_akademik', StatusAkademik::AKTIF->value)
            ->when($this->batchJenjang !== 'SEMUA', fn ($q) => $q->where('jenjang', $this->batchJenjang))
            ->when($this->batchProdiId !== 'SEMUA', fn ($q) => $q->where('prodi_id', $this->batchProdiId))
            ->when($this->batchKategori !== 'SEMUA', fn ($q) => $q->where('kategori_beasiswa', $this->batchKategori))
            ->when($this->batchSemester !== 'SEMUA', fn ($q) => $q->where('semester_saat_ini', (int) $this->batchSemester))
            ->when($activeTa, fn ($q) => $q->where('tahun_akademik_id_saat_ini', $activeTa->id))
            ->get();

        if ($targets->isEmpty()) {
            $this->errorMessage = 'Tidak ada mahasiswa yang sesuai dengan kriteria filter.';

            return;
        }

        $generated = 0;
        $skipped = 0;
        foreach ($targets as $student) {
            $result = $this->generateForStudent($mb, $student, $activeTa);
            if ($result['success']) {
                $generated++;
            } else {
                $skipped++;
            }
        }

        $this->successMessage = "Berhasil membangkitkan {$generated} tagihan baru. ({$skipped} dilewati karena sudah ada atau tidak memenuhi syarat)";
    }

    public function render()
    {
        $students = Mahasiswa::with('prodi')
            ->when($this->search, fn ($q) => $q->where(fn ($qq) => $qq->where('nama_lengkap', 'like', "%{$this->search}%")
                ->orWhere('nim', 'like', "%{$this->search}%")))
            ->orderBy('nama_lengkap')
            ->limit(50)
            ->get();

        $selectedStudent = $this->selectedStudentId ? Mahasiswa::with('prodi')->find($this->selectedStudentId) : null;

        $availableBiayas = collect();
        $existingTagihanIds = collect();
        if ($selectedStudent) {
            $availableBiayas = MasterBiaya::query()
                ->where(fn ($q) => $q->whereNull('prodi_id')->orWhere('prodi_id', $selectedStudent->prodi_id))
                ->where(fn ($q) => $q->whereNull('jenjang')->orWhere('jenjang', 'SEMUA')->orWhere('jenjang', $selectedStudent->jenjang))
                ->get();

            $existingTagihanIds = Tagihan::where('mahasiswa_id', $selectedStudent->id)->pluck('master_biaya_id')->all();
        }

        return view('livewire.billing.index', [
            'students' => $students,
            'selectedStudent' => $selectedStudent,
            'availableBiayas' => $availableBiayas,
            'existingTagihanIds' => $existingTagihanIds,
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
            'kategoriBeasiswas' => KategoriBeasiswa::all(),
            'masterBiayas' => MasterBiaya::orderByDesc('created_at')->get(),
        ]);
    }
}

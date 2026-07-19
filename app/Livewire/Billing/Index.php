<?php

namespace App\Livewire\Billing;

use App\Enums\StatusAkademik;
use App\Enums\StatusTagihan;
use App\Models\KategoriBeasiswa;
use App\Models\Mahasiswa;
use App\Models\MasterBiaya;
use App\Models\Pembayaran;
use App\Models\Prodi;
use App\Models\Tagihan;
use App\Models\TahunAkademik;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Index extends Component
{
    use WithFileUploads;

    public $importFile = null;

    public ?string $importMessage = null;
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

    public function downloadTagihanTemplate()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tagihan Lama');

        $headers = ['nim', 'nama_biaya', 'semester', 'nominal_awal', 'potongan_beasiswa', 'total_sudah_bayar', 'tanggal_jatuh_tempo'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
        $sheet->fromArray(['12345678', 'SPP Semester 3', 3, 2000000, 0, 2000000, '2026-06-30'], null, 'A2');
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx');
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, 'template_import_tagihan_lama.xlsx')->deleteFileAfterSend(true);
    }

    public function updatedImportFile(): void
    {
        $this->validate(['importFile' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        $spreadsheet = IOFactory::load($this->importFile->getRealPath());
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        $header = array_map(fn ($h) => trim((string) $h), array_shift($rows));

        $activeTa = TahunAkademik::where('is_active', true)->first();
        $tagihanCount = 0;
        $paymentCount = 0;

        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            $nim = trim((string) ($data['nim'] ?? ''));
            if ($nim === '') {
                continue;
            }

            $student = Mahasiswa::where('nim', $nim)->first();
            if (! $student) {
                continue;
            }

            $namaBiaya = trim((string) ($data['nama_biaya'] ?? 'Tagihan Import'));
            $semester = (int) ($data['semester'] ?? 1) ?: 1;
            $nominalAwal = (float) ($data['nominal_awal'] ?? 0);
            $potongan = (float) ($data['potongan_beasiswa'] ?? 0);
            $totalHarusBayar = max(0, $nominalAwal - $potongan);
            $totalSudahBayar = (float) ($data['total_sudah_bayar'] ?? 0);
            $sisaTagihan = max(0, $totalHarusBayar - $totalSudahBayar);
            $status = $sisaTagihan == 0 ? StatusTagihan::LUNAS->value : ($totalSudahBayar > 0 ? StatusTagihan::CICILAN->value : StatusTagihan::BELUM_LUNAS->value);

            $masterBiaya = MasterBiaya::firstOrCreate(
                ['nama_biaya' => $namaBiaya, 'semester' => $semester],
                ['angkatan' => $student->angkatan, 'nominal_baku' => $nominalAwal, 'bisa_dicicil' => true, 'jenjang' => 'SEMUA']
            );

            $exists = Tagihan::where('mahasiswa_id', $student->id)
                ->where('master_biaya_id', $masterBiaya->id)
                ->exists();
            if ($exists) {
                continue;
            }

            $jatuhTempo = trim((string) ($data['tanggal_jatuh_tempo'] ?? ''));

            $tagihan = Tagihan::create([
                'mahasiswa_id' => $student->id,
                'master_biaya_id' => $masterBiaya->id,
                'nominal_awal' => $nominalAwal,
                'potongan_beasiswa' => $potongan,
                'total_harus_bayar' => $totalHarusBayar,
                'total_sudah_bayar' => $totalSudahBayar,
                'sisa_tagihan' => $sisaTagihan,
                'status' => $status,
                'nama_tagihan_snapshot' => $namaBiaya,
                'nama_mahasiswa_snapshot' => $student->nama_lengkap,
                'semester' => $semester,
                'kategori_snapshot' => $student->kategori_beasiswa,
                'jatuh_tempo' => $jatuhTempo !== '' ? $jatuhTempo : null,
                'tahun_akademik_id_snapshot' => $activeTa?->id,
            ]);
            $tagihanCount++;

            if ($totalSudahBayar > 0) {
                Pembayaran::create([
                    'tagihan_id' => $tagihan->id,
                    'nomor_transaksi' => 'TRX-MIGRASI-'.$nim.'-'.now()->format('YmdHis').random_int(100, 999),
                    'tanggal_bayar' => now()->toDateString(),
                    'jumlah_bayar' => $totalSudahBayar,
                    'metode_bayar' => 'TUNAI',
                ]);
                $paymentCount++;
            }
        }

        $this->importFile = null;
        $this->importMessage = "Berhasil mengimpor {$tagihanCount} tagihan dengan {$paymentCount} data pembayaran terkait.";
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

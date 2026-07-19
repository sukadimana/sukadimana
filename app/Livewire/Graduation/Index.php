<?php

namespace App\Livewire\Graduation;

use App\Enums\ClearanceStatus;
use App\Enums\StatusAkademik;
use App\Enums\UserRole;
use App\Models\AlumniTracking;
use App\Models\Mahasiswa;
use App\Models\ProfilKampus;
use App\Models\Tagihan;
use App\Models\Yudisium;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Index extends Component
{
    public string $activeTab = 'yudisium';

    // Validation modal
    public ?int $validationYudisiumId = null;

    public string $validationField = '';

    public string $validationStatus = '';

    public string $validationNote = '';

    // Settings modal
    public bool $isSettingsModalOpen = false;

    public bool $settingsIsOpen = true;

    public string $settingsStartDate = '';

    public string $settingsEndDate = '';

    // Print
    public ?int $printYudisiumId = null;

    public ?int $printBebasMahasiswaId = null;

    // Alumni modal
    public bool $isAlumniModalOpen = false;

    public ?int $alumniMahasiswaId = null;

    public string $tahun_lulus = '';

    public string $nomor_hp_terbaru = '';

    public string $alamat_domisili = '';

    public string $status_pekerjaan = 'BELUM_KERJA';

    public string $kategori_pekerjaan = '';

    public string $nama_instansi = '';

    public string $jurusan_lanjutan = '';

    public string $prodi_lanjutan = '';

    public string $jabatan = '';

    public $gaji_pertama = 0;

    public function registerYudisium(int $mahasiswaId): void
    {
        if (Yudisium::where('mahasiswa_id', $mahasiswaId)->exists()) {
            return;
        }

        $totalSisa = Tagihan::where('mahasiswa_id', $mahasiswaId)->sum('sisa_tagihan');

        Yudisium::create([
            'mahasiswa_id' => $mahasiswaId,
            'bebas_pustaka' => ClearanceStatus::PENDING->value,
            'bebas_keuangan' => $totalSisa <= 0 ? ClearanceStatus::APPROVED->value : ClearanceStatus::PENDING->value,
            'bebas_akademik' => ClearanceStatus::PENDING->value,
            'status_akhir' => 'BELUM',
        ]);
    }

    protected function syncFinalStatus(Yudisium $yudisium): void
    {
        $allApproved = $yudisium->bebas_pustaka === ClearanceStatus::APPROVED->value
            && $yudisium->bebas_keuangan === ClearanceStatus::APPROVED->value
            && $yudisium->bebas_akademik === ClearanceStatus::APPROVED->value;

        $yudisium->update(['status_akhir' => $allApproved ? 'SIAP_WISUDA' : 'BELUM']);
    }

    public function setValidation(int $yudisiumId, string $field, string $status): void
    {
        if ($status === ClearanceStatus::PENDING->value) {
            $yudisium = Yudisium::findOrFail($yudisiumId);
            $noteField = str_replace('bebas_', 'catatan_', $field);
            $yudisium->update([$field => $status, $noteField => '']);
            $this->syncFinalStatus($yudisium);

            return;
        }

        $this->validationYudisiumId = $yudisiumId;
        $this->validationField = $field;
        $this->validationStatus = $status;
        $this->validationNote = '';
    }

    public function confirmValidation(): void
    {
        if (! $this->validationYudisiumId) {
            return;
        }

        $yudisium = Yudisium::findOrFail($this->validationYudisiumId);
        $noteField = str_replace('bebas_', 'catatan_', $this->validationField);
        $yudisium->update([
            $this->validationField => $this->validationStatus,
            $noteField => $this->validationNote,
        ]);
        $this->syncFinalStatus($yudisium);

        $this->validationYudisiumId = null;
        $this->validationNote = '';
    }

    public function openSettingsModal(): void
    {
        $profile = ProfilKampus::first();
        $this->settingsIsOpen = $profile->yudisium_is_open ?? true;
        $this->settingsStartDate = optional($profile?->yudisium_start_date)->toDateString() ?? '';
        $this->settingsEndDate = optional($profile?->yudisium_end_date)->toDateString() ?? '';
        $this->isSettingsModalOpen = true;
    }

    public function saveSettings(): void
    {
        ProfilKampus::updateOrCreate([], [
            'yudisium_is_open' => $this->settingsIsOpen,
            'yudisium_start_date' => $this->settingsStartDate ?: null,
            'yudisium_end_date' => $this->settingsEndDate ?: null,
        ]);

        $this->isSettingsModalOpen = false;
    }

    public function openAlumniModal(int $mahasiswaId): void
    {
        $this->alumniMahasiswaId = $mahasiswaId;
        $existing = AlumniTracking::where('mahasiswa_id', $mahasiswaId)->first();

        $this->tahun_lulus = $existing->tahun_lulus ?? (string) now()->year;
        $this->nomor_hp_terbaru = $existing->nomor_hp_terbaru ?? '';
        $this->alamat_domisili = $existing->alamat_domisili ?? '';
        $this->status_pekerjaan = $existing->status_pekerjaan ?? 'BELUM_KERJA';
        $this->kategori_pekerjaan = $existing->kategori_pekerjaan ?? '';
        $this->nama_instansi = $existing->nama_instansi ?? '';
        $this->jurusan_lanjutan = $existing->jurusan_lanjutan ?? '';
        $this->prodi_lanjutan = $existing->prodi_lanjutan ?? '';
        $this->jabatan = $existing->jabatan ?? '';
        $this->gaji_pertama = $existing->gaji_pertama ?? 0;

        $this->isAlumniModalOpen = true;
    }

    public function saveAlumni(): void
    {
        $this->validate([
            'tahun_lulus' => 'required|string|max:10',
            'nomor_hp_terbaru' => 'nullable|string|max:20',
            'status_pekerjaan' => 'required|in:KERJA,WIRAUSAHA,LANJUT_STUDI,BELUM_KERJA',
        ]);

        AlumniTracking::updateOrCreate(
            ['mahasiswa_id' => $this->alumniMahasiswaId],
            [
                'tahun_lulus' => $this->tahun_lulus,
                'nomor_hp_terbaru' => $this->nomor_hp_terbaru,
                'alamat_domisili' => $this->alamat_domisili,
                'status_pekerjaan' => $this->status_pekerjaan,
                'kategori_pekerjaan' => $this->kategori_pekerjaan,
                'nama_instansi' => $this->nama_instansi,
                'jurusan_lanjutan' => $this->jurusan_lanjutan,
                'prodi_lanjutan' => $this->prodi_lanjutan,
                'jabatan' => $this->jabatan,
                'gaji_pertama' => $this->gaji_pertama ?: 0,
                'tanggal_isi' => now()->toDateString(),
            ]
        );

        $this->isAlumniModalOpen = false;
    }

    protected function waMessage(Mahasiswa $student, AlumniTracking $alumni): string
    {
        $statusDisplay = match ($alumni->status_pekerjaan) {
            'KERJA' => 'Bekerja',
            'LANJUT_STUDI' => 'Lanjut Studi',
            'WIRAUSAHA' => 'Wirausaha',
            default => 'Belum Bekerja',
        };

        return "Assalamu'alaikum Wr. Wb\n\nHalo {$student->nama_lengkap},\n\nTerima kasih telah berpartisipasi dalam pengisian Tracer Study!\n\nBerikut adalah ringkasan data yang Anda isikan:\nNIM: {$student->nim}\nTahun Lulus: {$alumni->tahun_lulus}\nStatus: {$statusDisplay}\n\nSekali lagi, terima kasih atas kontribusi Anda. Semoga sukses selalu!\n\nWassalamu'alaikum Wr. Wb";
    }

    public function sendWhatsAppApi(int $mahasiswaId): void
    {
        $student = Mahasiswa::findOrFail($mahasiswaId);
        $alumni = AlumniTracking::where('mahasiswa_id', $mahasiswaId)->first();
        $profile = ProfilKampus::first();

        if (! $alumni?->nomor_hp_terbaru || ! $profile?->wa_api_url) {
            return;
        }

        $hp = preg_replace('/\D/', '', $alumni->nomor_hp_terbaru);
        if (str_starts_with($hp, '0')) {
            $hp = '62'.substr($hp, 1);
        }

        Http::withHeaders(['Authorization' => $profile->wa_api_key ?? ''])
            ->post($profile->wa_api_url, ['phone' => $hp, 'message' => $this->waMessage($student, $alumni)]);
    }

    public function exportTracerStudy()
    {
        $graduates = Mahasiswa::with('prodi')->where('status_akademik', StatusAkademik::LULUS->value)->get();
        $alumniByMahasiswa = AlumniTracking::whereIn('mahasiswa_id', $graduates->pluck('id'))->get()->keyBy('mahasiswa_id');

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Tracer Study');

        $headers = ['NIM', 'Nama Lengkap', 'Angkatan', 'Tahun Masuk', 'Program Studi', 'Jenjang', 'Tahun Lulus', 'No HP', 'Status Pengisian', 'Status Pekerjaan', 'Kategori Pekerjaan', 'Nama Instansi/Kampus', 'Jurusan Lanjutan', 'Prodi Lanjutan', 'Tanggal Isi'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A1:O1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');

        $row = 2;
        foreach ($graduates as $student) {
            $alumni = $alumniByMahasiswa->get($student->id);
            $sheet->fromArray([
                $student->nim,
                $student->nama_lengkap,
                $student->angkatan,
                $student->tahun_masuk ?: explode('/', $student->angkatan)[0],
                $student->prodi->nama_prodi ?? $student->prodi_id,
                $student->jenjang ?: ($student->prodi->jenjang ?? 'S1'),
                $alumni?->tahun_lulus ?? '-',
                $alumni?->nomor_hp_terbaru ?? '-',
                $alumni ? 'Sudah Mengisi' : 'Belum Mengisi',
                $alumni?->status_pekerjaan ?? '-',
                $alumni?->kategori_pekerjaan ?? '-',
                $alumni?->nama_instansi ?? '-',
                $alumni?->jurusan_lanjutan ?? '-',
                $alumni?->prodi_lanjutan ?? '-',
                $alumni?->tanggal_isi?->translatedFormat('j F Y') ?? '-',
            ], null, "A{$row}");
            $row++;
        }

        foreach (range('A', 'O') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx');
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, 'Hasil_Tracer_Study_'.now()->format('Ymd_His').'.xlsx')->deleteFileAfterSend(true);
    }

    public function render()
    {
        $role = Auth::user()->role;

        $yudisiums = Yudisium::with('mahasiswa')->get();
        $eligibleUnregistered = $role !== UserRole::PERPUS
            ? Mahasiswa::where('semester_saat_ini', '>=', 8)
                ->whereDoesntHave('yudisium')
                ->get()
            : collect();

        $profile = ProfilKampus::first();
        $yudisiumPeriod = [
            'isOpen' => $profile->yudisium_is_open ?? true,
            'startDate' => $profile?->yudisium_start_date,
            'endDate' => $profile?->yudisium_end_date,
        ];

        $graduates = Mahasiswa::with('prodi')->where('status_akademik', StatusAkademik::LULUS->value)->get();
        $alumniByMahasiswa = AlumniTracking::whereIn('mahasiswa_id', $graduates->pluck('id'))->get()->keyBy('mahasiswa_id');

        $printYudisium = $this->printYudisiumId ? Yudisium::with('mahasiswa.prodi')->find($this->printYudisiumId) : null;
        $printBebasMahasiswa = $this->printBebasMahasiswaId ? Mahasiswa::with('prodi')->find($this->printBebasMahasiswaId) : null;
        $printBebasTagihans = $printBebasMahasiswa ? Tagihan::where('mahasiswa_id', $printBebasMahasiswa->id)->get() : collect();

        return view('livewire.graduation.index', [
            'role' => $role,
            'yudisiums' => $yudisiums,
            'eligibleUnregistered' => $eligibleUnregistered,
            'yudisiumPeriod' => $yudisiumPeriod,
            'graduates' => $graduates,
            'alumniByMahasiswa' => $alumniByMahasiswa,
            'profilKampus' => $profile,
            'printYudisium' => $printYudisium,
            'printBebasMahasiswa' => $printBebasMahasiswa,
            'printBebasTagihans' => $printBebasTagihans,
        ]);
    }
}

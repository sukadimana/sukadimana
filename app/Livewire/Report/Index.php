<?php

namespace App\Livewire\Report;

use App\Enums\StatusTagihan;
use App\Models\MasterBiaya;
use App\Models\Prodi;
use App\Models\Tagihan;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Index extends Component
{
    public string $filterType = 'ALL';

    public string $filterJenjang = 'ALL';

    public string $filterProdi = 'ALL';

    public string $filterMasterBiaya = 'ALL';

    public string $filterSemester = 'ALL';

    public function updatedFilterJenjang(): void
    {
        $this->filterProdi = 'ALL';
    }

    protected function filteredQuery()
    {
        return Tagihan::with(['mahasiswa.prodi'])
            ->when($this->filterType === 'LUNAS', fn ($q) => $q->where('status', StatusTagihan::LUNAS->value))
            ->when($this->filterType === 'TUNGGAKAN', fn ($q) => $q->where('status', '!=', StatusTagihan::LUNAS->value))
            ->when($this->filterJenjang !== 'ALL', fn ($q) => $q->whereHas('mahasiswa', fn ($qq) => $qq->where('jenjang', $this->filterJenjang)))
            ->when($this->filterProdi !== 'ALL', fn ($q) => $q->whereHas('mahasiswa', fn ($qq) => $qq->where('prodi_id', $this->filterProdi)))
            ->when($this->filterMasterBiaya !== 'ALL', fn ($q) => $q->where('master_biaya_id', $this->filterMasterBiaya))
            ->when($this->filterSemester !== 'ALL', fn ($q) => $q->where(fn ($qq) => $qq->where('semester', $this->filterSemester)
                ->orWhereHas('mahasiswa', fn ($qqq) => $qqq->where('semester_saat_ini', $this->filterSemester))));
    }

    public function exportXlsx()
    {
        $rows = $this->filteredQuery()->get();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Tagihan');

        $headers = ['NIM', 'Nama Mahasiswa', 'Smt Mhs', 'Jenjang', 'Program Studi', 'Jenis Tagihan', 'Smt Tagihan', 'Total Tagihan', 'Sudah Dibayar', 'Sisa Tunggakan', 'Status'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');

        $row = 2;
        $totalTagihan = 0;
        $totalBayar = 0;
        $totalSisa = 0;

        foreach ($rows as $t) {
            $totalTagihan += $t->total_harus_bayar;
            $totalBayar += $t->total_sudah_bayar;
            $totalSisa += $t->sisa_tagihan;

            $sheet->fromArray([
                $t->mahasiswa->nim ?? '-',
                $t->mahasiswa->nama_lengkap ?? '-',
                $t->mahasiswa->semester_saat_ini ?? '-',
                $t->mahasiswa->jenjang ?? '-',
                $t->mahasiswa->prodi->nama_prodi ?? '-',
                $t->nama_tagihan_snapshot,
                $t->semester,
                (float) $t->total_harus_bayar,
                (float) $t->total_sudah_bayar,
                (float) $t->sisa_tagihan,
                str_replace('_', ' ', $t->status),
            ], null, "A{$row}");
            $row++;
        }

        $sheet->setCellValue("F{$row}", 'TOTAL');
        $sheet->setCellValue("H{$row}", $totalTagihan);
        $sheet->setCellValue("I{$row}", $totalBayar);
        $sheet->setCellValue("J{$row}", $totalSisa);
        $sheet->getStyle("F{$row}:J{$row}")->getFont()->setBold(true);

        $sheet->getStyle("H2:J{$row}")->getNumberFormat()->setFormatCode('#,##0');
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $filename = 'Laporan_Keuangan_'.now()->format('Ymd_His').'.xlsx';
        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx');
        (new Xlsx($spreadsheet))->save($tempPath);

        return response()->download($tempPath, $filename)->deleteFileAfterSend(true);
    }

    public function render()
    {
        $filteredTags = $this->filteredQuery()->orderByDesc('created_at')->limit(100)->get();
        $totalCount = $this->filteredQuery()->count();

        $totalTagihan = $this->filteredQuery()->sum('total_harus_bayar');
        $totalDibayar = $this->filteredQuery()->sum('total_sudah_bayar');
        $totalTunggakan = $this->filteredQuery()->sum('sisa_tagihan');

        $semesterOpts = Tagihan::whereNotNull('semester')->distinct()->orderBy('semester')->pluck('semester');

        return view('livewire.report.index', [
            'filteredTags' => $filteredTags,
            'totalCount' => $totalCount,
            'totalTagihan' => $totalTagihan,
            'totalDibayar' => $totalDibayar,
            'totalTunggakan' => $totalTunggakan,
            'semesterOpts' => $semesterOpts,
            'prodis' => Prodi::orderBy('nama_prodi')->get(),
            'masterBiayas' => MasterBiaya::orderBy('nama_biaya')->get(),
        ]);
    }
}

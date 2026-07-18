<?php

namespace App\Livewire\Dashboard;

use App\Enums\StatusAkademik;
use App\Enums\StatusTagihan;
use App\Enums\UserRole;
use App\Models\ActivityLog;
use App\Models\AlumniTracking;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\TahunAkademik;
use App\Models\Tagihan;
use App\Models\Yudisium;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    protected function getProdiKeywords(string $prodi): array
    {
        return match ($prodi) {
            'MPI' => ['MPI', 'MANAJEMEN PENDIDIKAN'],
            'AS' => ['AS', 'AHWAL', 'HUKUM KELUARGA'],
            default => [],
        };
    }

    protected function getStudentCount($students, $prodis, string $jenjang, ?string $prodiCode, ?string $gender): int
    {
        $prodiIds = null;
        if ($prodiCode) {
            $keywords = $this->getProdiKeywords($prodiCode);
            $prodiIds = $prodis->filter(function ($p) use ($keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains(strtoupper($p->nama_prodi), $kw) || str_contains(strtoupper($p->kode_prodi), $kw)) {
                        return true;
                    }
                }

                return false;
            })->pluck('id');
        }

        return $students->filter(function ($s) use ($jenjang, $gender, $prodiIds) {
            $matchJenjang = $jenjang === 'ALL' || ($s->jenjang ?: 'S1') === $jenjang;
            $matchGender = ! $gender || $s->jenis_kelamin === $gender;
            $matchProdi = $prodiIds === null || $prodiIds->contains($s->prodi_id);

            return $matchJenjang && $matchGender && $matchProdi;
        })->count();
    }

    public function render()
    {
        $role = Auth::user()->role;

        $students = Mahasiswa::all();
        $prodis = Prodi::all();
        $tagihans = Tagihan::all();
        $alumni = AlumniTracking::all();
        $yudisiums = Yudisium::all();
        $activeTahunAkademik = TahunAkademik::where('is_active', true)->first();

        $totalStudents = $students->count();
        $activeStudents = $students->where('status_akademik', StatusAkademik::AKTIF->value)->count();

        $totalOutstanding = $tagihans->sum('sisa_tagihan');
        $totalCollected = $tagihans->sum('total_sudah_bayar');

        $employedAlumni = $alumni->whereIn('status_pekerjaan', ['KERJA', 'WIRAUSAHA'])->count();
        $totalAlumni = $alumni->count();

        $lulusanCount = $yudisiums->where('status_akhir', 'SIAP_WISUDA')->count();

        $s1MPI_L = $this->getStudentCount($students, $prodis, 'S1', 'MPI', 'L');
        $s1MPI_P = $this->getStudentCount($students, $prodis, 'S1', 'MPI', 'P');
        $s1MPI = $this->getStudentCount($students, $prodis, 'S1', 'MPI', null);

        $s1AS_L = $this->getStudentCount($students, $prodis, 'S1', 'AS', 'L');
        $s1AS_P = $this->getStudentCount($students, $prodis, 'S1', 'AS', 'P');
        $s1AS = $this->getStudentCount($students, $prodis, 'S1', 'AS', null);

        $s1Total = $this->getStudentCount($students, $prodis, 'S1', null, null);

        $s2MPI_L = $this->getStudentCount($students, $prodis, 'S2', 'MPI', 'L');
        $s2MPI_P = $this->getStudentCount($students, $prodis, 'S2', 'MPI', 'P');
        $s2MPI = $this->getStudentCount($students, $prodis, 'S2', 'MPI', null);

        $s2Total = $this->getStudentCount($students, $prodis, 'S2', null, null);

        $combinedTotal = $s1Total + $s2Total;

        $overdueBills = $tagihans->filter(fn ($t) => $t->status === StatusTagihan::BELUM_LUNAS->value && $t->jatuh_tempo && $t->jatuh_tempo->isPast())->count();
        $pendingYudisiums = $yudisiums->where('status_akhir', 'BELUM')->count();

        $showFinancials = in_array($role, [UserRole::ADMIN, UserRole::KEUANGAN]);
        $showDynamicWarnings = in_array($role, [UserRole::ADMIN, UserRole::KEUANGAN, UserRole::AKADEMIK]) && ($overdueBills > 0 || $pendingYudisiums > 0);

        $activityLogs = $role === UserRole::ADMIN
            ? ActivityLog::orderByDesc('timestamp')->limit(10)->get()
            : collect();

        return view('livewire.dashboard.index', compact(
            'totalStudents', 'activeStudents', 'totalOutstanding', 'totalCollected',
            'employedAlumni', 'totalAlumni', 'lulusanCount', 'activeTahunAkademik',
            's1MPI_L', 's1MPI_P', 's1MPI', 's1AS_L', 's1AS_P', 's1AS', 's1Total',
            's2MPI_L', 's2MPI_P', 's2MPI', 's2Total', 'combinedTotal',
            'overdueBills', 'pendingYudisiums', 'showFinancials', 'showDynamicWarnings',
            'activityLogs', 'role'
        ));
    }
}

<?php

namespace App\Livewire\DatabaseManagement;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    protected array $wipeableTables = [
        'nilais', 'mata_kuliahs', 'dispensasis', 'pembayarans', 'tagihans',
        'yudisiums', 'alumni_trackings', 'mahasiswas', 'master_biayas',
        'kategori_beasiswas', 'tahun_akademiks', 'prodis', 'activity_logs',
    ];

    public $restoreFile = null;

    public bool $showRestoreConfirm = false;

    public bool $showResetConfirm = false;

    public string $resetCode = '';

    public ?string $statusType = null;

    public ?string $statusText = null;

    public function isSqlite(): bool
    {
        return config('database.default') === 'sqlite';
    }

    public function updatedRestoreFile(): void
    {
        $this->validate(['restoreFile' => 'file|max:51200']);
        $this->showRestoreConfirm = true;
    }

    public function cancelRestore(): void
    {
        $this->showRestoreConfirm = false;
        $this->restoreFile = null;
    }

    public function downloadBackup()
    {
        if (! $this->isSqlite()) {
            $this->statusType = 'error';
            $this->statusText = 'Backup file hanya tersedia untuk koneksi SQLite. Untuk MySQL, gunakan mysqldump atau fitur backup dari panel hosting Anda.';

            return;
        }

        $path = config('database.connections.sqlite.database');
        $filename = 'SIAKAD_Backup_'.now()->format('Y-m-d').'.sqlite';

        return response()->download($path, $filename);
    }

    public function executeRestore(): void
    {
        if (! $this->restoreFile || ! $this->isSqlite()) {
            $this->showRestoreConfirm = false;

            return;
        }

        $header = file_get_contents($this->restoreFile->getRealPath(), false, null, 0, 16);
        if (! str_starts_with($header, 'SQLite format 3')) {
            $this->statusType = 'error';
            $this->statusText = 'File yang diunggah bukan file backup SQLite yang valid.';
            $this->showRestoreConfirm = false;
            $this->restoreFile = null;

            return;
        }

        $path = config('database.connections.sqlite.database');
        DB::disconnect();
        copy($this->restoreFile->getRealPath(), $path);

        $this->showRestoreConfirm = false;
        $this->restoreFile = null;
        $this->statusType = 'success';
        $this->statusText = 'Restore berhasil. Silakan muat ulang halaman untuk melihat data terbaru.';
    }

    public function executeReset(): void
    {
        if ($this->resetCode !== 'RESET SEMUA DATA') {
            return;
        }

        $driver = config('database.default');

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($this->wipeableTables as $table) {
            DB::table($table)->truncate();
        }

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $this->showResetConfirm = false;
        $this->resetCode = '';
        $this->statusType = 'success';
        $this->statusText = 'Reset berhasil. Seluruh data akademik/keuangan telah dihapus (akun pengguna & profil kampus tetap tersimpan).';
    }

    public function render()
    {
        return view('livewire.database-management.index');
    }
}

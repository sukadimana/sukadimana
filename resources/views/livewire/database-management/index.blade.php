<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Manajemen Database</h2>
            <p class="text-gray-500 mt-1">Backup, restore, dan reset data sistem (Hanya Admin)</p>
        </div>
        <x-icon name="database" class="text-blue-500 w-8 h-8" />
    </div>

    @if ($statusText)
        <div class="p-4 mx-6 mt-6 rounded-lg font-medium text-sm border flex items-start gap-2 {{ $statusType === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
            <span>{{ $statusText }}</span>
        </div>
    @endif

    @if (! $this->isSqlite())
        <div class="mx-6 mt-6 p-4 bg-blue-50 text-blue-700 rounded-lg text-sm border border-blue-100">
            Koneksi database saat ini menggunakan <strong>{{ config('database.default') }}</strong>. Backup/restore berbasis file hanya tersedia untuk SQLite — gunakan <code class="bg-blue-100 px-1 rounded">mysqldump</code> atau fitur backup dari panel hosting Anda.
        </div>
    @endif

    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="border border-gray-200 rounded-xl p-6 flex flex-col items-center text-center justify-between">
            <div class="bg-blue-100 p-4 rounded-full text-blue-600 mb-4">
                <x-icon name="dashboard" class="w-8 h-8" />
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-2">Backup Data</h3>
            <p class="text-sm text-gray-500 mb-6">Unduh seluruh database sebagai cadangan Anda.</p>
            <button wire:click="downloadBackup" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex justify-center items-center gap-2">
                Mulai Backup
            </button>
        </div>

        <div class="border border-gray-200 rounded-xl p-6 flex flex-col items-center text-center justify-between">
            <div class="bg-green-100 p-4 rounded-full text-green-600 mb-4">
                <x-icon name="dashboard" class="w-8 h-8" />
            </div>
            <h3 class="font-bold text-gray-800 text-lg mb-2">Restore Data</h3>
            <p class="text-sm text-gray-500 mb-6">Kembalikan data dari file backup (.sqlite). <br><span class="text-red-500 font-semibold text-xs">Peringatan: Akan menimpa data yang ada!</span></p>
            <label class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex justify-center items-center gap-2 cursor-pointer">
                Pilih File Backup
                <input type="file" wire:model="restoreFile" accept=".sqlite" class="hidden">
            </label>
            @error('restoreFile') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="border border-red-200 bg-red-50 rounded-xl p-6 flex flex-col items-center text-center justify-between">
            <div class="bg-red-100 p-4 rounded-full text-red-600 mb-4">
                <x-icon name="alert-circle" class="w-8 h-8" />
            </div>
            <h3 class="font-bold text-red-800 text-lg mb-2">Reset Semua Data</h3>
            <p class="text-sm text-red-600 mb-6">Menghapus SELURUH data tagihan, mahasiswa, dll kecuali akun pengguna dan profil kampus.</p>
            <button wire:click="$set('showResetConfirm', true)" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex justify-center items-center gap-2">
                Hapus Semua Data
            </button>
        </div>
    </div>

    <div class="bg-gray-50 p-6 border-t border-gray-100 text-sm text-gray-600">
        <h4 class="font-bold mb-2 flex items-center gap-2"><x-icon name="database" class="w-4 h-4" /> Informasi Hosting/Migrasi Data:</h4>
        <ol class="list-decimal pl-5 space-y-1">
            <li>Lakukan <strong>Backup Data</strong> di sini sebelum memindahkan sistem/hosting baru.</li>
            <li>Di server/hosting yang baru, gunakan fitur <strong>Restore Data</strong> dan pilih file backup yang telah diunduh.</li>
            <li>Klik <strong>Hapus Semua Data</strong> untuk mengembalikan sistem ke kondisi bersih (data uji coba).</li>
        </ol>
    </div>

    @if ($showRestoreConfirm)
        <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-xl font-bold mb-2">Konfirmasi Restore Data</h3>
                <p class="text-gray-600 mb-6 border-l-4 border-red-500 pl-3 bg-red-50 py-2 text-sm text-red-800">
                    Perhatian: Mengembalikan (restore) data akan <strong>MENIMPA DAN MENGHAPUS</strong> seluruh data yang ada di dalam database saat ini.
                </p>
                <div class="flex gap-3 justify-end mt-4">
                    <button wire:click="cancelRestore" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 font-medium">Batal</button>
                    <button wire:click="executeRestore" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium whitespace-nowrap">Ya, Timpa Data!</button>
                </div>
            </div>
        </div>
    @endif

    @if ($showResetConfirm)
        <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 border-t-4 border-red-600">
                <h3 class="text-xl font-bold mb-2 text-gray-900">Konfirmasi Reset Sistem</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Apakah Anda yakin ingin <strong class="text-red-600">MENGHAPUS SEMUA</strong> data Master Biaya, Program Studi, Tahun Akademik, Mahasiswa, Tagihan, dan Pembayaran? Ini tidak dapat dibatalkan!
                </p>
                <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-200">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Ketik "RESET SEMUA DATA" untuk konfirmasi</label>
                    <input type="text" wire:model.live="resetCode" class="w-full text-center border-gray-300 border rounded py-2 px-3 focus:outline-none focus:ring-2 focus:ring-red-500 font-bold" placeholder="RESET SEMUA DATA" autocomplete="off">
                </div>
                <div class="flex gap-3 justify-end">
                    <button wire:click="$set('showResetConfirm', false)" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 bg-gray-50 hover:bg-gray-100 font-medium">Batal</button>
                    <button wire:click="executeReset" @disabled($resetCode !== 'RESET SEMUA DATA') class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">Reset Sekarang</button>
                </div>
            </div>
        </div>
    @endif
</div>

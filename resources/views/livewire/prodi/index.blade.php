<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Program Studi</h2>
            <p class="text-gray-500">Kelola daftar fakultas dan jurusan di kampus</p>
        </div>
        @if ($isAdmin)
            <button wire:click="openModal" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-md active:scale-95">
                <x-icon name="plus" class="w-5 h-5" />
                Tambah Prodi
            </button>
        @endif
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="relative flex-1">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input type="text" wire:model.live="search" placeholder="Cari kode atau nama prodi..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($prodis as $prodi)
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-start justify-between relative z-10">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <x-icon name="book-open" class="w-6 h-6" />
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $prodi->jenjang === 'S1' ? 'bg-green-100 text-green-700' : 'bg-purple-100 text-purple-700' }}">
                        {{ $prodi->jenjang }}
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">{{ $prodi->nama_prodi }}</h3>
                <p class="text-sm text-gray-500 mb-4 font-mono">Kode: {{ $prodi->kode_prodi }}</p>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <x-icon name="alert-circle" class="w-3.5 h-3.5" />
                    <span>Digunakan untuk tarif dan data mahasiswa</span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 italic bg-white rounded-2xl border-2 border-dashed border-gray-200">
                Belum ada data prodi ditemukan.
            </div>
        @endforelse
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-2xl w-full max-w-md relative z-10 shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">Tambah Program Studi Baru</h3>
                    <button wire:click="$set('isModalOpen', false)" class="p-1 hover:bg-gray-200 rounded-full transition-colors">
                        <x-icon name="x-mark" class="w-6 h-6 text-gray-500" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Prodi</label>
                        <input type="text" wire:model="kode_prodi" placeholder="Contoh: IF, SI, AK" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('kode_prodi') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('kode_prodi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program Studi</label>
                        <input type="text" wire:model="nama_prodi" placeholder="Nama lengkap prodi" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('nama_prodi') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('nama_prodi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="$set('jenjang', 'S1')" class="py-2 rounded-lg font-medium border transition-all {{ $jenjang === 'S1' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                Sarjana (S1)
                            </button>
                            <button type="button" wire:click="$set('jenjang', 'S2')" class="py-2 rounded-lg font-medium border transition-all {{ $jenjang === 'S2' ? 'bg-blue-600 text-white border-blue-600 shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                Magister (S2)
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-md">
                            Simpan Prodi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

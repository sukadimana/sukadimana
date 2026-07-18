<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Katalog Tarif (Master Biaya)</h2>
            <p class="text-gray-500">Atur komponen biaya dan tarif resmi kampus</p>
        </div>
        @if ($isAdmin)
            <button wire:click="openAddModal" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-md active:scale-95">
                <x-icon name="plus" class="w-5 h-5" />
                Tambah Komponen
            </button>
        @endif
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="relative flex-1">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input type="text" wire:model.live="search" placeholder="Cari nama biaya atau angkatan..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($biayas as $biaya)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="h-10 w-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <x-icon name="dollar" class="w-5 h-5" />
                        </div>
                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider {{ $biaya->bisa_dicicil ? 'bg-blue-50 text-blue-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $biaya->bisa_dicicil ? 'Dapat Dicicil' : 'Sekali Bayar' }}
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ $biaya->nama_biaya }}</h3>
                    <p class="text-2xl font-black text-blue-600 mb-4">Rp {{ number_format($biaya->nominal_baku, 0, ',', '.') }}</p>

                    <div class="space-y-2 border-t border-gray-50 pt-4">
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Angkatan:</span>
                            <span class="font-semibold text-gray-700">{{ $biaya->angkatan }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Semester:</span>
                            <span class="font-semibold text-gray-700">{{ $biaya->semester ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-gray-400">Berlaku Untuk:</span>
                            <span class="font-semibold text-blue-600">
                                {{ $biaya->prodi ? $biaya->prodi->nama_prodi : 'Semua Prodi' }}
                                {{ $biaya->jenjang && $biaya->jenjang !== 'SEMUA' ? "({$biaya->jenjang})" : '' }}
                            </span>
                        </div>
                    </div>
                </div>
                @if ($isAdmin)
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                        <button wire:click="edit({{ $biaya->id }})" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit">
                            <x-icon name="pencil" class="w-4.5 h-4.5" />
                        </button>
                        <button wire:click="confirmDelete({{ $biaya->id }}, '{{ addslashes($biaya->nama_biaya) }}')" class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus">
                            <x-icon name="trash" class="w-4.5 h-4.5" />
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-500 italic bg-white rounded-2xl border-2 border-dashed border-gray-200">
                Katalog biaya masih kosong.
            </div>
        @endforelse
    </div>

    @if ($itemToDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('itemToDeleteId', null)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 p-6 flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-4">
                    <x-icon name="trash" class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Komponen Biaya</h3>
                <p class="text-gray-500 mb-6 text-sm">Apakah Anda yakin ingin menghapus <strong>{{ $itemToDeleteName }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3 w-full">
                    <button wire:click="$set('itemToDeleteId', null)" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-lg transition-colors">Batal</button>
                    <button wire:click="delete" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    @endif

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-2xl w-full max-w-md relative z-10 shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">{{ $isEditing ? 'Edit Komponen Biaya' : 'Tambah Komponen Biaya' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="p-1 hover:bg-gray-200 rounded-full transition-colors">
                        <x-icon name="x-mark" class="w-6 h-6 text-gray-500" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Komponen Biaya</label>
                        <input type="text" wire:model="nama_biaya" placeholder="Contoh: SPP Tetap Ganjil" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('nama_biaya') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('nama_biaya') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Angkatan</label>
                            <input type="text" wire:model="angkatan" placeholder="2025/2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                            <input type="number" wire:model="nominal_baku" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('nominal_baku') ? 'border-red-300' : 'border-gray-300' }}">
                            @error('nominal_baku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <input type="number" min="1" max="14" wire:model="semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                            <select wire:model="jenjang" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="SEMUA">Semua Jenjang</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi (Opsional)</label>
                        <select wire:model="prodi_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="">Berlaku Untuk Semua Prodi</option>
                            @foreach ($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->jenjang }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <input type="checkbox" id="can_install" wire:model="bisa_dicicil" class="h-5 w-5 rounded text-blue-600 focus:ring-blue-500">
                        <label for="can_install" class="text-sm font-medium text-blue-800">Tagihan ini dapat dibayar dengan sistem cicilan</label>
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-md">
                            {{ $isEditing ? 'Perbarui Data' : 'Simpan Tarif' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

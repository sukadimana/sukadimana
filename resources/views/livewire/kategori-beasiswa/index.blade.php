<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Manajemen Kategori Beasiswa</h2>
            <p class="text-gray-500 text-sm mt-1">Kelola jenis-jenis penerimaan beasiswa/keringanan</p>
        </div>
        <button wire:click="openCreate" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
            <x-icon name="plus" class="w-4 h-4" />
            Tambah Kategori
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                    <th class="p-4 font-semibold">Nama Kategori Beasiswa</th>
                    <th class="p-4 font-semibold">Tipe Potongan</th>
                    <th class="p-4 font-semibold text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="p-4 font-medium text-gray-900">{{ $item->nama }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 rounded text-xs font-bold {{ $item->tipe_potongan === 'FULL' ? 'bg-green-100 text-green-700' : ($item->tipe_potongan === 'CUSTOM' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ match($item->tipe_potongan) {
                                    'FULL' => 'POTONGAN PENUH (Sesuai Nominal Tagihan)',
                                    'CUSTOM' => 'POTONGAN NOMINAL (Disesuaikan Per Mahasiswa)',
                                    default => 'TIDAK ADA POTONGAN',
                                } }}
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button wire:click="edit({{ $item->id }})" class="p-1 px-2 flex items-center gap-1.5 text-blue-600 hover:bg-blue-50 rounded text-xs font-medium">
                                    <x-icon name="pencil" class="w-3.5 h-3.5" /> Edit
                                </button>
                                <button wire:click="confirmDelete({{ $item->id }})" class="p-1 px-2 flex items-center gap-1.5 text-red-600 hover:bg-red-50 rounded text-xs font-medium">
                                    <x-icon name="trash" class="w-3.5 h-3.5" /> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-500">Belum ada master kategori beasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($deleteConfirmId)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('deleteConfirmId', null)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 flex flex-col p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <x-icon name="trash" class="h-6 w-6 text-red-600" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Kategori</h3>
                <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus kategori beasiswa ini?</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('deleteConfirmId', null)" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg">Batal</button>
                    <button wire:click="delete" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold mb-4">{{ $currentEditId ? 'Edit' : 'Tambah' }} Kategori Beasiswa</h3>
                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori Beasiswa</label>
                        <input type="text" wire:model="nama" placeholder="Contoh: KIP-Kuliah, Tahfidz, BAZNAS" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Potongan</label>
                        <select wire:model="tipe_potongan" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="NONE">Tidak Ada Potongan (Tagihan Penuh)</option>
                            <option value="CUSTOM">Potongan Sebagian/Nominal Khusus</option>
                            <option value="FULL">Potongan Penuh / Gratis</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-2">
                            * <strong>Potongan Penuh:</strong> Mahasiswa ini secara otomatis digratiskan seluruh biayanya jika mendapat tagihan.<br>
                            * <strong>Potongan Sebagian:</strong> Anda bisa mengatur nominal tertentu untuk tiap mahasiswa.
                        </p>
                    </div>
                    <div class="flex gap-3 justify-end mt-6">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">{{ $currentEditId ? 'Simpan Perubahan' : 'Tambah' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Tahun Akademik</h2>
            <p class="text-gray-500">Kelola tahun akademik dan semester yang aktif saat ini</p>
        </div>
        @if ($canManage)
            <button wire:click="openAddModal" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                <x-icon name="plus" class="w-5 h-5" />
                Tambah Tahun Akademik
            </button>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 rounded-tl-xl font-bold">Nama Tahun Akademik</th>
                        <th class="p-4 font-bold">Tipe Semester</th>
                        <th class="p-4 font-bold text-center">Status</th>
                        @if ($canManage)
                            <th class="p-4 rounded-tr-xl font-bold text-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tahunAkademiks as $ta)
                        <tr class="hover:bg-gray-50 transition-colors {{ $ta->is_active ? 'bg-blue-50/30' : '' }}">
                            <td class="p-4 font-medium text-gray-900">{{ $ta->nama }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-md {{ $ta->semester_tipe === 'GANJIL' ? 'bg-orange-100 text-orange-700' : 'bg-teal-100 text-teal-700' }}">
                                    {{ $ta->semester_tipe }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                @if ($ta->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                        <x-icon name="check-circle" class="w-3.5 h-3.5" /> Aktif
                                    </span>
                                @else
                                    <button wire:click="setActive({{ $ta->id }})" wire:confirm="Jadikan Tahun Akademik ini sebagai yang aktif? Mengubah status aktif akan mempengaruhi kemunculan mahasiswa di tagihan." @disabled(! $canManage) class="px-3 py-1 bg-gray-100 text-gray-600 hover:bg-gray-200 rounded-full text-xs font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        Set Aktif
                                    </button>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="p-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="openEditModal({{ $ta->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                            <x-icon name="pencil" class="w-4.5 h-4.5" />
                                        </button>
                                        <button wire:click="delete({{ $ta->id }})" wire:confirm="Apakah Anda yakin ingin menghapus Tahun Akademik ini?" @disabled($ta->is_active) class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                            <x-icon name="trash" class="w-4.5 h-4.5" />
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Belum ada data tahun akademik. Tambahkan setidaknya satu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold">{{ $isEditing ? 'Edit Tahun Akademik' : 'Tambah Tahun Akademik' }}</h3>
                </div>
                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama (misal: 2025/2026 Ganjil)</label>
                        <input type="text" wire:model="nama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" placeholder="2025/2026 Ganjil">
                        @error('nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Semester</label>
                        <select wire:model="semester_tipe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="GANJIL">Ganjil</option>
                            <option value="GENAP">Genap</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 mt-4 bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                        <input type="checkbox" id="is_active" wire:model="is_active" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <label for="is_active" class="text-sm font-medium text-yellow-800">Langsung jadikan aktif</label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

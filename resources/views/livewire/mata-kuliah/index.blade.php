<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Manajemen Mata Kuliah</h2>
            <p class="text-gray-500">Kelola daftar mata kuliah per program studi & semester untuk input nilai KHS</p>
        </div>
        <button wire:click="openCreate" class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-semibold transition-all shadow-md active:scale-95">
            <x-icon name="plus" class="w-5 h-5" />
            Tambah Mata Kuliah
        </button>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <div class="relative flex-1">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input type="text" wire:model.live="search" placeholder="Cari kode atau nama mata kuliah..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                        <th class="p-4 font-bold">Kode</th>
                        <th class="p-4 font-bold">Nama Mata Kuliah</th>
                        <th class="p-4 font-bold text-center">SKS</th>
                        <th class="p-4 font-bold">Program Studi</th>
                        <th class="p-4 font-bold text-center">Semester</th>
                        <th class="p-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($mataKuliahs as $mk)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="p-4 font-mono text-sm text-gray-700">{{ $mk->kode_mk }}</td>
                            <td class="p-4 font-medium text-gray-900">{{ $mk->nama_mk }}</td>
                            <td class="p-4 text-center font-bold text-gray-700">{{ $mk->sks }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ $mk->prodi->nama_prodi ?? '-' }}</td>
                            <td class="p-4 text-center text-sm text-gray-600">{{ $mk->semester }}</td>
                            <td class="p-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="edit({{ $mk->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $mk->id }})" wire:confirm="Hapus mata kuliah ini? Nilai yang sudah diinput untuk mata kuliah ini juga akan terhapus." class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500">Belum ada data mata kuliah.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-2xl w-full max-w-md relative z-10 shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                    <h3 class="text-xl font-bold text-gray-900">{{ $currentEditId ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="p-1 hover:bg-gray-200 rounded-full transition-colors">
                        <x-icon name="x-mark" class="w-6 h-6 text-gray-500" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Mata Kuliah</label>
                        <input type="text" wire:model="kode_mk" placeholder="Contoh: MK001" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('kode_mk') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('kode_mk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Mata Kuliah</label>
                        <input type="text" wire:model="nama_mk" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('nama_mk') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('nama_mk') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKS</label>
                            <input type="number" min="1" max="6" wire:model="sks" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                            <input type="number" min="1" max="14" wire:model="semester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                        <select wire:model="prodi_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('prodi_id') ? 'border-red-300' : 'border-gray-300' }}">
                            <option value="">Pilih Program Studi</option>
                            @foreach ($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->jenjang }})</option>
                            @endforeach
                        </select>
                        @error('prodi_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="pt-4 flex gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors shadow-md">
                            {{ $currentEditId ? 'Perbarui' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

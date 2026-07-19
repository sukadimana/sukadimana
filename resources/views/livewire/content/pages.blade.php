<div class="space-y-6 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Halaman Statis - {{ $channel === 'satgas_ppk' ? 'Satgas PPK' : 'Kampus' }}</h2>
            <p class="text-gray-500">Kelola halaman seperti Profil, Visi Misi, Sejarah, dll.</p>
        </div>
        <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
            <x-icon name="plus" class="w-5 h-5" />
            Tambah Halaman
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Urutan Menu</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($pages as $page)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $page->title }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 font-mono">/{{ $page->slug }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $page->menu_order }}</td>
                            <td class="px-6 py-4">
                                @if ($page->is_published)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-green-100 text-green-700 border-green-200">Tampil</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-600 border-gray-200">Draf</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button wire:click="edit({{ $page->id }})" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition-colors" title="Edit">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $page->id }})" wire:confirm="Hapus halaman ini?" class="text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition-colors" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Belum ada halaman untuk kanal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl relative z-10 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">{{ $editingId ? 'Ubah Halaman' : 'Tambah Halaman Baru' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 overflow-y-auto space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Halaman</label>
                        <input type="text" wire:model.live="title" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('title') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug URL</label>
                        <input type="text" wire:model="slug" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('slug') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Isi Halaman</label>
                        <textarea wire:model="content" rows="8" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Menu</label>
                        <input type="number" wire:model="menu_order" class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_published" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Tampilkan di website</span>
                    </label>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors min-w-[140px]">
                            {{ $editingId ? 'Perbarui Halaman' : 'Simpan Halaman' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<div class="space-y-6 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Berita &amp; Pengumuman - {{ $channelLabel }}</h2>
            <p class="text-gray-500">Kelola konten berita yang tampil di website publik</p>
        </div>
        <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
            <x-icon name="plus" class="w-5 h-5" />
            Tulis Berita
        </button>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="relative">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input type="text" wire:model.live="search" placeholder="Cari judul berita..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Berita</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($posts as $post)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($post->image)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($post->image) }}" alt="" class="w-14 h-14 rounded-lg object-cover shrink-0">
                                    @else
                                        <div class="w-14 h-14 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 shrink-0">
                                            <x-icon name="image" class="w-6 h-6" />
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $post->title }}</p>
                                        <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($post->excerpt, 60) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($post->is_published)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-green-100 text-green-700 border-green-200">Terbit</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-600 border-gray-200">Draf</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $post->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button wire:click="edit({{ $post->id }})" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition-colors" title="Edit">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $post->id }})" wire:confirm="Hapus berita ini?" class="text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition-colors" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Belum ada berita untuk kanal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($posts->total() > 0)
            <div class="p-4 border-t border-gray-200">
                {{ $posts->links() }}
            </div>
        @endif
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl relative z-10 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">{{ $editingId ? 'Ubah Berita' : 'Tulis Berita Baru' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 overflow-y-auto space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Berita</label>
                        <input type="text" wire:model.live="title" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('title') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug URL</label>
                        <input type="text" wire:model="slug" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('slug') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('slug') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Singkat</label>
                        <input type="text" wire:model="excerpt" placeholder="Ringkasan yang tampil di daftar berita" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Isi Berita</label>
                        <textarea wire:model="content" rows="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('content') ? 'border-red-300' : 'border-gray-300' }}"></textarea>
                        @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Sampul</label>
                        @if ($existingImage)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($existingImage) }}" alt="" class="h-24 rounded-lg object-cover mb-2">
                        @endif
                        <input type="file" wire:model="newImage" accept="image/*" class="w-full text-sm">
                        @error('newImage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="newImage" class="text-xs text-blue-500 mt-1">Mengunggah...</div>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="is_published" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Terbitkan sekarang</span>
                    </label>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors min-w-[140px]">
                            {{ $editingId ? 'Perbarui Berita' : 'Simpan Berita' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

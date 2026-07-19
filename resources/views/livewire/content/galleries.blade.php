<div class="space-y-6 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Galeri Foto - {{ $channel === 'satgas_ppk' ? 'Satgas PPK' : 'Kampus' }}</h2>
            <p class="text-gray-500">Kelola foto yang tampil di galeri website</p>
        </div>
        <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
            <x-icon name="plus" class="w-5 h-5" />
            Unggah Foto
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @if ($photos->isEmpty())
            <p class="text-center text-gray-500 py-8">Belum ada foto untuk kanal ini.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($photos as $photo)
                    <div class="relative group rounded-lg overflow-hidden border border-gray-200 aspect-square">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->image) }}" alt="{{ $photo->title }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-colors flex items-end p-2 opacity-0 group-hover:opacity-100">
                            <button wire:click="confirmDelete({{ $photo->id }})" class="bg-white text-red-600 p-1.5 rounded-lg" title="Hapus">
                                <x-icon name="trash" class="w-4 h-4" />
                            </button>
                        </div>
                        @if ($photo->title)
                            <div class="absolute bottom-0 inset-x-0 bg-black bg-opacity-60 text-white text-xs p-1.5 truncate">{{ $photo->title }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md relative z-10 flex flex-col">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Unggah Foto Baru</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul (opsional)</label>
                        <input type="text" wire:model="title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                        <input type="file" wire:model="newImage" accept="image/*" class="w-full text-sm">
                        @error('newImage') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="newImage" class="text-xs text-blue-500 mt-1">Mengunggah...</div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors min-w-[120px]">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($deleteConfirmId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('deleteConfirmId', null)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 p-6 flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-4">
                    <x-icon name="trash" class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Foto</h3>
                <p class="text-gray-500 mb-6 text-sm">Apakah Anda yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3 w-full">
                    <button wire:click="$set('deleteConfirmId', null)" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-lg transition-colors">Batal</button>
                    <button wire:click="delete" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
            <x-icon name="shield" class="w-5 h-5" />
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Pengaturan Satgas PPK</h2>
            <p class="text-sm text-gray-500">Kelola identitas dan kontak Satgas PPK yang tampil di microsite publik.</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @if ($saveSuccess)
            <div class="col-span-1 md:col-span-2 p-4 bg-green-100 text-green-700 rounded-lg font-medium">{{ $saveSuccess }}</div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Satgas</label>
                <input type="text" wire:model="satgas_nama" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('satgas_nama') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea wire:model="satgas_deskripsi" rows="4" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Pengaduan</label>
                <input type="email" wire:model="satgas_email" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('satgas_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon / Hotline</label>
                <input type="text" wire:model="satgas_telepon" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Satgas PPK</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors">
                @if ($satgas_logo_url)
                    <div class="flex flex-col items-center">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($satgas_logo_url) }}" alt="Logo Satgas Preview" class="h-32 object-contain mb-4 rounded">
                        <label class="text-sm text-blue-600 font-medium hover:underline cursor-pointer">
                            Ganti Logo
                            <input type="file" wire:model="newLogo" accept="image/*" class="hidden">
                        </label>
                    </div>
                @else
                    <div class="py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <x-icon name="alert-circle" class="text-gray-400 w-6 h-6" />
                        </div>
                        <p class="text-sm text-gray-500 mb-1">Upload logo Satgas PPK di sini.</p>
                        <p class="text-xs text-gray-400 mb-4">Format disarankan: PNG (Maks 2MB)</p>
                        <label class="inline-block px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg shadow-sm hover:bg-gray-50 transition-colors cursor-pointer">
                            Pilih Gambar
                            <input type="file" wire:model="newLogo" accept="image/*" class="hidden">
                        </label>
                    </div>
                @endif
                @error('newLogo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div wire:loading wire:target="newLogo" class="text-xs text-blue-500 mt-2">Mengunggah...</div>
            </div>
            <div class="bg-blue-50 text-blue-800 text-sm p-4 rounded-lg flex items-start gap-3 mt-4">
                <x-icon name="shield" class="w-4 h-4 mt-0.5 shrink-0" />
                <p>Informasi ini tampil pada microsite publik Satgas PPK yang dapat diakses tanpa login, terpisah dari website kampus utama namun tetap dikelola dari satu panel admin ini.</p>
            </div>
        </div>

        <div class="col-span-full border-t border-gray-100 pt-6 mt-4 flex justify-end">
            <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

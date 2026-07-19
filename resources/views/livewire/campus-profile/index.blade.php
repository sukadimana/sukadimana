<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
            <x-icon name="building" class="w-5 h-5" />
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">Profil Kampus</h2>
            <p class="text-sm text-gray-500">Kelola identitas dan logo institusi untuk keperluan cetak.</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @if ($saveSuccess)
            <div class="col-span-1 md:col-span-2 p-4 bg-green-100 text-green-700 rounded-lg font-medium">{{ $saveSuccess }}</div>
        @endif

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kampus</label>
                <input type="text" wire:model="nama_kampus" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama_kampus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea wire:model="alamat_kampus" rows="3" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                @error('alamat_kampus') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" wire:model="telepon" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('telepon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="text" wire:model="website" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Petugas Pembayaran (Nama Terang)</label>
                <input type="text" wire:model="petugas_pembayaran" placeholder="Contoh: Sukadi, S.Pd.I" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <div class="col-span-1 md:col-span-2 space-y-4">
            <div class="border-t border-gray-100 pt-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Pengaturan API WhatsApp</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WA API URL</label>
                        <input type="text" wire:model="wa_api_url" placeholder="https://api.wa-provider.com/send" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">WA API Key / Token</label>
                        <input type="password" wire:model="wa_api_key" placeholder="Token atau API Key Anda" class="w-full p-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Gunakan fitur ini jika kampus Anda berlangganan API WhatsApp pihak ketiga dan memiliki URL Endpoint POST.</p>
            </div>
        </div>

        <div class="space-y-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo Kampus</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-50 transition-colors">
                @if ($logo_url)
                    <div class="flex flex-col items-center">
                        <img src="{{ $logo_url }}" alt="Logo Kampus Preview" class="h-32 object-contain mb-4 rounded">
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
                        <p class="text-sm text-gray-500 mb-1">Upload logo institusi di sini.</p>
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
                <x-icon name="building" class="w-4 h-4 mt-0.5 shrink-0" />
                <p>Informasi dan logo ini akan digunakan sebagai header (kop surat) pada saat pencetakan kwitansi pembayaran, KHS, dan kelulusan mahasiswa.</p>
            </div>
        </div>

        <div class="col-span-full border-t border-gray-100 pt-6 mt-4 flex justify-end">
            <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

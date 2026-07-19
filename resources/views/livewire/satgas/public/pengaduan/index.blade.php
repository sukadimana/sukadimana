<div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Formulir Pengaduan</h1>
    <p class="text-gray-500 mb-8">Laporan Anda akan ditangani secara rahasia oleh Satgas PPKS. Anda dapat memilih untuk melapor secara anonim.</p>

    @if ($referenceCode)
        <div class="p-6 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center gap-2 text-green-700 font-bold mb-2">
                <x-icon name="check-circle" class="w-5 h-5" />
                Pengaduan Berhasil Dikirim
            </div>
            <p class="text-sm text-green-800 mb-3">Simpan kode referensi berikut untuk memantau tindak lanjut laporan Anda melalui kontak Satgas PPKS:</p>
            <p class="font-mono text-lg font-bold text-green-900 bg-white border border-green-200 rounded-lg px-4 py-2 inline-block">{{ $referenceCode }}</p>
        </div>
    @else
        <form wire:submit="submit" class="space-y-5 bg-white border border-gray-200 rounded-xl p-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="is_anonymous" class="w-4 h-4 text-red-700 rounded border-gray-300 focus:ring-red-500">
                <span class="text-sm font-medium text-gray-700">Laporkan secara anonim (identitas tidak dicatat)</span>
            </label>

            @if (! $is_anonymous)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelapor</label>
                    <input type="text" wire:model="reporter_name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 outline-none {{ $errors->has('reporter_name') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('reporter_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kontak (Email / No. HP)</label>
                    <input type="text" wire:model="reporter_contact" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 outline-none {{ $errors->has('reporter_contact') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('reporter_contact') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kronologi / Uraian Kejadian</label>
                <textarea wire:model="description" rows="8" placeholder="Jelaskan kejadian secara rinci: waktu, tempat, pihak yang terlibat, dan kronologi." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-red-500 outline-none {{ $errors->has('description') ? 'border-red-300' : 'border-gray-300' }}"></textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full px-6 py-3 bg-red-700 text-white font-bold rounded-lg hover:bg-red-800 shadow-sm transition-colors">
                Kirim Pengaduan
            </button>
        </form>
    @endif
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-16">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Hubungi Kami</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="font-medium text-gray-900">{{ $profil->alamat_kampus ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Telepon</p>
                <p class="font-medium text-gray-900">{{ $profil->telepon ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium text-gray-900">{{ $profil->email ?? '-' }}</p>
            </div>
        </div>

        <div>
            @if ($sent)
                <div class="p-4 bg-green-100 text-green-700 rounded-lg font-medium mb-4">Pesan Anda berhasil terkirim. Terima kasih!</div>
            @endif

            <form wire:submit="send" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" wire:model="name" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('name') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('email') ? 'border-red-300' : 'border-gray-300' }}">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subjek</label>
                    <input type="text" wire:model="subject" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                    <textarea wire:model="message" rows="5" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('message') ? 'border-red-300' : 'border-gray-300' }}"></textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                    Kirim Pesan
                </button>
            </form>
        </div>
    </div>
</div>

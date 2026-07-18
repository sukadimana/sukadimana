<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <h2 class="font-semibold text-center text-lg text-gray-800 mb-6">Buat Password Baru</h2>

        @if ($status)
            <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm text-center">{{ $status }}</div>
        @endif

        <form wire:submit="resetPassword" class="space-y-4 text-left">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" wire:model="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>

            <button type="submit" class="w-full flex items-center justify-center gap-3 bg-blue-600 border border-transparent text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                Simpan Password Baru
            </button>
        </form>
    </div>
</div>

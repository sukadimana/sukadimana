<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <h2 class="font-semibold text-center text-lg text-gray-800 mb-2">Reset Password</h2>
        <p class="text-sm text-gray-500 text-center mb-6">Masukkan email Anda untuk menerima link reset password.</p>

        @if ($status)
            <div class="mb-4 bg-blue-50 text-blue-700 p-3 rounded-lg text-sm text-center">{{ $status }}</div>
        @endif

        <form wire:submit="sendResetLink" class="space-y-4 text-left">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Terdaftar</label>
                <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full flex items-center justify-center gap-3 bg-blue-600 border border-transparent text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                Kirim Link Reset
            </button>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-800">Kembali ke Login</a>
            </div>
        </form>
    </div>
</div>

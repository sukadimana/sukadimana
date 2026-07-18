<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-8 text-center">
        <div class="flex justify-center mb-6">
            @if ($profilKampus?->logo_url)
                <img src="{{ $profilKampus->logo_url }}" alt="Logo Kampus" class="h-24 object-contain">
            @else
                <div class="h-16 w-16 bg-blue-700 rounded-2xl flex items-center justify-center text-white shadow-lg rotate-3">
                    <x-icon name="book-open" class="w-8 h-8" />
                </div>
            @endif
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $profilKampus->nama_kampus ?? 'SIAKAD Pro' }}</h1>
        <p class="text-gray-500 mb-8 max-w-sm mx-auto">{{ $profilKampus->alamat_kampus ?? 'Sistem Informasi Akademik Terpadu - Kampus Digital' }}</p>

        @if ($errorMsg)
            <div class="mb-4 bg-red-50 text-red-600 p-3 rounded-lg text-sm flex items-center gap-2 justify-center">
                <x-icon name="alert-circle" class="w-4 h-4" />
                {{ $errorMsg }}
            </div>
        @endif

        @if ($isLockedOut)
            <div class="mb-6">
                <div class="p-4 bg-red-100 text-red-800 rounded-lg font-medium mb-4">
                    Anda telah gagal login 3 kali. Demi keamanan, akun Anda dikunci sementara. Silakan atur ulang password.
                </div>
                <button wire:click="goToReset" class="w-full bg-blue-600 border border-transparent text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition">
                    Lanjut Reset Password
                </button>
            </div>
        @else
            <form wire:submit="authenticate" class="space-y-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" wire:model="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CAPTCHA: {{ $captchaA }} + {{ $captchaB }} = ?</label>
                    <input type="number" wire:model="captchaAnswer" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none" required placeholder="Hasil penjumlahan">
                </div>

                <button type="submit" class="w-full flex items-center justify-center gap-3 bg-blue-600 border border-transparent text-white px-6 py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all active:scale-95 disabled:opacity-50" wire:loading.attr="disabled" wire:target="authenticate">
                    <span wire:loading.remove wire:target="authenticate">Masuk</span>
                    <span wire:loading wire:target="authenticate">Memproses...</span>
                </button>

                <div class="text-center mt-4">
                    <a href="{{ route('forgot-password') }}" class="text-sm text-blue-600 hover:text-blue-800">Lupa Password / Reset</a>
                </div>
            </form>
        @endif
    </div>
</div>

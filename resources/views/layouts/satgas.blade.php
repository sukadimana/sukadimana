<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? (($profil->satgas_nama ?? 'Satgas PPKS') . ' - ' . ($profil->nama_kampus ?? config('app.name'))) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    @php $profil = \App\Models\ProfilKampus::first(); @endphp

    <header class="bg-red-800 text-white sticky top-0 z-30 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('satgas.public.home') }}" class="flex items-center gap-2 font-bold text-lg">
                <x-icon name="shield" class="w-6 h-6" />
                <span>{{ $profil->satgas_nama ?? 'Satgas PPKS' }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium text-red-100">
                <a href="{{ route('satgas.public.home') }}" class="px-3 py-2 rounded-lg hover:bg-red-700 hover:text-white {{ request()->routeIs('satgas.public.home') ? 'text-white bg-red-700' : '' }}">Beranda</a>
                <a href="{{ route('satgas.public.berita.index') }}" class="px-3 py-2 rounded-lg hover:bg-red-700 hover:text-white {{ request()->routeIs('satgas.public.berita.*') ? 'text-white bg-red-700' : '' }}">Berita</a>
                <a href="{{ route('satgas.public.galeri') }}" class="px-3 py-2 rounded-lg hover:bg-red-700 hover:text-white {{ request()->routeIs('satgas.public.galeri') ? 'text-white bg-red-700' : '' }}">Galeri</a>
                @foreach (\App\Models\Page::channel('satgas_ppk')->published()->orderBy('menu_order')->get() as $navPage)
                    <a href="{{ route('satgas.public.halaman', $navPage->slug) }}" class="px-3 py-2 rounded-lg hover:bg-red-700 hover:text-white {{ request()->routeIs('satgas.public.halaman') && request()->route('slug') === $navPage->slug ? 'text-white bg-red-700' : '' }}">{{ $navPage->title }}</a>
                @endforeach
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('satgas.public.pengaduan') }}" class="px-4 py-2 bg-white hover:bg-red-50 text-red-800 text-sm font-bold rounded-lg transition-colors">Lapor Sekarang</a>
                <a href="{{ route('public.home') }}" class="hidden sm:inline px-3 py-2 text-xs font-medium text-red-100 hover:text-white">Website Kampus</a>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <p class="text-white font-bold text-lg mb-2">{{ $profil->satgas_nama ?? 'Satgas PPKS' }}</p>
                <p class="text-sm text-gray-400">{{ $profil->satgas_deskripsi ?? 'Satuan Tugas Pencegahan dan Penanganan Kekerasan Seksual di lingkungan kampus, sesuai Permendikbudristek No. 30 Tahun 2021.' }}</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-2">Kontak Pengaduan</p>
                <p class="text-sm text-gray-400">{{ $profil->satgas_email ?? '-' }}</p>
                <p class="text-sm text-gray-400">{{ $profil->satgas_telepon ?? '-' }}</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-2">Tautan</p>
                <a href="{{ route('satgas.public.pengaduan') }}" class="text-sm text-gray-400 hover:text-white block mb-1">Formulir Pengaduan</a>
                <a href="{{ route('public.home') }}" class="text-sm text-gray-400 hover:text-white block">Website Kampus Utama</a>
            </div>
        </div>
        <div class="border-t border-gray-800 text-center text-xs text-gray-500 py-4">
            &copy; {{ date('Y') }} {{ $profil->satgas_nama ?? 'Satgas PPKS' }} &middot; {{ $profil->nama_kampus ?? '' }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>

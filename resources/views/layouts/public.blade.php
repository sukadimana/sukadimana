<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? ($profil->nama_kampus ?? config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    @php $profil = \App\Models\ProfilKampus::first(); @endphp

    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('public.home') }}" class="flex items-center gap-2 font-bold text-lg text-blue-700">
                <x-icon name="book-open" class="w-6 h-6" />
                <span>{{ $profil->nama_kampus ?? 'SIAKAD Pro' }}</span>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium text-gray-600">
                <a href="{{ route('public.home') }}" class="px-3 py-2 rounded-lg hover:bg-gray-100 hover:text-blue-700 {{ request()->routeIs('public.home') ? 'text-blue-700' : '' }}">Beranda</a>
                <a href="{{ route('public.berita.index') }}" class="px-3 py-2 rounded-lg hover:bg-gray-100 hover:text-blue-700 {{ request()->routeIs('public.berita.*') ? 'text-blue-700' : '' }}">Berita</a>
                <a href="{{ route('public.program-studi') }}" class="px-3 py-2 rounded-lg hover:bg-gray-100 hover:text-blue-700 {{ request()->routeIs('public.program-studi') ? 'text-blue-700' : '' }}">Program Studi</a>
                <a href="{{ route('public.galeri') }}" class="px-3 py-2 rounded-lg hover:bg-gray-100 hover:text-blue-700 {{ request()->routeIs('public.galeri') ? 'text-blue-700' : '' }}">Galeri</a>
                @foreach (\App\Models\Page::channel('kampus')->published()->orderBy('menu_order')->get() as $navPage)
                    <a href="{{ route('public.halaman', $navPage->slug) }}" class="px-3 py-2 rounded-lg hover:bg-gray-100 hover:text-blue-700 {{ request()->routeIs('public.halaman') && request()->route('slug') === $navPage->slug ? 'text-blue-700' : '' }}">{{ $navPage->title }}</a>
                @endforeach
                <a href="{{ route('public.kontak') }}" class="px-3 py-2 rounded-lg hover:bg-gray-100 hover:text-blue-700 {{ request()->routeIs('public.kontak') ? 'text-blue-700' : '' }}">Kontak</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('satgas.public.home') }}" class="hidden sm:flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                    <x-icon name="shield" class="w-4 h-4" />
                    Satgas PPK
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">Portal SIAKAD</a>
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-gray-900 text-gray-300 mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <p class="text-white font-bold text-lg mb-2">{{ $profil->nama_kampus ?? 'SIAKAD Pro' }}</p>
                <p class="text-sm text-gray-400">{{ $profil->alamat_kampus ?? '' }}</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-2">Kontak</p>
                <p class="text-sm text-gray-400">{{ $profil->email ?? '-' }}</p>
                <p class="text-sm text-gray-400">{{ $profil->telepon ?? '-' }}</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-2">Tautan</p>
                <a href="{{ route('satgas.public.home') }}" class="text-sm text-gray-400 hover:text-white block mb-1">Satgas PPK</a>
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white block">Portal SIAKAD</a>
            </div>
        </div>
        <div class="border-t border-gray-800 text-center text-xs text-gray-500 py-4">
            &copy; {{ date('Y') }} {{ $profil->nama_kampus ?? 'SIAKAD Pro' }}. Seluruh hak cipta dilindungi.
        </div>
    </footer>

    @livewireScripts
</body>
</html>

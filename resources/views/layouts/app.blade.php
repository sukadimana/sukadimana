<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-50 flex">
        {{-- Mobile Sidebar Backdrop --}}
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-20 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 print:hidden"
        >
            <div class="h-16 flex items-center justify-center border-b border-gray-200 bg-blue-700">
                <div class="flex items-center gap-2 text-white font-bold text-xl">
                    <x-icon name="book-open" class="h-6 w-6" />
                    <span>SIAKAD Pro</span>
                </div>
            </div>

            <div class="flex flex-col h-[calc(100vh-64px)] overflow-hidden">
                <div class="p-4 flex-1 overflow-y-auto">
                    <div class="mb-6">
                        <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>
                        @include('layouts.partials.nav-item', ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'])

                        @auth
                            @if (auth()->user()->hasRole('admin', 'akademik'))
                                @include('layouts.partials.nav-item', ['route' => 'mahasiswa.index', 'icon' => 'users', 'label' => 'Manajemen Mahasiswa'])
                                @include('layouts.partials.nav-item', ['route' => 'prodi.index', 'icon' => 'book-open', 'label' => 'Manajemen Prodi'])
                                @include('layouts.partials.nav-item', ['route' => 'tahun-akademik.index', 'icon' => 'calendar', 'label' => 'Tahun Akademik'])
                                @include('layouts.partials.nav-item', ['route' => 'master-biaya.index', 'icon' => 'dollar', 'label' => 'Manajemen Biaya'])
                            @endif
                        @endauth
                    </div>

                    @auth
                        @if (auth()->user()->hasRole('admin', 'keuangan'))
                        <div class="mb-6">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Keuangan</p>
                            <p class="px-4 py-3 text-sm text-gray-400 italic">Segera hadir</p>
                        </div>
                        @endif

                        @if (auth()->user()->hasRole('admin'))
                        <div class="mb-6">
                            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pengaturan</p>
                            <p class="px-4 py-3 text-sm text-gray-400 italic">Segera hadir</p>
                        </div>
                        @endif
                    @endauth
                </div>

                <div class="p-4 border-t border-gray-100 bg-white">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                            <x-icon name="logout" class="w-5 h-5" />
                            <span>Keluar Sesi</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 shadow-sm z-10 print:hidden">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-md hover:bg-gray-100 text-gray-600">
                    <x-icon name="menu" class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-4 ml-auto">
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-semibold text-gray-800">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-medium">{{ auth()->user()->role?->label() ?? 'Anonymous' }}</span>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold border border-blue-200">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto p-6 print:p-0">
                <div class="max-w-7xl mx-auto print:max-w-none">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>

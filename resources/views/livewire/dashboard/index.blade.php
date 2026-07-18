<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Ringkasan Dashboard</h1>
        <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg border border-gray-200">
            TA {{ $activeTahunAkademik->nama ?? '(Belum Ada Tahun Akademik Aktif)' }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Mahasiswa</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</h3>
                <p class="text-xs mt-2 font-medium text-gray-400">{{ $activeStudents }} Aktif</p>
            </div>
            <div class="p-3 rounded-lg bg-blue-500"><x-icon name="users" class="w-6 h-6 text-white" /></div>
        </div>

        @if ($showFinancials)
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Dana Terkumpul</p>
                    <h3 class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalCollected, 0, ',', '.') }}</h3>
                    <p class="text-xs mt-2 font-medium text-gray-400">Rp {{ number_format($totalOutstanding, 0, ',', '.') }} Belum Lunas</p>
                </div>
                <div class="p-3 rounded-lg bg-emerald-500"><x-icon name="credit-card" class="w-6 h-6 text-white" /></div>
            </div>
        @endif

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Alumni Bekerja</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $totalAlumni > 0 ? round(($employedAlumni / $totalAlumni) * 100) . '%' : '0%' }}</h3>
                <p class="text-xs mt-2 font-medium text-gray-400">{{ $employedAlumni }} / {{ $totalAlumni }} Terlacak</p>
            </div>
            <div class="p-3 rounded-lg bg-violet-500"><x-icon name="dashboard" class="w-6 h-6 text-white" /></div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-start justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Lulusan</p>
                <h3 class="text-2xl font-bold text-gray-900">{{ $lulusanCount }}</h3>
                <p class="text-xs mt-2 font-medium text-gray-400">Siap Wisuda</p>
            </div>
            <div class="p-3 rounded-lg bg-orange-500"><x-icon name="graduation-cap" class="w-6 h-6 text-white" /></div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Statistik Mahasiswa</h2>
            <p class="text-sm text-gray-500">Distribusi S1 dan S2 berdasarkan program studi dan jenis kelamin</p>
        </div>
        <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-lg font-bold">
            Total Gabungan S1 & S2: <span class="text-xl ml-1">{{ $combinedTotal }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900">Jenjang S-1</h3>
                <span class="bg-gray-100 text-gray-800 text-sm font-bold px-3 py-1 rounded-full">{{ $s1Total }} Total</span>
            </div>
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-blue-900">Manajemen Pendidikan Islam (MPI)</h4>
                        <span class="font-bold text-blue-700">{{ $s1MPI }} Total</span>
                    </div>
                    <div class="flex gap-4 text-sm font-medium">
                        <div class="text-blue-600">Laki-laki: {{ $s1MPI_L }}</div>
                        <div class="text-pink-600">Perempuan: {{ $s1MPI_P }}</div>
                    </div>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-green-900">Ahwal Asy-Syakhshiyyah (AS)</h4>
                        <span class="font-bold text-green-700">{{ $s1AS }} Total</span>
                    </div>
                    <div class="flex gap-4 text-sm font-medium">
                        <div class="text-green-600">Laki-laki: {{ $s1AS_L }}</div>
                        <div class="text-pink-600">Perempuan: {{ $s1AS_P }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900">Jenjang S-2</h3>
                <span class="bg-gray-100 text-gray-800 text-sm font-bold px-3 py-1 rounded-full">{{ $s2Total }} Total</span>
            </div>
            <div class="space-y-4">
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-bold text-purple-900">Manajemen Pendidikan Islam (MPI)</h4>
                        <span class="font-bold text-purple-700">{{ $s2MPI }} Total</span>
                    </div>
                    <div class="flex gap-4 text-sm font-medium">
                        <div class="text-purple-600">Laki-laki: {{ $s2MPI_L }}</div>
                        <div class="text-pink-600">Perempuan: {{ $s2MPI_P }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            @if ($showFinancials)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex justify-between items-center">
                        <span>Kesehatan Keuangan (Tagihan)</span>
                        @if ($activeTahunAkademik)
                            <span class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-700 rounded-full">{{ $activeTahunAkademik->nama }}</span>
                        @endif
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-red-50 p-6 rounded-xl border border-red-100 flex flex-col justify-center items-center text-center">
                            <p class="text-red-600 font-bold mb-2">Total Tagihan Belum Terbayar</p>
                            <p class="text-3xl font-black text-red-700">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-xl border border-green-100 flex flex-col justify-center items-center text-center">
                            <p class="text-green-600 font-bold mb-2">Total Sudah Terbayar</p>
                            <p class="text-3xl font-black text-green-700">Rp {{ number_format($totalCollected, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($showDynamicWarnings)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Peringatan Terbaru</h3>
                    <div class="space-y-4">
                        @if ($overdueBills > 0)
                            <div class="flex gap-4 p-4 bg-red-50 text-red-700 rounded-lg border border-red-100">
                                <x-icon name="alert-circle" class="w-5 h-5 shrink-0" />
                                <div>
                                    <p class="font-bold">Batas Waktu SPP Melewati Jatuh Tempo</p>
                                    <p class="text-sm">Terdapat {{ $overdueBills }} tagihan yang belum lunas dan telah melewati batas pembayaran.</p>
                                </div>
                            </div>
                        @endif
                        @if ($pendingYudisiums > 0)
                            <div class="flex gap-4 p-4 bg-blue-50 text-blue-700 rounded-lg border border-blue-100">
                                <x-icon name="graduation-cap" class="w-5 h-5 shrink-0" />
                                <div>
                                    <p class="font-bold">Pendaftaran Yudisium</p>
                                    <p class="text-sm">Terdapat {{ $pendingYudisiums }} mahasiswa yang mendaftar yudisium dan belum divalidasi final ("Belum Siap Wisuda").</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        @if ($role === \App\Enums\UserRole::ADMIN)
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-full border-t-4 border-t-blue-600">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                        Aktivitas Sistem
                    </h3>
                    <div class="space-y-4 max-h-[380px] overflow-y-auto pr-2">
                        @forelse ($activityLogs as $log)
                            <div class="relative pl-4 border-l-2 border-gray-100 pb-4 last:border-0 last:pb-0">
                                <div class="absolute w-2.5 h-2.5 bg-blue-500 rounded-full -left-[5.5px] top-1 ring-4 ring-white"></div>
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $log->action }}</p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <span class="font-semibold text-blue-700">{{ $log->user_name }}</span>
                                        <span>•</span>
                                        <span>{{ $log->timestamp->translatedFormat('j F Y, H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-gray-400 italic text-sm">Belum ada log aktivitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

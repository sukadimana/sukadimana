<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Input Nilai KHS</h2>
        <p class="text-gray-500">Input nilai mata kuliah mahasiswa per tahun akademik</p>
    </div>

    @if (! $selectedMahasiswa)
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <label class="block text-sm font-medium text-gray-700 mb-1">Cari Mahasiswa</label>
            <div class="relative">
                <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                <input type="text" wire:model.live.debounce.300ms="searchMahasiswa" placeholder="Cari Nama atau NIM..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            @if ($searchMahasiswa !== '')
                <div class="mt-3 border border-gray-100 rounded-lg divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    @forelse ($mahasiswaResults as $m)
                        <button wire:click="selectMahasiswa({{ $m->id }})" class="w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $m->nama_lengkap }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $m->nim }} &middot; {{ $m->prodi->nama_prodi ?? '-' }}</p>
                            </div>
                            <span class="text-xs text-gray-400">Semester {{ $m->semester_saat_ini }}</span>
                        </button>
                    @empty
                        <div class="p-4 text-center text-gray-500 text-sm">Tidak ditemukan.</div>
                    @endforelse
                </div>
            @endif
        </div>
    @else
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $selectedMahasiswa->nama_lengkap }}</h3>
                    <p class="text-sm text-gray-500 font-mono">{{ $selectedMahasiswa->nim }} &middot; {{ $selectedMahasiswa->prodi->nama_prodi ?? '-' }}</p>
                </div>
                <button wire:click="clearSelection" class="text-sm text-blue-600 hover:underline">Ganti Mahasiswa</button>
            </div>

            @if ($saveMessage)
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg font-medium text-sm">{{ $saveMessage }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Akademik</label>
                    <select wire:model.live="selectedTahunAkademikId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach ($tahunAkademiks as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Semester Mata Kuliah</label>
                    <select wire:model.live="filterSemester" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        @for ($i = 1; $i <= 14; $i++)
                            <option value="{{ $i }}">Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            @if ($mataKuliahs->isEmpty())
                <div class="py-8 text-center text-gray-500 italic bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                    Belum ada mata kuliah untuk prodi & semester ini. Tambahkan di menu Mata Kuliah.
                </div>
            @else
                <form wire:submit="saveGrades" class="space-y-4">
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-gray-600">Kode</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600">Mata Kuliah</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 text-center">SKS</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 text-center w-32">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($mataKuliahs as $mk)
                                    <tr>
                                        <td class="px-4 py-3 font-mono text-gray-600">{{ $mk->kode_mk }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $mk->nama_mk }}</td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $mk->sks }}</td>
                                        <td class="px-4 py-3">
                                            <select wire:model="grades.{{ $mk->id }}" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                                <option value="">- Belum Dinilai -</option>
                                                @foreach ($nilaiOptions as $huruf)
                                                    <option value="{{ $huruf }}">{{ $huruf }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors shadow-sm">
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            @endif
        </div>
    @endif
</div>

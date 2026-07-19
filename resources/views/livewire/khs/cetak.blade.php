<div class="space-y-6">
    <div class="print:hidden">
        <h2 class="text-2xl font-bold text-gray-900">Cetak KHS (Kartu Hasil Studi)</h2>
        <p class="text-gray-500">Cari mahasiswa dan pilih tahun akademik untuk mencetak KHS</p>
    </div>

    @if (! $selectedMahasiswa)
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 print:hidden">
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
                        </button>
                    @empty
                        <div class="p-4 text-center text-gray-500 text-sm">Tidak ditemukan.</div>
                    @endforelse
                </div>
            @endif
        </div>
    @else
        <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between print:hidden">
            <div class="flex items-center gap-4">
                <select wire:model.live="selectedTahunAkademikId" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                    @foreach ($tahunAkademiks as $ta)
                        <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
                    @endforeach
                </select>
                <button wire:click="clearSelection" class="text-sm text-blue-600 hover:underline">Ganti Mahasiswa</button>
            </div>
            <button onclick="window.print()" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors shadow-sm">
                <x-icon name="printer" class="w-5 h-5" />
                Cetak KHS
            </button>
        </div>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 print:shadow-none print:border-0 print:rounded-none">
            {{-- Letterhead --}}
            <div class="flex items-center gap-4 border-b-2 border-gray-800 pb-4 mb-6">
                @if ($profilKampus?->logo_url)
                    <img src="{{ $profilKampus->logo_url }}" alt="Logo" class="h-16 object-contain">
                @endif
                <div>
                    <h1 class="text-xl font-bold text-gray-900 uppercase">{{ $profilKampus->nama_kampus ?? 'SIAKAD Pro' }}</h1>
                    <p class="text-sm text-gray-600">{{ $profilKampus->alamat_kampus ?? '' }}</p>
                    <p class="text-xs text-gray-500">{{ $profilKampus->telepon ?? '' }} {{ $profilKampus->email ? '· '.$profilKampus->email : '' }}</p>
                </div>
            </div>

            <h2 class="text-center font-bold text-lg uppercase underline mb-6">Kartu Hasil Studi (KHS)</h2>

            <div class="grid grid-cols-2 gap-2 text-sm mb-6">
                <div class="flex"><span class="w-32 text-gray-500">Nama</span><span>: {{ $selectedMahasiswa->nama_lengkap }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Tahun Akademik</span><span>: {{ $tahunAkademiks->firstWhere('id', $selectedTahunAkademikId)?->nama }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">NIM</span><span>: {{ $selectedMahasiswa->nim }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Semester</span><span>: {{ $selectedMahasiswa->semester_saat_ini }}</span></div>
                <div class="flex"><span class="w-32 text-gray-500">Program Studi</span><span>: {{ $selectedMahasiswa->prodi->nama_prodi ?? '-' }}</span></div>
            </div>

            <table class="w-full text-sm border-collapse mb-6">
                <thead>
                    <tr class="border-y-2 border-gray-800">
                        <th class="py-2 text-left font-bold">Kode</th>
                        <th class="py-2 text-left font-bold">Mata Kuliah</th>
                        <th class="py-2 text-center font-bold">SKS</th>
                        <th class="py-2 text-center font-bold">Nilai</th>
                        <th class="py-2 text-center font-bold">Bobot</th>
                        <th class="py-2 text-center font-bold">SKS x Bobot</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nilaiSemester as $n)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-mono">{{ $n->mataKuliah->kode_mk }}</td>
                            <td class="py-2">{{ $n->mataKuliah->nama_mk }}</td>
                            <td class="py-2 text-center">{{ $n->mataKuliah->sks }}</td>
                            <td class="py-2 text-center font-bold">{{ $n->nilai_huruf }}</td>
                            <td class="py-2 text-center">{{ number_format($n->bobot, 2) }}</td>
                            <td class="py-2 text-center">{{ number_format($n->mataKuliah->sks * $n->bobot, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-500 italic">Belum ada nilai untuk tahun akademik ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4 text-center print:bg-white print:border print:border-gray-400">
                    <p class="text-xs text-gray-500 uppercase font-bold">IP Semester (IPS)</p>
                    <p class="text-2xl font-black text-gray-900">{{ number_format($ips['ip'], 2) }}</p>
                    <p class="text-xs text-gray-400">{{ $ips['sks'] }} SKS</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center print:bg-white print:border print:border-gray-400">
                    <p class="text-xs text-gray-500 uppercase font-bold">IP Kumulatif (IPK)</p>
                    <p class="text-2xl font-black text-gray-900">{{ number_format($ipk['ip'], 2) }}</p>
                    <p class="text-xs text-gray-400">{{ $ipk['sks'] }} SKS total</p>
                </div>
            </div>

            <div class="flex justify-end">
                <div class="text-center text-sm">
                    <p>{{ now()->translatedFormat('j F Y') }}</p>
                    <p class="mt-16 font-bold underline">{{ $profilKampus->petugas_pembayaran ?? 'Kepala Program Studi' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laporan Keuangan</h2>
            <p class="text-gray-500">Ekspor data pembayaran dan filter tingkat/semester</p>
        </div>
        <button wire:click="exportCsv" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-bold shadow-sm flex items-center gap-2 transition">
            <x-icon name="file-text" class="w-4 h-4" />
            Export CSV (Excel)
        </button>
    </div>

    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <h3 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wider">Filter Laporan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Status Pembayaran</label>
                <select wire:model.live="filterType" class="w-full border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-blue-500 py-2 px-3 focus:outline-none focus:bg-white border">
                    <option value="ALL">Semua Status</option>
                    <option value="LUNAS">Sudah Lunas</option>
                    <option value="TUNGGAKAN">Ada Tunggakan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Tingkatan S1/S2</label>
                <select wire:model.live="filterJenjang" class="w-full border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-blue-500 py-2 px-3 focus:outline-none focus:bg-white border">
                    <option value="ALL">Semua S1 & S2</option>
                    <option value="S1">S1</option>
                    <option value="S2">S2</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Program Studi</label>
                <select wire:model.live="filterProdi" class="w-full border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-blue-500 py-2 px-3 focus:outline-none focus:bg-white border">
                    <option value="ALL">Semua Program Studi</option>
                    @foreach ($prodis as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Kategori (Master Biaya)</label>
                <select wire:model.live="filterMasterBiaya" class="w-full border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-blue-500 py-2 px-3 focus:outline-none focus:bg-white border">
                    <option value="ALL">Semua Kategori</option>
                    @foreach ($masterBiayas as $mb)
                        <option value="{{ $mb->id }}">{{ $mb->nama_biaya }} {{ $mb->angkatan ? "({$mb->angkatan})" : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Semester Tagihan</label>
                <select wire:model.live="filterSemester" class="w-full border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-blue-500 py-2 px-3 focus:outline-none focus:bg-white border">
                    <option value="ALL">Semua Semester</option>
                    @foreach ($semesterOpts as $s)
                        <option value="{{ $s }}">Semester {{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-lg"><x-icon name="dollar" class="w-6 h-6" /></div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Tagihan Filter</p>
                <p class="text-lg font-black text-gray-900">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-green-100 text-green-600 rounded-lg"><x-icon name="dollar" class="w-6 h-6" /></div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Dibayar Filter</p>
                <p class="text-lg font-black text-green-600">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="p-3 bg-red-100 text-red-600 rounded-lg"><x-icon name="dollar" class="w-6 h-6" /></div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Tunggakan Filter</p>
                <p class="text-lg font-black text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Mahasiswa</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Tagihan</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-right">Total</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-right">Dibayar</th>
                    <th class="px-4 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-right">Sisa</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($filteredTags as $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <p class="font-bold text-gray-900">{{ $t->mahasiswa->nama_lengkap ?? '-' }}</p>
                            <p class="text-xs text-gray-500 font-mono">{{ $t->mahasiswa->nim ?? '-' }}</p>
                            <p class="text-[10px] text-blue-600 font-bold">{{ $t->mahasiswa->jenjang ?? '-' }} - Smt {{ $t->mahasiswa->semester_saat_ini ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $t->nama_tagihan_snapshot }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium text-right">Rp {{ number_format($t->total_harus_bayar, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-green-600 font-medium text-right">Rp {{ number_format($t->total_sudah_bayar, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-right text-red-600">Rp {{ number_format($t->sisa_tagihan, 0, ',', '.') }}</td>
                        <td class="px-6 py-3 text-center">
                            <span class="px-2 py-1 rounded text-[10px] font-bold {{ $t->status === 'LUNAS' ? 'bg-green-100 text-green-700' : ($t->status === 'CICILAN' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ str_replace('_', ' ', $t->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Tidak ada data tagihan sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($totalCount > 100)
            <div class="p-4 text-center text-sm text-gray-500 italic bg-gray-50">Menampilkan 100 data pertama dari {{ $totalCount }}. Ekspor ke CSV untuk melihat semua data.</div>
        @endif
    </div>
</div>

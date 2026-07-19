<div class="space-y-6">
    <div class="print:hidden">
        <h1 class="text-2xl font-bold text-gray-900">Cetak Tanggungan Biaya</h1>
        <p class="text-gray-500">Cetak surat keterangan tanggungan untuk masing-masing mahasiswa.</p>
    </div>

    @if (! $selectedStudent)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 print:hidden">
            <div class="mb-6 relative">
                <x-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari berdasarkan NIM atau Nama Mahasiswa..." class="w-full pl-12 pr-4 py-3 border-2 border-gray-100 bg-gray-50 rounded-xl focus:border-blue-500 focus:bg-white outline-none transition-all">
            </div>

            @if (strlen($search) > 2)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($results as $student)
                        <button wire:click="selectStudent({{ $student->id }})" class="p-4 rounded-xl border border-gray-200 text-left hover:border-blue-500 hover:bg-blue-50/50 transition-colors bg-white flex flex-col gap-2">
                            <div>
                                <h4 class="font-bold text-gray-900 leading-tight">{{ $student->nama_lengkap }}</h4>
                                <p class="font-mono text-sm text-gray-500">{{ $student->nim }}</p>
                            </div>
                            <div class="text-xs font-semibold text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded">{{ $student->prodi->nama_prodi ?? 'Unknown Prodi' }}</div>
                        </button>
                    @empty
                        <div class="col-span-full text-center py-8 text-gray-500">Tidak ada mahasiswa ditemukan.</div>
                    @endforelse
                </div>
            @endif
        </div>
    @else
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex justify-between items-center print:hidden">
            <div class="flex flex-col gap-1">
                <h2 class="font-bold text-xl text-gray-900">{{ $selectedStudent->nama_lengkap }}</h2>
                <div class="flex items-center gap-3">
                    <span class="text-gray-600 font-mono text-sm px-2 py-0.5 bg-gray-100 rounded">{{ $selectedStudent->nim }}</span>
                    @if ($isLunas)
                        <span class="flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded border border-green-200">
                            <x-icon name="check-circle" class="w-3.5 h-3.5" /> LUNAS
                        </span>
                    @else
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded border border-red-200">SISA TANGGUNGAN: Rp {{ number_format($totalSisa, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <button wire:click="clearSelection" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold transition">Ganti Mahasiswa</button>
                <button wire:click="$set('showCetakModal', true)" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition flex items-center gap-2 shadow-sm">
                    <x-icon name="printer" class="w-4 h-4" />
                    Preview & Cetak
                </button>
            </div>
        </div>
    @endif

    @if ($showCetakModal && $selectedStudent)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-500/80 print:bg-white print:p-0 print:relative print:inset-auto">
            <div class="bg-white rounded-xl w-full max-w-4xl relative z-10 shadow-2xl overflow-hidden print:shadow-none print:rounded-none flex flex-col max-h-[90vh]">
                <div class="flex-1 overflow-y-auto print:overflow-visible">
                    <div class="p-8 bg-white text-black max-w-[21cm] mx-auto print:mx-0 print:p-0 relative" style="min-height: 29.7cm;">
                        <div class="flex justify-between items-start border-b-2 border-gray-800 pb-4 mb-6">
                            <div class="flex items-center gap-4">
                                @if ($profilKampus?->logo_url)
                                    <img src="{{ $profilKampus->logo_url }}" alt="Logo" class="w-20 h-20 object-contain">
                                @else
                                    <div class="w-20 h-20 border-2 border-gray-800 rounded-full flex items-center justify-center font-bold text-gray-400 text-xs">LOGO</div>
                                @endif
                                <div>
                                    <h1 class="font-bold text-xl leading-tight uppercase">{{ $profilKampus->nama_kampus ?? 'SIAKAD Pro' }}</h1>
                                    <p class="text-xs mt-1">{{ $profilKampus->alamat_kampus ?? '' }}</p>
                                    <p class="text-xs">Telp: {{ $profilKampus->telepon ?? '-' }} | Email: {{ $profilKampus->email ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <h2 class="text-center font-bold text-lg underline mb-6 uppercase tracking-wider">Surat Keterangan Tanggungan Pembayaran</h2>

                        <table class="w-full text-sm mb-6 relative z-20">
                            <tbody>
                                <tr>
                                    <td class="w-40 py-1 font-semibold">Nama Mahasiswa</td>
                                    <td class="w-4">:</td>
                                    <td>{{ $selectedStudent->nama_lengkap }}</td>
                                    <td class="w-40 py-1 font-semibold text-right pr-4">Tanggal Dicetak</td>
                                    <td class="w-4">:</td>
                                    <td class="text-right">{{ now()->translatedFormat('j F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-semibold">NIM</td>
                                    <td>:</td>
                                    <td>{{ $selectedStudent->nim }}</td>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td class="py-1 font-semibold">Program Studi</td>
                                    <td>:</td>
                                    <td>{{ $selectedStudent->prodi->nama_prodi ?? '-' }} ({{ $selectedStudent->jenjang ?? '-' }})</td>
                                    <td colspan="3"></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="relative">
                            @if ($isLunas)
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-0 overflow-hidden">
                                    <div class="text-[120px] font-black text-gray-300/40 -rotate-[30deg] tracking-widest whitespace-nowrap select-none">LUNAS</div>
                                </div>
                            @endif

                            @if ($studentTagihans->isNotEmpty())
                                <table class="w-full text-sm border-collapse border border-gray-300 mb-8 relative z-10 bg-white/60">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="border border-gray-300 p-2 text-left">No</th>
                                            <th class="border border-gray-300 p-2 text-left">Deskripsi Tagihan</th>
                                            <th class="border border-gray-300 p-2 text-right">Nominal Awal</th>
                                            <th class="border border-gray-300 p-2 text-right">Potongan</th>
                                            <th class="border border-gray-300 p-2 text-right">Sudah Dibayar</th>
                                            <th class="border border-gray-300 p-2 text-right">Sisa Tanggungan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($studentTagihans as $i => $tagihan)
                                            <tr>
                                                <td class="border border-gray-300 p-2 text-center">{{ $i + 1 }}</td>
                                                <td class="border border-gray-300 p-2">
                                                    {{ $tagihan->nama_tagihan_snapshot }} {{ $tagihan->semester ? "(Semester {$tagihan->semester})" : '' }}
                                                    @if ($tagihan->jatuh_tempo)
                                                        <div class="text-[10px] text-gray-500">Jatuh Tempo: {{ $tagihan->jatuh_tempo->translatedFormat('j F Y') }}</div>
                                                    @endif
                                                </td>
                                                <td class="border border-gray-300 p-2 text-right font-mono text-xs">Rp {{ number_format($tagihan->nominal_awal, 0, ',', '.') }}</td>
                                                <td class="border border-gray-300 p-2 text-right font-mono text-xs">Rp {{ number_format($tagihan->potongan_beasiswa, 0, ',', '.') }}</td>
                                                <td class="border border-gray-300 p-2 text-right font-mono text-xs text-green-600">Rp {{ number_format($tagihan->total_sudah_bayar, 0, ',', '.') }}</td>
                                                <td class="border border-gray-300 p-2 text-right font-mono text-xs font-bold text-red-600">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="bg-gray-50 font-bold">
                                            <td colspan="5" class="border border-gray-300 p-2 text-right">TOTAL TANGGUNGAN</td>
                                            <td class="border border-gray-300 p-2 text-right font-mono text-sm text-red-600">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <div class="py-12 flex flex-col items-center justify-center border border-gray-300 rounded text-center relative z-10 bg-white/60 mb-8">
                                    <p class="text-gray-500 italic font-semibold">Belum ada tagihan.</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-end pr-12 mt-12 mb-8 relative z-20">
                            <div class="text-center">
                                <p class="text-sm mb-16">Bagian Keuangan,</p>
                                <p class="font-bold underline text-sm">{{ $profilKampus->petugas_pembayaran ?? $petugas }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-100 flex justify-end gap-3 print:hidden border-t border-gray-200">
                    <button onclick="window.print()" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 transition flex items-center gap-2">
                        <x-icon name="printer" class="w-4 h-4" />
                        Cetak Dokumen
                    </button>
                    <button wire:click="$set('showCetakModal', false)" class="px-6 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>

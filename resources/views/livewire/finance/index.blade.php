<div class="space-y-6">
    <div class="print:hidden">
        <h2 class="text-2xl font-bold text-gray-900">Sistem Pembayaran Satu Pintu</h2>
        <p class="text-gray-500">Penagihan terpusat dan pelacakan pembayaran</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 print:hidden">
        {{-- Left: Bill List --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col gap-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="relative flex-1">
                        <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-4.5 h-4.5" />
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIM atau Nama Mahasiswa..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm">
                    </div>
                    <div class="flex gap-2">
                        <select wire:model.live="filterJenjang" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="all">S1/S2</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                        </select>
                        <select wire:model.live="filterStatus" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="all">Status Bayar</option>
                            <option value="BELUM_LUNAS">Belum Lunas</option>
                            <option value="CICILAN">Cicilan</option>
                            <option value="LUNAS">Lunas</option>
                        </select>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row gap-4">
                    <select wire:model.live="filterProdi" class="flex-1 px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        <option value="all">Semua Program Studi</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->nama_prodi }} ({{ $prodi->jenjang }})</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterKategori" class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                        <option value="all">Kategori/Beasiswa</option>
                        <option value="NONE">TIDAK ADA BEASISWA / PENUH</option>
                        @foreach ($kategoriBeasiswas as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center flex-wrap gap-2">
                    <h3 class="font-bold text-gray-700">Tagihan Aktif ({{ $tagihans->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                    @forelse ($tagihans as $tagihan)
                        <div wire:click="selectTagihan({{ $tagihan->id }})" class="p-4 cursor-pointer transition-all hover:bg-blue-50 border-l-4 {{ $selectedTagihan?->id === $tagihan->id ? 'bg-blue-50 border-blue-500' : 'border-transparent' }}">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $tagihan->nama_tagihan_snapshot ?: 'Tagihan Tanpa Nama' }} {{ $tagihan->semester ? "(Semester {$tagihan->semester})" : '' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $tagihan->mahasiswa->nama_lengkap ?? 'Mahasiswa Tidak Ditemukan' }} <span class="text-gray-400">| {{ $tagihan->mahasiswa->nim ?? '' }}</span></p>
                                    @if ($tagihan->jatuh_tempo)
                                        <p class="text-xs mt-1 font-bold {{ $tagihan->jatuh_tempo->isPast() && $tagihan->status !== 'LUNAS' ? 'text-red-500' : 'text-gray-500' }}">
                                            Jatuh Tempo: {{ $tagihan->jatuh_tempo->translatedFormat('j F Y') }}
                                        </p>
                                    @endif
                                </div>
                                <span class="px-2 py-1 rounded-md text-xs font-bold border {{ match($tagihan->status) { 'LUNAS' => 'bg-green-100 text-green-800 border-green-200', 'CICILAN' => 'bg-yellow-100 text-yellow-800 border-yellow-200', default => 'bg-red-100 text-red-800 border-red-200' } }}">
                                    {{ str_replace('_', ' ', $tagihan->status) }}
                                </span>
                            </div>
                            <div class="grid grid-cols-3 gap-4 text-sm mt-3">
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider">Total Tagihan</p>
                                    <p class="font-medium text-gray-800">Rp {{ number_format($tagihan->total_harus_bayar, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider">Sudah Bayar</p>
                                    <p class="font-medium text-green-600">Rp {{ number_format($tagihan->total_sudah_bayar, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs uppercase tracking-wider">Sisa Tagihan</p>
                                    <p class="font-bold {{ $tagihan->sisa_tagihan > 0 ? 'text-red-600' : 'text-gray-600' }}">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 italic">Belum ada tagihan aktif.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Detail --}}
        <div class="lg:col-span-1">
            @if ($selectedTagihan)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">Rincian & Logika</h3>
                        @if ($relatedPayments->isEmpty())
                            <button wire:click="confirmDeleteTagihan" class="text-white hover:bg-red-700 bg-red-600 font-bold px-3 py-1.5 rounded-lg transition text-xs">Hapus Tagihan</button>
                        @endif
                    </div>

                    <div class="space-y-3 text-sm">
                        @if ($selectedTagihan->jatuh_tempo)
                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded text-red-600 font-medium">
                                <span>Jatuh Tempo</span>
                                <span>{{ $selectedTagihan->jatuh_tempo->translatedFormat('j F Y') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Nominal Awal</span>
                            <span class="font-mono">Rp {{ number_format($selectedTagihan->nominal_awal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Potongan Beasiswa</span>
                            <span class="font-mono">- Rp {{ number_format($selectedTagihan->potongan_beasiswa, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-gray-200 my-2"></div>
                        <div class="flex justify-between font-bold text-gray-800">
                            <span>Total Harus Bayar</span>
                            <span>Rp {{ number_format($selectedTagihan->total_harus_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Riwayat Pembayaran</h4>
                        @if ($relatedPayments->isNotEmpty())
                            <div class="space-y-3">
                                @foreach ($relatedPayments as $pay)
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-xs font-mono text-gray-500">{{ $pay->nomor_transaksi }}</span>
                                            <span class="text-xs font-bold text-blue-600">{{ $pay->metode_bayar }}</span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">{{ $pay->tanggal_bayar->translatedFormat('j F Y') }}</span>
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-gray-800">Rp {{ number_format($pay->jumlah_bayar, 0, ',', '.') }}</span>
                                                <button wire:click="reprint({{ $pay->id }})" class="p-1 hover:bg-white rounded border border-transparent hover:border-gray-200 text-blue-600 transition-all" title="Cetak Kwitansi">
                                                    <x-icon name="printer" class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic text-center py-4 bg-gray-50 rounded-lg">Belum ada pembayaran tercatat.</p>
                        @endif
                    </div>

                    @if ($selectedTagihan->sisa_tagihan > 0)
                        <button wire:click="openPaymentModal" class="w-full mt-6 bg-blue-600 text-white py-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-100 flex items-center justify-center gap-2 active:scale-95">
                            <x-icon name="credit-card" class="w-4.5 h-4.5" />
                            Catat Pembayaran Baru
                        </button>
                    @endif
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center bg-gray-50 rounded-xl border border-dashed border-gray-300 p-8 text-center min-h-[300px]">
                    <p class="text-gray-500 font-medium">Pilih tagihan untuk melihat rincian pembayaran dan logika perhitungan.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($deleteConfirmId)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('deleteConfirmId', null)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 flex flex-col p-6 text-center">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Tagihan</h3>
                <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus tagihan ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('deleteConfirmId', null)" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg">Batal</button>
                    <button wire:click="deleteTagihan" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if ($isPaymentModalOpen && $selectedTagihan)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="$set('isPaymentModalOpen', false)"></div>
            <div class="bg-white rounded-2xl w-full max-w-md relative z-10 shadow-2xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-blue-600 text-white">
                    <div>
                        <h3 class="text-xl font-bold">Catat Pembayaran</h3>
                        <p class="text-blue-100 text-sm">Tagihan: {{ $selectedTagihan->nama_tagihan_snapshot }} {{ $selectedTagihan->semester ? "(Semester {$selectedTagihan->semester})" : '' }}</p>
                    </div>
                    <button wire:click="$set('isPaymentModalOpen', false)" class="p-1 hover:bg-white/10 rounded-full transition-colors">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <form wire:submit="submitPayment" class="p-6 space-y-4">
                    @if ($errorMessage)
                        <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm">{{ $errorMessage }}</div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar (Rp)</label>
                        <input type="number" wire:model="paymentAmount" max="{{ $selectedTagihan->sisa_tagihan }}" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none font-bold text-xl text-blue-600">
                        <p class="mt-1 text-xs text-gray-400">Sisa Tagihan: Rp {{ number_format($selectedTagihan->sisa_tagihan, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" wire:click="$set('paymentMethod', 'TUNAI')" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 transition-all font-semibold {{ $paymentMethod === 'TUNAI' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-100 text-gray-400' }}">
                                Tunai
                            </button>
                            <button type="button" wire:click="$set('paymentMethod', 'TRANSFER')" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 transition-all font-semibold {{ $paymentMethod === 'TRANSFER' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-100 text-gray-400' }}">
                                Transfer
                            </button>
                            <button type="button" wire:click="$set('paymentMethod', 'PAYMENT')" class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl border-2 transition-all font-semibold {{ $paymentMethod === 'PAYMENT' ? 'border-blue-600 bg-blue-50 text-blue-600' : 'border-gray-100 text-gray-400' }}">
                                Payment
                            </button>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold flex items-center justify-center gap-3 hover:bg-blue-700 transition shadow-lg shadow-blue-100">
                            <x-icon name="check-circle" class="w-5 h-5" />
                            Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Receipt Modal --}}
    @if ($receipt)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 print:relative print:inset-auto print:p-0">
            <div class="absolute inset-0 bg-black/70 print:hidden" wire:click="$set('showReceiptId', null)"></div>
            <div class="bg-white rounded-xl w-full max-w-[21cm] relative z-10 shadow-2xl overflow-hidden print:shadow-none print:rounded-none">
                <div class="p-8 bg-white text-black">
                    <div class="flex justify-between items-start border-b-2 border-gray-800 pb-2 mb-4">
                        <div class="flex items-center gap-4 flex-1 pl-8 relative">
                            <div class="absolute left-0 top-0 bottom-0 flex items-center">
                                @if ($profilKampus?->logo_url)
                                    <img src="{{ $profilKampus->logo_url }}" alt="Logo" class="w-[70px] h-[70px] object-contain">
                                @else
                                    <div class="w-[70px] h-[70px] border-2 border-gray-800 rounded-full flex items-center justify-center font-bold text-gray-500 text-[10px]">LOGO</div>
                                @endif
                            </div>
                            <div class="text-center w-full pr-12">
                                <h1 class="font-bold text-lg leading-tight uppercase tracking-wider">{{ $profilKampus->nama_kampus ?? 'SIAKAD Pro' }}</h1>
                                <p class="text-[10px] leading-tight mt-1">{{ $profilKampus->alamat_kampus ?? '' }}</p>
                                <p class="text-[10px] leading-tight mt-1">Telepon : {{ $profilKampus->telepon ?? '-' }}</p>
                            </div>
                        </div>
                        @if ($isReprint)
                            <div class="border border-red-500 text-red-500 px-4 py-2 font-bold tracking-widest uppercase whitespace-nowrap rounded-sm text-base">DUPLIKAT</div>
                        @endif
                    </div>

                    <h2 class="text-center font-bold text-sm underline mb-4 tracking-wider">KWITANSI PEMBAYARAN</h2>

                    <div class="space-y-1.5 text-xs px-16">
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <span>No. Kwitansi</span><span>:</span><span class="font-mono uppercase">{{ $receipt->nomor_transaksi }}</span>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <span>Nama</span><span>:</span><span class="font-bold uppercase">{{ $receipt->tagihan->mahasiswa->nama_lengkap ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <span>Program Studi / Semester</span><span>:</span><span>{{ $receipt->tagihan->mahasiswa->prodi->nama_prodi ?? '-' }} / Semester {{ $receipt->tagihan->mahasiswa->semester_saat_ini ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <span>Program Pilihan</span><span>:</span><span>{{ $receipt->tagihan->nama_tagihan_snapshot ?? '-' }} {{ $receipt->tagihan->semester ? "(Semester {$receipt->tagihan->semester})" : '' }}</span>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr] mt-2">
                            <span>Metode Pembayaran</span><span>:</span><span class="font-bold">{{ $receipt->metode_bayar }}</span>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <span>Jumlah (Rp)</span><span>:</span><span class="font-bold text-sm">Rp {{ number_format($receipt->jumlah_bayar, 0, ',', '.') }}</span>
                        </div>
                        <div class="grid grid-cols-[140px_10px_1fr]">
                            <span>Tanggal</span><span>:</span><span>{{ $receipt->tanggal_bayar->translatedFormat('j F Y') }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-end mt-8 px-12 mb-2">
                        <p class="text-[10px] italic text-red-600 font-bold max-w-[50%] mr-12 pb-2">Simpan bukti pembayaran ini jangan sampai hilang. Dicetak secara sistem.</p>
                        <div class="text-center flex flex-col items-center pr-8">
                            <span class="text-xs mb-10 text-gray-800">Petugas,</span>
                            <span class="text-sm font-bold underline text-gray-900">{{ $profilKampus->petugas_pembayaran ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-gray-50 flex gap-3 print:hidden">
                    <button onclick="window.print()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-blue-700 transition">
                        <x-icon name="printer" class="w-4.5 h-4.5" />
                        Cetak Sekarang
                    </button>
                    <button wire:click="$set('showReceiptId', null)" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-50 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

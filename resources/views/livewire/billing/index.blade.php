<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Penagihan (Generate Tagihan)</h2>
        <p class="text-gray-500">Bangkitkan tagihan biaya perkuliahan mahasiswa</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
        <label class="text-sm font-bold text-gray-700 w-1/4">Global: Batas Waktu Pembayaran (Jatuh Tempo)</label>
        <input type="date" wire:model="jatuhTempoDate" class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
        <p class="text-xs text-gray-400 w-1/4">Akan diterapkan ke semua tagihan yang dibangkitkan.</p>
    </div>

    <div class="flex gap-2 p-1 bg-gray-100 rounded-xl w-fit">
        <button wire:click="$set('activeTab', 'SINGLE')" class="flex items-center gap-2 px-6 py-2 rounded-lg font-bold transition-all {{ $activeTab === 'SINGLE' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <x-icon name="plus" class="w-4 h-4" />
            Per Individu
        </button>
        <button wire:click="$set('activeTab', 'BATCH')" class="flex items-center gap-2 px-6 py-2 rounded-lg font-bold transition-all {{ $activeTab === 'BATCH' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
            <x-icon name="users" class="w-4 h-4" />
            Batch Generate
        </button>
    </div>

    @if ($successMessage)
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
            <p class="font-medium">{{ $successMessage }}</p>
        </div>
    @endif
    @if ($errorMessage)
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <x-icon name="alert-circle" class="w-5 h-5 shrink-0" />
            <p class="font-medium">{{ $errorMessage }}</p>
        </div>
    @endif

    @if ($activeTab === 'SINGLE')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-5 space-y-4">
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center gap-3">
                    <x-icon name="search" class="text-gray-400 w-5 h-5" />
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari NIM atau Nama..." class="flex-1 outline-none text-sm">
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-h-[600px] flex flex-col">
                    <div class="p-4 border-b border-gray-100 bg-gray-50 font-semibold text-gray-700 text-sm">Daftar Mahasiswa</div>
                    <div class="overflow-y-auto divide-y divide-gray-100">
                        @foreach ($students as $student)
                            <button wire:click="selectStudent({{ $student->id }})" class="w-full text-left p-4 hover:bg-blue-50 transition-colors flex items-center gap-4 {{ $selectedStudent?->id === $student->id ? 'bg-blue-50 border-r-4 border-blue-600' : '' }}">
                                <div class="h-10 w-10 shrink-0 rounded-full flex items-center justify-center font-bold {{ $selectedStudent?->id === $student->id ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-600' }}">
                                    {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold truncate {{ $selectedStudent?->id === $student->id ? 'text-blue-700' : 'text-gray-900' }}">{{ $student->nama_lengkap }}</p>
                                    <p class="text-[10px] text-gray-500 font-mono italic">{{ $student->jenjang }} &middot; {{ $student->prodi->nama_prodi ?? '-' }} &middot; Sem {{ $student->semester_saat_ini }}</p>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                @if ($selectedStudent)
                    <div class="space-y-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <div class="flex items-start gap-6">
                                <div class="h-20 w-20 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-blue-200">
                                    {{ strtoupper(substr($selectedStudent->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $selectedStudent->nama_lengkap }}</h3>
                                    <p class="text-gray-500 mb-2">{{ $selectedStudent->nim }} &middot; {{ $selectedStudent->prodi->nama_prodi ?? '-' }}</p>
                                    <div class="flex gap-2">
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-wider">{{ $selectedStudent->jenjang }}</span>
                                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] font-bold uppercase tracking-wider">Semester {{ $selectedStudent->semester_saat_ini }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-6 border-b border-gray-100 flex items-center gap-3 bg-gray-50">
                                <x-icon name="file-text" class="w-5 h-5 text-amber-600" />
                                <h4 class="font-bold text-gray-900">Terbitkan Tagihan Baru</h4>
                            </div>
                            <div class="p-6 grid gap-4">
                                @forelse ($availableBiayas as $mb)
                                    @php $isExists = in_array($mb->id, $existingTagihanIds); @endphp
                                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50/30 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400">
                                                <x-icon name="credit-card" class="w-5 h-5" />
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $mb->nama_biaya }}</p>
                                                <p class="text-[10px] text-gray-500">Semester {{ $mb->semester ?? 1 }} &middot; Nominal: Rp {{ number_format($mb->nominal_baku, 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                        @if ($isExists)
                                            <div class="text-sm font-semibold text-green-600 flex items-center gap-1 bg-green-50 px-3 py-1.5 rounded-lg border border-green-200">
                                                <x-icon name="check-circle" class="w-3.5 h-3.5" /> Sudah Dibuat
                                            </div>
                                        @else
                                            <button wire:click="generateSingle({{ $mb->id }})" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all shadow-md active:scale-95">
                                                <x-icon name="plus" class="w-4 h-4" />
                                                <span>Generate</span>
                                            </button>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-gray-500 text-sm italic text-center py-4">Tidak ada komponen biaya yang berlaku untuk prodi/jenjang mahasiswa ini.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-3xl border-2 border-dashed border-gray-200 h-full min-h-[400px] flex flex-col items-center justify-center p-12 text-center">
                        <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 mb-6 border border-gray-100">
                            <x-icon name="users" class="w-10 h-10" />
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Pilih Mahasiswa</h3>
                        <p class="text-gray-500 max-w-sm">Pilih mahasiswa di sebelah kiri untuk membangkitkan tagihan individu.</p>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                    <div class="flex items-center gap-2 text-blue-600 font-bold mb-2">
                        Filter Batch
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Jenjang Pendidikan</label>
                        <select wire:model="batchJenjang" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="SEMUA">Semua Jenjang</option>
                            <option value="S1">S1 (Sarjana)</option>
                            <option value="S2">S2 (Magister)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Program Studi</label>
                        <select wire:model="batchProdiId" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="SEMUA">Semua Program Studi</option>
                            @foreach ($prodis as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_prodi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Semester Aktif</label>
                        <select wire:model="batchSemester" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="SEMUA">Semua Semester</option>
                            @for ($i = 1; $i <= 14; $i++)
                                <option value="{{ $i }}">Semester {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Kategori Mahasiswa</label>
                        <select wire:model="batchKategori" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                            <option value="SEMUA">Semua Kategori</option>
                            <option value="NONE">TIDAK ADA BEASISWA / PENUH</option>
                            @foreach ($kategoriBeasiswas as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 space-y-6">
                <div class="bg-blue-600 rounded-2xl p-8 text-white shadow-xl shadow-blue-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-black mb-1">Batch Generate</h3>
                        <p class="text-blue-100">Bebaskan tagihan sekaligus berdasarkan kriteria filter.</p>
                    </div>
                    <x-icon name="users" class="w-14 h-14 opacity-20" />
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Komponen Biaya Yang Akan Diterbitkan:</label>
                        <select wire:model.live="selectedBiayaForBatch" class="w-full px-6 py-4 bg-gray-50 border-2 border-gray-100 rounded-2xl text-lg font-bold text-gray-900 outline-none focus:border-blue-600 transition-all">
                            <option value="">-- Pilih Master Biaya --</option>
                            @foreach ($masterBiayas as $mb)
                                <option value="{{ $mb->id }}">{{ $mb->nama_biaya }} (Rp {{ number_format($mb->nominal_baku, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-6 bg-amber-50 rounded-2xl border border-amber-100">
                        <div class="flex items-start gap-4">
                            <x-icon name="alert-circle" class="text-amber-600 shrink-0 w-6 h-6" />
                            <div>
                                <p class="font-bold text-amber-900">Perhatian Sebelum Memulai</p>
                                <p class="text-sm text-amber-700 mt-1">Sistem akan secara otomatis mencari mahasiswa yang sesuai dengan filter di samping dan menerbitkan tagihan sesuai pilihan di atas.</p>
                            </div>
                        </div>
                    </div>

                    <button wire:click="batchGenerate" wire:confirm="Yakin ingin membangkitkan tagihan batch sesuai filter ini?" @disabled(! $selectedBiayaForBatch) class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-xl flex items-center justify-center gap-3 shadow-lg shadow-blue-100 transition-all active:scale-95 disabled:grayscale disabled:opacity-50">
                        <x-icon name="check-circle" class="w-6 h-6" />
                        Bangkitkan Tagihan (Batch)
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

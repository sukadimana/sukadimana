<div>
    <div class="space-y-6 print:hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Gerbang Kelulusan Akademik</h2>
            <p class="text-gray-500">Kelola bebas tanggungan Yudisium dan studi pelacakan Alumni</p>
        </div>
        <div class="flex bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
            <button wire:click="$set('activeTab', 'yudisium')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'yudisium' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:text-gray-900' }}">
                Daftar Periksa Yudisium
            </button>
            <button wire:click="$set('activeTab', 'alumni')" class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'alumni' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:text-gray-900' }}">
                Pelacakan Alumni
            </button>
        </div>
    </div>

    @if ($activeTab === 'yudisium')
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col md:flex-row gap-6 items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-icon name="calendar" class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Periode Pendaftaran Yudisium</h3>
                        <p class="text-sm text-gray-500">
                            {{ $yudisiumPeriod['startDate'] ? $yudisiumPeriod['startDate']->translatedFormat('j F Y') : '-' }}
                            s/d
                            {{ $yudisiumPeriod['endDate'] ? $yudisiumPeriod['endDate']->translatedFormat('j F Y') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    @if ($yudisiumPeriod['isOpen'])
                        <span class="px-4 py-2 bg-green-50 text-green-700 rounded-full text-sm font-bold border border-green-200">Pendaftaran Dibuka</span>
                    @else
                        <span class="px-4 py-2 bg-red-50 text-red-700 rounded-full text-sm font-bold border border-red-200">Pendaftaran Ditutup</span>
                    @endif
                    @if ($role === \App\Enums\UserRole::ADMIN)
                        <button wire:click="openSettingsModal" class="text-sm px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg border border-gray-200">Edit</button>
                    @endif
                </div>
            </div>

            <div class="bg-amber-50 p-4 rounded-xl border border-amber-100 flex items-start gap-3">
                <x-icon name="alert-circle" class="text-amber-600 shrink-0 mt-0.5 w-4 h-4" />
                <p class="text-xs text-amber-800"><strong>Ketentuan Yudisium:</strong> Hanya mahasiswa semester 8 keatas yang diperbolehkan mendaftar. Seluruh tunggakan keuangan dan perpustakaan harus diselesaikan sebelum status akhir disetujui.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700">Daftar Pendaftaran Yudisium</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-6 py-4 font-semibold text-gray-500 uppercase text-xs">Mahasiswa</th>
                                <th class="px-4 py-4 font-semibold text-gray-500 uppercase text-xs text-center">Semester</th>
                                <th class="px-6 py-4 font-semibold text-gray-500 uppercase text-xs text-center">Perpustakaan</th>
                                <th class="px-6 py-4 font-semibold text-gray-500 uppercase text-xs text-center">Keuangan</th>
                                <th class="px-6 py-4 font-semibold text-gray-500 uppercase text-xs text-center">Akademik</th>
                                <th class="px-6 py-4 font-semibold text-gray-500 uppercase text-xs">Opsi / Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($yudisiums as $y)
                                @continue(! $y->mahasiswa)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $y->mahasiswa->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $y->mahasiswa->nim }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-blue-600">{{ $y->mahasiswa->semester_saat_ini }}</td>

                                    @foreach (['bebas_pustaka' => ['catatan_pustaka', ['admin', 'perpus']], 'bebas_keuangan' => ['catatan_keuangan', ['admin', 'keuangan']], 'bebas_akademik' => ['catatan_akademik', ['admin', 'akademik', 'petugas_yudisium']]] as $field => [$noteField, $allowedRoles])
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <div class="flex items-center gap-2">
                                                    @php $status = $y->$field; @endphp
                                                    <x-icon name="{{ $status === 'APPROVED' ? 'check-circle' : ($status === 'REJECTED' ? 'x-mark' : 'loader') }}" class="w-4 h-4 {{ $status === 'APPROVED' ? 'text-green-500' : ($status === 'REJECTED' ? 'text-red-500' : 'text-yellow-500') }}" />
                                                    <span class="text-[10px] font-bold text-gray-600">{{ match($status) { 'APPROVED' => 'Disetujui', 'REJECTED' => 'Ditolak', default => 'Menunggu' } }}</span>
                                                </div>
                                                @if (in_array($role->value, $allowedRoles))
                                                    <div class="flex gap-1">
                                                        @if ($status === 'PENDING')
                                                            <button wire:click="setValidation({{ $y->id }}, '{{ $field }}', 'APPROVED')" class="p-1 bg-green-50 text-green-600 rounded hover:bg-green-100" title="Setujui"><x-icon name="check-circle" class="w-3.5 h-3.5" /></button>
                                                            <button wire:click="setValidation({{ $y->id }}, '{{ $field }}', 'REJECTED')" class="p-1 bg-red-50 text-red-600 rounded hover:bg-red-100" title="Tolak"><x-icon name="x-mark" class="w-3.5 h-3.5" /></button>
                                                        @else
                                                            <button wire:click="setValidation({{ $y->id }}, '{{ $field }}', 'PENDING')" class="p-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200" title="Reset">Reset</button>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if ($y->$noteField)
                                                    <p class="text-[10px] text-gray-500 max-w-[120px] text-center italic mt-1">Catatan: {{ $y->$noteField }}</p>
                                                @endif
                                            </div>
                                        </td>
                                    @endforeach

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[9px] font-black tracking-widest {{ $y->status_akhir === 'SIAP_WISUDA' ? 'bg-green-600 text-white' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $y->status_akhir === 'SIAP_WISUDA' ? 'SIAP WISUDA' : 'PROSES VALIDASI' }}
                                            </span>
                                            @if ($y->bebas_pustaka === 'APPROVED' && $y->bebas_keuangan === 'APPROVED' && $y->bebas_akademik === 'APPROVED')
                                                <button wire:click="$set('printYudisiumId', {{ $y->id }})" class="flex items-center gap-1 text-xs text-blue-600 font-bold hover:underline">
                                                    <x-icon name="printer" class="w-3 h-3" /> Cetak Bukti Daftar
                                                </button>
                                            @endif
                                            @if ($y->bebas_keuangan === 'APPROVED')
                                                <button wire:click="$set('printBebasMahasiswaId', {{ $y->mahasiswa->id }})" class="flex items-center gap-1 text-xs text-emerald-600 font-bold hover:underline">
                                                    <x-icon name="file-text" class="w-3 h-3" /> Cetak Bebas Tanggungan
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                            @foreach ($eligibleUnregistered as $s)
                                <tr class="bg-blue-50/30 opacity-70">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $s->nama_lengkap }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $s->nim }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-blue-600">{{ $s->semester_saat_ini }}</td>
                                    <td colspan="3" class="px-6 py-4 text-center text-xs text-gray-400 italic font-medium">Mahasiswa belum mendaftar Yudisium</td>
                                    <td class="px-6 py-4">
                                        <button wire:click="registerYudisium({{ $s->id }})" class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm hover:bg-blue-700 flex items-center gap-1">
                                            <x-icon name="plus" class="w-3 h-3" /> Daftarkan
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($yudisiums->isEmpty() && $eligibleUnregistered->isEmpty())
                                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">Belum ada mahasiswa semester 8 yang terdeteksi.</td></tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($graduates as $s)
                    @php $alumni = $alumniByMahasiswa->get($s->id); @endphp
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col gap-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                <div>
                                    <h3 class="font-bold text-gray-900">{{ $s->nama_lengkap }}</h3>
                                    <p class="text-sm text-gray-500">NIM: {{ $s->nim }} | Angkatan {{ $s->angkatan }}</p>
                                </div>
                            </div>
                            @if ($alumni)
                                <span class="bg-green-50 text-green-700 px-2 py-1 rounded-md text-[10px] font-bold border border-green-100 uppercase tracking-wider">{{ $alumni->status_pekerjaan }}</span>
                            @endif
                        </div>

                        @if ($alumni)
                            <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                <div class="text-sm text-gray-700"><span class="font-medium">{{ $alumni->jabatan ?: 'N/A' }}</span> di <span class="font-medium">{{ $alumni->nama_instansi ?: 'N/A' }}</span></div>
                                <div class="text-sm text-gray-700">{{ $alumni->nomor_hp_terbaru }}</div>
                            </div>
                        @else
                            <div class="bg-gray-50 rounded-lg p-3 border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 py-4">
                                <p class="text-xs italic">Data tracer belum diisi</p>
                            </div>
                        @endif

                        <div class="mt-auto flex flex-col gap-2">
                            <button wire:click="openAlumniModal({{ $s->id }})" class="text-sm text-blue-600 font-bold hover:underline flex items-center gap-2">
                                <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                {{ $alumni ? 'Perbarui Data Tracer' : 'Isi Data Tracer' }}
                            </button>
                            @if ($alumni && $alumni->nomor_hp_terbaru)
                                @php
                                    $hp = preg_replace('/\D/', '', $alumni->nomor_hp_terbaru);
                                    $hp = str_starts_with($hp, '0') ? '62'.substr($hp, 1) : $hp;
                                @endphp
                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2 mt-1">
                                    @if ($profilKampus?->wa_api_url)
                                        <button wire:click="sendWhatsAppApi({{ $s->id }})" class="text-xs text-blue-600 font-bold hover:bg-blue-50 px-2 py-1 rounded transition flex items-center gap-1 border border-blue-200">API WA</button>
                                    @endif
                                    <a href="https://wa.me/{{ $hp }}" target="_blank" class="text-xs text-green-600 font-bold hover:bg-green-50 px-2 py-1 rounded transition flex items-center gap-1 border border-green-200 ml-auto">Manual WA</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500 italic bg-white rounded-xl border border-gray-200 shadow-sm">Belum ada data mahasiswa dengan status LULUS.</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Validation Note Modal --}}
    @if ($validationYudisiumId)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Catatan {{ $validationStatus === 'APPROVED' ? 'Persetujuan' : 'Penolakan' }}</h3>
                    <button wire:click="$set('validationYudisiumId', null)" class="text-gray-400 hover:text-gray-500"><x-icon name="x-mark" class="w-5 h-5" /></button>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tambahkan catatan detail (opsional)</label>
                    <textarea wire:model="validationNote" rows="4" class="w-full border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-3 resize-none"></textarea>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 border-t border-gray-100">
                    <button wire:click="$set('validationYudisiumId', null)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button wire:click="confirmValidation" class="px-4 py-2 text-sm font-bold text-white rounded-lg {{ $validationStatus === 'APPROVED' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}">
                        {{ $validationStatus === 'APPROVED' ? 'Setujui' : 'Tolak' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Settings Modal --}}
    @if ($isSettingsModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isSettingsModalOpen', false)"></div>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md relative z-10 flex flex-col">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Pengaturan Yudisium</h3>
                    <button wire:click="$set('isSettingsModalOpen', false)" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="w-5 h-5" /></button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="settingsIsOpen" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Pendaftaran Dibuka</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" wire:model="settingsStartDate" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" wire:model="settingsEndDate" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl border-t border-gray-100">
                    <button wire:click="$set('isSettingsModalOpen', false)" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                    <button wire:click="saveSettings" class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700">Simpan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Alumni Modal --}}
    @if ($isAlumniModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isAlumniModalOpen', false)"></div>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg relative z-10 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">Data Tracer Study</h3>
                    <button wire:click="$set('isAlumniModalOpen', false)" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="w-5 h-5" /></button>
                </div>
                <form wire:submit="saveAlumni" class="p-6 overflow-y-auto space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Lulus</label>
                            <input type="text" wire:model="tahun_lulus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP / WA Terbaru</label>
                            <input type="text" wire:model="nomor_hp_terbaru" placeholder="08xxxxxxxxxx" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Domisili</label>
                        <textarea wire:model="alamat_domisili" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status Pekerjaan</label>
                        <select wire:model.live="status_pekerjaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="BELUM_KERJA">Belum Bekerja</option>
                            <option value="KERJA">Bekerja</option>
                            <option value="WIRAUSAHA">Wirausaha</option>
                            <option value="LANJUT_STUDI">Lanjut Studi</option>
                        </select>
                    </div>

                    @if ($status_pekerjaan === 'KERJA')
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                <select wire:model="kategori_pekerjaan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                    <option value="">-</option>
                                    <option value="PNS">PNS</option>
                                    <option value="NON_PNS">Non PNS</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                                <input type="text" wire:model="jabatan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Instansi</label>
                            <input type="text" wire:model="nama_instansi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Gaji Pertama (Rp)</label>
                            <input type="number" wire:model="gaji_pertama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    @elseif ($status_pekerjaan === 'WIRAUSAHA')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha</label>
                            <input type="text" wire:model="nama_instansi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    @elseif ($status_pekerjaan === 'LANJUT_STUDI')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kampus</label>
                            <input type="text" wire:model="nama_instansi" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                                <input type="text" wire:model="jurusan_lanjutan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                                <input type="text" wire:model="prodi_lanjutan" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 flex justify-end gap-3">
                        <button type="button" wire:click="$set('isAlumniModalOpen', false)" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    </div>

    {{-- PRINT: Bukti Pendaftaran Yudisium --}}
    @if ($printYudisium)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 print:relative print:inset-auto print:p-0">
        <div class="absolute inset-0 bg-black/70 print:hidden" wire:click="$set('printYudisiumId', null)"></div>
        <div class="bg-white rounded-xl w-full max-w-3xl relative z-10 shadow-2xl overflow-hidden print:shadow-none print:rounded-none">
            <div class="p-8 border-2 border-gray-900 m-4 print:border-0 print:m-0">
                <div class="text-center mb-8 border-b-2 border-gray-900 pb-6">
                    @if ($profilKampus?->logo_url)
                        <img src="{{ $profilKampus->logo_url }}" alt="Logo" class="h-20 mx-auto mb-4 object-contain">
                    @endif
                    <h1 class="text-2xl font-black uppercase tracking-widest">Surat Pendaftaran Yudisium</h1>
                    <p class="text-sm font-bold mt-1">{{ $profilKampus->nama_kampus ?? 'SIAKAD Pro' }}</p>
                    @if ($profilKampus?->alamat_kampus)
                        <p class="text-xs text-gray-700 mt-1">{{ $profilKampus->alamat_kampus }}</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div><p class="text-xs font-bold text-gray-500 uppercase mb-1">Nama Mahasiswa</p><p class="text-lg font-black">{{ $printYudisium->mahasiswa->nama_lengkap }}</p></div>
                    <div><p class="text-xs font-bold text-gray-500 uppercase mb-1">NIM</p><p class="text-lg font-black">{{ $printYudisium->mahasiswa->nim }}</p></div>
                    <div><p class="text-xs font-bold text-gray-500 uppercase mb-1">Program Studi</p><p class="text-lg font-black">{{ $printYudisium->mahasiswa->prodi->nama_prodi ?? '-' }}</p></div>
                    <div><p class="text-xs font-bold text-gray-500 uppercase mb-1">Semester</p><p class="text-lg font-black">{{ $printYudisium->mahasiswa->semester_saat_ini }}</p></div>
                </div>
                <div class="space-y-4 mb-12">
                    <div class="flex items-center justify-between border-b border-gray-200 py-2"><span class="font-bold">Bebas Perpustakaan & Karya Ilmiah</span><span class="font-black text-green-700">&#10003; VALID</span></div>
                    <div class="flex items-center justify-between border-b border-gray-200 py-2"><span class="font-bold">Bebas Administrasi Keuangan</span><span class="font-black text-green-700">&#10003; VALID</span></div>
                    <div class="flex items-center justify-between border-b border-gray-200 py-2"><span class="font-bold">Berita Acara Munaqosah (Akademik)</span><span class="font-black text-green-700">&#10003; VALID</span></div>
                </div>
                <div class="mt-20 flex justify-end">
                    <div class="text-center w-64 border-t border-gray-900 pt-2">
                        <p class="text-sm font-bold">Petugas</p>
                        <div class="h-16"></div>
                        <p class="font-black underline uppercase">{{ $profilKampus->petugas_pembayaran ?? '-' }}</p>
                        <p class="text-[10px] font-bold">Dicetak pada {{ now()->translatedFormat('j F Y, H:i') }}</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex gap-3 print:hidden">
                <button onclick="window.print()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-blue-700"><x-icon name="printer" class="w-4 h-4" /> Cetak Sekarang</button>
                <button wire:click="$set('printYudisiumId', null)" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>
    @endif

    {{-- PRINT: Surat Bebas Tanggungan --}}
    @if ($printBebasMahasiswa)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 print:relative print:inset-auto print:p-0">
        <div class="absolute inset-0 bg-black/70 print:hidden" wire:click="$set('printBebasMahasiswaId', null)"></div>
        <div class="bg-white rounded-xl w-full max-w-3xl relative z-10 shadow-2xl overflow-hidden print:shadow-none print:rounded-none">
            <div class="p-8">
                <div class="text-center border-b-4 border-double border-gray-900 pb-4 mb-4">
                    @if ($profilKampus?->logo_url)
                        <img src="{{ $profilKampus->logo_url }}" alt="Logo" class="h-20 mx-auto mb-4 object-contain">
                    @endif
                    <h1 class="text-xl font-bold uppercase">{{ $profilKampus->nama_kampus ?? '' }}</h1>
                    @if ($profilKampus?->alamat_kampus)
                        <p class="text-sm">{{ $profilKampus->alamat_kampus }}</p>
                    @endif
                </div>
                <h2 class="text-center text-lg underline mb-6">SURAT KETERANGAN BEBAS TANGGUNGAN</h2>
                <p>Yang bertanda tangan di bawah ini menerangkan bahwa mahasiswa:</p>
                <table class="ml-6 my-3 text-sm">
                    <tr><td class="pr-4 py-1">Nama</td><td class="py-1">: <strong>{{ $printBebasMahasiswa->nama_lengkap }}</strong></td></tr>
                    <tr><td class="pr-4 py-1">NIM</td><td class="py-1">: {{ $printBebasMahasiswa->nim }}</td></tr>
                    <tr><td class="pr-4 py-1">Program Studi</td><td class="py-1">: {{ $printBebasMahasiswa->prodi->nama_prodi ?? '-' }}</td></tr>
                </table>
                <p>Berdasarkan catatan administrasi keuangan, mahasiswa tersebut di atas dinyatakan <strong>BEBAS BIAYA</strong>/Telah Lunas dengan rincian kewajiban sebagai berikut:</p>
                <table class="w-full border-collapse mt-3 text-sm">
                    <thead>
                        <tr><th class="border border-gray-900 px-2 py-1">No</th><th class="border border-gray-900 px-2 py-1">Keterangan Biaya</th><th class="border border-gray-900 px-2 py-1">Dibebankan</th><th class="border border-gray-900 px-2 py-1">Dibayar</th><th class="border border-gray-900 px-2 py-1">Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($printBebasTagihans as $i => $t)
                            <tr>
                                <td class="border border-gray-900 px-2 py-1 text-center">{{ $i + 1 }}</td>
                                <td class="border border-gray-900 px-2 py-1">{{ $t->nama_tagihan_snapshot }}</td>
                                <td class="border border-gray-900 px-2 py-1 text-right">Rp {{ number_format($t->total_harus_bayar, 0, ',', '.') }}</td>
                                <td class="border border-gray-900 px-2 py-1 text-right">Rp {{ number_format($t->total_sudah_bayar, 0, ',', '.') }}</td>
                                <td class="border border-gray-900 px-2 py-1 text-center">{{ $t->sisa_tagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="mt-4 text-sm">Surat keterangan ini diberikan sebagai persyaratan untuk pengambilan Ijazah serta keperluan administrasi lainnya.</p>
                <div class="flex justify-end mt-8">
                    <div class="text-center w-64">
                        <p>{{ now()->translatedFormat('j F Y') }}</p>
                        <p class="mt-1">Petugas Keuangan / Bendahara</p>
                        <p class="font-bold uppercase underline mt-16">{{ $profilKampus->petugas_pembayaran ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="p-4 bg-gray-50 flex gap-3 print:hidden">
                <button onclick="window.print()" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-blue-700"><x-icon name="printer" class="w-4 h-4" /> Cetak Sekarang</button>
                <button wire:click="$set('printBebasMahasiswaId', null)" class="px-6 py-3 bg-white border border-gray-200 text-gray-700 rounded-lg font-bold hover:bg-gray-50">Tutup</button>
            </div>
        </div>
    </div>
    @endif
</div>

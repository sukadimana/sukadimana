<div class="space-y-6 relative">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Direktori Mahasiswa</h2>
            <p class="text-gray-500">Kelola status akademik dan profil mahasiswa</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
            <button wire:click="openPromoteModal" class="bg-amber-100 hover:bg-amber-200 text-amber-700 px-4 py-2 rounded-lg font-bold border border-amber-200 transition-colors flex items-center gap-2">
                <x-icon name="dashboard" class="w-4 h-4" />
                Proses Kenaikan Semester
            </button>
            <button wire:click="openAddModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                <x-icon name="plus" class="w-5 h-5" />
                Tambah Mahasiswa Baru
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <x-icon name="search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
            <input type="text" wire:model.live="search" placeholder="Cari Nama atau NIM..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="filterJenjang" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="all">Semua Jenjang</option>
                <option value="S1">S1 (Sarjana)</option>
                <option value="S2">S2 (Magister)</option>
            </select>
        </div>
        <div class="w-full md:w-64">
            <select wire:model.live="filterProdi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="all">Semua Program Studi</option>
                @foreach ($prodis as $p)
                    <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->jenjang }})</option>
                @endforeach
            </select>
        </div>
        <div class="w-full md:w-48">
            <select wire:model.live="filterStatusAkademik" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                <option value="all">Semua Status</option>
                @foreach ($statusOptions as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">L/P</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenjang</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Program Studi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Angkatan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Sem</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($students as $student)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $student->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">UID: {{ $student->user_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-sm text-gray-700">{{ $student->nim }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-700">{{ $student->jenis_kelamin ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold">{{ $student->jenjang ?? 'S1' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-900">{{ $student->prodi->nama_prodi ?? 'Unknown' }}</p>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $student->prodi->jenjang ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div>{{ $student->angkatan }}</div>
                                <div class="text-[10px] text-gray-400">Masuk: {{ $student->tahun_masuk ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-bold">{{ $student->semester_saat_ini ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match ($student->status_akademik) {
                                        'AKTIF' => 'bg-green-100 text-green-700 border-green-200',
                                        'CUTI' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'DO' => 'bg-red-100 text-red-700 border-red-200',
                                        'LULUS' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColor }}">{{ $student->status_akademik }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button wire:click="edit({{ $student->id }})" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition-colors" title="Edit">
                                        <x-icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="confirmDelete({{ $student->id }}, '{{ addslashes($student->nama_lengkap) }}')" class="text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition-colors" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-gray-500">Tidak ada mahasiswa yang ditemukan sesuai kriteria Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($students->total() > 0)
            <div class="bg-gray-50 p-4 border-t border-gray-200 flex flex-col sm:flex-row gap-3 justify-between items-center text-sm">
                <span class="font-medium text-gray-700">Total Filter: {{ $totalFiltered }} Mahasiswa</span>
                <div class="flex gap-4">
                    <span class="text-blue-700 font-bold">Laki-laki: {{ $totalLaki }}</span>
                    <span class="text-pink-600 font-bold">Perempuan: {{ $totalPerempuan }}</span>
                </div>
            </div>
            <div class="p-4 border-t border-gray-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- ADD/EDIT STUDENT MODAL --}}
    @if ($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isModalOpen', false)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg relative z-10 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <h3 class="text-xl font-bold text-gray-900">{{ $isEditing ? 'Ubah Data Mahasiswa' : 'Tambah Mahasiswa Baru' }}</h3>
                    <button wire:click="$set('isModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <form wire:submit="save" class="p-6 overflow-y-auto space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" wire:model="nim" placeholder="contoh: 23010005" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('nim') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('nim') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="nama_lengkap" placeholder="contoh: Budi Santoso" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('nama_lengkap') ? 'border-red-300' : 'border-gray-300' }}">
                        @error('nama_lengkap') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <select wire:model="jenis_kelamin" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                            <select wire:model="prodi_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none {{ $errors->has('prodi_id') ? 'border-red-300' : 'border-gray-300' }}">
                                <option value="">Pilih Program Studi</option>
                                @foreach ($prodis as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_prodi }} ({{ $p->jenjang }})</option>
                                @endforeach
                            </select>
                            @error('prodi_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Angkatan</label>
                            <input type="text" wire:model="angkatan" placeholder="2025/2026" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Masuk</label>
                            <input type="text" wire:model="tahun_masuk" placeholder="2025" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Akademik</label>
                            <select wire:model="status_akademik" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                @foreach ($statusOptions as $s)
                                    <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang</label>
                            <select wire:model="jenjang" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Semester Saat Ini</label>
                            <input type="number" min="1" max="14" wire:model="semester_saat_ini" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Beasiswa</label>
                            <select wire:model="kategori_beasiswa" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="NONE">TIDAK ADA BEASISWA / PENUH</option>
                                @foreach ($kategoriBeasiswas as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors min-w-[140px]">
                            {{ $isEditing ? 'Perbarui Data' : 'Simpan Mahasiswa' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- DELETE CONFIRMATION MODAL --}}
    @if ($studentToDeleteId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('studentToDeleteId', null)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 p-6 flex flex-col items-center text-center">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-4">
                    <x-icon name="trash" class="w-6 h-6" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Data Mahasiswa</h3>
                <p class="text-gray-500 mb-6 text-sm">Apakah Anda yakin ingin menghapus <strong>{{ $studentToDeleteName }}</strong>? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3 w-full">
                    <button wire:click="$set('studentToDeleteId', null)" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-lg transition-colors">Batal</button>
                    <button wire:click="delete" class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg transition-colors">Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- PROMOTE MODAL --}}
    @if ($isPromoteModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isPromoteModalOpen', false)"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl relative z-10 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Proses Kenaikan Semester</h3>
                        <p class="text-sm text-gray-500">Pilih mahasiswa yang akan dinaikkan semesternya.</p>
                    </div>
                    <button wire:click="$set('isPromoteModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto w-full">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm font-bold text-gray-700">{{ count($selectedForPromotion) }} mahasiswa terpilih</span>
                        <button wire:click="toggleSelectAllPromotion" class="text-xs text-blue-600 font-bold hover:underline">
                            {{ count($selectedForPromotion) === $activeStudentsForPromotion->count() ? 'Batalkan Pilihan Semua' : 'Pilih Semua Mahasiswa Aktif' }}
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 w-10"></th>
                                    <th class="px-4 py-3 font-semibold text-gray-600">NIM / Nama</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 text-center">Semester Saat Ini</th>
                                    <th class="px-4 py-3 font-semibold text-gray-600 text-center">Menjadi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($activeStudentsForPromotion as $student)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 w-10 text-center">
                                            <input type="checkbox" wire:model.live="selectedForPromotion" value="{{ $student->id }}" class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900">{{ $student->nama_lengkap }}</div>
                                            <div class="text-xs text-gray-500 font-mono">{{ $student->nim }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $student->semester_saat_ini ?? 1 }}</td>
                                        <td class="px-4 py-3 text-center font-bold text-blue-600">
                                            @if (($student->semester_saat_ini ?? 1) >= 8)
                                                <span class="px-2 py-0.5 rounded text-[10px] bg-blue-100 text-blue-700 whitespace-nowrap border border-blue-200 uppercase font-bold">Lulus</span>
                                            @else
                                                {{ ($student->semester_saat_ini ?? 1) + 1 }}
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Tidak ada mahasiswa aktif.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <button type="button" wire:click="$set('isPromoteModalOpen', false)" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-200 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button wire:click="promoteSemester" wire:confirm="Proses kenaikan semester untuk mahasiswa terpilih?" @disabled(count($selectedForPromotion) === 0) class="px-4 py-2 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 shadow-sm transition-colors min-w-[140px] disabled:opacity-70 disabled:bg-amber-300">
                        Proses Kenaikan ({{ count($selectedForPromotion) }})
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

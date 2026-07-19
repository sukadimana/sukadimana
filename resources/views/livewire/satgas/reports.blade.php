<div class="space-y-6 relative">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Pengaduan Satgas PPK</h2>
        <p class="text-gray-500">Kelola laporan pengaduan kekerasan seksual yang masuk dari website</p>
    </div>

    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <select wire:model.live="filterStatus" class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
            <option value="all">Semua Status</option>
            <option value="BARU">Baru</option>
            <option value="DIPROSES">Diproses</option>
            <option value="SELESAI">Selesai</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kode Referensi</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelapor</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-sm text-gray-700">{{ $report->reference_code }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if ($report->is_anonymous)
                                    <span class="italic text-gray-400">Anonim</span>
                                @else
                                    {{ $report->reporter_name ?: '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $report->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColor = match ($report->status) {
                                        'BARU' => 'bg-red-100 text-red-700 border-red-200',
                                        'DIPROSES' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'SELESAI' => 'bg-green-100 text-green-700 border-green-200',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusColor }}">{{ $report->status }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <button wire:click="view({{ $report->id }})" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition-colors" title="Lihat Detail">
                                    <x-icon name="eye" class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">Belum ada pengaduan yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->total() > 0)
            <div class="p-4 border-t border-gray-200">
                {{ $reports->links() }}
            </div>
        @endif
    </div>

    @if ($viewingReport)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="close"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl relative z-10 flex flex-col max-h-[90vh]">
                <div class="flex items-center justify-between p-6 border-b border-gray-100">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Detail Pengaduan</h3>
                        <p class="text-sm text-gray-500 font-mono">{{ $viewingReport->reference_code }}</p>
                    </div>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Nama Pelapor</p>
                            <p class="font-medium text-gray-900">{{ $viewingReport->is_anonymous ? 'Anonim' : ($viewingReport->reporter_name ?: '-') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Kontak</p>
                            <p class="font-medium text-gray-900">{{ $viewingReport->is_anonymous ? '-' : ($viewingReport->reporter_contact ?: '-') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-gray-500 text-sm mb-1">Isi Laporan</p>
                        <p class="text-gray-900 whitespace-pre-line bg-gray-50 border border-gray-200 rounded-lg p-4">{{ $viewingReport->description }}</p>
                    </div>

                    <form wire:submit="save" class="space-y-4 border-t border-gray-100 pt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Penanganan</label>
                            <select wire:model="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                                <option value="BARU">Baru</option>
                                <option value="DIPROSES">Diproses</option>
                                <option value="SELESAI">Selesai</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Internal Satgas</label>
                            <textarea wire:model="admin_notes" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" wire:click="close" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">
                                Tutup
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 shadow-sm transition-colors">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

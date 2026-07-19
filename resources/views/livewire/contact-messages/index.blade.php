<div class="space-y-6 relative">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Pesan Kontak Website</h2>
        <p class="text-gray-500">Pesan yang dikirim pengunjung melalui formulir kontak website
            @if ($unreadCount > 0)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">{{ $unreadCount }} belum dibaca</span>
            @endif
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengirim</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subjek</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($messages as $msg)
                        <tr class="hover:bg-gray-50 transition-colors {{ ! $msg->is_read ? 'font-semibold' : '' }}">
                            <td class="px-6 py-4">
                                <p class="text-gray-900">{{ $msg->name }}</p>
                                <p class="text-xs text-gray-500 font-normal">{{ $msg->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $msg->subject ?: '(Tanpa subjek)' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-normal">{{ $msg->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4">
                                @if ($msg->is_read)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-gray-100 text-gray-600 border-gray-200">Dibaca</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border bg-blue-100 text-blue-700 border-blue-200">Baru</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1">
                                    <button wire:click="view({{ $msg->id }})" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition-colors" title="Lihat">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="delete({{ $msg->id }})" wire:confirm="Hapus pesan ini?" class="text-red-600 hover:bg-red-50 p-1.5 rounded-lg transition-colors" title="Hapus">
                                        <x-icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 font-normal">Belum ada pesan yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($messages->total() > 0)
            <div class="p-4 border-t border-gray-200">
                {{ $messages->links() }}
            </div>
        @endif
    </div>

    @if ($viewingMessage)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="close"></div>
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg relative z-10 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">{{ $viewingMessage->subject ?: '(Tanpa subjek)' }}</h3>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-6 h-6" />
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-4">Dari <strong>{{ $viewingMessage->name }}</strong> ({{ $viewingMessage->email }}) &middot; {{ $viewingMessage->created_at->format('d M Y H:i') }}</p>
                <p class="text-gray-900 whitespace-pre-line bg-gray-50 border border-gray-200 rounded-lg p-4">{{ $viewingMessage->message }}</p>
                <div class="mt-6 flex justify-end">
                    <button wire:click="close" class="px-4 py-2 text-gray-700 font-medium hover:bg-gray-100 rounded-lg transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>

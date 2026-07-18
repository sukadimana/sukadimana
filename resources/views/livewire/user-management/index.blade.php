<div class="space-y-6">
    {{-- Admin Profile Form --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                <x-icon name="shield" class="w-5 h-5" />
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Profil Saya</h2>
                <p class="text-sm text-gray-500">Perbarui identitas akun Anda sendiri.</p>
            </div>
        </div>

        <form wire:submit="saveAdminProfile" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if ($saveSuccessAdmin)
                <div class="col-span-1 md:col-span-2 p-3 bg-green-100 text-green-700 rounded-lg font-medium mb-2">{{ $saveSuccessAdmin }}</div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" wire:model="profileName" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                @error('profileName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" wire:model="profileEmail" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                @error('profileEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Password (Opsional)</label>
                <input type="password" wire:model="newPassword" placeholder="Masukkan password baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                @error('newPassword') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" wire:model="confirmPassword" placeholder="Ulangi password baru" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="col-span-full mt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition">Simpan Profil Saya</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <x-icon name="users" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Manajemen Pengguna</h2>
                    <p class="text-sm text-gray-500">Kelola akun untuk Petugas Perpustakaan, Yudisium, Keuangan, dll.</p>
                </div>
            </div>
            <button wire:click="openCreate" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <x-icon name="plus" class="w-5 h-5" />
                Tambah Pengguna
            </button>
        </div>

        @if ($saveSuccessUser)
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg font-medium">{{ $saveSuccessUser }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Nama Lengkap</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Role</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $u->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">{{ $u->role->label() }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="edit({{ $u->id }})" class="text-blue-600 hover:text-blue-800 p-1 mx-1"><x-icon name="pencil" class="w-4.5 h-4.5" /></button>
                                @if ($u->id !== auth()->id())
                                    <button wire:click="confirmDelete({{ $u->id }})" class="text-red-600 hover:text-red-800 p-1 mx-1"><x-icon name="trash" class="w-4.5 h-4.5" /></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if ($deleteConfirmId)
            <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('deleteConfirmId', null)"></div>
                <div class="bg-white rounded-xl shadow-xl w-full max-w-sm relative z-10 flex flex-col p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <x-icon name="trash" class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Hapus Pengguna</h3>
                    <p class="text-sm text-gray-500 mb-6">Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
                    <div class="flex justify-center gap-3">
                        <button wire:click="$set('deleteConfirmId', null)" class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 font-medium rounded-lg">Batal</button>
                        <button wire:click="delete" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg">Ya, Hapus</button>
                    </div>
                </div>
            </div>
        @endif

        @if ($isModalOpen)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black bg-opacity-50" wire:click="$set('isModalOpen', false)"></div>
                <div class="bg-white rounded-xl shadow-xl w-full max-w-md relative z-10 flex flex-col">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900">{{ $editingUserId ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                    </div>
                    <form wire:submit="save" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" wire:model="displayName" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            @error('displayName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input type="password" wire:model="password" placeholder="{{ $editingUserId ? 'Kosongkan jika tidak ingin mengubah password' : 'Password untuk login' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Role / Peran</label>
                            <select wire:model="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                @foreach ($roles as $r)
                                    <option value="{{ $r->value }}">{{ $r->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-8 flex justify-end gap-3 pt-4">
                            <button type="button" wire:click="$set('isModalOpen', false)" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan Data</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

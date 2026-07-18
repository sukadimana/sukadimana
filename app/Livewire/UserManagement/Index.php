<?php

namespace App\Livewire\UserManagement;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Index extends Component
{
    // Admin self-profile
    public string $profileName = '';

    public string $profileEmail = '';

    public string $newPassword = '';

    public string $confirmPassword = '';

    public ?string $saveSuccessAdmin = null;

    // User list CRUD
    public bool $isModalOpen = false;

    public ?int $editingUserId = null;

    public string $displayName = '';

    public string $email = '';

    public string $password = '';

    public string $role = 'perpus';

    public ?string $saveSuccessUser = null;

    public ?int $deleteConfirmId = null;

    public function mount(): void
    {
        $this->profileName = Auth::user()->name;
        $this->profileEmail = Auth::user()->email;
    }

    public function saveAdminProfile(): void
    {
        $this->validate([
            'profileName' => 'required|string|max:255',
            'profileEmail' => ['required', 'email', Rule::unique('users', 'email')->ignore(Auth::id())],
            'newPassword' => 'nullable|min:6|same:confirmPassword',
        ], [
            'newPassword.same' => 'Password baru dan konfirmasi tidak cocok!',
        ]);

        $user = Auth::user();
        $user->name = $this->profileName;
        $user->email = $this->profileEmail;
        if ($this->newPassword) {
            $user->password = Hash::make($this->newPassword);
        }
        $user->save();

        $this->newPassword = '';
        $this->confirmPassword = '';
        $this->saveSuccessAdmin = 'Profil admin berhasil diperbarui.';
    }

    public function openCreate(): void
    {
        $this->reset(['displayName', 'email', 'password', 'editingUserId']);
        $this->role = 'perpus';
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function edit(int $id): void
    {
        $u = User::findOrFail($id);
        $this->editingUserId = $u->id;
        $this->displayName = $u->name;
        $this->email = $u->email;
        $this->password = '';
        $this->role = $u->role->value;
        $this->resetErrorBag();
        $this->isModalOpen = true;
    }

    public function save(): void
    {
        $this->validate([
            'displayName' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'password' => $this->editingUserId ? 'nullable|min:6' : 'required|min:6',
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
        ]);

        $payload = [
            'name' => $this->displayName,
            'email' => $this->email,
            'role' => $this->role,
        ];
        if ($this->password) {
            $payload['password'] = Hash::make($this->password);
        }

        if ($this->editingUserId) {
            User::findOrFail($this->editingUserId)->update($payload);
            $this->saveSuccessUser = 'Pengaturan pengguna berhasil disimpan.';
        } else {
            $payload['password'] = Hash::make($this->password);
            User::create($payload);
            $this->saveSuccessUser = 'Pengguna berhasil ditambahkan.';
        }

        $this->isModalOpen = false;
    }

    public function confirmDelete(int $id): void
    {
        if ($id === Auth::id()) {
            return;
        }
        $this->deleteConfirmId = $id;
    }

    public function delete(): void
    {
        if ($this->deleteConfirmId) {
            User::destroy($this->deleteConfirmId);
        }
        $this->deleteConfirmId = null;
    }

    public function render()
    {
        return view('livewire.user-management.index', [
            'users' => User::orderBy('name')->get(),
            'roles' => UserRole::cases(),
        ]);
    }
}

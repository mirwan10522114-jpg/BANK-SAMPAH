<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Manajemen User')] class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public string $roleFilter = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    // Form state
    public bool $formModal = false;
    public bool $deleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    // Form fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $password = '';
    public array $selectedRoles = [];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function sortField(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[Computed]
    public function userList()
    {
        return User::query()
            ->when($this->search !== '', function ($q) {
                $like = '%'.$this->search.'%';
                $q->where(function ($q) use ($like): void {
                    $q->where('name', 'like', $like)
                      ->orWhere('email', 'like', $like)
                      ->orWhere('phone', 'like', $like)
                      ->orWhere('member_code', 'like', $like);
                });
            })
            ->when($this->roleFilter !== '', fn ($q) => $q->whereJsonContains('roles', $this->roleFilter))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    #[Computed]
    public function roleOptions(): array
    {
        return [
            ['id' => '', 'label' => 'Semua Role'],
            ['id' => UserRole::Admin->value, 'label' => 'Admin'],
            ['id' => UserRole::Owner->value, 'label' => 'Owner'],
            ['id' => UserRole::Nasabah->value, 'label' => 'Nasabah'],
            ['id' => UserRole::Koperasi->value, 'label' => 'Koperasi'],
        ];
    }

    public function startCreating(): void
    {
        $this->resetForm();
        $this->selectedRoles = [UserRole::Nasabah->value];
        $this->formModal = true;
    }

    public function startEditing(int $id): void
    {
        $user = User::findOrFail($id);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = (string) ($user->phone ?? '');
        $this->address = (string) ($user->address ?? '');
        $this->password = '';
        $this->selectedRoles = $user->roles ?? [UserRole::Nasabah->value];

        $this->formModal = true;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email'.($this->editingId ? ','.$this->editingId : ''),
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'in:admin,owner,nasabah,koperasi',
        ];

        if (! $this->editingId) {
            $rules['password'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $this->validate($rules, [
            'selectedRoles.required' => 'Pilih minimal satu role.',
            'selectedRoles.min' => 'Pilih minimal satu role.',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'roles' => array_values($this->selectedRoles),
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $user->update($data);
            $this->success('User berhasil diperbarui.');
        } else {
            $data['password'] = $this->password;
            $user = User::create($data);
            // email_verified_at not in fillable — set directly after create
            $user->forceFill(['email_verified_at' => now()])->save();
            $this->success('User berhasil dibuat.');
        }

        $this->formModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $user = User::findOrFail($this->deletingId);

        if ($user->id === auth()->id()) {
            $this->error('Tidak dapat menghapus akun sendiri.');
            $this->deleteModal = false;
            return;
        }

        $user->delete();
        $this->deletingId = null;
        $this->deleteModal = false;
        $this->success('User dihapus.');
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'email', 'phone', 'address', 'password', 'selectedRoles']);
        $this->resetErrorBag();
    }
}; ?>

<section class="w-full space-y-4">
    <x-mary-header
        title="{{ __('Manajemen User') }}"
        subtitle="{{ __('Kelola semua akun pengguna, role, dan hak akses.') }}"
        separator
        progress-indicator>
        <x-slot:actions>
            <x-mary-button
                label="{{ __('Tambah User') }}"
                icon="o-plus"
                class="btn-primary"
                wire:click="startCreating" />
        </x-slot:actions>
    </x-mary-header>

    {{-- Filter & Search --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <x-mary-input
            wire:model.live.debounce.300="search"
            icon="o-magnifying-glass"
            placeholder="{{ __('Cari nama, email, telepon...') }}"
            class="flex-1" />
        <select wire:model.live="roleFilter" class="select select-bordered bg-white text-sm min-w-[160px]">
            @foreach($this->roleOptions as $opt)
                <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <x-mary-card shadow>
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="bg-base-200 text-xs uppercase tracking-wider text-base-content/60">
                        <th class="w-8">#</th>
                        <th>
                            <button wire:click="sortField('name')" class="flex items-center gap-1 hover:text-primary font-semibold">
                                Nama
                                @if($sortBy === 'name')
                                    <x-mary-icon name="{{ $sortDirection === 'asc' ? 'o-chevron-up' : 'o-chevron-down' }}" class="size-3" />
                                @endif
                            </button>
                        </th>
                        <th>
                            <button wire:click="sortField('email')" class="flex items-center gap-1 hover:text-primary font-semibold">
                                Email
                                @if($sortBy === 'email')
                                    <x-mary-icon name="{{ $sortDirection === 'asc' ? 'o-chevron-up' : 'o-chevron-down' }}" class="size-3" />
                                @endif
                            </button>
                        </th>
                        <th>Telepon</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->userList as $user)
                        <tr class="hover border-b border-base-200">
                            <td class="text-xs text-base-content/40 font-mono">{{ $user->member_code ?? '—' }}</td>
                            <td class="font-medium text-sm">{{ $user->name }}</td>
                            <td class="text-sm text-base-content/70">
                                {{ $user->email }}
                                @if($user->email_verified_at)
                                    <x-mary-icon name="o-check-badge" class="size-3.5 text-success inline ml-1" title="Email terverifikasi" />
                                @endif
                            </td>
                            <td class="text-sm text-base-content/70">{{ $user->phone ?? '—' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles ?? [] as $role)
                                        @php
                                            $color = match($role) {
                                                'admin' => 'badge-error',
                                                'owner' => 'badge-warning',
                                                'nasabah' => 'badge-info',
                                                'koperasi' => 'badge-success',
                                                default => 'badge-ghost',
                                            };
                                        @endphp
                                        <span class="badge badge-sm {{ $color }} font-semibold">{{ ucfirst($role) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($user->email_verified_at)
                                    <span class="badge badge-sm badge-success badge-soft">Aktif</span>
                                @else
                                    <span class="badge badge-sm badge-ghost">Belum verifikasi</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <x-mary-button
                                        icon="o-pencil-square"
                                        class="btn-ghost btn-sm text-info"
                                        wire:click="startEditing({{ $user->id }})"
                                        tooltip="Edit" />
                                    @if($user->id !== auth()->id())
                                        <x-mary-button
                                            icon="o-trash"
                                            class="btn-ghost btn-sm text-error"
                                            wire:click="confirmDelete({{ $user->id }})"
                                            tooltip="Hapus" />
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-base-content/40">
                                <x-mary-icon name="o-users" class="size-12 mx-auto mb-2 opacity-30" />
                                <p>Tidak ada user ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->userList->hasPages())
            <div class="mt-4 px-1">
                {{ $this->userList->links() }}
            </div>
        @endif
    </x-mary-card>

    {{-- Form Modal --}}
    <x-mary-modal wire:model="formModal" :title="$editingId ? __('Edit User') : __('Tambah User Baru')" class="backdrop-blur-sm" box-class="max-w-xl">
        <form id="form-user" wire:submit="save">
            <div class="space-y-4">
                <x-mary-input wire:model="name" label="Nama Lengkap" placeholder="Masukkan nama lengkap" required />
                <x-mary-input wire:model="email" label="Email" type="email" placeholder="contoh@email.com" required />
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-mary-input wire:model="phone" label="Telepon" placeholder="08xxx" />
                    <x-mary-input
                        wire:model="password"
                        label="{{ $editingId ? 'Password Baru (opsional)' : 'Password' }}"
                        type="password"
                        placeholder="{{ $editingId ? 'Kosongkan jika tidak diubah' : 'Min. 8 karakter' }}"
                        :required="! $editingId" />
                </div>
                <x-mary-textarea wire:model="address" label="Alamat" placeholder="Alamat lengkap" rows="2" />

                {{-- Role selector --}}
                <div>
                    <label class="block text-sm font-semibold text-base-content mb-2">
                        Role <span class="text-error">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach([UserRole::Nasabah, UserRole::Koperasi, UserRole::Admin, UserRole::Owner] as $role)
                            @php
                                $color = match($role->value) {
                                    'admin' => 'border-error/40 has-[:checked]:border-error has-[:checked]:bg-error/10',
                                    'owner' => 'border-warning/40 has-[:checked]:border-warning has-[:checked]:bg-warning/10',
                                    'nasabah' => 'border-info/40 has-[:checked]:border-info has-[:checked]:bg-info/10',
                                    'koperasi' => 'border-success/40 has-[:checked]:border-success has-[:checked]:bg-success/10',
                                    default => 'border-base-300',
                                };
                                $badgeColor = match($role->value) {
                                    'admin' => 'text-error',
                                    'owner' => 'text-warning',
                                    'nasabah' => 'text-info',
                                    'koperasi' => 'text-success',
                                    default => '',
                                };
                                $desc = match($role->value) {
                                    'admin' => 'Akses penuh sistem',
                                    'owner' => 'Akses pemilik',
                                    'nasabah' => 'Nabung sampah',
                                    'koperasi' => 'Anggota koperasi',
                                    default => '',
                                };
                            @endphp
                            <label class="flex items-start gap-3 p-3 border-2 rounded-lg cursor-pointer transition-all {{ $color }}">
                                <input
                                    type="checkbox"
                                    wire:model="selectedRoles"
                                    value="{{ $role->value }}"
                                    class="checkbox checkbox-sm mt-0.5" />
                                <div>
                                    <p class="text-sm font-semibold {{ $badgeColor }}">{{ $role->label() }}</p>
                                    <p class="text-xs text-base-content/50">{{ $desc }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedRoles')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('selectedRoles.*')
                        <p class="text-error text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </form>
        <x-slot:actions>
            <x-mary-button label="Batal" class="btn-ghost" wire:click="$set('formModal', false)" />
            <x-mary-button
                :label="$editingId ? __('Perbarui') : __('Buat User')"
                icon="o-check"
                class="btn-primary"
                type="submit"
                form="form-user"
                wire:loading.attr="disabled" />
        </x-slot:actions>
    </x-mary-modal>

    {{-- Delete Confirm Modal --}}
    <x-mary-modal wire:model="deleteModal" title="Hapus User" class="backdrop-blur-sm" box-class="max-w-sm">
        <p class="text-sm text-base-content/70">User ini akan dihapus permanen. Transaksi terkait tetap tersimpan.</p>
        <x-slot:actions>
            <x-mary-button label="Batal" class="btn-ghost" wire:click="$set('deleteModal', false)" />
            <x-mary-button label="Hapus" icon="o-trash" class="btn-error" wire:click="delete" wire:loading.attr="disabled" />
        </x-slot:actions>
    </x-mary-modal>
</section>

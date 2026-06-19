<?php

use App\Concerns\PartnerValidationRules;
use App\Models\Partner;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Mitra')] class extends Component {
    use PartnerValidationRules, Toast, WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $type = 'pengepul';

    public string $phone = '';

    public string $email = '';

    public string $address = '';

    public string $notes = '';

    public bool $is_active = true;

    public ?int $deletingId = null;

    public bool $formModal = false;

    public bool $deleteModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => __('Informasi Mitra'), 'sortable' => false],
            ['key' => 'type_label', 'label' => __('Jenis'), 'class' => 'hidden md:table-cell', 'sortable' => false],
            ['key' => 'phone', 'label' => __('Telepon'), 'class' => 'hidden lg:table-cell', 'sortable' => false],
            ['key' => 'address', 'label' => __('Alamat Lengkap'), 'sortable' => false, 'class' => 'w-3/12'],
            ['key' => 'status_label', 'label' => __('Status'), 'sortable' => false],
        ];
    }

    #[Computed]
    public function partners()
    {
        return Partner::query()
            ->when($this->search !== '', function ($q) {
                $like = '%'.$this->search.'%';
                $q->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('phone', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function rules(): array
    {
        return $this->partnerRules();
    }

    public function startCreating(): void
    {
        $this->resetForm();
        $this->formModal = true;
    }

    public function startEditing(int $id): void
    {
        $partner = Partner::findOrFail($id);

        $this->editingId = $partner->id;
        $this->name = $partner->name;
        $this->type = $partner->type;
        $this->phone = (string) ($partner->phone ?? '');
        $this->email = (string) ($partner->email ?? '');
        $this->address = (string) ($partner->address ?? '');
        $this->notes = (string) ($partner->notes ?? '');
        $this->is_active = (bool) $partner->is_active;

        $this->formModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            Partner::findOrFail($this->editingId)->update($validated);
            $this->success(__('Mitra berhasil diperbarui.'));
        } else {
            Partner::create($validated);
            $this->success(__('Mitra berhasil ditambahkan.'));
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

        try {
            Partner::findOrFail($this->deletingId)->delete();
            $this->success(__('Mitra dihapus.'));
        } catch (\Illuminate\Database\QueryException) {
            $this->error(__('Mitra tidak bisa dihapus karena masih ada transaksi penjualan terkait.'));
        }

        $this->deletingId = null;
        $this->deleteModal = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'phone', 'email', 'address', 'notes']);
        $this->type = 'pengepul';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function typeOptions(): array
    {
        return [
            ['id' => 'pengepul', 'name' => __('Pengepul')],
            ['id' => 'pabrik', 'name' => __('Pabrik')],
            ['id' => 'lainnya', 'name' => __('Lainnya')],
        ];
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Mitra') }}"
        subtitle="{{ __('Pengepul/pabrik penerima sampah dari Bank Sampah.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari mitra...') }}"
                clearable
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary"
                label="{{ __('Tambah Mitra') }}"
                wire:click="startCreating"
                data-test="partner-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <!-- Bungkus tabel dengan card putih -->
    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200">
        <x-mary-table
            :headers="$this->headers"
            :rows="$this->partners"
            with-pagination
        >
            <!-- Kolom Informasi Mitra (Nama + Badge Email) -->
            @scope('cell_name', $row)
                <div class="font-medium text-base-content">{{ $row->name }}</div>
                @if ($row->email)
                    <x-mary-badge value="{{ $row->email }}" class="badge-success badge-soft badge-sm mt-1" />
                @endif
            @endscope

            <!-- Kolom Jenis (Badge Outline) -->
            @scope('cell_type_label', $row)
                <div class="badge badge-outline text-xs font-medium">{{ ucfirst($row->type) }}</div>
            @endscope

            <!-- Kolom Telepon (Ikon + Nomor) -->
            @scope('cell_phone', $row)
                <div class="flex items-center gap-2 text-base-content/80">
                    <x-mary-icon name="o-phone" class="w-4 h-4 text-success/70" />
                    <span class="text-sm">{{ $row->phone ?: '-' }}</span>
                </div>
            @endscope

            <!-- Kolom Alamat Lengkap -->
            @scope('cell_address', $row)
                <div class="max-w-xs truncate text-sm text-base-content/70" title="{{ $row->address }}">
                    {{ $row->address ?: '-' }}
                </div>
            @endscope

            <!-- Kolom Status -->
            @scope('cell_status_label', $row)
                @if ($row->is_active)
                    <x-mary-badge value="{{ __('Aktif') }}" class="badge-success badge-soft badge-sm font-medium" />
                @else
                    <x-mary-badge value="{{ __('Non-aktif') }}" class="badge-ghost badge-sm font-medium" />
                @endif
            @endscope

            <!-- Kolom Aksi -->
            @scope('actions', $row)
                <div class="flex items-center gap-1">
                    <x-mary-button
                        icon="o-pencil-square"
                        wire:click="startEditing({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/70"
                    />
                    <x-mary-button
                        icon="o-trash"
                        wire:click="confirmDelete({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/70 hover:text-error"
                    />
                </div>
            @endscope
        </x-mary-table>
    </div>

    <!-- Modal Form (Tambah/Edit) -->
    <x-mary-modal
        wire:model="formModal"
        title="{{ $editingId ? __('Edit Mitra') : __('Tambah Mitra') }}"
        separator
        box-class="max-w-xl"
    >
        <x-mary-form wire:submit="save" no-separator>
            <div class="grid gap-4 md:grid-cols-2">
                <x-mary-input wire:model="name" label="{{ __('Nama mitra') }}" icon="o-building-office" required />
                <x-mary-select
                    wire:model="type"
                    label="{{ __('Jenis') }}"
                    :options="$this->typeOptions()"
                    option-label="name"
                    option-value="id"
                    required
                />
                <x-mary-input wire:model="phone" label="{{ __('Telepon') }}" icon="o-phone" />
                <x-mary-input wire:model="email" label="{{ __('Email') }}" icon="o-envelope" type="email" />
            </div>

            <x-mary-textarea wire:model="address" label="{{ __('Alamat Lengkap') }}" rows="3" placeholder="Masukkan alamat lengkap mitra..." />
            <x-mary-textarea wire:model="notes" label="{{ __('Catatan') }}" rows="2" />
            <x-mary-toggle wire:model="is_active" label="{{ __('Aktif') }}" right />

            <x-slot:actions>
                <x-mary-button label="{{ __('Batal') }}" @click="$wire.formModal = false" />
                <x-mary-button type="submit" label="{{ __('Simpan') }}" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <!-- Modal Hapus -->
    <x-mary-modal wire:model="deleteModal" title="{{ __('Hapus Mitra') }}" box-class="max-w-md">
        <p class="text-sm text-base-content/70">
            {{ __('Mitra tidak bisa dihapus jika masih punya transaksi penjualan. Pertimbangkan nonaktifkan saja.') }}
        </p>

        <x-slot:actions>
            <x-mary-button label="{{ __('Batal') }}" @click="$wire.deleteModal = false" />
            <x-mary-button label="{{ __('Hapus') }}" class="btn-error" wire:click="delete" spinner />
        </x-slot:actions>
    </x-mary-modal>
</section>
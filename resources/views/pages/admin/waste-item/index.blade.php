<?php

use App\Concerns\WasteItemValidationRules;
use App\Concerns\WastePriceValidationRules;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use App\Models\WastePrice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Barang Sampah')] class extends Component {
    use Toast, WasteItemValidationRules, WastePriceValidationRules, WithPagination;

    public string $search = '';

    public ?int $category_filter = null;

    public ?int $editingId = null;

    public ?int $waste_category_id = null;

    public string $code = '';

    public string $name = '';

    public string $unit = 'kg';

    public string $price_per_unit = '';

    public string $description = '';

    public bool $is_active = true;

    public ?int $deletingId = null;

    public bool $formModal = false;

    public bool $deleteModal = false;

    public ?int $historyItemId = null;

    public bool $historyModal = false;

    public ?int $priceItemId = null;

    public string $price_new = '';

    public string $price_effective_from = '';

    public string $price_notes = '';

    public bool $priceModal = false;

    public function mount(): void
    {
        $this->price_effective_from = now()->toDateString();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => __('Informasi Barang'), 'class' => 'w-80'],
            ['key' => 'category_label', 'label' => __('Kategori'), 'class' => 'hidden md:table-cell w-40'],
            ['key' => 'unit', 'label' => __('Satuan'), 'class' => 'hidden md:table-cell w-28'],
            ['key' => 'price_label', 'label' => __('Harga Aktif'), 'class' => 'w-40', 'sortable' => false],
            ['key' => 'status_label', 'label' => __('Status'), 'class' => 'w-32 text-center', 'sortable' => false],
        ];
    }

    #[Computed]
    public function items()
    {
        return WasteItem::query()
            ->with('category:id,name,code_prefix')
            ->when($this->search !== '', fn ($q) => $q->where(function ($inner) {
                $inner->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            }))
            ->when($this->category_filter, fn ($q) => $q->where('waste_category_id', $this->category_filter))
            ->orderBy('code')
            ->paginate(20);
    }

    #[Computed]
    public function categoryOptions(): array
    {
        return WasteCategory::query()
            ->orderBy('code_prefix')
            ->get(['id', 'name', 'code_prefix'])
            ->map(fn ($c) => ['id' => $c->id, 'name' => "{$c->code_prefix} — {$c->name}"])
            ->toArray();
    }

    #[Computed]
    public function historyItem(): ?WasteItem
    {
        if (! $this->historyItemId) {
            return null;
        }

        return WasteItem::with(['prices' => fn ($q) => $q->orderByDesc('effective_from')->orderByDesc('id')])
            ->find($this->historyItemId);
    }

    #[Computed]
    public function priceItem(): ?WasteItem
    {
        return $this->priceItemId ? WasteItem::find($this->priceItemId) : null;
    }

    public function rules(): array
    {
        return $this->wasteItemRules($this->editingId, includePrice: $this->editingId === null);
    }

    public function startCreating(): void
    {
        $this->resetForm();
        $this->formModal = true;
    }

    public function startEditing(int $id): void
    {
        $item = WasteItem::findOrFail($id);

        $this->editingId = $item->id;
        $this->waste_category_id = $item->waste_category_id;
        $this->code = $item->code;
        $this->name = $item->name;
        $this->unit = $item->unit;
        $this->price_per_unit = (string) (float) $item->price_per_unit;
        $this->description = (string) ($item->description ?? '');
        $this->is_active = (bool) $item->is_active;

        $this->formModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['code'] = strtoupper($validated['code']);

        DB::transaction(function () use ($validated) {
            if ($this->editingId) {
                $item = WasteItem::findOrFail($this->editingId);

                $item->update([
                    ...$validated,
                    'slug' => Str::slug($validated['name'].'-'.$validated['code']),
                ]);

                $this->success(__('Barang berhasil diperbarui.'));
            } else {
                $item = WasteItem::create([
                    ...$validated,
                    'slug' => Str::slug($validated['name'].'-'.$validated['code']),
                ]);

                WastePrice::create([
                    'waste_item_id' => $item->id,
                    'price_per_unit' => (float) $validated['price_per_unit'],
                    'effective_from' => now()->toDateString(),
                    'notes' => 'Harga awal saat barang dibuat.',
                    'created_by' => Auth::id(),
                ]);

                $this->success(__('Barang berhasil ditambahkan.'));
            }
        });

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
            WasteItem::findOrFail($this->deletingId)->delete();
            $this->success(__('Barang dihapus.'));
        } catch (\Illuminate\Database\QueryException $e) {
            $this->error(__('Tidak bisa hapus: barang masih dipakai di transaksi.'));
        }

        $this->deletingId = null;
        $this->deleteModal = false;
    }

    public function showHistory(int $id): void
    {
        $this->historyItemId = $id;
        $this->historyModal = true;
    }

    public function startSettingPrice(int $id): void
    {
        $this->resetPriceForm();
        $this->priceItemId = $id;
        $this->priceModal = true;
    }

    public function savePrice(): void
    {
        $validated = $this->validate([
            'priceItemId' => ['required', 'integer', \Illuminate\Validation\Rule::exists(WasteItem::class, 'id')],
            'price_new' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_effective_from' => ['required', 'date'],
            'price_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($validated) {
            WastePrice::create([
                'waste_item_id' => $validated['priceItemId'],
                'price_per_unit' => (float) $validated['price_new'],
                'effective_from' => $validated['price_effective_from'],
                'notes' => $validated['price_notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            WasteItem::whereKey($validated['priceItemId'])
                ->update(['price_per_unit' => (float) $validated['price_new']]);
        });

        $this->priceModal = false;
        $this->success(__('Harga baru tersimpan.'));
        $this->resetPriceForm();
        unset($this->items);
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'waste_category_id', 'code', 'name', 'unit', 'price_per_unit', 'description', 'is_active']);
        $this->is_active = true;
        $this->unit = 'kg';
        $this->resetErrorBag();
    }

    private function resetPriceForm(): void
    {
        $this->reset(['priceItemId', 'price_new', 'price_notes']);
        $this->price_effective_from = now()->toDateString();
        $this->resetErrorBag();
    }
}; ?>

<section class="w-full space-y-4">
    <x-mary-header
        title="{{ __('Barang Sampah') }}"
        subtitle="{{ __('Master barang + riwayat harga. Ubah harga via tombol Set Harga agar riwayat tercatat.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <div class="flex flex-col gap-2 md:flex-row">
                <x-mary-input
                    wire:model.live.debounce.300ms="search"
                    icon="o-magnifying-glass"
                    placeholder="{{ __('Cari barang / kode...') }}"
                    clearable
                    class="input-md bg-base-100 shadow-sm border-base-300 focus:border-primary md:w-60"
                />
                <x-mary-select
                    wire:model.live="category_filter"
                    :options="$this->categoryOptions"
                    option-label="name"
                    option-value="id"
                    placeholder="{{ __('Semua kategori') }}"
                    icon="o-tag"
                    class="bg-base-100 shadow-sm border-base-300 md:w-56"
                />
            </div>
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary shadow-sm font-semibold text-sm"
                wire:click="startCreating"
                label="{{ __('Tambah Barang') }}"
                data-test="item-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="overflow-hidden rounded-xl border border-base-200 bg-base-100 shadow-sm">
        <x-mary-table
            :headers="$this->headers"
            :rows="$this->items"
            with-pagination
            per-page="perPage"
            class="table-sm"
        >
            {{-- Kolom Informasi Barang --}}
            @scope('cell_name', $row)
                <div class="flex flex-col gap-1 py-2">
                    <span class="font-semibold text-[15px] leading-tight tracking-tight text-base-content hover:text-primary transition-colors cursor-pointer">
                        {{ $row->name }}
                    </span>
                    <span class="text-xs font-mono font-bold tracking-widest text-primary bg-primary/10 px-2 py-0.5 rounded-md border border-primary/20 w-fit">
                        {{ $row->code }}
                    </span>
                    @if ($row->description)
                        <span class="text-xs text-base-content/45 font-normal leading-relaxed max-w-[260px] truncate" title="{{ $row->description }}">
                            {{ $row->description }}
                        </span>
                    @endif
                </div>
            @endscope

            {{-- Kolom Kategori --}}
            @scope('cell_category_label', $row)
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                        <x-mary-icon name="o-folder" class="w-4 h-4" />
                    </div>
                    <span class="text-sm font-medium text-base-content/80">{{ $row->category?->name ?? '—' }}</span>
                </div>
            @endscope

            {{-- Kolom Satuan --}}
            @scope('cell_unit', $row)
                <span class="text-sm font-semibold text-base-content/70 bg-base-200/60 px-3 py-1 rounded-lg border border-base-300/40">
                    {{ $row->unit }}
                </span>
            @endscope

            {{-- Kolom Harga --}}
            @scope('cell_price_label', $row)
                @if ((float) $row->price_per_unit > 0)
                    <div class="flex flex-col items-start gap-0">
                        <span class="font-bold text-xl leading-none text-base-content/90">
                            Rp {{ number_format((float) $row->price_per_unit, 0, ',', '.') }}
                        </span>
                        <span class="text-[10px] text-base-content/45 font-semibold uppercase tracking-widest mt-0.5">
                            per {{ $row->unit }}
                        </span>
                    </div>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-warning/10 text-warning border border-warning/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-warning inline-block"></span>
                        {{ __('Belum di-set') }}
                    </span>
                @endif
            @endscope

            {{-- Kolom Status --}}
            @scope('cell_status_label', $row)
                <div class="text-center">
                    @if ($row->is_active)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-success/10 text-success border border-success/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-success inline-block"></span>
                            {{ __('Aktif') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-base-200 text-base-content/50 border border-base-300/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-base-content/30 inline-block"></span>
                            {{ __('Non-aktif') }}
                        </span>
                    @endif
                </div>
            @endscope

            {{-- Kolom Actions --}}
            @scope('actions', $row)
                <div class="flex items-center gap-0.5 justify-end">
                    <x-mary-button
                        icon="o-banknotes"
                        wire:click="startSettingPrice({{ $row->id }})"
                        class="btn-ghost btn-sm text-success hover:bg-success/10 rounded-lg"
                        tooltip="{{ __('Set Harga') }}"
                        data-test="item-set-price-{{ $row->id }}"
                    />
                    <x-mary-button
                        icon="o-clock"
                        wire:click="showHistory({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/50 hover:text-info hover:bg-info/10 rounded-lg"
                        tooltip="{{ __('Riwayat harga') }}"
                    />
                    <x-mary-button
                        icon="o-pencil-square"
                        wire:click="startEditing({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg"
                        data-test="item-edit-{{ $row->id }}"
                    />
                    <x-mary-button
                        icon="o-trash"
                        wire:click="confirmDelete({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/40 hover:text-error hover:bg-error/10 rounded-lg"
                        data-test="item-delete-{{ $row->id }}"
                    />
                </div>
            @endscope
        </x-mary-table>
    </div>

    {{-- Modal Tambah/Edit Barang --}}
    <x-mary-modal
        wire:model="formModal"
        title="{{ $editingId ? __('Edit Barang') : __('Tambah Barang Baru') }}"
        subtitle="{{ $editingId ? __('Ubah metadata barang. Harga diatur lewat tombol Set Harga.') : __('Isi kategori, kode unik & harga awal. Riwayat harga tercatat otomatis.') }}"
        separator
        box-class="max-w-xl rounded-2xl"
    >
        <x-mary-form wire:submit="save" no-separator class="space-y-5">

            <x-mary-select
                wire:model="waste_category_id"
                label="{{ __('Kategori') }}"
                :options="$this->categoryOptions"
                option-label="name"
                option-value="id"
                placeholder="{{ __('Pilih kategori') }}"
                icon="o-tag"
                class="select-bordered"
                required
            />

            <div class="grid gap-4 md:grid-cols-4">
                <x-mary-input
                    wire:model="code"
                    label="{{ __('Kode') }}"
                    icon="o-hashtag"
                    placeholder="KT1"
                    maxlength="16"
                    class="uppercase font-mono font-bold input-bordered tracking-widest"
                    required
                />
                <div class="md:col-span-3">
                    <x-mary-input
                        wire:model="name"
                        label="{{ __('Nama Barang') }}"
                        placeholder="Contoh: Dus / PET Botol Bersih"
                        class="input-bordered font-medium"
                        required
                    />
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <x-mary-input
                    wire:model="unit"
                    label="{{ __('Satuan') }}"
                    icon="o-scale"
                    placeholder="kg"
                    class="input-bordered"
                    required
                />
                @if (! $editingId)
                    <x-mary-input
                        wire:model="price_per_unit"
                        label="{{ __('Harga Awal (Rp)') }}"
                        icon="o-banknotes"
                        type="number"
                        step="0.01"
                        min="0"
                        class="input-bordered"
                        required
                    />
                @endif
            </div>

            <x-mary-textarea
                wire:model="description"
                label="{{ __('Deskripsi (Opsional)') }}"
                placeholder="Tuliskan spesifikasi barang ini..."
                rows="2"
                class="textarea-bordered text-sm leading-relaxed"
            />

            <div class="flex items-center justify-between p-4 rounded-xl border border-base-200 bg-base-200/30">
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-semibold tracking-tight text-base-content">{{ __('Status Aktif Barang') }}</span>
                    <span class="text-xs text-base-content/55 leading-relaxed">{{ __('Barang non-aktif tidak bisa dipilih saat transaksi.') }}</span>
                </div>
                <x-mary-toggle wire:model="is_active" class="toggle-success" right />
            </div>

            <x-slot:actions>
                <x-mary-button label="{{ __('Batalkan') }}" @click="$wire.formModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button
                    label="{{ __('Simpan Barang') }}"
                    class="btn-primary font-semibold shadow-sm px-5 text-sm"
                    type="submit"
                    icon="o-check"
                    spinner="save"
                    data-test="item-save-button"
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    {{-- Modal Set Harga --}}
    <x-mary-modal
        wire:model="priceModal"
        title="{{ __('Set Harga Baru') }}"
        subtitle="{{ $this->priceItem?->code }} — {{ $this->priceItem?->name }}"
        separator
        box-class="max-w-lg rounded-2xl"
    >
        <x-mary-form wire:submit="savePrice" no-separator class="space-y-5">
            <x-mary-input
                wire:model="price_new"
                label="{{ __('Harga per Satuan (Rp)') }}"
                icon="o-banknotes"
                type="number"
                step="0.01"
                min="0"
                class="input-bordered"
                required
            />
            <x-mary-input
                wire:model="price_effective_from"
                label="{{ __('Berlaku Sejak') }}"
                icon="o-calendar"
                type="date"
                class="input-bordered"
                required
            />
            <x-mary-textarea
                wire:model="price_notes"
                label="{{ __('Catatan (Opsional)') }}"
                placeholder="{{ __('Alasan perubahan harga...') }}"
                rows="2"
                class="textarea-bordered text-sm"
            />

            <x-slot:actions>
                <x-mary-button label="{{ __('Batalkan') }}" @click="$wire.priceModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button
                    label="{{ __('Simpan Harga') }}"
                    class="btn-primary font-semibold shadow-sm px-5 text-sm"
                    type="submit"
                    icon="o-check"
                    spinner="savePrice"
                    data-test="price-save-button"
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    {{-- Modal Hapus --}}
    <x-mary-modal wire:model="deleteModal" title="{{ __('Konfirmasi Hapus Barang') }}" box-class="max-w-md rounded-2xl">
        <div class="flex flex-col gap-3 py-2">
            <p class="text-sm text-base-content/65 leading-relaxed">
                {{ __('Barang hanya bisa dihapus jika belum pernah dipakai dalam transaksi. Jika sudah dipakai, ubah status menjadi Non-aktif saja.') }}
            </p>
        </div>

        <x-slot:actions>
            <x-mary-button label="{{ __('Batal') }}" @click="$wire.deleteModal = false" class="btn-ghost text-sm font-medium" />
            <x-mary-button
                label="{{ __('Ya, Hapus') }}"
                class="btn-error font-semibold text-sm px-4"
                wire:click="delete"
                spinner
                data-test="item-confirm-delete"
            />
        </x-slot:actions>
    </x-mary-modal>

    {{-- Modal Riwayat Harga --}}
    <x-mary-modal
        wire:model="historyModal"
        title="{{ __('Riwayat Harga') }}"
        subtitle="{{ $this->historyItem?->code }} — {{ $this->historyItem?->name }}"
        separator
        box-class="max-w-xl rounded-2xl"
    >
        @if ($this->historyItem)
            <div class="max-h-[60vh] overflow-y-auto rounded-xl border border-base-200">
                <table class="table table-sm w-full">
                    <thead class="bg-base-200/50">
                        <tr>
                            <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">{{ __('Berlaku Sejak') }}</th>
                            <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">{{ __('Harga') }}</th>
                            <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">{{ __('Catatan') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($this->historyItem->prices as $price)
                            <tr wire:key="price-{{ $price->id }}" class="hover:bg-base-200/30 transition-colors">
                                <td class="whitespace-nowrap text-sm font-medium text-base-content/80">
                                    {{ $price->effective_from->format('d M Y') }}
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="font-bold text-sm text-base-content/90">
                                        Rp {{ number_format((float) $price->price_per_unit, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-xs text-base-content/55 leading-relaxed">{{ $price->notes ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-8 text-base-content/40 text-sm">
                                    {{ __('Belum ada riwayat harga.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <x-slot:actions>
            <x-mary-button label="{{ __('Tutup') }}" @click="$wire.historyModal = false" class="btn-ghost text-sm font-medium" />
        </x-slot:actions>
    </x-mary-modal>
</section>
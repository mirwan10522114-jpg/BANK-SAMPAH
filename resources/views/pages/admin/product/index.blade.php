<?php

use App\Concerns\ProductValidationRules;
use App\Models\Product;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Produk')] class extends Component {
    use ProductValidationRules, Toast, WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public string $image = '';

    public string $unit = 'pcs';

    public string $price = '';

    public string $points_cost = '0';

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
            ['key' => 'name', 'label' => __('Informasi Produk'), 'class' => 'w-80'],
            ['key' => 'price_label', 'label' => __('Harga Jual'), 'class' => 'w-36'],
            ['key' => 'points_cost_label', 'label' => __('Poin Tukar'), 'class' => 'hidden md:table-cell w-36'],
            ['key' => 'stock_label', 'label' => __('Sisa Stok'), 'class' => 'hidden lg:table-cell w-32'],
            ['key' => 'status_label', 'label' => __('Status'), 'class' => 'w-28 text-center', 'sortable' => false],
        ];
    }

    #[Computed]
    public function products()
    {
        return Product::query()
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->paginate(15);
    }

    public function rules(): array
    {
        return $this->productRules($this->editingId);
    }

    public function startCreating(): void
    {
        $this->resetForm();
        $this->formModal = true;
    }

    public function startEditing(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->description = (string) ($product->description ?? '');
        $this->image = (string) ($product->image ?? '');
        $this->unit = $product->unit;
        $this->price = (string) $product->price;
        $this->points_cost = (string) (int) $product->points_cost;
        $this->is_active = (bool) $product->is_active;

        $this->formModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($validated);
            $this->success(__('Produk berhasil diperbarui.'));
        } else {
            Product::create([
                ...$validated,
                'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            ]);
            $this->success(__('Produk berhasil ditambahkan.'));
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
            Product::findOrFail($this->deletingId)->delete();
            $this->success(__('Produk dihapus.'));
        } catch (\Illuminate\Database\QueryException) {
            $this->error(__('Produk tidak bisa dihapus karena masih terkait dengan transaksi pengolahan.'));
        }

        $this->deletingId = null;
        $this->deleteModal = false;
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'description', 'image', 'price']);
        $this->points_cost = '0';
        $this->unit = 'pcs';
        $this->is_active = true;
        $this->resetErrorBag();
    }
}; ?>

<section class="w-full space-y-4">
    <x-mary-header
        title="{{ __('Produk Hasil Olahan') }}"
        subtitle="{{ __('Kelola master data produk seperti paving block, kompos, kursi, atau pakan ternak.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari nama produk...') }}"
                class="input-md bg-base-100 shadow-sm border-base-300 focus:border-primary w-72"
                clearable
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary shadow-sm font-semibold text-sm"
                label="{{ __('Tambah Produk') }}"
                wire:click="startCreating"
                data-test="product-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="overflow-hidden rounded-xl border border-base-200 bg-base-100 shadow-sm">
        <x-mary-table
            :headers="$this->headers"
            :rows="$this->products"
            with-pagination
            per-page="perPage"
            class="table-sm"
        >
            @scope('cell_name', $row)
                <div class="flex items-center gap-4 py-2">
                    <div class="avatar">
                        <div class="w-12 h-12 rounded-xl bg-base-200 flex items-center justify-center border border-base-300/60 shadow-sm">
                            @if($row->image)
                                <img src="{{ $row->image }}" alt="{{ $row->name }}" class="object-cover rounded-xl" onerror="this.style.display='none'" />
                            @else
                                <x-mary-icon name="o-cube" class="w-6 h-6 text-base-content/30" />
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col gap-0.5">
                        <span class="font-bold text-sm text-base-content hover:text-primary transition-colors cursor-pointer">
                            {{ $row->name }}
                        </span>
                        @if ($row->description)
                            <span class="text-xs text-base-content/50 font-medium max-w-[220px] truncate" title="{{ $row->description }}">
                                {{ $row->description }}
                            </span>
                        @endif
                    </div>
                </div>
            @endscope

            @scope('cell_price_label', $row)
                <span class="text-sm font-mono font-medium text-base-content/80 bg-base-200/50 px-2.5 py-1 rounded border border-base-300/30">
                    Rp {{ number_format((float) $row->price, 0, ',', '.') }}
                </span>
            @endscope

            @scope('cell_points_cost_label', $row)
                @if ((int) $row->points_cost > 0)
                    <div class="flex items-center gap-1.5 text-accent font-semibold text-sm">
                        <x-mary-icon name="o-sparkles" class="w-4 h-4" />
                        {{ number_format((int) $row->points_cost, 0, ',', '.') }}
                    </div>
                @else
                    <span class="text-base-content/30 text-xs">—</span>
                @endif
            @endscope

            @scope('cell_stock_label', $row)
                <div class="flex items-baseline gap-1.5">
                    <span class="font-bold text-sm text-base-content/90">
                        {{ rtrim(rtrim(number_format((float) $row->stock, 3, ',', '.'), '0'), ',') }}
                    </span>
                    <span class="text-xs text-base-content/50 font-semibold uppercase tracking-wider">{{ $row->unit }}</span>
                </div>
            @endscope

            @scope('cell_status_label', $row)
                <div class="text-center">
                    @if ($row->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success border border-success/20">
                            {{ __('Aktif') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-base-200 text-base-content/60 border border-base-300/40">
                            {{ __('Non-aktif') }}
                        </span>
                    @endif
                </div>
            @endscope

            @scope('actions', $row)
                <div class="flex items-center gap-0.5 justify-end">
                    <x-mary-button 
                        icon="o-pencil-square" 
                        wire:click="startEditing({{ $row->id }})" 
                        class="btn-ghost btn-sm text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg" 
                    />
                    <x-mary-button 
                        icon="o-trash" 
                        wire:click="confirmDelete({{ $row->id }})" 
                        class="btn-ghost btn-sm text-base-content/40 hover:text-error hover:bg-error/10 rounded-lg" 
                    />
                </div>
            @endscope
        </x-mary-table>
    </div>

    <x-mary-modal
        wire:model="formModal"
        title="{{ $editingId ? __('Edit Data Produk') : __('Tambah Produk Baru') }}"
        subtitle="{{ __('Lengkapi informasi detail produk hasil olahan untuk ditampilkan di katalog penjualan.') }}"
        separator
        box-class="max-w-2xl rounded-2xl"
    >
        <x-mary-form wire:submit="save" no-separator class="space-y-6">
            
            <div class="grid gap-5 md:grid-cols-2">
                
                <x-mary-input wire:model="name" label="{{ __('Nama Produk') }}" icon="o-cube" placeholder="Contoh: Paving Block Hexagon" required class="input-bordered" />
                <x-mary-input wire:model="unit" label="{{ __('Satuan Produk') }}" icon="o-scale" placeholder="pcs, kg, liter..." required class="input-bordered" />

                <x-mary-input wire:model="price" label="{{ __('Harga Jual') }}" type="number" step="0.01" min="0" prefix="Rp" required class="input-bordered" />
                <x-mary-input
                    wire:model="points_cost"
                    label="{{ __('Harga Poin Tukar') }}"
                    icon="o-sparkles"
                    type="number"
                    min="0"
                    hint="{{ __('Isi 0 jika tidak bisa ditukar poin.') }}"
                    required
                    class="input-bordered"
                />

                <div class="md:col-span-2">
                    <x-mary-textarea wire:model="description" label="{{ __('Deskripsi Singkat') }}" placeholder="Tuliskan spesifikasi atau detail kegunaan produk..." rows="2" class="textarea-bordered" />
                </div>

                <div class="md:col-span-2">
                    <x-mary-input wire:model="image" label="{{ __('URL Gambar Produk (Opsional)') }}" icon="o-photo" placeholder="/images/demo/merchandise.jpg" class="input-bordered" />
                </div>
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl border border-base-200 bg-base-200/30">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-semibold text-base-content">{{ __('Status Visibilitas Produk') }}</span>
                    <span class="text-xs text-base-content/60">{{ __('Jika non-aktif, produk tidak akan muncul di halaman penjualan/penukaran.') }}</span>
                </div>
                <x-mary-toggle wire:model="is_active" class="toggle-success" right />
            </div>

            <x-slot:actions>
                <x-mary-button label="{{ __('Batalkan') }}" @click="$wire.formModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button 
                    type="submit" 
                    label="{{ __('Simpan Produk') }}" 
                    class="btn-primary font-semibold shadow-sm px-5 text-sm" 
                    icon="o-check"
                    spinner="save" 
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <x-mary-modal wire:model="deleteModal" title="{{ __('Konfirmasi Hapus Produk') }}" box-class="max-w-md rounded-2xl">
        <div class="flex flex-col gap-3 py-2">
            <p class="text-sm text-base-content/70 leading-relaxed">
                {{ __('Apakah Anda yakin ingin menghapus produk ini? Produk yang sudah pernah digunakan dalam transaksi penjualan, penukaran, atau pengolahan tidak dapat dihapus demi menjaga integritas data sistem.') }}
            </p>
        </div>

        <x-slot:actions>
            <x-mary-button label="{{ __('Batal') }}" @click="$wire.deleteModal = false" class="btn-ghost text-sm font-medium" />
            <x-mary-button 
                label="{{ __('Ya, Hapus') }}" 
                class="btn-error font-semibold text-sm px-4" 
                wire:click="delete" 
                spinner 
            />
        </x-slot:actions>
    </x-mary-modal>
</section>  
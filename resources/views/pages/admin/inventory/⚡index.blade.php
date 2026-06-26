<?php

use App\Models\Inventory;
use App\Models\WasteCategory;
use App\Models\WasteItem;
use App\Services\InventoryService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Inventory Sampah')] class extends Component {
    public string $search = '';

    public ?int $category_filter = null;

    #[Url(as: 'source')]
    public string $source = InventoryService::SOURCE_NABUNG;

    public function mount(): void
    {
        if (! in_array($this->source, [InventoryService::SOURCE_NABUNG, InventoryService::SOURCE_SEDEKAH], true)) {
            $this->source = InventoryService::SOURCE_NABUNG;
        }
    }

    public function updatingSource(): void
    {
        // Allow URL to sync when source changes via tab click.
    }

    #[Computed]
    public function rows()
    {
        $stocks = Inventory::query()
            ->where('source', $this->source)
            ->pluck('stock', 'waste_item_id');

        return WasteItem::query()
            ->with('category:id,name,code_prefix')
            ->when($this->search !== '', fn ($q) => $q->where(function ($inner) {
                $inner->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code', 'like', '%'.$this->search.'%');
            }))
            ->when($this->category_filter, fn ($q) => $q->where('waste_category_id', $this->category_filter))
            ->orderBy('code')
            ->get()
            ->map(function (WasteItem $item) use ($stocks) {
                $item->setAttribute('stock', (float) ($stocks[$item->id] ?? 0));

                return $item;
            });
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

    public function setSource(string $source): void
    {
        if (in_array($source, [InventoryService::SOURCE_NABUNG, InventoryService::SOURCE_SEDEKAH], true)) {
            $this->source = $source;
        }
    }

    public function sourceLabel(): string
    {
        return $this->source === InventoryService::SOURCE_NABUNG ? __('Sampah Nabung') : __('Sampah Sedekah');
    }

    public function sourceHint(): string
    {
        return $this->source === InventoryService::SOURCE_NABUNG
            ? __('Stok dari nabung nasabah — dijual ke mitra.')
            : __('Stok dari sedekah — dipakai untuk pengolahan jadi produk.');
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Inventory') }}: {{ $this->sourceLabel() }}"
        subtitle="{{ $this->sourceHint() }}"
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
                    class="md:w-56"
                />
                <x-mary-select
                    wire:model.live="category_filter"
                    :options="$this->categoryOptions"
                    option-label="name"
                    option-value="id"
                    placeholder="{{ __('Semua kategori') }}"
                    icon="o-tag"
                    class="md:w-56"
                />
            </div>
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-clock"
                label="{{ __('Riwayat Pergerakan') }}"
                link="{{ route('admin.inventory.movements', ['source' => $source]) }}"
            />
        </x-slot:actions>
    </x-mary-header>

    <div role="tablist" class="tabs tabs-boxed mb-4 w-fit">
        <button
            type="button"
            wire:click="setSource('nabung')"
            class="tab {{ $source === 'nabung' ? 'tab-active' : '' }}"
            data-test="inventory-tab-nabung"
        >
            {{ __('Sampah Nabung') }}
        </button>
        <button
            type="button"
            wire:click="setSource('sedekah')"
            class="tab {{ $source === 'sedekah' ? 'tab-active' : '' }}"
            data-test="inventory-tab-sedekah"
        >
            {{ __('Sampah Sedekah') }}
        </button>
    </div>

    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" style="font-family:'Inter',system-ui,sans-serif">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase w-20">{{ __('Kode') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Barang') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden md:table-cell">{{ __('Kategori') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden md:table-cell">{{ __('Satuan') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Stok') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden lg:table-cell">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse ($this->rows as $row)
                        @php $stock = (float) $row->stock; @endphp
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-4 py-3 text-sm font-mono text-base-content/70">{{ $row->code }}</td>

                            <td class="px-4 py-3 text-sm font-medium text-base-content">{{ $row->name }}</td>

                            <td class="px-4 py-3 hidden md:table-cell">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-base-200 text-base-content/70">
                                    {{ $row->category?->name ?? '—' }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-base-content/70 hidden md:table-cell">{{ $row->unit }}</td>

                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold {{ $stock > 0 ? 'text-success' : 'text-base-content/40' }}">
                                    {{ rtrim(rtrim(number_format($stock, 1, ',', '.'), '0'), ',') }}
                                </span>
                                <span class="text-xs text-base-content/50">{{ $row->unit }}</span>
                            </td>

                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if (! $row->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-base-200 text-base-content/50">
                                        {{ __('Non-aktif') }}
                                    </span>
                                @elseif ($stock > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success">
                                        {{ __('Tersedia') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-warning/10 text-warning">
                                        {{ __('Kosong') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-base-content/50 italic">
                                {{ __('Tidak ada data barang.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
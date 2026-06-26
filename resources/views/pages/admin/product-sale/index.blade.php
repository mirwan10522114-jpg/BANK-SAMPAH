<?php

use App\Models\ProductSale;
use App\Services\ProductSalesService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Penjualan Produk')] class extends Component {
    use Toast, WithPagination;

    public string $search = '';

    public string $status_filter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'transacted_at_label', 'label' => __('Tanggal'), 'class' => 'hidden md:table-cell w-32 whitespace-nowrap', 'sortable' => false],
            ['key' => 'buyer_label', 'label' => __('Pembeli'), 'class' => 'w-52', 'sortable' => false],
            ['key' => 'items_label', 'label' => __('Produk'), 'class' => 'w-64 max-w-xs', 'sortable' => false],
            ['key' => 'total_value_label', 'label' => __('Total'), 'class' => 'w-32 whitespace-nowrap', 'sortable' => false],
            ['key' => 'status_label', 'label' => __('Status'), 'class' => 'w-32 whitespace-nowrap', 'sortable' => false],
            ['key' => 'method_label', 'label' => __('Metode'), 'class' => 'w-28 whitespace-nowrap', 'sortable' => false],
            ['key' => 'action_label', 'label' => __('Aksi'), 'class' => 'w-16 text-center', 'sortable' => false],
        ];
    }

    #[Computed]
    public function sales()
    {
        return ProductSale::query()
            ->with(['items', 'buyer:id,name'])
            ->when($this->search !== '', fn ($q) => $q->where(function ($inner) {
                $inner->where('buyer_name', 'like', '%'.$this->search.'%')
                    ->orWhere('buyer_phone', 'like', '%'.$this->search.'%');
            }))
            ->when($this->status_filter !== '', fn ($q) => $q->where('payment_status', $this->status_filter))
            ->orderByDesc('transacted_at')
            ->orderByDesc('id')
            ->paginate(15);
    }

    #[Computed]
    public function summary(): array
    {
        $base = ProductSale::query();
        $paid = (clone $base)->paid()->sum('total_value');
        $pending = (clone $base)->pending()->sum('total_value');
        $count = (clone $base)->count();

        return [
            'paid' => (float) $paid,
            'pending' => (float) $pending,
            'count' => (int) $count,
        ];
    }

    public function statusOptions(): array
    {
        return [
            ['id' => 'paid', 'name' => __('Lunas')],
            ['id' => 'pending', 'name' => __('Belum lunas')],
        ];
    }

    public function markPaid(int $id, ProductSalesService $service): void
    {
        $sale = ProductSale::findOrFail($id);
        $service->markPaid($sale);
        $this->success(__('Transaksi ditandai lunas.'));
        unset($this->sales, $this->summary);
    }
}; ?>

<section class="w-full space-y-4">
    <x-mary-header
        title="{{ __('Penjualan Produk') }}"
        subtitle="{{ __('Catatan penjualan produk olahan ke pembeli. Rekap pendapatan per periode ada di dashboard.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <div class="flex flex-col gap-2 md:flex-row">
                <x-mary-input
                    wire:model.live.debounce.300ms="search"
                    icon="o-magnifying-glass"
                    placeholder="{{ __('Cari pembeli / no HP...') }}"
                    clearable
                    class="md:w-56"
                />
                <x-mary-select
                    wire:model.live="status_filter"
                    :options="$this->statusOptions()"
                    option-label="name"
                    option-value="id"
                    placeholder="{{ __('Semua status') }}"
                    class="md:w-40"
                />
            </div>
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary shadow-sm font-semibold"
                link="{{ route('admin.product-sale.create') }}"
                label="{{ __('Penjualan Baru') }}"
                data-test="product-sale-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <!-- ===== KARTU RINGKASAN ===== -->
    <section aria-label="{{ __('Ringkasan penjualan produk') }}" class="grid gap-3 md:grid-cols-3">
        <article class="rounded-xl border border-base-200 bg-base-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-base-200 text-base-content/60 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            </div>
            <div>
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-base-content/45">{{ __('Total Transaksi') }}</h2>
                <p class="mt-0.5 text-2xl font-bold text-base-content tabular-nums" aria-label="{{ __(':n transaksi', ['n' => $this->summary['count']]) }}">
                    {{ $this->summary['count'] }}
                </p>
            </div>
        </article>

        <article class="rounded-xl border border-success/20 bg-success/5 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-success/15 text-success flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-success/70">{{ __('Lunas') }}</h2>
                <p class="mt-0.5 text-2xl font-bold text-success tabular-nums" aria-label="{{ __('Total lunas :amt rupiah', ['amt' => number_format($this->summary['paid'], 0, ',', '.')]) }}">
                    Rp {{ number_format($this->summary['paid'], 0, ',', '.') }}
                </p>
            </div>
        </article>

        <article class="rounded-xl border border-warning/20 bg-warning/5 shadow-sm p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-warning/15 text-warning flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <h2 class="text-[11px] font-bold uppercase tracking-wider text-warning/80">{{ __('Belum Lunas') }}</h2>
                <p class="mt-0.5 text-2xl font-bold text-warning tabular-nums" aria-label="{{ __('Total belum lunas :amt rupiah', ['amt' => number_format($this->summary['pending'], 0, ',', '.')]) }}">
                    Rp {{ number_format($this->summary['pending'], 0, ',', '.') }}
                </p>
            </div>
        </article>
    </section>

    <!-- ===== TABEL TRANSAKSI ===== -->
    <div class="overflow-hidden rounded-xl border border-base-200 bg-base-100 shadow-sm">
        <x-mary-table
            :headers="$this->headers"
            :rows="$this->sales"
            with-pagination
            class="table-sm"
        >
            @scope('cell_transacted_at_label', $row)
                <div class="text-xs font-medium text-base-content/70">
                    {{ $row->transacted_at->format('d M Y') }}
                    <div class="text-[11px] text-base-content/40">{{ $row->transacted_at->format('H:i') }}</div>
                </div>
            @endscope

            @scope('cell_buyer_label', $row)
                <div class="py-1">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-sm text-base-content">{{ $row->buyer_name }}</span>
                        @if ($row->buyer)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-primary/10 text-primary">
                                {{ __('Nasabah') }}
                            </span>
                        @endif
                    </div>
                    <a
                        href="tel:{{ $row->buyer_phone }}"
                        class="text-xs text-base-content/50 font-medium hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary rounded transition-colors"
                        aria-label="{{ __('Hubungi :phone', ['phone' => $row->buyer_phone]) }}"
                    >
                        {{ $row->buyer_phone }}
                    </a>
                </div>
            @endscope

            @scope('cell_items_label', $row)
                <ul class="text-xs space-y-1">
                    @foreach ($row->items->take(2) as $it)
                        <li class="flex items-baseline gap-1.5">
                            <span class="font-semibold text-base-content/70 tabular-nums shrink-0">{{ rtrim(rtrim(number_format((float) $it->quantity, 3, ',', '.'), '0'), ',') }}×</span>
                            <span class="text-base-content/80 truncate">{{ $it->product_name_snapshot }}</span>
                        </li>
                    @endforeach
                    @if ($row->items->count() > 2)
                        <li class="text-[11px] text-base-content/40 font-medium">
                            {{ __('+:n item lagi', ['n' => $row->items->count() - 2]) }}
                        </li>
                    @endif
                </ul>
            @endscope

            @scope('cell_total_value_label', $row)
                <span class="font-bold text-sm text-base-content tabular-nums" aria-label="{{ __('Total :amt rupiah', ['amt' => number_format((float) $row->total_value, 0, ',', '.')]) }}">
                    Rp {{ number_format((float) $row->total_value, 0, ',', '.') }}
                </span>
            @endscope

            @scope('cell_status_label', $row)
                @if ($row->payment_status === 'paid')
                    <span class="inline-flex items-center gap-1 w-fit px-2.5 py-1 rounded-full text-xs font-semibold bg-success/10 text-success border border-success/20">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        {{ __('Lunas') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 w-fit px-2.5 py-1 rounded-full text-xs font-semibold bg-warning/10 text-warning border border-warning/20">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ __('Belum Lunas') }}
                    </span>
                @endif
            @endscope

            @scope('cell_method_label', $row)
                @php
                    $methodIcons = [
                        'cash' => 'M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
                        'qris' => null,
                        'transfer' => 'M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3',
                    ];
                    $method = strtolower($row->payment_method);
                @endphp
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-base-200/70 text-[11px] font-bold text-base-content/65 uppercase tracking-wide" aria-label="{{ __('Metode :method', ['method' => $row->payment_method]) }}">
                    @if ($method === 'cash')
                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>
                    @elseif ($method === 'qris')
                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><path d="M14 14h3v3h-3zM21 14v3M14 21h3M18 18h3v3"/></svg>
                    @else
                        <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3"/></svg>
                    @endif
                    {{ strtoupper($row->payment_method) }}
                </span>
            @endscope

            @scope('cell_action_label', $row)
                <div class="flex justify-center">
                    @if ($row->payment_status === 'pending')
                        <x-mary-button
                            icon="o-check-circle"
                            wire:click="markPaid({{ $row->id }})"
                            class="btn-ghost btn-sm text-base-content/50 hover:text-success hover:bg-success/10 rounded-lg cursor-pointer"
                            tooltip="{{ __('Tandai Lunas') }}"
                            aria-label="{{ __('Tandai transaksi #:id (:buyer) sebagai lunas', ['id' => $row->id, 'buyer' => $row->buyer_name]) }}"
                            wire:confirm="{{ __('Tandai transaksi ini sebagai lunas?') }}"
                            data-test="product-sale-mark-paid-{{ $row->id }}"
                        />
                    @else
                        <span class="text-base-content/20" aria-hidden="true">—</span>
                        <span class="sr-only">{{ __('Tidak ada aksi') }}</span>
                    @endif
                </div>
            @endscope
        </x-mary-table>
    </div>
</section>
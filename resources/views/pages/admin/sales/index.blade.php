<?php

use App\Models\SalesTransaction;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Penjualan')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function transactions()
    {
        return SalesTransaction::query()
            ->with(['partner:id,name', 'items.item'])
            ->when($this->search !== '', function ($q) {
                $q->whereHas('partner', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            })
            ->orderByDesc('transacted_at')
            ->orderByDesc('id')
            ->paginate(15);
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Penjualan Sampah') }}"
        subtitle="{{ __('Catatan penjualan sampah ke mitra pengepul/pabrik. Stok inventory otomatis berkurang.') }}"
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
                label="{{ __('Penjualan Baru') }}"
                link="{{ route('admin.sales.create') }}"
                data-test="sales-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" style="font-family:'Inter',system-ui,sans-serif">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Tanggal') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Mitra') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Jenis') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Berat (Kg)') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Harga/Kg') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Subtotal') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Ket') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse ($this->transactions as $trx)
                        @if($trx->items && $trx->items->count() > 0)
                            @foreach ($trx->items as $item)
                                @php
                                    $itemName = $item->item_name_snapshot ?: ($item->item->name ?? '-');
                                    $price = $item->price_per_unit ?? 0;
                                    $subtotal = $item->subtotal ?? ($price * $item->quantity);
                                    $qtyFormatted = rtrim(rtrim(number_format((float) $item->quantity, 2, ',', '.'), '0'), ',');
                                @endphp
                                <tr class="hover:bg-base-200/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-base-content/80 whitespace-nowrap">
                                        {{ $trx->transacted_at->format('d/m/Y') }}
                                        <div class="text-xs text-base-content/50">{{ $trx->transacted_at->format('H:i') }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-sm font-medium text-base-content">
                                        {{ $trx->partner?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-base-content/80">{{ $itemName }}</td>

                                    <td class="px-4 py-3 text-sm text-right text-base-content/80 tabular-nums">{{ $qtyFormatted }}</td>

                                    <td class="px-4 py-3 text-sm text-right text-base-content/60 tabular-nums">
                                        Rp{{ number_format((float) $price, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3 text-sm text-right font-semibold text-primary tabular-nums">
                                        Rp{{ number_format((float) $subtotal, 0, ',', '.') }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="max-w-[150px] truncate text-xs text-base-content/60" title="{{ $trx->notes ?? '-' }}">
                                            {{ $trx->notes ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="hover:bg-base-200/50 transition-colors">
                                <td class="px-4 py-3 text-sm text-base-content/80 whitespace-nowrap">{{ $trx->transacted_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-base-content">{{ $trx->partner?->name ?? '—' }}</td>
                                <td colspan="5" class="px-4 py-3 text-center text-sm text-base-content/50 italic">
                                    Tidak ada rincian barang.
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-base-content/50 italic">
                                {{ __('Belum ada riwayat penjualan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->transactions->hasPages())
            <div class="px-4 py-4 bg-base-200/50 border-t border-base-200">
                {{ $this->transactions->links() }}
            </div>
        @endif
    </div>
</section>
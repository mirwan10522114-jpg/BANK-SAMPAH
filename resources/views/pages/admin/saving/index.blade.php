<?php

use App\Models\SavingTransaction;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Transaksi Nabung')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function transactions()
    {
        return SavingTransaction::query()
            // Kita cukup meload relasi 'user' dan 'items' saja.
            // Relasi ke master barang (wasteItem) dipakai sebagai fallback
            // jika data snapshot kosong, jadi tetap di-eager-load.
            ->with(['user:id,name,email,member_code', 'items.wasteItem'])
            ->when($this->search !== '', function ($q) {
                $q->whereHas('user', function ($q) {
                    $like = '%'.$this->search.'%';
                    $q->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('member_code', 'like', $like);
                });
            })
            // Mengurutkan dari transaksi terbaru menggunakan method bawaan Laravel
            ->latest()
            ->paginate(15);
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Transaksi Nabung') }}"
        subtitle="{{ __('Riwayat rincian item transaksi nabung nasabah.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari kode, nama, nasabah...') }}"
                clearable
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary"
                label="{{ __('Nabung Baru') }}"
                link="{{ route('admin.saving.create') }}"
                data-test="saving-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase">{{ __('Tanggal') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase">{{ __('Kode') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase">{{ __('Nama') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase">{{ __('Jenis') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase text-right">{{ __('Jumlah (Kg)') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase text-right">{{ __('Harga/Kg') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase text-right">{{ __('Jumlah (Rp)') }}</th>
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase">{{ __('Ket') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->transactions as $trx)
                        @if($trx->items && $trx->items->count() > 0)
                            @foreach ($trx->items as $item)
                                @php
                                    // Prioritaskan data snapshot (tidak berubah walau master data diedit).
                                    // Fallback ke relasi wasteItem kalau snapshot kosong.
                                    $itemName = $item->item_name_snapshot ?: ($item->wasteItem->name ?? '-');
                                    $price = $item->price_per_unit_snapshot ?? 0;
                                    $subtotal = $item->subtotal ?? ($price * $item->quantity);
                                    // Bulatkan jumlah (kg) ke maksimal 2 angka di belakang koma, tanpa nol berlebih
                                    $qtyFormatted = rtrim(rtrim(number_format((float) $item->quantity, 2, ',', '.'), '0'), ',');
                                @endphp
                                <tr class="hover:bg-base-200/30 border-b border-base-200/60 last:border-0">
                                    <td class="text-sm text-base-content/80">{{ $trx->created_at->format('d/m/Y') }}</td>

                                    <td>
                                        <span class="text-xs font-mono font-semibold text-base-content/50">
                                            {{ $trx->user->member_code ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="text-sm font-medium text-base-content">{{ strtoupper($trx->user->name) }}</td>

                                    <td class="text-sm text-base-content/80">{{ $itemName }}</td>

                                    <td class="text-sm text-right text-base-content/80 tabular-nums">{{ $qtyFormatted }}</td>

                                    <td class="text-sm text-right text-base-content/60 tabular-nums">Rp{{ number_format((float) $price, 0, ',', '.') }}</td>

                                    <td class="text-sm text-right font-semibold text-primary tabular-nums">Rp{{ number_format((float) $subtotal, 0, ',', '.') }}</td>

                                    <td>
                                        <div class="max-w-[150px] truncate text-xs text-base-content/60" title="{{ $trx->notes ?? '-' }}">
                                            {{ $trx->notes ?? '-' }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            {{-- Jika transaksi ada tapi tidak punya detail item --}}
                            <tr class="hover:bg-base-200/30 border-b border-base-200/60 last:border-0">
                                <td class="text-sm text-base-content/80">{{ $trx->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="text-xs font-mono font-semibold text-base-content/50">
                                        {{ $trx->user->member_code ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-sm font-medium text-base-content">{{ strtoupper($trx->user->name) }}</td>
                                <td colspan="5" class="text-center text-sm text-base-content/50 italic">
                                    Tidak ada rincian barang.
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-sm text-base-content/50 italic">
                                {{ __('Belum ada riwayat transaksi nabung.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Bagian Pagination --}}
        @if($this->transactions->hasPages())
            <div class="p-4 bg-base-200/20 border-t border-base-200">
                {{ $this->transactions->links() }}
            </div>
        @endif
    </div>
</section>
<?php

use App\Models\SedekahTransaction;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Transaksi Sedekah')] class extends Component {
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function transactions()
    {
        return SedekahTransaction::query()
            ->with(['user:id,name,email,member_code', 'items.item'])
            ->when($this->search !== '', function ($q) {
                $like = '%'.$this->search.'%';
                $q->where('donor_name', 'like', $like)
                    ->orWhereHas('user', function ($q) use ($like) {
                        $q->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like)
                            ->orWhere('member_code', 'like', $like);
                    });
            })
            ->orderByDesc('transacted_at')
            ->orderByDesc('id')
            ->paginate(15);
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Transaksi Sedekah') }}"
        subtitle="{{ __('Catatan sampah sumbangan — tidak menghasilkan saldo atau poin, langsung masuk inventory.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari kode, donor...') }}"
                clearable
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary"
                label="{{ __('Sedekah Baru') }}"
                link="{{ route('admin.sedekah.create') }}"
                data-test="sedekah-create-button"
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
                        <th class="font-semibold text-[11px] tracking-wider text-base-content/60 uppercase">{{ __('Ket') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->transactions as $trx)
                        @php
                            $donorName = $trx->donor_name ?: ($trx->user?->name ?? __('Anonim'));
                            $memberCode = $trx->user?->member_code;
                        @endphp
                        @if($trx->items && $trx->items->count() > 0)
                            @foreach ($trx->items as $item)
                                @php
                                    $itemName = $item->item_name_snapshot ?: ($item->item->name ?? '-');
                                    $qtyFormatted = rtrim(rtrim(number_format((float) $item->quantity, 2, ',', '.'), '0'), ',');
                                @endphp
                                <tr class="hover:bg-base-200/30 border-b border-base-200/60 last:border-0">
                                    <td class="text-sm text-base-content/80">{{ $trx->transacted_at->format('d/m/Y') }}</td>

                                    <td>
                                        <span class="text-xs font-mono font-semibold text-base-content/50">
                                            {{ $memberCode ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="text-sm font-medium text-base-content">{{ strtoupper($donorName) }}</td>

                                    <td class="text-sm text-base-content/80">{{ $itemName }}</td>

                                    <td class="text-sm text-right text-base-content/80 tabular-nums">{{ $qtyFormatted }}</td>

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
                                <td class="text-sm text-base-content/80">{{ $trx->transacted_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="text-xs font-mono font-semibold text-base-content/50">
                                        {{ $memberCode ?? '—' }}
                                    </span>
                                </td>
                                <td class="text-sm font-medium text-base-content">{{ strtoupper($donorName) }}</td>
                                <td colspan="3" class="text-center text-sm text-base-content/50 italic">
                                    Tidak ada rincian barang.
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-sm text-base-content/50 italic">
                                {{ __('Belum ada riwayat transaksi sedekah.') }}
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
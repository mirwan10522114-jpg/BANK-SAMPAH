<?php

use App\Models\BalanceHistory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Saldo')] class extends Component {
    use WithPagination;

    public string $bucket = '';

    public function updatingBucket(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'created_at_label', 'label' => __('Tanggal'), 'sortable' => false],
            ['key' => 'bucket_label', 'label' => __('Bucket'), 'sortable' => false],
            ['key' => 'type_label', 'label' => __('Jenis'), 'sortable' => false],
            ['key' => 'amount_label', 'label' => __('Jumlah'), 'sortable' => false],
            ['key' => 'balance_after_label', 'label' => __('Saldo Setelah'), 'class' => 'hidden md:table-cell', 'sortable' => false],
            ['key' => 'description', 'label' => __('Keterangan'), 'class' => 'hidden lg:table-cell', 'sortable' => false],
        ];
    }

    #[Computed]
    public function histories()
    {
        return BalanceHistory::query()
            ->where('user_id', Auth::id())
            ->when($this->bucket !== '', fn ($q) => $q->where('bucket', $this->bucket))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);
    }

    #[Computed]
    public function balance()
    {
        return Auth::user()->balance;
    }

    public function bucketOptions(): array
    {
        return [
            ['id' => 'tertahan', 'name' => __('Tertahan')],
            ['id' => 'tersedia', 'name' => __('Tersedia')],
        ];
    }
}; ?>

@php
    $rupiah = fn(float $v) => 'Rp ' . number_format($v, 0, ',', '.');
@endphp

<section class="w-full flex flex-col gap-6 pb-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-base-content">{{ __('Saldo') }}</h1>
            <p class="text-sm text-base-content/50 mt-0.5">{{ __('Rincian saldo dan pergerakannya.') }}</p>
        </div>
        <x-mary-select
            wire:model.live="bucket"
            :options="$this->bucketOptions()"
            option-label="name"
            option-value="id"
            placeholder="{{ __('Semua bucket') }}"
            class="w-full sm:w-52"
        />
    </div>

    {{-- SALDO CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-gradient-to-br from-success/10 to-success/5 border border-success/20 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-success/15 flex items-center justify-center flex-shrink-0">
                <x-mary-icon name="o-banknotes" class="size-6 text-success" />
            </div>
            <div>
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">{{ __('Saldo Tersedia') }}</div>
                <div class="text-2xl font-black text-success leading-tight mt-0.5">
                    {{ $rupiah((float) ($this->balance->saldo_tersedia ?? 0)) }}
                </div>
                <div class="text-xs text-base-content/40 mt-0.5">{{ __('Siap dicairkan kapan saja') }}</div>
            </div>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-warning/10 to-warning/5 border border-warning/20 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-warning/15 flex items-center justify-center flex-shrink-0">
                <x-mary-icon name="o-clock" class="size-6 text-warning" />
            </div>
            <div>
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">{{ __('Saldo Tertahan') }}</div>
                <div class="text-2xl font-black text-warning leading-tight mt-0.5">
                    {{ $rupiah((float) ($this->balance->saldo_tertahan ?? 0)) }}
                </div>
                <div class="text-xs text-base-content/40 mt-0.5">{{ __('Menunggu release admin') }}</div>
            </div>
        </div>
    </div>

    {{-- RIWAYAT --}}
    <div class="rounded-2xl border border-base-200 bg-base-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-base-200">
            <div class="text-sm font-bold text-base-content">{{ __('Riwayat Pergerakan Saldo') }}</div>
            <span class="text-xs text-base-content/40">
                {{ $this->histories->total() }} {{ __('total transaksi') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-base-200 bg-base-200/40">
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 whitespace-nowrap">{{ __('Tanggal') }}</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 whitespace-nowrap">{{ __('Bucket') }}</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40">{{ __('Jenis') }}</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 text-right whitespace-nowrap">{{ __('Jumlah') }}</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 text-right whitespace-nowrap hidden md:table-cell">{{ __('Saldo Setelah') }}</th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 hidden lg:table-cell">{{ __('Keterangan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse ($this->histories as $row)
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-base-content">{{ $row->created_at->format('d M Y') }}</div>
                                <div class="text-xs text-base-content/40">{{ $row->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if ($row->bucket === 'tertahan')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-warning/15 text-warning">
                                        {{ __('Tertahan') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-success/15 text-success">
                                        {{ __('Tersedia') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-base-content/70 capitalize">{{ $row->type }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span @class([
                                    'font-bold tabular-nums',
                                    'text-success' => (float) $row->amount > 0,
                                    'text-error' => (float) $row->amount < 0,
                                    'text-base-content/60' => (float) $row->amount === 0.0,
                                ])>
                                    {{ (float) $row->amount > 0 ? '+' : '' }}{{ $rupiah((float) $row->amount) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap text-base-content/60 hidden md:table-cell">
                                {{ $rupiah((float) $row->balance_after) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-base-content/60 hidden lg:table-cell">
                                {{ $row->description ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12">
                                <div class="flex flex-col items-center gap-3 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-base-200 flex items-center justify-center">
                                        <x-mary-icon name="o-banknotes" class="size-7 text-base-content/20" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-base-content/60">{{ __('Belum ada riwayat saldo') }}</div>
                                        <div class="text-xs text-base-content/40 mt-1">{{ __('Pergerakan saldo akan muncul di sini') }}</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($this->histories->hasPages())
            <div class="px-5 py-4 border-t border-base-200">
                {{ $this->histories->links() }}
            </div>
        @endif
    </div>
</section>
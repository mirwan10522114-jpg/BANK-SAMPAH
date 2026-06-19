<?php

use App\Models\ProcessingTransaction;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Pengolahan Sampah')] class extends Component {
    use WithPagination;

    #[Computed]
    public function transactions()
    {
        return ProcessingTransaction::query()
            ->with(['inputs', 'outputs'])
            ->orderByDesc('transacted_at')
            ->orderByDesc('id')
            ->paginate(15);
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Pengolahan Sampah') }}"
        subtitle="{{ __('Catat proses sampah diolah menjadi produk hasil olahan. Inventory sampah berkurang, stok produk bertambah.') }}"
        separator
        progress-indicator
    >
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary"
                label="{{ __('Pengolahan Baru') }}"
                link="{{ route('admin.processing.create') }}"
                data-test="processing-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" style="font-family:'Inter',system-ui,sans-serif">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Tanggal') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Sampah Input') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Kategori') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Berat Input') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Produk Dihasilkan') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Jumlah Output') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden lg:table-cell">{{ __('Catatan') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse ($this->transactions as $trx)
                        @php
                            $inputs = $trx->inputs;
                            $outputs = $trx->outputs;
                            $maxRows = max($inputs->count(), $outputs->count(), 1);
                        @endphp

                        @for ($i = 0; $i < $maxRows; $i++)
                            @php
                                $input = $inputs->get($i);
                                $output = $outputs->get($i);
                            @endphp
                            <tr class="hover:bg-base-200/50 transition-colors">
                                {{-- Tanggal --}}
                                <td class="px-4 py-3 text-sm text-base-content/80 whitespace-nowrap">
                                    {{ $trx->transacted_at->format('d/m/Y') }}
                                    <div class="text-xs text-base-content/50">{{ $trx->transacted_at->format('H:i') }}</div>
                                </td>

                                {{-- Sampah Input --}}
                                <td class="px-4 py-3 text-sm text-base-content">
                                    {{ $input?->item_name_snapshot ?? '—' }}
                                </td>

                                {{-- Kategori --}}
                                <td class="px-4 py-3">
                                    @if ($input)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-base-200 text-base-content/70">
                                            {{ $input->category_name_snapshot ?? '—' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Berat Input --}}
                                <td class="px-4 py-3 text-right">
                                    @if ($input)
                                        <span class="text-sm font-semibold text-base-content">
                                            {{ rtrim(rtrim(number_format((float) $input->quantity, 1, ',', '.'), '0'), ',') }}
                                        </span>
                                        <span class="text-xs text-base-content/50">{{ $input->unit_snapshot }}</span>
                                    @endif
                                </td>

                                {{-- Produk Dihasilkan --}}
                                <td class="px-4 py-3 text-sm text-base-content">
                                    {{ $output?->product_name_snapshot ?? '—' }}
                                </td>

                                {{-- Jumlah Output --}}
                                <td class="px-4 py-3 text-right">
                                    @if ($output)
                                        <span class="text-sm font-semibold text-primary">
                                            {{ rtrim(rtrim(number_format((float) $output->quantity, 1, ',', '.'), '0'), ',') }}
                                        </span>
                                        <span class="text-xs text-base-content/50">{{ $output->unit_snapshot }}</span>
                                    @endif
                                </td>

                                {{-- Catatan --}}
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <div class="max-w-[160px] truncate text-sm text-base-content/70" title="{{ $trx->notes ?? '—' }}">
                                        {{ $trx->notes ?? '—' }}
                                    </div>
                                </td>
                            </tr>
                        @endfor
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-base-content/50 italic">
                                {{ __('Belum ada riwayat pengolahan.') }}
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
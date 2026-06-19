<?php

use App\Models\Balance;
use App\Models\User;
use App\Models\WithdrawalRequest;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Pencairan') ] class extends Component {
    use Toast, WithPagination;

    public ?int $user_id = null;

    public string $amount = '';

    public string $method = 'cash';

    public string $bank_name = '';

    public string $account_number = '';

    public string $account_name = '';

    public string $notes = '';

    public bool $formModal = false;

    #[Computed]
    public function withdrawals()
    {
        return WithdrawalRequest::query()
            ->with('user:id,name,email,member_code')
            ->orderByDesc('processed_at')
            ->orderByDesc('id')
            ->paginate(15);
    }

    #[Computed]
    public function nasabahOptions(): array
    {
        return User::nasabah()
            ->with('balance')
            ->whereHas('balance', fn ($q) => $q->where('saldo_tersedia', '>', 0))
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name.' — '.__('tersedia').' Rp '.number_format((float) $u->balance->saldo_tersedia, 0, ',', '.'),
            ])
            ->toArray();
    }

    #[Computed]
    public function selectedBalance(): ?Balance
    {
        return $this->user_id ? Balance::firstWhere('user_id', $this->user_id) : null;
    }

    public function startCreating(): void
    {
        $this->reset(['user_id', 'amount', 'bank_name', 'account_number', 'account_name', 'notes']);
        $this->method = 'cash';
        $this->resetErrorBag();
        $this->formModal = true;
    }

    public function rules(): array
    {
        $rules = [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'method' => ['required', 'in:cash,transfer'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        if ($this->method === 'transfer') {
            $rules['bank_name'] = ['required', 'string', 'max:64'];
            $rules['account_number'] = ['required', 'string', 'max:32'];
            $rules['account_name'] = ['required', 'string', 'max:128'];
        }

        return $rules;
    }

    public function save(BalanceService $service): void
    {
        $this->validate();

        $nasabah = User::nasabah()->findOrFail($this->user_id);

        try {
            $service->withdraw(
                $nasabah,
                (float) $this->amount,
                $this->method,
                Auth::user(),
                meta: [
                    'bank_name' => $this->bank_name !== '' ? $this->bank_name : null,
                    'account_number' => $this->account_number !== '' ? $this->account_number : null,
                    'account_name' => $this->account_name !== '' ? $this->account_name : null,
                ],
                notes: $this->notes !== '' ? $this->notes : null,
            );
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->success(__('Pencairan tersimpan.'));
        $this->formModal = false;
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Pencairan') }}"
        subtitle="{{ __('Pencairan saldo tersedia — cash atau transfer — yang sudah diproses admin.') }}"
        separator
        progress-indicator
    >
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary"
                label="{{ __('Pencairan Baru') }}"
                wire:click="startCreating"
                data-test="withdrawal-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" style="font-family:'Inter',system-ui,sans-serif">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden md:table-cell">{{ __('Tanggal') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase w-20">{{ __('Kode') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Nasabah') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Jumlah') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Metode') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden lg:table-cell">{{ __('Detail Rekening') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse ($this->withdrawals as $row)
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-4 py-3 text-sm text-base-content/80 whitespace-nowrap hidden md:table-cell">
                                {{ $row->processed_at->format('d/m/Y') }}
                                <div class="text-xs text-base-content/50">{{ $row->processed_at->format('H:i') }}</div>
                            </td>

                            <td class="px-4 py-3 text-xs font-mono font-semibold text-base-content/50">
                                {{ $row->user?->member_code ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-base-content">{{ $row->user?->name ?? '—' }}</div>
                                <div class="text-xs text-base-content/50">{{ $row->user?->email }}</div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-base-content">
                                    Rp {{ number_format((float) $row->amount, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if ($row->method === 'transfer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-info/10 text-info">
                                        {{ __('Transfer') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success">
                                        {{ __('Cash') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 hidden lg:table-cell">
                                @if ($row->method === 'transfer' && $row->bank_name)
                                    <div class="text-sm text-base-content">{{ $row->bank_name }}</div>
                                    <div class="text-xs text-base-content/50">
                                        {{ $row->account_number }} @if($row->account_name) — {{ $row->account_name }} @endif
                                    </div>
                                @else
                                    <span class="text-sm text-base-content/40">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-base-content/50 italic">
                                {{ __('Belum ada riwayat pencairan.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->withdrawals->hasPages())
            <div class="px-4 py-4 bg-base-200/50 border-t border-base-200">
                {{ $this->withdrawals->links() }}
            </div>
        @endif
    </div>

    <x-mary-modal
        wire:model="formModal"
        title="{{ __('Pencairan Baru') }}"
        subtitle="{{ __('Hanya nasabah dengan saldo tersedia > 0 yang bisa dipilih.') }}"
        separator
        box-class="max-w-lg rounded-2xl"
    >
        <x-mary-form wire:submit="save" no-separator class="space-y-4">
            <x-mary-select
                wire:model.live="user_id"
                label="{{ __('Nasabah') }}"
                :options="$this->nasabahOptions"
                option-label="name"
                option-value="id"
                placeholder="{{ __('Pilih nasabah') }}"
                icon="o-user"
                required
                class="select-bordered"
            />

            @if ($this->selectedBalance)
                <div class="rounded-xl border border-base-200 bg-base-200/30 p-3 text-sm flex justify-between items-center">
                    <span class="text-base-content/60">{{ __('Saldo tersedia') }}</span>
                    <span class="font-semibold text-success">
                        Rp {{ number_format((float) $this->selectedBalance->saldo_tersedia, 0, ',', '.') }}
                    </span>
                </div>
            @endif

            <x-mary-input
                wire:model="amount"
                label="{{ __('Jumlah (Rp)') }}"
                type="number"
                step="0.01"
                min="0"
                icon="o-banknotes"
                required
                class="input-bordered"
            />

            <x-mary-select
                wire:model.live="method"
                label="{{ __('Metode') }}"
                :options="[
                    ['id' => 'cash', 'name' => __('Cash (tunai)')],
                    ['id' => 'transfer', 'name' => __('Transfer bank')],
                ]"
                option-label="name"
                option-value="id"
                required
                class="select-bordered"
            />

            @if ($method === 'transfer')
                <div class="rounded-xl border border-base-200 bg-base-200/20 p-4 space-y-3">
                    <p class="text-xs font-semibold uppercase tracking-wider text-base-content/50">{{ __('Detail Rekening Tujuan') }}</p>
                    <x-mary-input wire:model="bank_name" label="{{ __('Nama bank') }}" required class="input-bordered" />
                    <x-mary-input wire:model="account_number" label="{{ __('Nomor rekening') }}" required class="input-bordered" />
                    <x-mary-input wire:model="account_name" label="{{ __('Atas nama') }}" required class="input-bordered" />
                </div>
            @endif

            <x-mary-textarea wire:model="notes" label="{{ __('Catatan') }}" rows="2" class="textarea-bordered" />

            <x-slot:actions>
                <x-mary-button label="{{ __('Batal') }}" @click="$wire.formModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button
                    type="submit"
                    label="{{ __('Simpan Pencairan') }}"
                    class="btn-primary font-semibold shadow-sm px-5 text-sm"
                    spinner="save"
                    data-test="withdrawal-save-button"
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</section>x
<?php

use App\Models\Balance;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Release Saldo')] class extends Component {
    use Toast, WithPagination;

    public string $search = '';

    public ?int $releasingUserId = null;

    public string $amount = '';

    public string $notes = '';

    public bool $releaseModal = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function nasabahList()
    {
        return User::nasabah()
            ->with('balance')
            ->whereHas('balance', fn ($q) => $q->where('saldo_tertahan', '>', 0))
            ->when($this->search !== '', function ($q) {
                $like = '%'.$this->search.'%';
                $q->where(fn ($q) => $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('member_code', 'like', $like));
            })
            ->orderBy('name')
            ->paginate(15);
    }

    #[Computed]
    public function releasingUser(): ?User
    {
        return $this->releasingUserId
            ? User::with('balance')->find($this->releasingUserId)
            : null;
    }

    public function startRelease(int $userId): void
    {
        $this->releasingUserId = $userId;
        $this->amount = (string) (Balance::where('user_id', $userId)->value('saldo_tertahan') ?? '');
        $this->notes = '';
        $this->resetErrorBag();
        $this->releaseModal = true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function save(BalanceService $service): void
    {
        $this->validate();

        if (! $this->releasingUserId) {
            return;
        }

        $nasabah = User::nasabah()->findOrFail($this->releasingUserId);

        try {
            $service->release(
                $nasabah,
                (float) $this->amount,
                Auth::user(),
                $this->notes !== '' ? $this->notes : null,
            );
        } catch (\InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return;
        }

        $this->success(__('Saldo berhasil dipindahkan ke tersedia.'));
        $this->releaseModal = false;
        $this->reset(['releasingUserId', 'amount', 'notes']);
    }
}; ?>

<section class="w-full">
    <x-mary-header
        title="{{ __('Release Saldo') }}"
        subtitle="{{ __('Pindahkan saldo nasabah dari tertahan menjadi tersedia ketika dana dari mitra sudah diterima.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari kode, nasabah...') }}"
                clearable
            />
        </x-slot:middle>
    </x-mary-header>

    <div class="bg-base-100 rounded-xl shadow-sm border border-base-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" style="font-family:'Inter',system-ui,sans-serif">
                <thead class="bg-base-200/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase w-20">{{ __('Kode') }}</th>
                        <th class="px-4 py-3 text-left text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Nasabah') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Saldo Tertahan') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase hidden md:table-cell">{{ __('Saldo Tersedia') }}</th>
                        <th class="px-4 py-3 text-right text-[11px] font-semibold tracking-wider text-base-content/60 uppercase">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    @forelse ($this->nasabahList as $row)
                        <tr class="hover:bg-base-200/50 transition-colors">
                            <td class="px-4 py-3 text-xs font-mono font-semibold text-base-content/50">
                                {{ $row->member_code ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex size-8 items-center justify-center rounded-full bg-base-200 text-xs font-bold text-base-content/70 uppercase">
                                        {{ substr($row->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-base-content">{{ $row->name }}</div>
                                        <div class="text-xs text-base-content/50">{{ $row->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-warning">
                                    Rp {{ number_format((float) $row->balance->saldo_tertahan, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right hidden md:table-cell">
                                <span class="text-sm text-base-content/70">
                                    Rp {{ number_format((float) $row->balance->saldo_tersedia, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-right">
                                <x-mary-button
                                    icon="o-arrow-right-circle"
                                    label="{{ __('Release') }}"
                                    wire:click="startRelease({{ $row->id }})"
                                    class="btn-primary btn-sm"
                                    data-test="release-{{ $row->id }}"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-base-content/50 italic">
                                {{ __('Tidak ada saldo tertahan yang perlu di-release.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->nasabahList->hasPages())
            <div class="px-4 py-4 bg-base-200/50 border-t border-base-200">
                {{ $this->nasabahList->links() }}
            </div>
        @endif
    </div>

    <x-mary-modal
        wire:model="releaseModal"
        title="{{ __('Release Saldo Tertahan') }}"
        :subtitle="$this->releasingUser?->name"
        separator
        box-class="max-w-md rounded-2xl"
    >
        @if ($this->releasingUser)
            <div class="rounded-xl border border-base-200 bg-base-200/30 p-4 space-y-2 text-sm mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-base-content/60">{{ __('Kode Nasabah') }}</span>
                    <span class="font-mono font-semibold text-base-content/70">{{ $this->releasingUser->member_code ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-base-content/60">{{ __('Saldo tertahan') }}</span>
                    <span class="font-semibold text-warning">
                        Rp {{ number_format((float) $this->releasingUser->balance->saldo_tertahan, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-base-content/60">{{ __('Saldo tersedia') }}</span>
                    <span class="text-base-content/80">
                        Rp {{ number_format((float) $this->releasingUser->balance->saldo_tersedia, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @endif

        <x-mary-form wire:submit="save" no-separator class="space-y-4">
            <x-mary-input
                wire:model="amount"
                label="{{ __('Jumlah yang di-release (Rp)') }}"
                type="number"
                step="0.01"
                min="0"
                icon="o-banknotes"
                required
                class="input-bordered"
            />
            <x-mary-textarea wire:model="notes" label="{{ __('Catatan (opsional)') }}" rows="2" class="textarea-bordered" />

            <x-slot:actions>
                <x-mary-button label="{{ __('Batal') }}" @click="$wire.releaseModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button
                    type="submit"
                    label="{{ __('Release') }}"
                    class="btn-primary font-semibold shadow-sm px-5 text-sm"
                    spinner="save"
                    data-test="release-save-button"
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>
</section>
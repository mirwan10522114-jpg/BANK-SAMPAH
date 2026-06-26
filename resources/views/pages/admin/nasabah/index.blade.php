<?php

use App\Concerns\NasabahValidationRules;
use App\Enums\UserRole;
use App\Models\PointHistory;
use App\Models\Redemption;
use App\Models\SavingTransaction;
use App\Models\SedekahTransaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Nasabah')] class extends Component {
    use NasabahValidationRules, Toast, WithPagination;

    public string $search = '';

    public string $sortBy = 'name';

    public string $sortDirection = 'asc';

    public ?int $editingUserId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public bool $is_member = false;

    public ?string $member_joined_at = null;

    public ?int $deletingUserId = null;

    public bool $formModal = false;

    public bool $deleteModal = false;

    // Dashboard personal nasabah
    public ?int $viewingUserId = null;

    public bool $dashboardModal = false;

    public string $trendRange = '6bulan';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortField(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // Fungsi agar tanggal otomatis terisi/kosong saat toggle member ditekan
    public function updatedIsMember($value): void
    {
        if ($value && empty($this->member_joined_at)) {
            $this->member_joined_at = now()->format('Y-m-d');
        } elseif (! $value) {
            $this->member_joined_at = null;
        }
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => __('Kode'), 'class' => 'w-20 text-center', 'sortable' => false],
            ['key' => 'name', 'label' => __('Informasi Nasabah'), 'class' => 'w-64'],
            ['key' => 'phone', 'label' => __('Telepon'), 'class' => 'w-36'],
            ['key' => 'address', 'label' => __('Alamat Lengkap'), 'class' => 'hidden md:table-cell max-w-xs'],
            ['key' => 'member_label', 'label' => __('Status'), 'class' => 'w-28 text-center', 'sortable' => false],
            ['key' => 'member_joined_label', 'label' => __('Bergabung'), 'class' => 'hidden lg:table-cell w-36', 'sortable' => false],
        ];
    }

    #[Computed]
    public function nasabahList()
    {
        return User::query()
            ->nasabah()
            ->when($this->search !== '', fn ($q) => $q->where(function ($q): void {
                $like = '%'.$this->search.'%';
                $q->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhere('member_code', 'like', $like);
            }))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    public function rules(): array
    {
        return $this->nasabahRules($this->editingUserId);
    }

    public function startCreating(): void
    {
        $this->resetForm();

        // Otomatisasi nasabah baru menjadi member dengan tanggal hari ini
        $this->is_member = true;
        $this->member_joined_at = now()->format('Y-m-d');

        $this->formModal = true;
    }

    public function startEditing(int $id): void
    {
        $user = User::nasabah()->findOrFail($id);

        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = (string) ($user->phone ?? '');
        $this->address = (string) ($user->address ?? '');
        $this->is_member = (bool) $user->is_member;
        $this->member_joined_at = $user->member_joined_at?->format('Y-m-d');

        $this->formModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        if (! $validated['is_member']) {
            $validated['member_joined_at'] = null;
        }

        if ($this->editingUserId) {
            $user = User::nasabah()->findOrFail($this->editingUserId);
            $user->update($validated);
            $this->success(__('Nasabah berhasil diperbarui.'));
        } else {
            User::create([
                ...$validated,
                'roles' => [UserRole::Nasabah->value],
                'password' => Str::random(32),
            ]);
            $this->success(__('Nasabah berhasil ditambahkan.'));
        }

        $this->formModal = false;
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingUserId = $id;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->deletingUserId) {
            return;
        }

        $user = User::nasabah()->findOrFail($this->deletingUserId);
        $user->delete();
        $this->deletingUserId = null;
        $this->deleteModal = false;

        $this->success(__('Nasabah dihapus.'));
    }

    // ==========================================
    // DASHBOARD PERSONAL NASABAH
    // ==========================================

    public function openDashboard(int $id): void
    {
        $this->viewingUserId = $id;
        $this->trendRange = '6bulan';
        $this->dashboardModal = true;
    }

    public function setTrendRange(string $range): void
    {
        $this->trendRange = $range;

        $this->dispatch('trend-range-updated', labels: $this->monthlyTrend['labels'], weights: $this->monthlyTrend['weights']);
    }

    #[Computed]
    public function viewingUser(): ?User
    {
        if (! $this->viewingUserId) {
            return null;
        }

        return User::nasabah()->with('balance')->find($this->viewingUserId);
    }

    #[Computed]
    public function savingHistory()
    {
        if (! $this->viewingUserId) {
            return collect();
        }

        return SavingTransaction::query()
            ->where('user_id', $this->viewingUserId)
            ->with('items')
            ->orderByDesc('transacted_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function sedekahHistory()
    {
        if (! $this->viewingUserId) {
            return collect();
        }

        return SedekahTransaction::query()
            ->where('user_id', $this->viewingUserId)
            ->with('items')
            ->orderByDesc('transacted_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function redemptionHistory()
    {
        if (! $this->viewingUserId) {
            return collect();
        }

        return Redemption::query()
            ->where('user_id', $this->viewingUserId)
            ->orderByDesc('redeemed_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function pointHistory()
    {
        if (! $this->viewingUserId) {
            return collect();
        }

        return PointHistory::query()
            ->where('user_id', $this->viewingUserId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function categoryBreakdown()
    {
        if (! $this->viewingUserId) {
            return collect();
        }

        return \App\Models\SavingTransactionItem::query()
            ->whereHas('transaction', fn ($q) => $q->where('user_id', $this->viewingUserId))
            ->selectRaw('category_name_snapshot, SUM(quantity) as total_qty, SUM(subtotal) as total_value')
            ->groupBy('category_name_snapshot')
            ->orderByDesc('total_qty')
            ->get();
    }

    #[Computed]
    public function monthlyTrend(): array
    {
        if (! $this->viewingUserId) {
            return ['labels' => [], 'weights' => []];
        }

        $monthsBack = match ($this->trendRange) {
            '3bulan' => 2,
            '6bulan' => 5,
            '1tahun' => 11,
            default => 5,
        };

        $months = collect(range($monthsBack, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $data = SavingTransaction::query()
            ->where('user_id', $this->viewingUserId)
            ->where('transacted_at', '>=', now()->subMonths($monthsBack)->startOfMonth())
            ->selectRaw('DATE_FORMAT(transacted_at, "%Y-%m") as ym, SUM(total_weight) as weight')
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $labels = [];
        $weights = [];

        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $labels[] = $month->translatedFormat('M Y');
            $weights[] = (float) ($data[$key]->weight ?? 0);
        }

        return ['labels' => $labels, 'weights' => $weights];
    }

    private function resetForm(): void
    {
        $this->reset(['editingUserId', 'name', 'email', 'phone', 'address', 'is_member', 'member_joined_at']);
        $this->resetErrorBag();
    }
}; ?>

<section class="w-full space-y-4">
    <!-- Header Page -->
    <x-mary-header
        title="{{ __('Nasabah') }}"
        subtitle="{{ __('Kelola data nasabah yang menabung atau menyumbang sampah.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari kode, nama, email, alamat...') }}"
                class="input-md bg-base-100 shadow-sm border-base-300 focus:border-primary w-80"
                clearable
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary shadow-sm font-semibold text-sm"
                wire:click="startCreating"
                label="{{ __('Tambah Nasabah') }}"
                data-test="nasabah-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <!-- Sort Toolbar -->
    <div class="flex items-center gap-2 px-1">
        <span class="text-xs font-semibold uppercase tracking-wider text-base-content/50">{{ __('Urutkan') }}:</span>
        <button
            wire:click="sortField('name')"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                {{ $sortBy === 'name' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/60 hover:bg-base-300' }}"
        >
            {{ __('Nama A-Z') }}
            @if($sortBy === 'name')
                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
            @endif
        </button>
        <button
            wire:click="sortField('member_code')"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                {{ $sortBy === 'member_code' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/60 hover:bg-base-300' }}"
        >
            {{ __('Kode Member') }}
            @if($sortBy === 'member_code')
                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
            @endif
        </button>
        <button
            wire:click="sortField('created_at')"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                {{ $sortBy === 'created_at' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/60 hover:bg-base-300' }}"
        >
            {{ __('Terbaru') }}
            @if($sortBy === 'created_at')
                <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
            @endif
        </button>
    </div>

    <!-- Tabel Data Nasabah -->
    <div class="overflow-hidden rounded-xl border border-base-200 bg-base-100 shadow-sm">
        <x-mary-table
            :headers="$this->headers"
            :rows="$this->nasabahList"
            with-pagination
            per-page="perPage"
            class="table-sm"
        >
            <!-- Kolom Kode Nasabah -->
            @scope('cell_id', $row)
                <span class="text-xs font-mono font-semibold text-base-content/50">
                    {{ $row->member_code ?? '—' }}
                </span>
            @endscope

            <!-- Kolom 1: Gabungan Avatar + Nama + Email (klik untuk buka dashboard) -->
            @scope('cell_name', $row)
                <div
                    class="flex items-center gap-3 py-1 cursor-pointer group"
                    wire:click="openDashboard({{ $row->id }})"
                    data-test="nasabah-open-dashboard-{{ $row->id }}"
                >
                    <div class="avatar placeholder">
                        <div class="bg-neutral/10 text-neutral font-bold rounded-full w-9 h-9 flex items-center justify-center text-xs uppercase tracking-wider group-hover:bg-primary/15 group-hover:text-primary transition-colors">
                            {{ substr($row->name, 0, 2) }}
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-base-content group-hover:text-primary transition-colors">
                            {{ $row->name }}
                        </span>
                        <span class="text-xs text-base-content/60 font-medium">
                            {{ $row->email }}
                        </span>
                    </div>
                </div>
            @endscope

            <!-- Kolom 2: Nomor Telepon -->
            @scope('cell_phone', $row)
                @if($row->phone)
                    <span class="text-sm font-mono font-semibold text-base-content/80 bg-base-200/50 px-2.5 py-1 rounded-lg whitespace-nowrap">
                        {{ $row->phone }}
                    </span>
                @else
                    <span class="text-base-content/30 text-xs">—</span>
                @endif
            @endscope

            <!-- Kolom 3: Alamat Lengkap -->
            @scope('cell_address', $row)
                @if($row->address)
                    <div class="max-w-xs truncate text-xs text-base-content/70 font-medium" title="{{ $row->address }}">
                        {{ $row->address }}
                    </div>
                @else
                    <span class="text-base-content/30 text-xs">—</span>
                @endif
            @endscope

            <!-- Kolom 4: Status Member -->
            @scope('cell_member_label', $row)
                <div class="text-center">
                    @if ($row->is_member)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success border border-success/20">
                            {{ __('Aktif') }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-base-200 text-base-content/60 border border-base-300/40">
                            {{ __('Tidak Aktif') }}
                        </span>
                    @endif
                </div>
            @endscope

            <!-- Kolom 5: Tanggal Bergabung -->
            @scope('cell_member_joined_label', $row)
                <div class="text-xs font-medium text-base-content/70">
                    {{ $row->member_joined_at ? $row->member_joined_at->format('d M Y') : '—' }}
                </div>
            @endscope

            <!-- Kolom Aksi -->
            @scope('actions', $row)
                <div class="flex items-center gap-0.5 justify-end">
                    <x-mary-button
                        icon="o-chart-bar"
                        wire:click="openDashboard({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/70 hover:text-info hover:bg-info/10 rounded-lg"
                        tooltip="{{ __('Lihat Dashboard') }}"
                        data-test="nasabah-dashboard-{{ $row->id }}"
                    />
                    <x-mary-button
                        icon="o-pencil-square"
                        wire:click="startEditing({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg"
                        spinner
                        data-test="nasabah-edit-{{ $row->id }}"
                    />
                    <x-mary-button
                        icon="o-trash"
                        wire:click="confirmDelete({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/40 hover:text-error hover:bg-error/10 rounded-lg"
                        data-test="nasabah-delete-{{ $row->id }}"
                    />
                </div>
            @endscope
        </x-mary-table>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <x-mary-modal wire:model="formModal" title="{{ $editingUserId ? __('Edit Data Nasabah') : __('Tambah Nasabah Baru') }}" subtitle="{{ __('Gunakan alamat email aktif sebagai identitas login unik nasabah.') }}" separator box-class="max-w-2xl rounded-2xl">
        <x-mary-form wire:submit="save" no-separator class="space-y-5">

            @if($editingUserId)
                <div class="flex items-center gap-2 -mt-1 mb-1">
                    <span class="text-xs font-mono font-semibold text-base-content/50">{{ __('ID Nasabah') }}: #{{ $editingUserId }}</span>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <x-mary-input wire:model="name" label="{{ __('Nama Lengkap') }}" icon="o-user" placeholder="Nama sesuai identitas" required class="input-bordered" />
                <x-mary-input wire:model="email" label="{{ __('Alamat Email') }}" icon="o-envelope" type="email" placeholder="contoh@email.com" required class="input-bordered" />
                <x-mary-input wire:model="phone" label="{{ __('Nomor Telepon / WhatsApp') }}" icon="o-phone" type="tel" placeholder="08xxxxxxxxxx" class="input-bordered" />
                <x-mary-input wire:model="member_joined_at" label="{{ __('Tanggal Bergabung') }}" type="date" :disabled="! $is_member" class="input-bordered" />
            </div>

            <!-- Textarea Alamat Lengkap -->
            <x-mary-textarea wire:model="address" label="{{ __('Alamat Rumah Lengkap') }}" placeholder="Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, dan kecamatan..." rows="3" class="textarea-bordered" />

            <!-- Area Toggle Member -->
            <div class="flex items-center justify-between p-4 rounded-xl border border-base-200 bg-base-200/30">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-semibold text-base-content">{{ __('Status Aktif Nasabah') }}</span>
                    <span class="text-xs text-base-content/60">{{ __('Nasabah akan berhak mengumpulkan dan menukarkan poin tabungan.') }}</span>
                </div>
                <x-mary-toggle wire:model.live="is_member" class="toggle-primary" right />
            </div>

            <x-slot:actions>
                <x-mary-button label="{{ __('Batalkan') }}" @click="$wire.formModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button
                    label="{{ __('Simpan Perubahan') }}"
                    class="btn-primary font-semibold shadow-sm px-5 text-sm"
                    type="submit"
                    icon="o-check"
                    spinner="save"
                    data-test="nasabah-save-button"
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <!-- Modal Konfirmasi Hapus -->
    <x-mary-modal wire:model="deleteModal" title="{{ __('Konfirmasi Hapus Data') }}" box-class="max-w-md rounded-2xl">
        <div class="flex flex-col gap-3 py-2">
            <p class="text-sm text-base-content/70 leading-relaxed">
                {{ __('Apakah Anda yakin ingin menghapus data nasabah ini? Tindakan ini bersifat permanen dan data akun akan dihapus dari sistem, namun riwayat transaksi lama akan tetap dipertahankan demi validitas laporan keuangan.') }}
            </p>
        </div>

        <x-slot:actions>
            <x-mary-button label="{{ __('Batal') }}" @click="$wire.deleteModal = false" class="btn-ghost text-sm font-medium" />
            <x-mary-button
                label="{{ __('Ya, Hapus Permanen') }}"
                class="btn-error font-semibold text-sm px-4"
                wire:click="delete"
                spinner
                data-test="nasabah-confirm-delete"
            />
        </x-slot:actions>
    </x-mary-modal>

    <!-- ============================================== -->
    <!-- MODAL DASHBOARD PERSONAL NASABAH -->
    <!-- ============================================== -->
    <x-mary-modal wire:model="dashboardModal" box-class="max-w-5xl rounded-2xl !p-0" persistent>
        @if ($this->viewingUser)
            @php($user = $this->viewingUser)
            @php($balance = $user->balance)

            <div class="bg-base-100 rounded-2xl overflow-hidden">

                <!-- ===== HEADER PROFIL ===== -->
                <div style="padding:24px 24px 20px;border-bottom:1px solid #ECECEC;background:linear-gradient(135deg, rgba(34,197,94,.06), #fff);">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                        <div style="display:flex;align-items:center;gap:16px;">
                            <div style="width:56px;height:56px;border-radius:16px;background:#22c55e;color:#fff;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:18px;text-transform:uppercase;letter-spacing:.05em;box-shadow:0 1px 3px rgba(0,0,0,.08);flex-shrink:0;">
                                {{ substr($user->name, 0, 2) }}
                            </div>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <h3 style="font-size:16px;font-weight:700;color:#1a1a1a;letter-spacing:-.01em;margin:0;line-height:1;">{{ $user->name }}</h3>
                                   @if ($user->is_member)
                                        <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:rgba(34,197,94,.12);color:#16803c;">{{ __('Aktif') }}</span>
                                    @else
                                        <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:#e5e7eb;color:#6b7280;">{{ __('Tidak Aktif') }}</span>
                                    @endif
                                </div>
                                <span style="font-size:11px;font-family:monospace;font-weight:700;color:#22c55e;letter-spacing:.08em;">{{ $user->member_code ?? '—' }}</span>
                            </div>
                        </div>
                        <button @click="$wire.dashboardModal = false" style="width:28px;height:28px;border:none;background:transparent;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;cursor:pointer;" class="text-base-content/40 hover:text-base-content hover:bg-base-200 transition-colors">
                            <svg style="width:16px;height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <!-- Info kontak ringkas -->
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:20px;margin-top:16px;font-size:13px;color:rgba(0,0,0,.55);font-weight:500;">
                        <span style="display:inline-flex;align-items:center;gap:8px;">
                            <svg style="width:15px;height:15px;flex-shrink:0;opacity:.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span style="letter-spacing:.3px;">{{ $user->phone ?? '—' }}</span>
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:8px;">
                            <svg style="width:15px;height:15px;flex-shrink:0;opacity:.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            <span>{{ __('Bergabung') }} {{ $user->member_joined_at?->format('d M Y') ?? '—' }}</span>
                        </span>
                        @if ($user->address)
                            <span style="display:inline-flex;align-items:center;gap:8px;max-width:320px;" title="{{ $user->address }}">
                                <svg style="width:15px;height:15px;flex-shrink:0;opacity:.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $user->address }}</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Body scrollable -->
                <div class="max-h-[70vh] overflow-y-auto px-6 py-5 space-y-5">

                    <!-- ===== KARTU STATISTIK ===== -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Saldo Tersedia') }}</span>
                            <div class="text-lg font-bold text-success mt-1.5 leading-none">Rp {{ number_format((float) ($balance?->saldo_tersedia ?? 0), 0, ',', '.') }}</div>
                        </div>
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Saldo Tertahan') }}</span>
                            <div class="text-lg font-bold text-warning mt-1.5 leading-none">Rp {{ number_format((float) ($balance?->saldo_tertahan ?? 0), 0, ',', '.') }}</div>
                        </div>
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Poin Saat Ini') }}</span>
                            <div class="text-lg font-bold text-primary mt-1.5 leading-none">{{ number_format($balance?->points ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold">pt</span></div>
                        </div>
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Total Ditabung') }}</span>
                            <div class="text-lg font-bold text-base-content mt-1.5 leading-none">{{ number_format($this->categoryBreakdown->sum('total_qty'), 1, ',', '.') }} <span class="text-xs font-semibold">kg</span></div>
                        </div>
                    </div>

                    <!-- ===== GRAFIK TREN ===== -->
                    <div class="rounded-xl border border-base-200 bg-base-100 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h4 class="text-sm font-bold text-base-content tracking-tight">{{ __('Tren Tabungan') }}</h4>
                                <p class="text-xs text-base-content/45 mt-0.5">{{ __('Berat sampah (kg) per bulan') }}</p>
                            </div>
                            <div class="flex items-center gap-1 bg-base-200/70 rounded-lg p-1">
                                <button
                                    wire:click="setTrendRange('3bulan')"
                                    type="button"
                                    class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors {{ $trendRange === '3bulan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}"
                                >{{ __('3 Bulan') }}</button>
                                <button
                                    wire:click="setTrendRange('6bulan')"
                                    type="button"
                                    class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors {{ $trendRange === '6bulan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}"
                                >{{ __('6 Bulan') }}</button>
                                <button
                                    wire:click="setTrendRange('1tahun')"
                                    type="button"
                                    class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors {{ $trendRange === '1tahun' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}"
                                >{{ __('1 Tahun') }}</button>
                            </div>
                        </div>

                        <div
                            wire:key="trend-wrapper-{{ $viewingUserId }}-{{ $trendRange }}"
                            wire:ignore
                            x-data="{
                                chart: null,
                                labels: @js($this->monthlyTrend['labels']),
                                weights: @js($this->monthlyTrend['weights']),
                                renderChart() {
                                    if (typeof Chart === 'undefined') {
                                        setTimeout(() => this.renderChart(), 80);
                                        return;
                                    }
                                    if (this.chart) {
                                        this.chart.destroy();
                                        this.chart = null;
                                    }
                                    const ctx = this.$refs.canvas.getContext('2d');
                                    this.chart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: this.labels,
                                            datasets: [{
                                                label: 'Berat (kg)',
                                                data: this.weights,
                                                backgroundColor: 'rgba(34,197,94,0.55)',
                                                hoverBackgroundColor: 'rgba(34,197,94,0.8)',
                                                borderRadius: 6,
                                                maxBarThickness: 40,
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 } } },
                                                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                                            }
                                        }
                                    });
                                }
                            }"
                            x-init="
                                $nextTick(() => requestAnimationFrame(() => renderChart()));
                            "
                            @trend-updated.window="
                                labels = $event.detail.labels;
                                weights = $event.detail.weights;
                                $nextTick(() => requestAnimationFrame(() => renderChart()));
                            "
                            class="relative w-full"
                            style="height: 224px;"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>

                    <!-- ===== BREAKDOWN KATEGORI ===== -->
                    <div class="rounded-xl border border-base-200 bg-base-100 p-5">
                        <h4 class="text-sm font-bold text-base-content tracking-tight mb-3">{{ __('Total Sampah per Kategori') }}</h4>
                        @if ($this->categoryBreakdown->isEmpty())
                            <p class="text-xs text-base-content/40 text-center py-6">{{ __('Belum ada data tabungan.') }}</p>
                        @else
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                                @foreach ($this->categoryBreakdown as $cat)
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-base-200/40">
                                        <span class="text-xs font-semibold text-base-content/75 truncate">{{ $cat->category_name_snapshot }}</span>
                                        <span class="text-xs font-bold text-success whitespace-nowrap ml-2">{{ number_format((float) $cat->total_qty, 1, ',', '.') }} kg</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- ===== TAB RIWAYAT ===== -->
                    <div x-data="{ tab: 'tabungan' }" class="rounded-xl border border-base-200 bg-base-100 overflow-hidden">
                        <div class="flex items-center gap-1 p-2 border-b border-base-200 bg-base-200/20 overflow-x-auto">
                            <button @click="tab = 'tabungan'" :class="tab === 'tabungan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">{{ __('Tabungan') }}</button>
                            <button @click="tab = 'sedekah'" :class="tab === 'sedekah' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">{{ __('Sedekah') }}</button>
                            <button @click="tab = 'poin'" :class="tab === 'poin' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">{{ __('Poin') }}</button>
                            <button @click="tab = 'redemption'" :class="tab === 'redemption' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">{{ __('Penukaran') }}</button>
                        </div>

                        <!-- Riwayat Tabungan -->
                        <div x-show="tab === 'tabungan'" class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Tanggal') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Barang') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Berat') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Poin') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Nilai') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    @forelse ($this->savingHistory as $trx)
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap">{{ $trx->transacted_at->format('d M Y, H:i') }}</td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[220px] truncate" title="{{ $trx->items->pluck('item_name_snapshot')->join(', ') }}">
                                                {{ $trx->items->pluck('item_name_snapshot')->take(3)->join(', ') }}{{ $trx->items->count() > 3 ? '…' : '' }}
                                            </td>
                                            <td class="px-4 py-3 text-xs text-base-content/70 text-right tabular-nums whitespace-nowrap">{{ number_format((float) $trx->total_weight, 1, ',', '.') }} kg</td>
                                            <td class="px-4 py-3 text-xs font-semibold text-primary text-right tabular-nums whitespace-nowrap">+{{ $trx->points_awarded }} pt</td>
                                            <td class="px-4 py-3 text-sm font-bold text-success text-right tabular-nums whitespace-nowrap">Rp {{ number_format((float) $trx->total_value, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-xs text-base-content/40">{{ __('Belum ada riwayat tabungan.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Riwayat Sedekah -->
                        <div x-show="tab === 'sedekah'" x-cloak class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Tanggal') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Barang') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Berat') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    @forelse ($this->sedekahHistory as $trx)
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap">{{ $trx->transacted_at->format('d M Y, H:i') }}</td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[280px] truncate" title="{{ $trx->items->pluck('item_name_snapshot')->join(', ') }}">
                                                {{ $trx->items->pluck('item_name_snapshot')->take(3)->join(', ') }}{{ $trx->items->count() > 3 ? '…' : '' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-bold text-secondary text-right tabular-nums whitespace-nowrap">{{ number_format((float) $trx->total_weight, 1, ',', '.') }} kg</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-8 text-xs text-base-content/40">{{ __('Belum ada riwayat sedekah.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Riwayat Poin -->
                        <div x-show="tab === 'poin'" x-cloak class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Tanggal') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Keterangan') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Poin') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    @forelse ($this->pointHistory as $ph)
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap">{{ $ph->created_at->format('d M Y, H:i') }}</td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[280px] truncate">{{ $ph->description ?? $ph->type }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-right tabular-nums whitespace-nowrap {{ $ph->points >= 0 ? 'text-success' : 'text-error' }}">
                                                {{ $ph->points >= 0 ? '+' : '' }}{{ $ph->points }} pt
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-8 text-xs text-base-content/40">{{ __('Belum ada riwayat poin.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Riwayat Penukaran Poin -->
                        <div x-show="tab === 'redemption'" x-cloak class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Tanggal') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">{{ __('Produk') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Jumlah') }}</th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">{{ __('Poin Terpakai') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    @forelse ($this->redemptionHistory as $rd)
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap">{{ $rd->redeemed_at->format('d M Y, H:i') }}</td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[220px] truncate">{{ $rd->product_name_snapshot }}</td>
                                            <td class="px-4 py-3 text-xs text-base-content/70 text-right tabular-nums whitespace-nowrap">{{ number_format((float) $rd->quantity, 0) }} {{ $rd->unit_snapshot }}</td>
                                            <td class="px-4 py-3 text-sm font-bold text-warning text-right tabular-nums whitespace-nowrap">-{{ $rd->points_used }} pt</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-8 text-xs text-base-content/40">{{ __('Belum ada riwayat penukaran.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </x-mary-modal>
</section>

@script
<script>
    // Saat trendRange berubah di server, broadcast event browser agar canvas re-render
    // tanpa harus menghancurkan wire:ignore wrapper.
    $wire.on('trend-range-updated', (data) => {
        window.dispatchEvent(new CustomEvent('trend-updated', { detail: data }));
    });
</script>
@endscript
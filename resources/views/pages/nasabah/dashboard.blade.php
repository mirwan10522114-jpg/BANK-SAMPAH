<?php

use App\Models\PointHistory;
use App\Models\Redemption;
use App\Models\SavingTransaction;
use App\Models\SavingTransactionItem;
use App\Models\SedekahTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Dashboard')] class extends Component {

    public string $trendRange = '6bulan';

    public function setTrendRange(string $range): void
    {
        $this->trendRange = $range;

        $this->dispatch('trend-range-updated', labels: $this->monthlyTrend['labels'], weights: $this->monthlyTrend['weights']);
    }

    #[Computed]
    public function balance()
    {
        return Auth::user()->balance;
    }

    #[Computed]
    public function categoryBreakdown()
    {
        $userId = Auth::id();

        return SavingTransactionItem::query()
            ->whereHas('transaction', fn ($q) => $q->where('user_id', $userId))
            ->selectRaw('category_name_snapshot, SUM(quantity) as total_qty, SUM(subtotal) as total_value')
            ->groupBy('category_name_snapshot')
            ->orderByDesc('total_qty')
            ->get();
    }

    #[Computed]
    public function monthlyTrend(): array
    {
        $userId = Auth::id();

        $monthsBack = match ($this->trendRange) {
            '3bulan' => 2,
            '6bulan' => 5,
            '1tahun' => 11,
            default => 5,
        };

        $months = collect(range($monthsBack, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());

        $data = SavingTransaction::query()
            ->where('user_id', $userId)
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

    #[Computed]
    public function savingHistory()
    {
        return SavingTransaction::query()
            ->where('user_id', Auth::id())
            ->with('items')
            ->orderByDesc('transacted_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function sedekahHistory()
    {
        return SedekahTransaction::query()
            ->where('user_id', Auth::id())
            ->with('items')
            ->orderByDesc('transacted_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function pointHistory()
    {
        return PointHistory::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function redemptionHistory()
    {
        return Redemption::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('redeemed_at')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $user = Auth::user();

        $anggota          = $user->koperasiAnggota;
        $isMember         = $anggota !== null;
        $totalSimpanan    = $isMember ? $anggota->total_simpanan : 0;
        $saldoSukarela    = $isMember ? $anggota->saldo_sukarela : 0;
        $sisaPinjaman     = $isMember ? $anggota->sisa_pinjaman : 0;
        $pinjamanBerjalan = $isMember ? $anggota->pinjamans()->where('status', 'berjalan')->count() : 0;
        $pinjamanTerakhir = $isMember ? $anggota->pinjamans()->latest('tanggal_pengajuan')->first() : null;

        return view('pages.nasabah.dashboard', compact(
            'isMember', 'anggota', 'totalSimpanan', 'saldoSukarela',
            'sisaPinjaman', 'pinjamanBerjalan', 'pinjamanTerakhir'
        ));
    }
}; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<div class="flex flex-col gap-8 pb-8">

    {{-- GREETING --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-base-content">Halo, {{ auth()->user()->name }} 👋</h1>
            <p class="text-sm text-base-content/50 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="hidden sm:flex items-center gap-2">
            <x-mary-button label="Cairkan Saldo" icon="o-wallet" class="btn-sm btn-primary" link="{{ route('nasabah.pencairan') }}" />
        </div>
    </div>

    {{-- ===== BANK SAMPAH ===== --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black uppercase tracking-[.15em] text-base-content/40">Bank Sampah</span>
            <div class="flex-1 h-px bg-base-300"></div>
        </div>

        {{-- ===== KARTU STATISTIK ===== --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">Saldo Tersedia</span>
                <div class="text-lg font-bold text-success mt-1.5 leading-none">
                    Rp {{ number_format((float) ($this->balance?->saldo_tersedia ?? 0), 0, ',', '.') }}
                </div>
            </div>
            <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">Saldo Tertahan</span>
                <div class="text-lg font-bold text-warning mt-1.5 leading-none">
                    Rp {{ number_format((float) ($this->balance?->saldo_tertahan ?? 0), 0, ',', '.') }}
                </div>
            </div>
            <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">Poin Saat Ini</span>
                <div class="text-lg font-bold text-primary mt-1.5 leading-none">
                    {{ number_format($this->balance?->points ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold">pt</span>
                </div>
            </div>
            <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40">Total Ditabung</span>
                <div class="text-lg font-bold text-base-content mt-1.5 leading-none">
                    {{ number_format($this->categoryBreakdown->sum('total_qty'), 1, ',', '.') }} <span class="text-xs font-semibold">kg</span>
                </div>
            </div>
        </div>

        {{-- ===== GRAFIK TREN ===== --}}
        <div class="rounded-xl border border-base-200 bg-base-100 p-5">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <h4 class="text-sm font-bold text-base-content tracking-tight">Tren Tabungan</h4>
                    <p class="text-xs text-base-content/45 mt-0.5">Berat sampah (kg) per bulan</p>
                </div>
                <div class="flex items-center gap-1 bg-base-200/70 rounded-lg p-1">
                    <button
                        wire:click="setTrendRange('3bulan')"
                        type="button"
                        class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors {{ $trendRange === '3bulan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}"
                    >3 Bulan</button>
                    <button
                        wire:click="setTrendRange('6bulan')"
                        type="button"
                        class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors {{ $trendRange === '6bulan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}"
                    >6 Bulan</button>
                    <button
                        wire:click="setTrendRange('1tahun')"
                        type="button"
                        class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors {{ $trendRange === '1tahun' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content' }}"
                    >1 Tahun</button>
                </div>
            </div>

            <div
                wire:key="trend-wrapper-{{ $trendRange }}"
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

        {{-- ===== BREAKDOWN KATEGORI ===== --}}
        <div class="rounded-xl border border-base-200 bg-base-100 p-5">
            <h4 class="text-sm font-bold text-base-content tracking-tight mb-3">Total Sampah per Kategori</h4>
            @if ($this->categoryBreakdown->isEmpty())
                <p class="text-xs text-base-content/40 text-center py-6">Belum ada data tabungan.</p>
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

        {{-- ===== TAB RIWAYAT ===== --}}
        <div x-data="{ tab: 'tabungan' }" class="rounded-xl border border-base-200 bg-base-100 overflow-hidden">
            <div class="flex items-center gap-1 p-2 border-b border-base-200 bg-base-200/20 overflow-x-auto">
                <button @click="tab = 'tabungan'" :class="tab === 'tabungan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">Tabungan</button>
                <button @click="tab = 'sedekah'" :class="tab === 'sedekah' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">Sedekah</button>
                <button @click="tab = 'poin'" :class="tab === 'poin' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">Poin</button>
                <button @click="tab = 'redemption'" :class="tab === 'redemption' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors">Penukaran</button>
            </div>

            {{-- Riwayat Tabungan --}}
            <div x-show="tab === 'tabungan'" class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-base-200 bg-base-200/40">
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Tanggal</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Barang</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Berat</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Poin</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Nilai</th>
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
                                <td colspan="5" class="text-center py-8 text-xs text-base-content/40">Belum ada riwayat tabungan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Riwayat Sedekah --}}
            <div x-show="tab === 'sedekah'" x-cloak class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-base-200 bg-base-200/40">
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Tanggal</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Barang</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Berat</th>
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
                                <td colspan="3" class="text-center py-8 text-xs text-base-content/40">Belum ada riwayat sedekah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Riwayat Poin --}}
            <div x-show="tab === 'poin'" x-cloak class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-base-200 bg-base-200/40">
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Tanggal</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Keterangan</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Poin</th>
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
                                <td colspan="3" class="text-center py-8 text-xs text-base-content/40">Belum ada riwayat poin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Riwayat Penukaran Poin --}}
            <div x-show="tab === 'redemption'" x-cloak class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-base-200 bg-base-200/40">
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Tanggal</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40">Produk</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Jumlah</th>
                            <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right">Poin Terpakai</th>
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
                                <td colspan="4" class="text-center py-8 text-xs text-base-content/40">Belum ada riwayat penukaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ===== KOPERASI ===== --}}
    <div class="flex flex-col gap-4">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-black uppercase tracking-[.15em] text-base-content/40">Koperasi</span>
            <div class="flex-1 h-px bg-base-300"></div>
        </div>

        @if($isMember)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-2xl bg-gradient-to-br from-primary/10 to-primary/5 border border-primary/20 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-primary/15 flex items-center justify-center">
                            <x-mary-icon name="o-building-library" class="size-5 text-primary" />
                        </div>
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Total Simpanan</div>
                    </div>
                    <div class="text-2xl font-black text-primary">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</div>
                    <div class="text-xs text-base-content/40 mt-1">Semua jenis simpanan</div>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-success/10 to-success/5 border border-success/20 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl bg-success/15 flex items-center justify-center">
                            <x-mary-icon name="o-arrow-trending-up" class="size-5 text-success" />
                        </div>
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Sukarela</div>
                    </div>
                    <div class="text-2xl font-black text-success">Rp {{ number_format($saldoSukarela, 0, ',', '.') }}</div>
                    <div class="text-xs text-base-content/40 mt-1">Dapat ditarik sewaktu-waktu</div>
                </div>

                <div class="rounded-2xl bg-base-100 border border-base-200 p-5 {{ $sisaPinjaman > 0 ? '!border-error/30 !bg-error/5' : '' }}">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl {{ $sisaPinjaman > 0 ? 'bg-error/15' : 'bg-base-200' }} flex items-center justify-center">
                            <x-mary-icon name="o-credit-card" class="size-5 {{ $sisaPinjaman > 0 ? 'text-error' : 'text-base-content/30' }}" />
                        </div>
                        <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider">Sisa Pinjaman</div>
                    </div>
                    <div class="text-2xl font-black {{ $sisaPinjaman > 0 ? 'text-error' : 'text-base-content/30' }}">
                        Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-base-content/40 mt-1">{{ $pinjamanBerjalan }} pinjaman aktif</div>
                </div>
            </div>

            @if($pinjamanTerakhir)
                <div class="rounded-2xl border border-base-200 bg-base-100 p-5">
                    <div class="text-sm font-bold text-base-content mb-3">Pinjaman Terakhir</div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <div class="text-[11px] text-base-content/40 font-semibold uppercase tracking-wider mb-1">Nomor</div>
                            <div class="text-sm font-bold font-mono">{{ $pinjamanTerakhir->nomor_pinjaman }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-base-content/40 font-semibold uppercase tracking-wider mb-1">Jumlah</div>
                            <div class="text-sm font-bold">Rp {{ number_format((float)$pinjamanTerakhir->jumlah_pinjaman, 0, ',', '.') }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-base-content/40 font-semibold uppercase tracking-wider mb-1">Status</div>
                            <span class="badge badge-sm font-semibold {{ match($pinjamanTerakhir->status) {
                                'berjalan'  => 'badge-warning',
                                'lunas'     => 'badge-success',
                                'disetujui' => 'badge-info',
                                'ditolak'   => 'badge-error',
                                default     => 'badge-ghost'
                            } }}">{{ ucfirst($pinjamanTerakhir->status) }}</span>
                        </div>
                        <div>
                            <div class="text-[11px] text-base-content/40 font-semibold uppercase tracking-wider mb-1">Pengajuan</div>
                            <div class="text-sm font-bold">{{ $pinjamanTerakhir->tanggal_pengajuan->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex gap-3">
                <x-mary-button label="Simpanan Saya" icon="o-building-library" class="btn-sm btn-outline btn-primary" link="{{ route('koperasi.member.simpanan') }}" />
                <x-mary-button label="Pinjaman Saya" icon="o-credit-card" class="btn-sm btn-outline" link="{{ route('koperasi.member.pinjaman') }}" />
            </div>

        @else
            <div class="rounded-2xl border border-dashed border-base-300 bg-base-100 py-12 flex flex-col items-center gap-3 text-center">
                <div class="w-14 h-14 rounded-2xl bg-base-200 flex items-center justify-center">
                    <x-mary-icon name="o-building-library" class="size-7 text-base-content/20" />
                </div>
                <div>
                    <div class="text-sm font-semibold text-base-content/60">Belum terdaftar sebagai anggota koperasi</div>
                    <div class="text-xs text-base-content/40 mt-1">Hubungi admin untuk mendaftar dan mulai menabung</div>
                </div>
            </div>
        @endif
    </div>
</div>

@script
<script>
    $wire.on('trend-range-updated', (data) => {
        window.dispatchEvent(new CustomEvent('trend-updated', { detail: data }));
    });
</script>
@endscript
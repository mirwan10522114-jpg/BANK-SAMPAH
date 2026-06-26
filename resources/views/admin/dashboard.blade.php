@php
    use App\Models\Balance;
    use App\Models\SavingTransaction;
    use App\Models\SedekahTransaction;
    use App\Models\User;
    use App\Models\WasteCategory;
    use Illuminate\Support\Facades\DB;

    // ── 1. KONTROL NASABAH & SALDO UTAMA ──
    $nasabahCount  = User::nasabah()->count();
    $memberCount   = User::nasabah()->where('is_member', true)->count();
    $totalTertahan = (float) Balance::sum('saldo_tertahan');
    $totalTersedia = (float) Balance::sum('saldo_tersedia');
    $totalSaldo    = $totalTertahan + $totalTersedia;

    $thisMonth    = now()->startOfMonth();
    $lastMonth    = now()->subMonth()->startOfMonth();
    $lastMonthEnd = now()->subMonth()->endOfMonth();

    // ── 2. METRIK TABUNGAN & SEDEKAH (ALL-TIME) ──
    $allSaving  = SavingTransaction::selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_value),0) as val, COALESCE(SUM(total_weight),0) as wgt')->first();
    $allSedekah = SedekahTransaction::selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_weight),0) as wgt')->first();

    // ── 3. DATA TREN 12 BULAN ──
    $months12 = collect();
    for ($i = 11; $i >= 0; $i--) {
        $m     = now()->subMonths($i);
        $start = $m->copy()->startOfMonth();
        $end   = $m->copy()->endOfMonth();

        $rowSaving  = SavingTransaction::whereBetween('transacted_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(total_value),0) as val, COALESCE(SUM(total_weight),0) as wgt, COUNT(*) as cnt')->first();
        $rowSedekah = SedekahTransaction::whereBetween('transacted_at', [$start, $end])
            ->selectRaw('COALESCE(SUM(total_weight),0) as wgt, COUNT(*) as cnt')->first();

        $months12->push([
            'label'       => $m->format('M Y'),
            'timestamp'   => $start->timestamp * 1000,
            'val'         => (float) $rowSaving->val,
            'wgt'         => (float) $rowSaving->wgt,
            'cnt'         => (int)   $rowSaving->cnt,
            'sedekah_wgt' => (float) $rowSedekah->wgt,
            'sedekah_cnt' => (int)   $rowSedekah->cnt,
        ]);
    }

    // ── 4. AGREGASI KATEGORI SAMPAH ──
    $categories = WasteCategory::all();
    $catLabels  = [];
    $catData    = [];

    try {
        $savingWeights  = DB::table('saving_transaction_items')
            ->join('waste_items', 'saving_transaction_items.waste_item_id', '=', 'waste_items.id')
            ->select('waste_items.waste_category_id', DB::raw('SUM(saving_transaction_items.weight) as sum_wgt'))
            ->groupBy('waste_items.waste_category_id')->pluck('sum_wgt', 'waste_category_id');

        $sedekahWeights = DB::table('sedekah_transaction_items')
            ->join('waste_items', 'sedekah_transaction_items.waste_item_id', '=', 'waste_items.id')
            ->select('waste_items.waste_category_id', DB::raw('SUM(sedekah_transaction_items.weight) as sum_wgt'))
            ->groupBy('waste_items.waste_category_id')->pluck('sum_wgt', 'waste_category_id');

        foreach ($categories as $cat) {
            $totalWgt = (float)($savingWeights->get($cat->id, 0)) + (float)($sedekahWeights->get($cat->id, 0));
            if ($totalWgt > 0) {
                $catLabels[] = $cat->name;
                $catData[]   = round($totalWgt, 2);
            }
        }
    } catch (\Exception $e) {
        $catLabels = [];
        $catData   = [];
    }

    // MODE TESTING — hanya aktif di non-production (local/staging) supaya
    // dashboard tidak kosong saat development. Otomatis OFF di production.
    $testingMode = ! app()->isProduction();
    if ($testingMode && empty($catData)) {
        $catLabels = $categories->pluck('name')->toArray();
        $catData   = $categories->map(fn() => rand(25, 200))->toArray();
    }

    // ── 5. TOP 10 PAHLAWAN LINGKUNGAN ──
    $topNasabahSavings  = SavingTransaction::groupBy('user_id')
        ->selectRaw('user_id, SUM(total_weight) as total_wgt')->pluck('total_wgt', 'user_id');
    $topNasabahSedekahs = SedekahTransaction::groupBy('user_id')
        ->selectRaw('user_id, SUM(total_weight) as total_wgt')->pluck('total_wgt', 'user_id');

    $topNasabahData = User::nasabah()->get()->map(function ($user) use ($topNasabahSavings, $topNasabahSedekahs) {
        return [
            'name'  => $user->name,
            'total' => round((float)$topNasabahSavings->get($user->id, 0) + (float)$topNasabahSedekahs->get($user->id, 0), 2),
        ];
    })->filter(fn($u) => $u['total'] > 0)->sortByDesc('total')->take(10)->values()->toArray();

    $recentSavings = SavingTransaction::with(['user:id,name,email','items.item.category'])
        ->orderByDesc('transacted_at')->orderByDesc('id')->limit(10)->get()
        ->map(function($tx) {
            $categories = $tx->items->map(fn($i) => optional(optional($i->item)->category)->name)->filter()->unique()->values()->toArray();
            return ['type'=>'tabungan','transacted_at'=>$tx->transacted_at,'user'=>$tx->user,'total_weight'=>$tx->total_weight,'total_value'=>$tx->total_value ?? 0,'categories'=>$categories];
        });

    $recentSedekahs = SedekahTransaction::with(['user:id,name,email','items.item.category'])
        ->orderByDesc('transacted_at')->orderByDesc('id')->limit(10)->get()
        ->map(function($tx) {
            $categories = $tx->items->map(fn($i) => optional(optional($i->item)->category)->name)->filter()->unique()->values()->toArray();
            return ['type'=>'sedekah','transacted_at'=>$tx->transacted_at,'user'=>$tx->user,'total_weight'=>$tx->total_weight,'total_value'=>0,'categories'=>$categories];
        });

    $recentTransactions = $recentSavings->concat($recentSedekahs)
        ->sortByDesc('transacted_at')->take(20)->values();

    $rupiah = fn($v) => 'Rp ' . number_format((float)$v, 0, ',', '.');
@endphp

<x-layouts::app :title="__('Analytics Dashboard')">

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root {
    --g900: #064e3b; --g800: #047857; --g700: #059669; --g100: #d1fae5; --g50: #ecfdf5;
    --a700: #b45309; --a500: #f59e0b; --a100: #fef3c7;
    --t700: #0f766e; --t100: #ccfbf1;
    --r600: #e11d48; --r100: #ffe4e6;
    --p700: #6d28d9; --p100: #ede9fe;
    --bg:   #EBE8DE; --surf: #ffffff; --bord: #e2e8f0;
    --txt:  #1e293b; --muted: #64748b; --sub: #94a3b8;
    --radius-lg: 16px; --radius-md: 10px;
    --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
    --shadow-hover: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
}

*, *::before, *::after { box-sizing: border-box; }
.db-wrap { font-family: 'Inter', sans-serif; background: var(--bg); min-height: 100%; color: var(--txt); }

.text-header   { font-size: 24px; font-weight: 800; letter-spacing: -0.02em; color: var(--txt); }
.text-subtitle { font-size: 13px; color: var(--muted); font-weight: 400; }
.panel-title   { font-size: 15px; font-weight: 700; color: var(--txt); }

.kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; }
.kpi-card { background: var(--surf); border-radius: var(--radius-lg); padding: 24px; border: 1px solid var(--bord); box-shadow: var(--shadow-sm); position: relative; overflow: hidden; transition: all 0.2s ease; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-hover); }
.kpi-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; }
.kpi-card.c-green::before  { background: var(--g700); }
.kpi-card.c-amber::before  { background: var(--a500); }
.kpi-card.c-teal::before   { background: var(--t700); }
.kpi-card.c-purple::before { background: var(--p700); }

.kpi-icon { width: 46px; height: 46px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.kpi-icon.c-green  { background: var(--g100); color: var(--g800); }
.kpi-icon.c-amber  { background: var(--a100); color: var(--a700); }
.kpi-icon.c-teal   { background: var(--t100); color: var(--t700); }
.kpi-icon.c-purple { background: var(--p100); color: var(--p700); }

.trend-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; letter-spacing: 0.02em; }
.badge-up   { background: var(--g50);  color: var(--g800); border: 1px solid var(--g100); }
.badge-down { background: var(--r100); color: var(--r600); border: 1px solid #fecdd3; }

.panel { background: var(--surf); border-radius: var(--radius-lg); border: 1px solid var(--bord); box-shadow: var(--shadow-md); overflow: hidden; display: flex; flex-direction: column; }
.panel-header { padding: 18px 24px; border-bottom: 1px solid var(--bord); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: #fff; }
.panel-body { padding: 24px; flex: 1; }

.filter-chip { padding: 6px 14px; border-radius: 8px; border: 1px solid var(--bord); background: var(--surf); font-size: 13px; font-weight: 600; cursor: pointer; color: var(--muted); transition: all 0.15s; }
.filter-chip:hover  { border-color: var(--g700); color: var(--g700); }
.filter-chip.active { background: var(--g800); border-color: var(--g800); color: #fff; box-shadow: 0 2px 4px rgba(4,120,87,0.2); }

.db-input { padding: 7px 14px; border-radius: 8px; border: 1px solid var(--bord); background: var(--surf); font-size: 13px; color: var(--txt); outline: none; transition: border-color 0.2s; }
.db-input:focus { border-color: var(--g700); box-shadow: 0 0 0 2px var(--g100); }

.tb-btn { padding: 7px 14px; border-radius: 8px; border: 1px solid var(--bord); background: var(--surf); font-size: 13px; font-weight: 600; cursor: pointer; color: var(--txt); display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s; }
.tb-btn:hover { background: var(--g50); color: var(--g800); border-color: var(--g100); }

.table-container { width: 100%; overflow-x: auto; }
.db-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.db-table th { padding: 14px 20px; background: #f8fafc; font-weight: 700; font-size: 12px; text-transform: uppercase; color: var(--muted); border-bottom: 2px solid var(--bord); letter-spacing: 0.05em; }
.db-table td { padding: 16px 20px; border-bottom: 1px solid var(--bord); vertical-align: middle; }
.db-table tr:hover td { background: var(--g50); }
.db-table tr:last-child td { border-bottom: none; }

.search-wrap { position: relative; width: 260px; max-width: 100%; }
.search-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); }
.search-wrap input { width: 100%; padding-left: 36px; }

.canvas-container { position: relative; width: 100%; min-height: 250px; flex: 1; }

@media (max-width: 768px) {
    .grid-2col { grid-template-columns: 1fr !important; }
}
</style>

<div class="db-wrap flex h-full w-full flex-1 flex-col gap-6 pb-12">

    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between flex-wrap gap-4 mt-2">
        <div>
            <h1 class="text-header">Executive Dashboard</h1>
            <p class="text-subtitle mt-1">Pemantauan analitik terpusat operasional Bank Sampah</p>
        </div>
        <div style="display:flex;gap:10px">
            <x-mary-button label="Nabung Baru"  icon="o-plus"  class="btn-primary btn-sm rounded-lg"   link="{{ route('admin.saving.create') }}" />
            <x-mary-button label="Sedekah Baru" icon="o-heart" class="btn-secondary btn-sm rounded-lg" link="{{ route('admin.sedekah.create') }}" />
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-grid">

        {{-- KPI 1: Nilai Tabungan --}}
        <div class="kpi-card c-green">
            <div class="flex justify-between items-start mb-4">
                <div class="kpi-icon c-green">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
                <span class="trend-badge badge-up">All-Time</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:var(--txt)">{{ $rupiah($allSaving->val) }}</div>
            <div style="font-size:13px;color:var(--muted);margin-top:6px;font-weight:500;">Total Nilai Tabungan Sampah</div>
        </div>

        {{-- KPI 2: Berat Tabungan --}}
        <div class="kpi-card c-amber">
            <div class="flex justify-between items-start mb-4">
                <div class="kpi-icon c-amber">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <span class="trend-badge badge-up">All-Time</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:var(--txt)">{{ number_format((float)$allSaving->wgt, 1, ',', '.') }} kg</div>
            <div style="font-size:13px;color:var(--muted);margin-top:6px;font-weight:500;">Total Sampah Ditabung</div>
        </div>

        {{-- KPI 3: Sedekah --}}
        <div class="kpi-card c-purple">
            <div class="flex justify-between items-start mb-4">
                <div class="kpi-icon c-purple">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </div>
                <span class="trend-badge badge-up">All-Time</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:var(--txt)">{{ number_format((float)$allSedekah->wgt, 1, ',', '.') }} kg</div>
            <div style="font-size:13px;color:var(--muted);margin-top:6px;font-weight:500;">Total Sampah Disedekahkan</div>
        </div>

        {{-- KPI 4: Total Nasabah --}}
        <div class="kpi-card c-teal">
            <div class="flex justify-between items-start mb-4">
                <div class="kpi-icon c-teal">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <span class="trend-badge badge-up">Terverifikasi</span>
            </div>
            <div style="font-size:26px;font-weight:800;color:var(--txt)">{{ number_format($nasabahCount) }}</div>
            <div style="font-size:13px;color:var(--muted);margin-top:6px;font-weight:500;">Total Nasabah Terdaftar</div>
        </div>

    </div>

    {{-- Filter Bar --}}
    <div class="panel">
        <div class="panel-header" style="border-bottom:none;">
            <div style="display:flex; flex-wrap:wrap; gap:8px; align-items:center; width:100%;">
                <span style="font-size:13px; font-weight:700; color:#64847c; text-transform:uppercase; letter-spacing:0.05em; margin-right:4px;">FILTER:</span>

                {{-- Data chart bersifat BULANAN, jadi filter juga per bulan.
                     Sebelumnya tombol "7 Hari"/"30 Hari" salah label dan keduanya
                     memetakan ke data-months=1 (copy-paste bug). --}}
                <button class="filter-chip active" data-months="1"  onclick="setChartRange(1, this)">1 Bulan</button>
                <button class="filter-chip"        data-months="3"  onclick="setChartRange(3, this)">3 Bulan</button>
                <button class="filter-chip"        data-months="6"  onclick="setChartRange(6, this)">6 Bulan</button>
                <button class="filter-chip"        data-months="12" onclick="setChartRange(12, this)">1 Tahun</button>
                <button class="filter-chip"        data-months="custom" onclick="toggleCustomRange(this)">Custom</button>

                <div id="customRange" style="display:none; gap:6px; align-items:center;" class="ml-2">
                    <input type="date" class="db-input" id="dateFrom">
                    <span style="color:var(--muted); font-size:12px; font-weight:600;">s/d</span>
                    <input type="date" class="db-input" id="dateTo">
                    <button class="tb-btn" style="background:var(--g800); color:#fff; padding:5px 12px;" onclick="applyCustomRange()">Terapkan</button>
                </div>


            </div>
        </div>
    </div>

    {{-- Main Charts Grid --}}
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:24px; align-items:stretch;" class="grid-2col">

        {{-- Trend Chart --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">Tren Komparatif Operasional</h3>
                    <p class="text-subtitle mt-1">Perbandingan pertumbuhan Tabungan vs Sedekah</p>
                </div>
                <button class="tb-btn" onclick="exportExcel()">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export
                </button>
            </div>
            <div class="panel-body flex flex-col " wire:ignore>
                <div class="canvas-container " style="min-height:320px;">
                    <canvas style="display: flex; align-items:center; justify-content: start;" class="flex align-items:center justify-start"id="mainChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Category Doughnut --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">Komposisi Jenis Sampah</h3>
                    <p class="text-subtitle mt-1">Total kilogram berdasarkan kategori</p>
                </div>
            </div>
            <div class="panel-body flex flex-col items-center justify-center" wire:ignore>
                @if(count($catData) > 0)
                    <div class="canvas-container" style="max-width:250px; min-height:250px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center text-center p-6 bg-gray-50 rounded-lg border border-dashed border-gray-300 w-full h-full">
                        <span style="font-size:2rem;">&#128230;</span>
                        <p style="font-size:14px; font-weight:600; color:#4b5563; margin-top:8px;">Belum Ada Data</p>
                        <p style="font-size:12px; color:#9ca3af; margin-top:4px;">Catat transaksi untuk melihat visualisasi ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>



    {{-- Secondary Charts Grid --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;" class="grid-2col">

        {{-- Top 10 Leaderboard --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">Top 10 Pahlawan Lingkungan</h3>
                    <p class="text-subtitle mt-1">Nasabah dengan kontribusi sampah terbanyak (Total Kg)</p>
                </div>
            </div>
            <div class="panel-body flex flex-col" wire:ignore>
                <div class="canvas-container" style="min-height:280px;">
                    <canvas id="topNasabahChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Saldo Structure --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">Struktur Saldo Kas Nasabah</h3>
                    <p class="text-subtitle mt-1">Distribusi keamanan finansial</p>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:18px; font-weight:800; color:var(--txt);">{{ $rupiah($totalSaldo) }}</div>
                    <div style="font-size:11px; font-weight:600; color:var(--muted); text-transform:uppercase;">Total Aset</div>
                </div>
            </div>
            <div class="panel-body flex flex-col justify-center gap-6">
                <div style="display:flex; align-items:center; justify-content:center; gap:30px; flex-wrap:wrap;">
                    <div style="width:150px; height:150px; position:relative;" wire:ignore>
                        <canvas id="donutChart"></canvas>
                    </div>
                    <div class="flex-1 flex flex-col gap-3" style="min-width:200px;">
                        <div style="padding:16px; border-radius:12px; border:1px solid var(--bord); border-left:4px solid var(--a500); background:var(--surf);">
                            <div class="flex justify-between items-center mb-1">
                                <span style="font-size:13px; font-weight:600; color:var(--muted);">Saldo Tertahan</span>
                                <span style="font-size:14px; font-weight:800; color:var(--txt);">{{ $rupiah($totalTertahan) }}</span>
                            </div>
                            <div style="font-size:11px; color:var(--muted);">Proses mitigasi mitra</div>
                        </div>
                        <div style="padding:16px; border-radius:12px; border:1px solid var(--g100); border-left:4px solid var(--g700); background:var(--g50);">
                            <div class="flex justify-between items-center mb-1">
                                <span style="font-size:13px; font-weight:600; color:var(--g900);">Saldo Tersedia</span>
                                <span style="font-size:14px; font-weight:800; color:var(--g800);">{{ $rupiah($totalTersedia) }}</span>
                            </div>
                            <div style="font-size:11px; color:var(--g700);">Siap dicairkan nasabah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions Table --}}
    <div class="panel">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Log Transaksi Terbaru</h3>
                <p class="text-subtitle mt-1">Aktivitas penabungan sampah secara real-time</p>
            </div>
            <div class="search-wrap">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>
                <input type="text" class="db-input" id="tableSearch" placeholder="Cari data..." oninput="filterTableBySearch()">
            </div>
        </div>
        <div class="table-container">
            <table class="db-table" id="txTable">
                <thead>
                    <tr>
                        <th style="width:13%">Tanggal &amp; Waktu</th>
                        <th style="width:22%">Informasi Nasabah</th>
                        <th style="width:25%">Kategori Sampah</th>
                        <th style="width:12%">Total Berat</th>
                        <th style="width:15%">Nilai Konversi</th>
                        <th style="width:13%">Tipe Transaksi</th>
                    </tr>
                </thead>
                <tbody id="txBody">
                    @forelse($recentTransactions as $tx)
                    @php
                        $isSedekah = $tx['type'] === 'sedekah';
                        $userName  = $tx['user']?->name ?? 'Pengguna Tidak Dikenal';
                        $userEmail = $tx['user']?->email ?? '-';
                        $initials  = strtoupper(substr($userName, 0, 2));
                        $bgIcon    = $isSedekah ? 'var(--p100)' : 'var(--g100)';
                        $clrIcon   = $isSedekah ? 'var(--p700)' : 'var(--g800)';
                    @endphp
                    <tr data-name="{{ strtolower($userName) }}">
                        <td>
                            <div style="font-weight:600; color:var(--txt);">{{ $tx['transacted_at']->format('d M Y') }}</div>
                            <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $tx['transacted_at']->format('H:i') }} WIB</div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:12px; background:{{ $bgIcon }}; color:{{ $clrIcon }}; flex-shrink:0;">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <div style="font-weight:600; color:var(--txt);">{{ $userName }}</div>
                                    <div style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $userEmail }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if(!empty($tx['categories']))
                                <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                    @foreach($tx['categories'] as $cat)
                                        <span style="background:#f1f5f9; border:1px solid #e2e8f0; color:#475569; padding:3px 8px; border-radius:5px; font-size:11px; font-weight:600; white-space:nowrap;">{{ $cat }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span style="font-size:12px; color:var(--muted); font-style:italic;">-</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-weight:700; color:var(--txt); font-size:14px;">{{ number_format((float)$tx['total_weight'], 2, ',', '.') }}</span>
                            <span style="font-size:12px; color:var(--muted);"> kg</span>
                        </td>
                        <td>
                            @if(!$isSedekah)
                                <span style="font-weight:700; color:var(--g800); font-size:14px;">{{ $rupiah((float)$tx['total_value']) }}</span>
                            @else
                                <span style="font-size:13px; color:var(--muted); font-style:italic;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($isSedekah)
                                <span style="background:var(--p100); border:1px solid #ddd6fe; color:var(--p700); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:var(--p700); display:inline-block;"></span>
                                    Sedekah
                                </span>
                            @else
                                <span style="background:var(--g50); border:1px solid var(--g100); color:var(--g800); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:700; display:inline-flex; align-items:center; gap:6px;">
                                    <span style="width:6px; height:6px; border-radius:50%; background:var(--g700); display:inline-block;"></span>
                                    Tabungan
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px 20px; color:var(--muted);">
                            Belum ada transaksi terbaru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
// ==========================================
// DATA DARI BACKEND
// ==========================================
const rawMonthsData = @json($months12);
const topNasabahRaw = @json($topNasabahData);
const catLabels     = @json($catLabels);
const catData       = @json($catData);

let mainChart, donutChart, topNasabahChart, categoryChart;

function generateColors(count) {
    const palette = [
        '#059669','#f59e0b','#0ea5e9','#8b5cf6','#ec4899',
        '#14b8a6','#f43f5e','#84cc16','#6366f1','#f97316'
    ];
    return Array.from({ length: count }, (_, i) => palette[i % palette.length]);
}

document.addEventListener('DOMContentLoaded', function () {

    Chart.defaults.font.family   = "'Inter', sans-serif";
    Chart.defaults.color         = '#64748b';
    Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
    Chart.defaults.plugins.tooltip.padding         = 12;
    Chart.defaults.plugins.tooltip.cornerRadius    = 8;

    // 1. MAIN TREND CHART
    try {
        const ctx = document.getElementById('mainChart').getContext('2d');
        mainChart = new Chart(ctx, {
            data: {
                labels: rawMonthsData.map(d => d.label),
                datasets: [
                    {
                        type: 'bar',
                        label: 'Nilai Ekonomi (Rp)',
                        data: rawMonthsData.map(d => d.val),
                        backgroundColor: '#d1fae5',
                        borderColor: '#059669',
                        borderWidth: 1.5,
                        borderRadius: 4,
                        yAxisID: 'yVal',
                        order: 3
                    },
                    {
                        type: 'line',
                        label: 'Tabungan Masuk (kg)',
                        data: rawMonthsData.map(d => d.wgt),
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        yAxisID: 'yWgt',
                        order: 1
                    },
                    {
                        type: 'line',
                        label: 'Sedekah Terkumpul (kg)',
                        data: rawMonthsData.map(d => d.sedekah_wgt),
                        borderColor: '#6d28d9',
                        backgroundColor: '#6d28d9',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        tension: 0.3,
                        yAxisID: 'yWgt',
                        order: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { 
                        position: 'top', 
                        align: 'center', // <-- Diubah dari 'end' menjadi 'center'
                        labels: { 
                            usePointStyle: true, 
                            boxWidth: 8,
                            padding: 20 // <-- Tambahan agar spasi antar icon lebih rapi
                        } 
                    }
                },
                scales: {
                    x:    { grid: { display: false } },
                    yVal: { position: 'left',  grid: { color: '#f1f5f9' }, ticks: { callback: v => 'Rp ' + (v / 1000).toLocaleString('id-ID') + 'k' } },
                    yWgt: { position: 'right', grid: { display: false },   ticks: { callback: v => v + ' kg' } }
                }
            }
        });
    } catch (e) { console.error('Main Chart Error:', e); }
    // 2. CATEGORY DOUGHNUT
    try {
        if (catData.length > 0) {
            const ctx2 = document.getElementById('categoryChart').getContext('2d');
            categoryChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catData,
                        backgroundColor: generateColors(catData.length),
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 15, font: { size: 11 } } },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const total = ctx.chart._metasets[ctx.datasetIndex].total;
                                    const pct   = Math.round((ctx.raw / total) * 100);
                                    return ' ' + ctx.label + ': ' + ctx.raw + ' kg (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    } catch (e) { console.error('Category Chart Error:', e); }

    // 3. TOP NASABAH HORIZONTAL BAR
    try {
        const ctx3 = document.getElementById('topNasabahChart').getContext('2d');
        topNasabahChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: topNasabahRaw.map(n => n.name),
                datasets: [{
                    label: 'Total Sampah (kg)',
                    data: topNasabahRaw.map(n => n.total),
                    backgroundColor: '#0f766e',
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: '#f1f5f9' }, ticks: { callback: v => v + ' kg' } },
                    y: { grid: { display: false } }
                }
            }
        });
    } catch (e) { console.error('Top Nasabah Chart Error:', e); }

    // 4. SALDO DONUT
    try {
        const ctx4 = document.getElementById('donutChart').getContext('2d');
        donutChart = new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: ['Saldo Tertahan', 'Saldo Tersedia'],
                datasets: [{
                    data: [{{ $totalTertahan }}, {{ $totalTersedia }}],
                    backgroundColor: ['#f59e0b', '#059669'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: { legend: { display: false } }
            }
        });
    } catch (e) { console.error('Donut Chart Error:', e); }

    // Default: tampilkan 1 bulan terakhir
    setChartRange(1);
});

// ==========================================
// FUNGSI GLOBAL
// ==========================================

window.setChartRange = function (monthsToKeep, btnElement) {
    document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
    if (btnElement) btnElement.classList.add('active');

    document.getElementById('customRange').style.display = 'none';
    if (!mainChart) return;

    const sliced = rawMonthsData.slice(-monthsToKeep);
    mainChart.data.labels            = sliced.map(d => d.label);
    mainChart.data.datasets[0].data  = sliced.map(d => d.val);
    mainChart.data.datasets[1].data  = sliced.map(d => d.wgt);
    mainChart.data.datasets[2].data  = sliced.map(d => d.sedekah_wgt);
    mainChart.update();
};

window.toggleCustomRange = function (btnElement) {
    document.querySelectorAll('.filter-chip').forEach(b => b.classList.remove('active'));
    btnElement.classList.add('active');
    document.getElementById('customRange').style.display = 'flex';
};

window.applyCustomRange = function () {
    const from = document.getElementById('dateFrom').value;
    const to   = document.getElementById('dateTo').value;
    if (!from || !to || !mainChart) return;

    const fromDate = new Date(from);
    const toDate   = new Date(to);

    const filtered = rawMonthsData.filter(d => {
        const mDate = new Date(d.timestamp);
        return mDate >= fromDate && mDate <= toDate;
    });

    mainChart.data.labels           = filtered.map(d => d.label);
    mainChart.data.datasets[0].data = filtered.map(d => d.val);
    mainChart.data.datasets[1].data = filtered.map(d => d.wgt);
    mainChart.data.datasets[2].data = filtered.map(d => d.sedekah_wgt);
    mainChart.update();
};

window.filterTableByDropdowns = function () {
    const nasabah  = document.getElementById('filterNasabah').value.toLowerCase();
    const kategori = document.getElementById('filterKategori').value.toLowerCase();

    document.querySelectorAll('#txBody tr').forEach(function (tr) {
        const name    = (tr.dataset.name || '').toLowerCase();
        const rowText = tr.innerText.toLowerCase();

        const matchNasabah  = !nasabah  || name.includes(nasabah);
        const matchKategori = !kategori || rowText.includes(kategori);

        tr.style.display = (matchNasabah && matchKategori) ? '' : 'none';
    });
};

window.filterTableBySearch = function () {
    const q = document.getElementById('tableSearch').value.toLowerCase();
    document.querySelectorAll('#txBody tr').forEach(function (tr) {
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
};

window.exportExcel = function () {
    const ws = XLSX.utils.json_to_sheet(rawMonthsData.map(function (d) {
        return {
            'Bulan':               d.label,
            'Tabungan Nilai (Rp)': d.val,
            'Tabungan Berat (kg)': d.wgt,
            'Sedekah Berat (kg)':  d.sedekah_wgt
        };
    }));
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Laporan Eksekutif');
    XLSX.writeFile(wb, 'Laporan-Analitik-Bank-Sampah.xlsx');
};
</script>

</x-layouts::app>
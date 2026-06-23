<div class="w-full pb-10" x-data="dashboardData()">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Header -->
    <x-mary-header
        title="{{ __('Dashboard') }}"
        subtitle="{{ __('Ringkasan performa koperasi Anda.') }}"
        separator
    >
        <x-slot:actions>
            <div class="flex items-center gap-3 text-sm font-medium text-base-content/70 bg-base-100 border border-base-200 px-4 py-2 rounded-lg shadow-sm" x-init="startClock()">
                <x-mary-icon name="o-calendar" class="size-4 text-primary" />
                <span x-text="currentDate"></span>
                <span class="text-base-content/20">|</span>
                <x-mary-icon name="o-clock" class="size-4 text-base-content/40" />
                <span x-text="currentTime" class="w-10 tabular-nums"></span>
            </div>
        </x-slot:actions>
    </x-mary-header>

    <!-- Filter Bar -->
    <div class="rounded-xl border border-base-200 bg-base-100 p-5 shadow-sm mb-6 mt-4">
        <div class="flex flex-col lg:flex-row items-end gap-4 w-full">
            <div class="w-full lg:w-48">
                <label class="block text-[11px] font-bold text-base-content/50 uppercase tracking-wider mb-1.5">{{ __('Periode') }}</label>
                <select wire:model.live="periode" class="select select-bordered w-full h-10 min-h-[2.5rem]">
                    <option value="bulan_ini">{{ __('Bulan Ini') }}</option>
                    <option value="6_bulan">{{ __('6 Bulan Terakhir') }}</option>
                    <option value="1_tahun">{{ __('1 Tahun Terakhir') }}</option>
                    <option value="custom">{{ __('Kustom') }}</option>
                </select>
            </div>

            @if ($periode === 'custom')
                <div class="w-full lg:w-auto flex items-center gap-3" x-transition>
                    <div class="flex-1 lg:w-44">
                        <label class="block text-[11px] font-bold text-base-content/50 uppercase tracking-wider mb-1.5">{{ __('Dari') }}</label>
                        <input type="date" wire:model="tanggal_mulai" class="input input-bordered w-full h-10 min-h-[2.5rem]" />
                    </div>
                    <span class="text-base-content/30 font-bold mb-2">-</span>
                    <div class="flex-1 lg:w-44">
                        <label class="block text-[11px] font-bold text-base-content/50 uppercase tracking-wider mb-1.5">{{ __('Sampai') }}</label>
                        <input type="date" wire:model="tanggal_akhir" class="input input-bordered w-full h-10 min-h-[2.5rem]" />
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-2 w-full lg:w-auto">
                <x-mary-button
                    wire:click="terapkanFilter"
                    label="{{ __('Terapkan') }}"
                    icon="o-funnel"
                    class="btn-primary shadow-sm font-semibold text-sm flex-1 lg:flex-none"
                    spinner="terapkanFilter"
                />
                <x-mary-button
                    wire:click="resetFilter"
                    label="{{ __('Reset') }}"
                    class="btn-ghost font-semibold text-sm flex-1 lg:flex-none"
                    spinner="resetFilter"
                />
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <div class="rounded-xl border border-base-200 bg-base-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-base-content/40 uppercase tracking-wider">{{ __('Total Kas (Likuid)') }}</h3>
                    <p class="text-2xl font-black text-base-content mt-1">Rp {{ number_format($totalKas, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-primary/10 text-primary rounded-lg shrink-0">
                    <x-mary-icon name="o-wallet" class="size-6" />
                </div>
            </div>
            <div class="mt-3 text-xs font-semibold text-primary flex items-center gap-1">
                <x-mary-icon name="o-arrow-trending-up" class="size-3" /> {{ __('Saldo Tunai') }}
            </div>
        </div>

        <div class="rounded-xl border border-base-200 bg-base-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-base-content/40 uppercase tracking-wider">{{ __('Total Simpanan') }}</h3>
                    <p class="text-2xl font-black text-base-content mt-1">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-success/10 text-success rounded-lg shrink-0">
                    <x-mary-icon name="o-building-library" class="size-6" />
                </div>
            </div>
            <div class="mt-3 text-xs font-semibold text-base-content/40">
                {{ __('Dana Anggota') }}
            </div>
        </div>

        <div class="rounded-xl border border-base-200 bg-base-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-base-content/40 uppercase tracking-wider">{{ __('Sisa Pinjaman') }}</h3>
                    <p class="text-2xl font-black text-base-content mt-1">Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-warning/10 text-warning rounded-lg shrink-0">
                    <x-mary-icon name="o-credit-card" class="size-6" />
                </div>
            </div>
            <div class="mt-3 text-xs font-semibold text-warning">
                {{ __('Uang di Peminjam') }}
            </div>
        </div>

        <div class="rounded-xl border border-base-200 bg-base-100 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-[11px] font-bold text-base-content/40 uppercase tracking-wider">{{ __('Total Anggota') }}</h3>
                    <p class="text-2xl font-black text-base-content mt-1">{{ $totalAnggota }}</p>
                </div>
                <div class="p-2.5 bg-info/10 text-info rounded-lg shrink-0">
                    <x-mary-icon name="o-user-group" class="size-6" />
                </div>
            </div>
            <div class="w-full flex items-center gap-1.5 mt-auto">
                <span class="badge badge-success badge-sm gap-1">{{ $anggotaAktif }} {{ __('Aktif') }}</span>
                <span class="badge badge-warning badge-sm gap-1">{{ $anggotaPasif }} {{ __('Pasif') }}</span>
                <span class="badge badge-error badge-sm gap-1">{{ $anggotaKeluar }} {{ __('Keluar') }}</span>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

        <div class="lg:col-span-2 rounded-xl border border-base-200 bg-base-100 p-6 shadow-sm flex flex-col">
            <h3 class="text-base font-bold text-base-content mb-6">{{ __('Arus Kas (Trend)') }}</h3>
            <div class="relative flex-1 min-h-[320px] w-full" wire:ignore>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <div class="rounded-xl border border-base-200 bg-base-100 p-6 shadow-sm flex flex-col">
            <h3 class="text-base font-bold text-base-content mb-6">{{ __('Komposisi Simpanan') }}</h3>

            <div class="relative h-[200px] w-full flex items-center justify-center" wire:ignore>
                <canvas id="donutChart"></canvas>
            </div>

            <!-- Legend interaktif: klik untuk toggle slice -->
            <div class="mt-8 space-y-2.5 px-1">
                <button
                    type="button"
                    @click="toggleSlice(0, $el)"
                    class="flex w-full justify-between items-center text-sm py-1 rounded-lg hover:bg-base-200/50 transition-colors px-2 -mx-2"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-3 rounded-full" style="background:#0F6E56"></div>
                        <span class="text-base-content/70 font-medium">{{ __('Pokok') }}</span>
                    </div>
                    <span class="font-bold text-base-content">Rp {{ number_format($komposisiSimpanan['pokok'], 0, ',', '.') }}</span>
                </button>
                <button
                    type="button"
                    @click="toggleSlice(1, $el)"
                    class="flex w-full justify-between items-center text-sm py-1 rounded-lg hover:bg-base-200/50 transition-colors px-2 -mx-2"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-3 rounded-full" style="background:#3B82F6"></div>
                        <span class="text-base-content/70 font-medium">{{ __('Wajib') }}</span>
                    </div>
                    <span class="font-bold text-base-content">Rp {{ number_format($komposisiSimpanan['wajib'], 0, ',', '.') }}</span>
                </button>
                <button
                    type="button"
                    @click="toggleSlice(2, $el)"
                    class="flex w-full justify-between items-center text-sm py-1 rounded-lg hover:bg-base-200/50 transition-colors px-2 -mx-2"
                >
                    <div class="flex items-center gap-3">
                        <div class="size-3 rounded-full" style="background:#F59E0B"></div>
                        <span class="text-base-content/70 font-medium">{{ __('Sukarela') }}</span>
                    </div>
                    <span class="font-bold text-base-content">Rp {{ number_format($komposisiSimpanan['sukarela'], 0, ',', '.') }}</span>
                </button>
            </div>
        </div>
    </div>

    <div
        id="chartDataContainer"
        class="hidden"
        data-labels='@json($chartBulan)'
        data-masuk='@json($chartPemasukan)'
        data-keluar='@json($chartPengeluaran)'
        data-simpanan='@json(array_values($komposisiSimpanan))'
    ></div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardData', () => ({
            currentDate: '',
            currentTime: '',
            lineChartInstance: null,
            donutChartInstance: null,

            init() {
                Chart.defaults.font.family = "'Inter', 'Segoe UI', 'Roboto', 'Arial', sans-serif";
                Chart.defaults.color = '#64748B';

                this.startClock();
                this.initCharts();

                Livewire.hook('morph.updated', () => {
                    this.updateCharts();
                });
            },

            startClock() {
                const updateTime = () => {
                    const now = new Date();
                    this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                };
                updateTime();
                setInterval(updateTime, 1000);
            },

            getChartData() {
                const el = document.getElementById('chartDataContainer');
                return {
                    labels: JSON.parse(el.getAttribute('data-labels')),
                    masuk: JSON.parse(el.getAttribute('data-masuk')),
                    keluar: JSON.parse(el.getAttribute('data-keluar')),
                    simpanan: JSON.parse(el.getAttribute('data-simpanan')),
                };
            },

            toggleSlice(index, buttonEl) {
                if (!this.donutChartInstance) return;
                this.donutChartInstance.toggleDataVisibility(index);
                this.donutChartInstance.update();

                const isVisible = this.donutChartInstance.getDataVisibility(index);
                buttonEl.classList.toggle('opacity-40', !isVisible);
            },

            initCharts() {
                const data = this.getChartData();

                const ctxLine = document.getElementById('lineChart').getContext('2d');

                const gradientMasuk = ctxLine.createLinearGradient(0, 0, 0, 300);
                gradientMasuk.addColorStop(0, 'rgba(34,197,94,0.20)');
                gradientMasuk.addColorStop(1, 'rgba(34,197,94,0)');

                const gradientKeluar = ctxLine.createLinearGradient(0, 0, 0, 300);
                gradientKeluar.addColorStop(0, 'rgba(239,68,68,0.20)');
                gradientKeluar.addColorStop(1, 'rgba(239,68,68,0)');

                this.lineChartInstance = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'Pemasukan',
                                data: data.masuk,
                                borderColor: '#22c55e',
                                backgroundColor: gradientMasuk,
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#22c55e',
                                pointHoverRadius: 6,
                            },
                            {
                                label: 'Pengeluaran',
                                data: data.keluar,
                                borderColor: '#EF4444',
                                backgroundColor: gradientKeluar,
                                borderWidth: 2.5,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#EF4444',
                                pointHoverRadius: 6,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'top',
                                align: 'end',
                                labels: { usePointStyle: true, boxWidth: 8, font: { size: 12, weight: '500' } },
                            },
                            tooltip: {
                                backgroundColor: 'rgba(17,24,39,0.9)',
                                titleFont: { size: 13 },
                                bodyFont: { size: 13 },
                                padding: 12,
                                callbacks: {
                                    label: (ctx) => {
                                        const label = ctx.dataset.label ?? '';
                                        const value = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(ctx.raw);
                                        return `${label}: ${value}`;
                                    },
                                },
                            },
                        },
                        scales: {
                            y: {
                                border: { display: false },
                                grid: { color: '#F1F5F9' },
                                ticks: {
    font: { size: 11 },
    callback: (value) => {
        if (value >= 1000000) {
            const v = Math.round((value / 1000000) * 10) / 10;
            return v.toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' jt';
        }
        if (value >= 1000) {
            const v = Math.round((value / 1000) * 10) / 10;
            return v.toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' rb';
        }
        return value;
    },
},
                            },
                            x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        },
                        animation: { duration: 600, easing: 'easeOutQuart' },
                    },
                });

                const ctxDonut = document.getElementById('donutChart').getContext('2d');
                this.donutChartInstance = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pokok', 'Wajib', 'Sukarela'],
                        datasets: [{
                            data: data.simpanan,
                            backgroundColor: ['#0F6E56', '#3B82F6', '#F59E0B'],
                            borderWidth: 0,
                            hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17,24,39,0.9)',
                                bodyFont: { size: 13 },
                                padding: 10,
                                callbacks: {
                                    label: (ctx) => ' ' + new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(ctx.raw),
                                },
                            },
                        },
                        animation: { animateRotate: true, duration: 700 },
                    },
                });
            },

            updateCharts() {
                const data = this.getChartData();

                if (this.lineChartInstance) {
                    this.lineChartInstance.data.labels = data.labels;
                    this.lineChartInstance.data.datasets[0].data = data.masuk;
                    this.lineChartInstance.data.datasets[1].data = data.keluar;
                    this.lineChartInstance.update();
                }

                if (this.donutChartInstance) {
                    this.donutChartInstance.data.datasets[0].data = data.simpanan;
                    this.donutChartInstance.update();
                }
            },
        }));
    });
</script>
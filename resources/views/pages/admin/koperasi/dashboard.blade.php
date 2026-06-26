<<<<<<< HEAD
@assets
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
@endassets

<div class="w-full pb-10" x-data="dashboardData()">
=======
<div class="w-full pb-10" x-data="dashboardData()">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ __('Dashboard') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Ringkasan performa koperasi Anda.') }}</p>
        </div>
        
        <div class="bg-white border border-gray-200 px-4 py-2 rounded-lg shadow-sm flex items-center gap-3 text-sm font-medium text-gray-600" x-init="startClock()">
            <x-mary-icon name="o-calendar" class="size-4 text-blue-500" />
            <span x-text="currentDate"></span>
            <span class="text-gray-300">|</span>
            <x-mary-icon name="o-clock" class="size-4 text-gray-400" />
            <span x-text="currentTime" class="w-10"></span>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row items-end gap-4 w-full">
            <div class="w-full lg:w-48">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Periode</label>
                <select wire:model="periode" wire:change="gantiPeriode" class="select select-bordered w-full h-10 min-h-[2.5rem] bg-white text-gray-700 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 border-gray-300">
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="6_bulan">6 Bulan Terakhir</option>
                    <option value="1_tahun">1 Tahun Terakhir</option>
                    <option value="custom">Kustom</option>
                </select>
            </div>
            
            <div class="w-full lg:w-auto flex items-center gap-3">
                <div class="flex-1 lg:w-44">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dari</label>
                    <input type="date" wire:model="tanggal_mulai" wire:key="tgl-mulai-{{ $tanggal_mulai }}" class="input input-bordered w-full h-10 min-h-[2.5rem] bg-white text-gray-700 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 border-gray-300" />
                </div>
                <span class="text-gray-400 font-bold mb-2">-</span>
                <div class="flex-1 lg:w-44">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sampai</label>
                    <input type="date" wire:model="tanggal_akhir" wire:key="tgl-akhir-{{ $tanggal_akhir }}" class="input input-bordered w-full h-10 min-h-[2.5rem] bg-white text-gray-700 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 border-gray-300" />
                </div>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-auto">
                <button wire:click="terapkanFilterKustom" wire:loading.attr="disabled" class="btn h-10 min-h-[2.5rem] bg-[#2563EB] hover:bg-[#1D4ED8] text-white border-none px-6 flex-1 lg:flex-none shadow-sm rounded-lg font-semibold">
                    <span wire:loading wire:target="terapkanFilterKustom" class="loading loading-spinner loading-xs"></span>
                    <x-mary-icon wire:loading.remove wire:target="terapkanFilterKustom" name="o-funnel" class="size-4 mr-1" /> Terapkan
                </button>
                <button wire:click="resetFilter" wire:loading.attr="disabled" class="btn h-10 min-h-[2.5rem] bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm flex-1 lg:flex-none rounded-lg font-semibold">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Total Kas (Likuid)') }}</h3>
                    <p class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($totalKas, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-blue-50 text-blue-500 rounded-lg shrink-0">
                    <x-mary-icon name="o-wallet" class="size-6" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Total Simpanan') }}</h3>
                    <p class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-green-50 text-green-500 rounded-lg shrink-0">
                    <x-mary-icon name="o-building-library" class="size-6" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Sisa Pinjaman') }}</h3>
                    <p class="text-2xl font-black text-gray-800 mt-1">Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-orange-50 text-orange-500 rounded-lg shrink-0">
                    <x-mary-icon name="o-credit-card" class="size-6" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Total Anggota (Aktif)') }}</h3>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ $totalAnggota }}</p>
                </div>
                <div class="p-2.5 bg-purple-50 text-purple-500 rounded-lg shrink-0">
                    <x-mary-icon name="o-user-group" class="size-6" />
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-base font-bold text-gray-800">{{ __('Arus Kas (Trend)') }}</h3>
            </div>
            <div class="relative h-[320px] w-full" wire:ignore>
                <canvas id="lineChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm flex flex-col">
            <h3 class="text-base font-bold text-gray-800 mb-6">{{ __('Komposisi Simpanan') }}</h3>
            <div class="relative h-[220px] w-full flex items-center justify-center" wire:ignore>
                <canvas id="donutChart"></canvas>
            </div>
            <div class="mt-8 space-y-3 px-2">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-3"><div class="size-3.5 rounded-full bg-[#3B82F6]"></div><span class="text-gray-600 font-medium">Pokok</span></div>
                    <span class="font-bold text-gray-800">Rp {{ number_format($komposisiSimpananArray[0] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-3"><div class="size-3.5 rounded-full bg-[#10B981]"></div><span class="text-gray-600 font-medium">Wajib</span></div>
                    <span class="font-bold text-gray-800">Rp {{ number_format($komposisiSimpananArray[1] ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center gap-3"><div class="size-3.5 rounded-full bg-[#F97316]"></div><span class="text-gray-600 font-medium">Sukarela</span></div>
                    <span class="font-bold text-gray-800">Rp {{ number_format($komposisiSimpananArray[2] ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<<<<<<< HEAD
@script
<script>
    // Simpan di luar Alpine data — elak Alpine deep-proxy Chart.js instance → stack overflow
    let _lineChart = null;
    let _donutChart = null;

    Alpine.data('dashboardData', () => ({
        currentDate: '',
        currentTime: '',

        init() {
            Chart.defaults.font.family = "'Inter', 'Segoe UI', 'Roboto', 'Arial', sans-serif";
            Chart.defaults.color = '#64748B';

            this.startClock();

            const initLabels = {!! json_encode($chartBulan) !!};
            const initMasuk = {!! json_encode($chartPemasukan) !!};
            const initKeluar = {!! json_encode($chartPengeluaran) !!};
            const initSimpanan = {!! json_encode($komposisiSimpananArray) !!};

            this.buildCharts(initLabels, initMasuk, initKeluar, initSimpanan);

            // Guna window event — elak $wire reactive proxy dalam Alpine effect
            window.addEventListener('charts-updated', (e) => {
                this.refreshCharts({
                    labels: e.detail.bulan,
                    masuk: e.detail.pemasukan,
                    keluar: e.detail.pengeluaran,
                    simpanan: e.detail.simpanan,
                });
            });
        },

        refreshCharts(data) {
            if (_lineChart && data.labels) {
                _lineChart.data.labels = data.labels;
                _lineChart.data.datasets[0].data = data.masuk;
                _lineChart.data.datasets[1].data = data.keluar;
                _lineChart.update();
            }
            if (_donutChart && data.simpanan) {
                _donutChart.data.datasets[0].data = data.simpanan;
                _donutChart.update();
            }
        },

        startClock() {
            const updateTime = () => {
                const now = new Date();
                const optionsDate = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                this.currentDate = now.toLocaleDateString('id-ID', optionsDate);
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
            };
            updateTime();
            setInterval(updateTime, 1000);
        },

        buildCharts(labels, masuk, keluar, simpanan) {
            const ctxLine = document.getElementById('lineChart').getContext('2d');
            const gradientMasuk = ctxLine.createLinearGradient(0, 0, 0, 300);
            gradientMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
            gradientMasuk.addColorStop(1, 'rgba(16, 185, 129, 0)');

            const gradientKeluar = ctxLine.createLinearGradient(0, 0, 0, 300);
            gradientKeluar.addColorStop(0, 'rgba(239, 68, 68, 0.2)');
            gradientKeluar.addColorStop(1, 'rgba(239, 68, 68, 0)');

            _lineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Pemasukan', data: masuk, borderColor: '#10B981', backgroundColor: gradientMasuk, borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#10B981' },
                        { label: 'Pengeluaran', data: keluar, borderColor: '#EF4444', backgroundColor: gradientKeluar, borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#EF4444' }
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12, weight: '500' } } }, tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12 } },
                    scales: { y: { border: { display: false }, grid: { color: '#F1F5F9', drawBorder: false }, ticks: { font: { size: 11 } } }, x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 11 } } } }
                }
            });

            const ctxDonut = document.getElementById('donutChart').getContext('2d');
            _donutChart = new Chart(ctxDonut, {
                type: 'doughnut',
                data: { labels: ['Pokok', 'Wajib', 'Sukarela'], datasets: [{ data: simpanan, backgroundColor: ['#3B82F6', '#10B981', '#F97316'], borderWidth: 0, hoverOffset: 4 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 10 } } }
            });
        }
    }));
</script>
@endscript
=======
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
                
                const initLabels = {!! json_encode($chartBulan) !!};
                const initMasuk = {!! json_encode($chartPemasukan) !!};
                const initKeluar = {!! json_encode($chartPengeluaran) !!};
                const initSimpanan = {!! json_encode($komposisiSimpananArray) !!};
                
                this.buildCharts(initLabels, initMasuk, initKeluar, initSimpanan);

                // Watch langsung ke property Livewire (bukan event custom).
                // Begitu loadData() di server selesai & chartBulan ke-update,
                // Alpine otomatis kebawa reaktif lewat $wire — tidak lewat
                // sistem event sama sekali, jadi tidak ada celah timing.
                this.$watch('$wire.chartBulan', () => {
                    this.refreshCharts({
                        labels: this.$wire.chartBulan,
                        masuk: this.$wire.chartPemasukan,
                        keluar: this.$wire.chartPengeluaran,
                        simpanan: this.$wire.komposisiSimpananArray,
                    });
                });
            },

            // Pembaruan Grafik
            refreshCharts(data) {
                if(this.lineChartInstance && data.labels) {
                    this.lineChartInstance.data.labels = data.labels;
                    this.lineChartInstance.data.datasets[0].data = data.masuk;
                    this.lineChartInstance.data.datasets[1].data = data.keluar;
                    this.lineChartInstance.update();
                }
                if(this.donutChartInstance && data.simpanan) {
                    this.donutChartInstance.data.datasets[0].data = data.simpanan;
                    this.donutChartInstance.update();
                }
            },

            startClock() {
                const updateTime = () => {
                    const now = new Date();
                    const optionsDate = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
                    this.currentDate = now.toLocaleDateString('id-ID', optionsDate);
                    this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                };
                updateTime();
                setInterval(updateTime, 1000);
            },

            buildCharts(labels, masuk, keluar, simpanan) {
                const ctxLine = document.getElementById('lineChart').getContext('2d');
                const gradientMasuk = ctxLine.createLinearGradient(0, 0, 0, 300);
                gradientMasuk.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); 
                gradientMasuk.addColorStop(1, 'rgba(16, 185, 129, 0)');

                const gradientKeluar = ctxLine.createLinearGradient(0, 0, 0, 300);
                gradientKeluar.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); 
                gradientKeluar.addColorStop(1, 'rgba(239, 68, 68, 0)');

                this.lineChartInstance = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Pemasukan', data: masuk, borderColor: '#10B981', backgroundColor: gradientMasuk, borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#10B981' },
                            { label: 'Pengeluaran', data: keluar, borderColor: '#EF4444', backgroundColor: gradientKeluar, borderWidth: 2.5, fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#fff', pointBorderColor: '#EF4444' }
                        ]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12, weight: '500' } } }, tooltip: { mode: 'index', intersect: false, backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 12 } },
                        scales: { y: { border: { display: false }, grid: { color: '#F1F5F9', drawBorder: false }, ticks: { font: { size: 11 } } }, x: { grid: { display: false, drawBorder: false }, ticks: { font: { size: 11 } } } }
                    }
                });

                const ctxDonut = document.getElementById('donutChart').getContext('2d');
                this.donutChartInstance = new Chart(ctxDonut, {
                    type: 'doughnut',
                    data: { labels: ['Pokok', 'Wajib', 'Sukarela'], datasets: [{ data: simpanan, backgroundColor: ['#3B82F6', '#10B981', '#F97316'], borderWidth: 0, hoverOffset: 4 }] },
                    options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 10 } } }
                });
            }
        }));
    });
</script>
>>>>>>> 368fa13fc346eac9fb8470d0ed8933b1febb10ea

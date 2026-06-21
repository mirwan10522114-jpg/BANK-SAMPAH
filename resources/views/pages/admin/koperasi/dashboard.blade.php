<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h1 class="text-2xl font-bold text-base-content">{{ __('Dashboard Koperasi') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Ringkasan aktivitas koperasi simpan pinjam') }}</p>
        </div>
        <span class="badge badge-warning badge-outline">{{ __('Dalam Pengembangan') }}</span>
    </div>

    <div class="stats stats-vertical lg:stats-horizontal shadow w-full mb-6 bg-base-100">
        <div class="stat">
            <div class="stat-figure text-primary">
                <x-mary-icon name="o-user-group" class="size-8" />
            </div>
            <div class="stat-title">{{ __('Total Anggota') }}</div>
            <div class="stat-value text-primary">0</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-success">
                <x-mary-icon name="o-banknotes" class="size-8" />
            </div>
            <div class="stat-title">{{ __('Total Simpanan') }}</div>
            <div class="stat-value text-success">Rp 0</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-warning">
                <x-mary-icon name="o-credit-card" class="size-8" />
            </div>
            <div class="stat-title">{{ __('Pinjaman Aktif') }}</div>
            <div class="stat-value text-warning">0</div>
        </div>
        <div class="stat">
            <div class="stat-figure text-error">
                <x-mary-icon name="o-exclamation-triangle" class="size-8" />
            </div>
            <div class="stat-title">{{ __('Tunggakan') }}</div>
            <div class="stat-value text-error">Rp 0</div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title text-base">{{ __('Aktivitas Terbaru') }}</h2>
            <div class="flex flex-col items-center justify-center py-12 text-center text-base-content/50">
                <x-mary-icon name="o-clipboard-document-list" class="size-12 mb-3" />
                <p class="font-medium">{{ __('Belum ada aktivitas') }}</p>
                <p class="text-sm">{{ __('Data akan muncul di sini setelah modul koperasi mulai digunakan.') }}</p>
            </div>
        </div>
    </div>
</div>

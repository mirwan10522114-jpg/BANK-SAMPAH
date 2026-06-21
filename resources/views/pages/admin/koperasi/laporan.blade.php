<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-base-content">{{ __('Laporan Koperasi') }}</h1>
        <p class="text-sm text-base-content/60">{{ __('Rekap simpanan, pinjaman, dan arus kas koperasi') }}</p>
    </div>

    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="flex flex-wrap items-end gap-3">
                <div class="form-control">
                    <label class="label"><span class="label-text">{{ __('Periode') }}</span></label>
                    <select class="select select-bordered" disabled>
                        <option>{{ __('Bulan Ini') }}</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">{{ __('Jenis Laporan') }}</span></label>
                    <select class="select select-bordered" disabled>
                        <option>{{ __('Simpanan') }}</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" disabled>
                    <x-mary-icon name="o-document-chart-bar" class="size-4" />
                    {{ __('Generate Laporan') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="flex flex-col items-center justify-center py-16 text-center text-base-content/50">
                <x-mary-icon name="o-document-chart-bar" class="size-12 mb-3" />
                <p class="font-medium">{{ __('Belum ada laporan dibuat') }}</p>
                <p class="text-sm">{{ __('Pilih periode dan jenis laporan, lalu klik Generate Laporan.') }}</p>
            </div>
        </div>
    </div>
</div>

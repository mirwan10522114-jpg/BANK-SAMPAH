<div>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-base-content">{{ __('Pengaturan Koperasi') }}</h1>
        <p class="text-sm text-base-content/60">{{ __('Konfigurasi umum modul koperasi simpan pinjam') }}</p>
    </div>

    <div class="card bg-base-100 shadow max-w-2xl">
        <div class="card-body space-y-4">
            <div class="form-control">
                <label class="label"><span class="label-text">{{ __('Nama Koperasi') }}</span></label>
                <input type="text" class="input input-bordered" placeholder="{{ __('Koperasi Bank Sampah') }}" disabled />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">{{ __('Bunga Simpanan (%/bulan)') }}</span></label>
                    <input type="number" class="input input-bordered" placeholder="0" disabled />
                </div>
                <div class="form-control">
                    <label class="label"><span class="label-text">{{ __('Bunga Pinjaman (%/bulan)') }}</span></label>
                    <input type="number" class="input input-bordered" placeholder="0" disabled />
                </div>
            </div>
            <div class="alert alert-warning">
                <x-mary-icon name="o-information-circle" class="size-5" />
                <span class="text-sm">{{ __('Form ini masih placeholder — logika simpan pengaturan belum diimplementasikan.') }}</span>
            </div>
            <div class="card-actions justify-end">
                <button type="button" class="btn btn-primary btn-sm" disabled>{{ __('Simpan Pengaturan') }}</button>
            </div>
        </div>
    </div>
</div>

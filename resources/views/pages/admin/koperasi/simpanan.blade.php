<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-base-content">{{ __('Simpanan Koperasi') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Catatan simpanan pokok, wajib, dan sukarela anggota') }}</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" disabled>
            <x-mary-icon name="o-plus" class="size-4" />
            {{ __('Catat Simpanan') }}
        </button>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('No') }}</th>
                            <th>{{ __('Nama Anggota') }}</th>
                            <th>{{ __('Jenis Simpanan') }}</th>
                            <th>{{ __('Jumlah') }}</th>
                            <th>{{ __('Tanggal') }}</th>
                            <th class="text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-base-content/50">
                                    <x-mary-icon name="o-banknotes" class="size-12 mb-3" />
                                    <p class="font-medium">{{ __('Belum ada data simpanan') }}</p>
                                    <p class="text-sm">{{ __('Fitur pencatatan simpanan sedang dalam pengembangan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

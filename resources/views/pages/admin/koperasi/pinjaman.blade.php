<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-base-content">{{ __('Pinjaman Koperasi') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Pengajuan dan angsuran pinjaman anggota') }}</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" disabled>
            <x-mary-icon name="o-plus" class="size-4" />
            {{ __('Ajukan Pinjaman') }}
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
                            <th>{{ __('Jumlah Pinjaman') }}</th>
                            <th>{{ __('Tenor') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th class="text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-base-content/50">
                                    <x-mary-icon name="o-credit-card" class="size-12 mb-3" />
                                    <p class="font-medium">{{ __('Belum ada data pinjaman') }}</p>
                                    <p class="text-sm">{{ __('Fitur pengajuan & angsuran pinjaman sedang dalam pengembangan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

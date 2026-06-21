<div>
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-base-content">{{ __('Anggota Koperasi') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Kelola data anggota koperasi simpan pinjam') }}</p>
        </div>
        <button type="button" class="btn btn-primary btn-sm" disabled>
            <x-mary-icon name="o-plus" class="size-4" />
            {{ __('Tambah Anggota') }}
        </button>
    </div>

    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('No') }}</th>
                            <th>{{ __('Nama') }}</th>
                            <th>{{ __('No. Anggota') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Tanggal Bergabung') }}</th>
                            <th class="text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-base-content/50">
                                    <x-mary-icon name="o-user-group" class="size-12 mb-3" />
                                    <p class="font-medium">{{ __('Belum ada data anggota') }}</p>
                                    <p class="text-sm">{{ __('Fitur pengelolaan anggota koperasi sedang dalam pengembangan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

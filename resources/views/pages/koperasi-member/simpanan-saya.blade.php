<div>
    <!-- Header atau Judul Halaman -->
    <h2 class="text-2xl font-bold mb-4">Riwayat Simpanan Saya</h2>

    <!-- Area Filter Tanggal -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-white rounded-lg shadow-sm border border-gray-100">
        <div>
            <x-input 
                type="date" 
                label="Dari Tanggal" 
                wire:model.live="tanggal_mulai" 
                icon="o-calendar" 
            />
        </div>
        <div>
            <x-input 
                type="date" 
                label="Sampai Tanggal" 
                wire:model.live="tanggal_akhir" 
                icon="o-calendar" 
            />
        </div>
        <div class="flex items-end">
            <!-- Tombol Reset Filter -->
            <x-button 
                label="Reset Filter" 
                wire:click="$set('tanggal_mulai', null); $set('tanggal_akhir', null)" 
                class="btn-outline w-full"
                icon="o-arrow-path" 
            />
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-sm border border-gray-100">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis Transaksi</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transaksi as $item)
                    <tr>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>
                            @if($item->jenis_transaksi == 'masuk')
                                <span class="badge badge-success">Setoran</span>
                            @else
                                <span class="badge badge-error">Penarikan</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>{{ $item->keterangan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            Tidak ada riwayat transaksi pada rentang waktu tersebut.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transaksi->links() }}
    </div>
</div>
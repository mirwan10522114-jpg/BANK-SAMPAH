<div class="w-full pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ __('Laporan Keuangan') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Ringkasan arus kas, pemasukan, dan pengeluaran koperasi.') }}</p>
        </div>
        
        <div>
            <button wire:click="eksporPDF" class="btn btn-sm bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 shadow-sm rounded-lg font-bold px-4">
                <x-mary-icon name="o-document-arrow-down" class="size-4 mr-1" /> Ekspor PDF
            </button>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm mb-6">
        <div class="flex flex-col lg:flex-row items-end gap-4 w-full">
            
            <div class="w-full lg:w-48">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Periode</label>
                <select wire:model.live="periode" class="select select-bordered w-full h-10 min-h-[2.5rem] bg-gray-50 text-sm font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-blue-100">
                    <option value="7_hari">7 Hari Terakhir</option>
                    <option value="bulan_ini">Bulan Ini</option>
                    <option value="tahun_ini">Tahun Ini</option>
                    <option value="custom">Kustom</option>
                </select>
            </div>
            
            <div class="w-full lg:w-auto flex items-center gap-3">
                <div class="flex-1 lg:w-40">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Dari Tanggal</label>
                    <input type="date" wire:model="tanggal_mulai" class="input input-bordered w-full h-10 min-h-[2.5rem] bg-gray-50 text-sm font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </div>
                <span class="text-gray-400 font-bold mb-2">-</span>
                <div class="flex-1 lg:w-40">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sampai Tanggal</label>
                    <input type="date" wire:model="tanggal_akhir" class="input input-bordered w-full h-10 min-h-[2.5rem] bg-gray-50 text-sm font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </div>
            </div>

            <div class="w-full lg:w-48">
                <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1.5">Tipe Transaksi</label>
                <select wire:model.live="tipe_filter" class="select select-bordered w-full h-10 min-h-[2.5rem] bg-gray-50 text-sm font-semibold text-gray-700 focus:bg-white focus:ring-2 focus:ring-blue-100">
                    <option value="">Semua Transaksi</option>
                    <option value="masuk">Pemasukan (Masuk)</option>
                    <option value="keluar">Pengeluaran (Keluar)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full lg:w-auto ml-auto">
                <button wire:click="terapkanFilter" class="btn h-10 min-h-[2.5rem] bg-blue-600 hover:bg-blue-700 text-white border-none px-6 flex-1 lg:flex-none shadow-sm rounded-lg font-bold">
                    <x-mary-icon name="o-funnel" class="size-4 mr-1" /> Filter
                </button>
                <button wire:click="resetFilter" class="btn h-10 min-h-[2.5rem] bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 shadow-sm flex-1 lg:flex-none rounded-lg font-bold">
                    Reset
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Total Transaksi') }}</h3>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($totalTransaksi, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-gray-50 text-gray-500 rounded-lg">
                    <x-mary-icon name="o-document-text" class="size-5" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Total Pemasukan') }}</h3>
                    <p class="text-2xl font-black text-green-600 mt-1">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-green-50 text-green-500 rounded-lg">
                    <x-mary-icon name="o-arrow-down-left" class="size-5" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Total Pengeluaran') }}</h3>
                    <p class="text-2xl font-black text-red-600 mt-1">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                </div>
                <div class="p-2.5 bg-red-50 text-red-500 rounded-lg">
                    <x-mary-icon name="o-arrow-up-right" class="size-5" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Surplus / Defisit') }}</h3>
                    <p class="text-2xl font-black {{ $saldoAkhir >= 0 ? 'text-blue-600' : 'text-orange-500' }} mt-1">
                        Rp {{ number_format($saldoAkhir, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-2.5 bg-blue-50 text-blue-500 rounded-lg">
                    <x-mary-icon name="o-banknotes" class="size-5" />
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full whitespace-nowrap">
                <thead class="bg-gray-50 border-b border-gray-200 text-gray-600 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="py-4 px-5">Tanggal</th>
                        <th>No. Referensi</th>
                        <th>Sumber</th>
                        <th>Keterangan Transaksi</th>
                        <th class="text-right">Pemasukan (Masuk)</th>
                        <th class="text-right px-5">Pengeluaran (Keluar)</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-gray-800">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-none">
                            <td class="py-3 px-5">
                                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->translatedFormat('d M Y') }}</span>
                            </td>
                            <td>
                                <span class="text-xs text-gray-500 font-mono">{{ $tx->nomor_referensi }}</span>
                            </td>
                            <td>
                                <span class="badge badge-ghost badge-sm uppercase font-bold text-[10px] text-gray-600">
                                    {{ str_replace('_', ' ', $tx->sumber) }}
                                </span>
                            </td>
                            <td>
                                <span class="font-semibold text-gray-700">{{ $tx->keterangan }}</span>
                            </td>
                            <td class="text-right">
                                @if($tx->tipe === 'masuk')
                                    <span class="font-black text-green-600">Rp {{ number_format($tx->jumlah, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="text-right px-5">
                                @if($tx->tipe === 'keluar')
                                    <span class="font-black text-red-600">Rp {{ number_format($tx->jumlah, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">
                                <x-mary-icon name="o-folder-open" class="size-10 text-gray-300 mx-auto mb-3" />
                                <p class="font-bold">Tidak ada data transaksi</p>
                                <p class="text-xs mt-1">Belum ada transaksi di rentang tanggal ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($transactions->hasPages())
            <div class="border-t border-gray-100 bg-gray-50 p-4">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
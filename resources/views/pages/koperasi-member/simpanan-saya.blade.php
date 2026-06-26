<div class="w-full pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ __('Simpanan Saya') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Rincian saldo simpanan koperasi Anda.') }}</p>
        </div>
    </div>

    @if (! $anggota)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <x-mary-icon name="o-exclamation-triangle" class="size-10 text-yellow-500 mx-auto mb-3" />
            <p class="text-gray-700 font-medium">Akun Anda belum terhubung ke data anggota koperasi.</p>
            <p class="text-sm text-gray-500 mt-1">Silakan hubungi admin untuk menghubungkan akun Anda.</p>
        </div>
    @else
        {{-- Saldo per jenis --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @foreach ($saldos as $saldo)
                <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                    <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                        Simpanan {{ ucfirst($saldo->jenis_simpanan) }}
                    </h3>
                    <p class="text-2xl font-black text-gray-800">
                        Rp {{ number_format($saldo->saldo, 0, ',', '.') }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Riwayat transaksi --}}
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800">Riwayat Transaksi (20 Terakhir)</h3>
            </div>
            @if ($transaksis->isEmpty())
                <div class="p-8 text-center text-gray-400 text-sm">Belum ada transaksi.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="px-5 py-3 text-left">Tanggal</th>
                                <th class="px-5 py-3 text-left">Jenis</th>
                                <th class="px-5 py-3 text-left">Tipe</th>
                                <th class="px-5 py-3 text-right">Jumlah</th>
                                <th class="px-5 py-3 text-left">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($transaksis as $tx)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 text-gray-600">
                                        {{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->translatedFormat('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3 capitalize text-gray-700">{{ $tx->jenis_simpanan }}</td>
                                    <td class="px-5 py-3">
                                        <span @class([
                                            'px-2 py-0.5 rounded-full text-xs font-semibold',
                                            'bg-green-100 text-green-700' => $tx->tipe === 'setor',
                                            'bg-red-100 text-red-700' => $tx->tipe !== 'setor',
                                        ])>{{ ucfirst($tx->tipe) }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-medium">
                                        Rp {{ number_format($tx->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-500">{{ $tx->keterangan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif
</div>

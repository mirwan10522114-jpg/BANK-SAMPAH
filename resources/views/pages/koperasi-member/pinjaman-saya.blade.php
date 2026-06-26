<div class="w-full pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ __('Pinjaman Saya') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Daftar pinjaman dan status angsuran Anda.') }}</p>
        </div>
    </div>

    @if (! $anggota)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <x-mary-icon name="o-exclamation-triangle" class="size-10 text-yellow-500 mx-auto mb-3" />
            <p class="text-gray-700 font-medium">Akun Anda belum terhubung ke data anggota koperasi.</p>
            <p class="text-sm text-gray-500 mt-1">Silakan hubungi admin untuk menghubungkan akun Anda.</p>
        </div>
    @elseif ($pinjamans->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400 text-sm">
            Tidak ada data pinjaman.
        </div>
    @else
        <div class="space-y-6">
            @foreach ($pinjamans as $pinjaman)
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5 flex flex-col md:flex-row gap-4 justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pinjaman</p>
                            <p class="text-xl font-black text-gray-800 mt-1">
                                Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjaman)->translatedFormat('d M Y') }}
                                &mdash; {{ $pinjaman->tenor }} bulan
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sisa</p>
                            <p class="text-xl font-black text-gray-800 mt-1">
                                Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                            </p>
                            <span @class([
                                'px-2 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block',
                                'bg-green-100 text-green-700' => $pinjaman->status === 'lunas',
                                'bg-blue-100 text-blue-700' => $pinjaman->status === 'berjalan',
                                'bg-gray-100 text-gray-600' => ! in_array($pinjaman->status, ['lunas', 'berjalan']),
                            ])>{{ ucfirst($pinjaman->status) }}</span>
                        </div>
                    </div>

                    @if ($pinjaman->angsurans && $pinjaman->angsurans->isNotEmpty())
                        <div class="border-t border-gray-100 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] tracking-wider">
                                    <tr>
                                        <th class="px-5 py-2 text-left">Ke-</th>
                                        <th class="px-5 py-2 text-left">Jatuh Tempo</th>
                                        <th class="px-5 py-2 text-right">Jumlah</th>
                                        <th class="px-5 py-2 text-left">Status</th>
                                        <th class="px-5 py-2 text-left">Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($pinjaman->angsurans as $angsuran)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-5 py-2 text-gray-600">{{ $angsuran->ke }}</td>
                                            <td class="px-5 py-2 text-gray-600">
                                                {{ \Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)->translatedFormat('d M Y') }}
                                            </td>
                                            <td class="px-5 py-2 text-right font-medium">
                                                Rp {{ number_format($angsuran->jumlah_angsuran, 0, ',', '.') }}
                                            </td>
                                            <td class="px-5 py-2">
                                                <span @class([
                                                    'px-2 py-0.5 rounded-full text-xs font-semibold',
                                                    'bg-green-100 text-green-700' => $angsuran->status === 'lunas',
                                                    'bg-yellow-100 text-yellow-700' => $angsuran->status === 'belum',
                                                ])>{{ ucfirst($angsuran->status) }}</span>
                                            </td>
                                            <td class="px-5 py-2 text-gray-500 text-xs">
                                                {{ $angsuran->tanggal_bayar ? \Carbon\Carbon::parse($angsuran->tanggal_bayar)->translatedFormat('d M Y') : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ __('Penarikan Sukarela') }}</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">{{ __('Otorisasi dan pencairan dana simpanan sukarela anggota.') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="join shadow-sm">
                <div class="join-item">
                    <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari No / Nama..." icon="o-magnifying-glass" class="input-sm border-gray-300" />
                </div>
                <select wire:model.live="statusFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Status</option>
                    <option value="menunggu">Menunggu</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                    <option value="dicairkan">Dicairkan</option>
                </select>
            </div>

            <button type="button" wire:click="openAddModal" class="btn btn-primary btn-sm bg-black hover:bg-gray-800 text-white border-none shadow-md">
                <x-mary-icon name="o-document-plus" class="size-4" />
                {{ __('Buat Pengajuan') }}
            </button>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs tracking-wider border-b border-gray-200">
                    <tr>
                        <th>{{ __('No. Pengajuan') }}</th>
                        <th>{{ __('Tanggal') }}</th>
                        <th>{{ __('Anggota Pemohon') }}</th>
                        <th>{{ __('Nominal Penarikan') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium">
                    @forelse ($pengajuans as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="font-mono font-bold text-gray-700">{{ $item->nomor_pengajuan }}</td>
                            <td>
                                <div class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="font-bold text-gray-900">{{ $item->anggota->nama ?? 'Anggota tidak ditemukan' }}</div>
                                <div class="font-mono text-xs text-gray-500">{{ $item->anggota->nomor_anggota ?? '—' }}</div>
                            </td>
                            <td class="font-black text-red-600">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($item->status === 'menunggu')
                                    <span class="badge badge-warning badge-sm font-bold text-white uppercase px-3">Menunggu</span>
                                @elseif($item->status === 'disetujui')
                                    <span class="badge badge-info badge-sm font-bold text-white uppercase px-3">Disetujui</span>
                                @elseif($item->status === 'ditolak')
                                    <span class="badge badge-error badge-sm font-bold text-white uppercase px-3">Ditolak</span>
                                @elseif($item->status === 'dicairkan')
                                    <span class="badge badge-success badge-sm font-bold text-white uppercase px-3">Dicairkan</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openProcessModal({{ $item->id }})" class="btn btn-xs btn-outline border-gray-300 shadow-sm" title="Lihat & Proses">
                                        <x-mary-icon name="o-eye" class="size-3" /> Detail
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                                    <x-mary-icon name="o-document-text" class="size-16 mb-4 text-gray-300" />
                                    <p class="font-bold text-lg">{{ __('Tidak ada data pengajuan') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pengajuans->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $pengajuans->links() }}
            </div>
        @endif
    </div>

    <x-mary-modal wire:model="addModal" title="Pengajuan Penarikan Simpanan" class="backdrop-blur-sm">
        <form id="form-pengajuan" wire:submit="simpanPengajuan">
            <div class="space-y-4">
                <x-mary-choices
                    label="Pilih Anggota Pemohon"
                    wire:model.live="selectedAnggotaId"
                    :options="$anggotaAktif"
                    option-label="nama_label"
                    option-value="id"
                    placeholder="Ketik nama / NIK..."
                    single
                    searchable
                    required
                />

                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-1">Saldo Sukarela Tersedia</p>
                    <p class="text-2xl font-black text-gray-900">Rp {{ number_format($saldoTersedia, 0, ',', '.') }}</p>
                </div>

                <x-mary-input wire:model="jumlah" label="Nominal Penarikan (Rp)" type="number" placeholder="Contoh: 500000" min="1000" required />
                <x-mary-textarea wire:model="alasan" label="Alasan Penarikan" placeholder="Tuliskan alasan penarikan dana..." required rows="3"></x-mary-textarea>
            </div>
        </form>
        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('addModal', false)">Batal</button>
            <button type="submit" form="form-pengajuan" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="simpanPengajuan" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="simpanPengajuan" name="o-paper-airplane" class="size-4" /> Kirim Pengajuan
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-modal wire:model="processModal" title="Proses Pengajuan Penarikan" class="backdrop-blur-sm" box-class="max-w-md">
        @if($processData)
            <div class="space-y-4 text-gray-800">
                <div class="p-5 bg-white border border-gray-200 shadow-sm rounded-lg">
                    <div class="flex justify-between items-center mb-3 border-b border-gray-100 pb-2">
                        <span class="font-bold text-gray-500 text-xs uppercase">No. Pengajuan</span>
                        <span class="font-mono font-black text-gray-900">{{ $processData->nomor_pengajuan }}</span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Anggota Pemohon:</span>
                            <span class="font-bold">{{ $processData->anggota->nama ?? 'Anggota tidak ditemukan' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tgl. Pengajuan:</span>
                            <span class="font-bold">{{ \Carbon\Carbon::parse($processData->tanggal_pengajuan)->format('d M Y - H:i') }}</span>
                        </div>
                        <div class="flex justify-between mt-2 pt-2 border-t border-dashed border-gray-200">
                            <span class="text-gray-500 font-bold">Nominal Diminta:</span>
                            <span class="font-black text-red-600 text-lg">Rp {{ number_format($processData->jumlah, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-2 bg-gray-50 p-3 rounded-md text-gray-600 italic text-xs">
                            "{{ $processData->alasan }}"
                        </div>
                    </div>

                    @if($processData->status !== 'menunggu')
                        <div class="mt-4 pt-4 border-t border-gray-200 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Diproses Oleh:</span>
                                <span class="font-bold">{{ $processData->nama_pengurus ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-gray-500">Tgl. Keputusan:</span>
                                <span class="font-bold">{{ $processData->tanggal_persetujuan ? \Carbon\Carbon::parse($processData->tanggal_persetujuan)->format('d M Y H:i') : '-' }}</span>
                            </div>
                            @if($processData->status === 'dicairkan')
                                <div class="flex justify-between mt-1 text-green-600">
                                    <span class="font-bold">Tgl. Pencairan Dana:</span>
                                    <span class="font-black">{{ \Carbon\Carbon::parse($processData->tanggal_pencairan)->format('d M Y H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                @if($processData->status === 'menunggu')
                    <div class="mt-4 p-4 border border-warning/30 bg-warning/5 rounded-lg shadow-inner">
                        <x-mary-input wire:model="nama_pengurus" label="Tanda Tangan Pengurus" placeholder="Ketik nama Anda di sini..." class="bg-white" required />
                        <label class="label"><span class="label-text-alt text-gray-500">* Wajib diisi sebagai rekam jejak persetujuan.</span></label>
                    </div>
                @elseif($processData->status === 'disetujui')
                    <div class="alert alert-info shadow-sm mt-4 text-xs font-medium">
                        Pengajuan ini telah disetujui. Silakan siapkan uang tunai/transfer, lalu klik <strong>Cairkan Dana</strong> untuk memotong saldo.
                    </div>
                @endif
            </div>

            <x-slot:actions>
                <button type="button" class="btn btn-ghost" wire:click="$set('processModal', false)">Tutup</button>

                @if($processData->status === 'menunggu')
                    <button type="button" wire:click="tolakPengajuan" wire:loading.attr="disabled" class="btn btn-error text-white font-bold border-none shadow-md">
                        <span wire:loading wire:target="tolakPengajuan" class="loading loading-spinner loading-xs"></span>
                        Tolak
                    </button>
                    <button type="button" wire:click="setujuiPengajuan" wire:loading.attr="disabled" class="btn btn-info text-white font-bold border-none shadow-md">
                        <span wire:loading wire:target="setujuiPengajuan" class="loading loading-spinner loading-xs"></span>
                        Setujui
                    </button>
                @elseif($processData->status === 'disetujui')
                    <button type="button" wire:click="cairkanDana" wire:loading.attr="disabled" class="btn bg-[#EA580C] hover:bg-[#C2410C] text-white font-bold border-none shadow-md w-full">
                        <span wire:loading wire:target="cairkanDana" class="loading loading-spinner loading-xs"></span>
                        <x-mary-icon wire:loading.remove wire:target="cairkanDana" name="o-banknotes" class="size-4" /> Cairkan Dana Sekarang
                    </button>
                @endif
            </x-slot:actions>
        @endif
    </x-mary-modal>
</div>
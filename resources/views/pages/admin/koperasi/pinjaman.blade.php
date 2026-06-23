<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ __('Pinjaman & Angsuran') }}</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">{{ __('Sistem manajemen kredit/pembiayaan anggota koperasi.') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="join shadow-sm">
                <div class="join-item">
                    <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari No Pinjaman / Anggota..." icon="o-magnifying-glass" class="input-sm border-gray-300" />
                </div>
                <select wire:model.live="statusFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Status</option>
                    <option value="berjalan">Berjalan</option>
                    <option value="lunas">Lunas</option>
                </select>
            </div>

            <button type="button" wire:click="openAngsuranModal" class="btn btn-outline btn-sm border-gray-300 text-gray-700 shadow-sm">
                <x-mary-icon name="o-banknotes" class="size-4" />
                {{ __('Bayar Angsuran') }}
            </button>

            <button type="button" wire:click="openPinjamanModal" class="btn btn-primary btn-sm bg-black hover:bg-gray-800 text-white border-none shadow-md">
                <x-mary-icon name="o-plus" class="size-4" />
                {{ __('Pinjaman Baru') }}
            </button>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs tracking-wider border-b border-gray-200">
                    <tr>
                        <th>{{ __('No. Pinjaman') }}</th>
                        <th>{{ __('Anggota') }}</th>
                        <th class="text-right">{{ __('Jumlah Pinjaman') }}</th>
                        <th>{{ __('Tenor') }}</th>
                        <th class="text-right">{{ __('Angsuran/Bulan') }}</th>
                        <th class="text-right">{{ __('Sisa Pinjaman') }}</th>
                        <th>{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium">
                    @forelse ($pinjamans as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="font-mono font-bold text-gray-700">{{ $p->nomor_pinjaman }}</td>
                            <td>
                                <div class="font-bold text-gray-900">{{ $p->anggota->nama }}</div>
                                <div class="font-mono text-xs text-gray-500">{{ $p->anggota->nomor_anggota }}</div>
                            </td>
                            <td class="text-right font-black text-gray-800">Rp {{ number_format($p->jumlah_pinjaman, 0, ',', '.') }}</td>
                            <td>{{ $p->tenor_bulan }} bulan</td>
                            <td class="text-right font-bold text-gray-700">Rp {{ number_format($p->angsuran_per_bulan, 0, ',', '.') }}</td>
                            <td class="text-right font-black {{ $p->sisa_pinjaman > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                Rp {{ number_format($p->sisa_pinjaman, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($p->status === 'lunas')
                                    <span class="badge badge-success badge-sm text-white font-bold uppercase"><x-mary-icon name="o-check-circle" class="size-3 mr-1"/> Lunas</span>
                                @elseif($p->status === 'berjalan')
                                    <span class="badge badge-warning badge-sm font-bold uppercase"><x-mary-icon name="o-clock" class="size-3 mr-1"/> Berjalan</span>
                                @else
                                    <span class="badge badge-ghost badge-sm font-bold uppercase">{{ ucfirst($p->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                                    <x-mary-icon name="o-document-text" class="size-16 mb-4 text-gray-300" />
                                    <p class="font-bold text-lg">{{ __('Belum ada data pinjaman') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pinjamans->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $pinjamans->links() }}
            </div>
        @endif
    </div>

    {{-- ===================== MODAL PENGAJUAN / PENCAIRAN PINJAMAN ===================== --}}
    <x-mary-modal wire:model="pinjamanModal" title="Form Pinjaman Baru" class="backdrop-blur-sm" box-class="w-11/12 max-w-2xl">
        <form id="form-pinjaman" wire:submit="savePinjaman">
            <div class="space-y-4">
                <x-mary-choices
                    label="Pilih Anggota Koperasi"
                    wire:model.live="selectedAnggotaId"
                    :options="$anggotaAktif"
                    option-label="nama_label"
                    option-value="id"
                    placeholder="Ketik NIK, No Anggota, atau Nama..."
                    single
                    searchable
                    required
                />

                <div class="grid grid-cols-2 gap-4">
                    <x-mary-input wire:model.live="jumlah_pinjaman" label="Jumlah Pinjaman (Rp)" type="number" placeholder="Contoh: 2000000" min="1000" required />
                    <x-mary-input wire:model.live="tenor_bulan" label="Tenor (Bulan)" type="number" placeholder="Contoh: 10" min="1" max="60" required />
                </div>

                @if($estimasiAngsuran > 0)
                    <div class="flex justify-between items-center p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <span class="font-bold text-gray-600 text-sm">Estimasi Angsuran / Bulan</span>
                        <span class="font-black text-xl text-primary">Rp {{ number_format($estimasiAngsuran, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-xs text-gray-400 italic -mt-2">*Tanpa bunga: angsuran = jumlah pinjaman ÷ tenor.</p>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <x-mary-input wire:model="biaya_admin" label="Biaya Admin (Rp, sekali bayar)" type="number" min="0" />
                    <x-mary-datetime wire:model="tanggal_pengajuan" label="Tanggal Pencairan" type="date" required />
                </div>

                <x-mary-input wire:model="keterangan_pinjaman" label="Keterangan (Opsional)" placeholder="Catatan tambahan..." />
            </div>
        </form>

        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('pinjamanModal', false)">Batal</button>
            <button type="submit" form="form-pinjaman" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="savePinjaman" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="savePinjaman" name="o-check-circle" class="size-4" />
                Cairkan Pinjaman
            </button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- ===================== MODAL PEMBAYARAN ANGSURAN ===================== --}}
    <x-mary-modal wire:model="angsuranModal" title="Form Pembayaran Angsuran" class="backdrop-blur-sm" box-class="w-11/12 max-w-2xl">
        <form id="form-angsuran" wire:submit="saveAngsuran">
            <div class="space-y-4">
                <x-mary-choices
                    label="Pilih Pinjaman Aktif"
                    wire:model.live="selectedPinjamanId"
                    :options="$pinjamanBerjalan"
                    option-label="pinjaman_label"
                    option-value="id"
                    placeholder="Ketik No Pinjaman atau Nama Anggota..."
                    single
                    searchable
                    required
                />

                @if($pinjamanAktif)
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 space-y-2">
                        <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Anggota</span><span class="font-bold">{{ $pinjamanAktif->anggota->nama }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Sisa Pinjaman</span><span class="font-black text-red-600">Rp {{ number_format($pinjamanAktif->sisa_pinjaman, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-500 font-medium">Angsuran Ke</span><span class="font-bold badge badge-primary badge-sm text-white">{{ $angsuranKe }} dari {{ $pinjamanAktif->tenor_bulan }}</span></div>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <x-mary-input wire:model="jumlah_bayar" label="Jumlah Bayar (Rp)" type="number" min="1" required />
                    <x-mary-datetime wire:model="tanggal_bayar" label="Tanggal Bayar" type="date" required />
                </div>

                <x-mary-input wire:model="keterangan_angsuran" label="Keterangan (Opsional)" placeholder="Catatan tambahan..." />
            </div>
        </form>

        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('angsuranModal', false)">Batal</button>
            <button type="submit" form="form-angsuran" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="saveAngsuran" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="saveAngsuran" name="o-check-circle" class="size-4" />
                Proses Pembayaran
            </button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- ===================== MODAL KUITANSI ===================== --}}
    <x-mary-modal wire:model="receiptModal" class="backdrop-blur-sm" box-class="w-11/12 max-w-md">
        @if($receiptData)
            <div id="print-area" class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl text-gray-800">
                <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
                    <h2 class="text-xl font-black uppercase tracking-widest">{{ config('app.name', 'Bank Sampah') }}</h2>
                    <p class="text-xs font-bold text-gray-500">Unit Koperasi Simpan Pinjam</p>
                </div>

                @if($receiptType === 'pinjaman')
                    <div class="space-y-2 text-sm font-medium">
                        <div class="flex justify-between"><span class="text-gray-500">No. Pinjaman</span> <span class="font-mono font-bold">{{ $receiptData->nomor_pinjaman }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Nama Anggota</span> <span class="font-bold uppercase">{{ $receiptData->anggota->nama }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Tenor</span> <span class="font-bold">{{ $receiptData->tenor_bulan }} bulan</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Angsuran/Bulan</span> <span class="font-bold">Rp {{ number_format($receiptData->angsuran_per_bulan, 0, ',', '.') }}</span></div>
                    </div>
                    <div class="my-4 py-4 border-y border-dashed border-gray-300 text-center bg-gray-50 rounded-lg">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Jumlah Pinjaman Dicairkan</p>
                        <p class="text-3xl font-black text-red-600">Rp {{ number_format($receiptData->jumlah_pinjaman, 0, ',', '.') }}</p>
                    </div>
                @else
                    <div class="space-y-2 text-sm font-medium">
                        <div class="flex justify-between"><span class="text-gray-500">No. Pinjaman</span> <span class="font-mono font-bold">{{ $receiptData->pinjaman->nomor_pinjaman }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Nama Anggota</span> <span class="font-bold uppercase">{{ $receiptData->pinjaman->anggota->nama }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Angsuran Ke</span> <span class="font-bold">{{ $receiptData->angsuran_ke }} dari {{ $receiptData->pinjaman->tenor_bulan }}</span></div>
                    </div>
                    <div class="my-4 py-4 border-y border-dashed border-gray-300 text-center bg-gray-50 rounded-lg">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Jumlah Pembayaran</p>
                        <p class="text-3xl font-black text-green-600">Rp {{ number_format($receiptData->jumlah_bayar, 0, ',', '.') }}</p>
                    </div>
                    <div class="space-y-2 text-sm font-medium">
                        <div class="flex justify-between"><span class="text-gray-500">Sisa Pinjaman</span> <span class="font-bold {{ $receiptData->sisa_pinjaman_setelah <= 0 ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($receiptData->sisa_pinjaman_setelah, 0, ',', '.') }}</span></div>
                        @if($receiptData->sisa_pinjaman_setelah <= 0)
                            <div class="text-center pt-2"><span class="badge badge-success text-white font-bold uppercase">Pinjaman Lunas</span></div>
                        @endif
                    </div>
                @endif

                <div class="mt-6 pt-4 border-t border-dashed border-gray-300 text-center">
                    <p class="text-xs text-gray-400 font-bold">Dilayani oleh: {{ $receiptData->user->name ?? 'Admin' }}</p>
                    <p class="text-[10px] text-gray-400 mt-2 italic">Dokumen ini sah dicetak oleh sistem secara otomatis.</p>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('receiptModal', false)">Tutup</button>
            <button type="button" onclick="printReceipt()" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <x-mary-icon name="o-printer" class="size-4" /> Cetak Kuitansi
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <script>
        // Cetak kuitansi diformat untuk printer thermal 48mm (lebar cetak ~32 karakter).
        // Pakai iframe tersembunyi (bukan window baru / innerHTML replace) agar
        // tidak memicu popup-blocker dan tidak merusak state Livewire halaman.
        function printReceipt() {
            var printArea = document.getElementById('print-area');
            if (!printArea) return;

            var printContents = printArea.innerHTML;

            var printStyle = `
                @page { size: 48mm auto; margin: 0; }
                * { box-sizing: border-box; }
                body {
                    font-family: 'Courier New', Courier, monospace;
                    color: #000;
                    width: 48mm;
                    margin: 0;
                    padding: 2mm 1.5mm;
                    font-size: 9px;
                    line-height: 1.35;
                }
                h2 { font-size: 11px; margin: 0 0 2px; text-align: center; text-transform: uppercase; }
                p { margin: 0; }
                .flex { display: flex; justify-content: space-between; gap: 4px; }
                .text-center { text-align: center; }
                .space-y-2 > * + * { margin-top: 2px; }
                .border-b, .border-y, .border-t { border-color: #000 !important; border-style: dashed !important; }
                .border-b { border-bottom-width: 1px; padding-bottom: 4px; margin-bottom: 4px; }
                .border-y { border-top-width: 1px; border-bottom-width: 1px; padding: 4px 0; margin: 4px 0; }
                .border-t { border-top-width: 1px; padding-top: 4px; margin-top: 4px; }
                .bg-gray-50, .bg-white { background: none !important; }
                .rounded-lg, .rounded-xl { border-radius: 0 !important; }
                .text-3xl { font-size: 13px; font-weight: 900; }
                .text-xl { font-size: 11px; }
                .text-green-600, .text-red-600, .text-gray-400, .text-gray-500, .text-primary { color: #000 !important; }
                .badge { border: 1px solid #000; padding: 1px 4px; font-size: 8px; border-radius: 0; }
                .uppercase { text-transform: uppercase; }
                .font-bold, .font-black { font-weight: 700; }
                .italic { font-style: italic; }
            `;

            var iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            // Pakai DOM API (bukan doc.write string-concat) supaya tidak bentrok
            // dengan script yang disuntikkan ekstensi/MCP browser logger ke setiap halaman.
            var idoc = iframe.contentDocument || iframe.contentWindow.document;

            var styleEl = idoc.createElement('style');
            styleEl.textContent = printStyle;
            idoc.head.appendChild(styleEl);

            var bodyWrap = idoc.createElement('div');
            bodyWrap.innerHTML = printContents;
            idoc.body.appendChild(bodyWrap);

            setTimeout(function () {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    console.error('Gagal mencetak kuitansi:', e);
                }
                setTimeout(function () {
                    if (iframe.parentNode) {
                        iframe.parentNode.removeChild(iframe);
                    }
                }, 1000);
            }, 100);
        }
    </script>
</div>
<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ __('Transaksi Simpanan') }}</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">{{ __('Catatan ledger setoran dan penarikan simpanan anggota.') }}</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="join shadow-sm">
                <div class="join-item">
                    <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari No Tx / Anggota..." icon="o-magnifying-glass" class="input-sm border-gray-300" />
                </div>
                <select wire:model.live="jenisFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Jenis</option>
                    <option value="pokok">Pokok</option>
                    <option value="wajib">Wajib</option>
                    <option value="sukarela">Sukarela</option>
                </select>
                <select wire:model.live="tipeFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Tipe</option>
                    <option value="setor">Setoran (Masuk)</option>
                    <option value="tarik">Penarikan (Keluar)</option>
                </select>
            </div>
            
            <button type="button" wire:click="openTransactionModal" class="btn btn-primary btn-sm bg-black hover:bg-gray-800 text-white border-none shadow-md">
                <x-mary-icon name="o-arrows-right-left" class="size-4" />
                {{ __('Transaksi Baru') }}
            </button>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs tracking-wider border-b border-gray-200">
                    <tr>
                        <th>{{ __('Waktu') }}</th>
                        <th>{{ __('No. Transaksi') }}</th>
                        <th>{{ __('Anggota') }}</th>
                        <th>{{ __('Jenis') }}</th>
                        <th>{{ __('Tipe') }}</th>
                        <th class="text-right">{{ __('Nominal') }}</th>
                        <th>{{ __('Keterangan') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($tx->tanggal_transaksi)->format('d/m/Y H:i') }}</td>
                            <td class="font-mono font-bold text-gray-700">{{ $tx->nomor_transaksi }}</td>
                            <td>
    <div class="font-bold text-gray-900">{{ $tx->anggota->nama ?? 'Anggota Dihapus' }}</div>
    <div class="font-mono text-xs text-gray-500">{{ $tx->anggota->nomor_anggota ?? '-' }}</div>
</td>
                            <td><span class="badge badge-ghost badge-sm uppercase font-bold">{{ $tx->jenis_simpanan }}</span></td>
                            <td>
                                @if($tx->tipe === 'setor')
                                    <span class="badge badge-success badge-sm text-white font-bold uppercase"><x-mary-icon name="o-arrow-down" class="size-3 mr-1"/> Setor</span>
                                @else
                                    <span class="badge badge-error badge-sm text-white font-bold uppercase"><x-mary-icon name="o-arrow-up" class="size-3 mr-1"/> Tarik</span>
                                @endif
                            </td>
                            <td class="text-right font-black {{ $tx->tipe === 'setor' ? 'text-green-600' : 'text-red-600' }}">
                                Rp {{ number_format($tx->jumlah, 0, ',', '.') }}
                            </td>
                            <td class="text-xs text-gray-500 truncate max-w-xs">{{ $tx->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                                    <x-mary-icon name="o-document-text" class="size-16 mb-4 text-gray-300" />
                                    <p class="font-bold text-lg">{{ __('Belum ada riwayat transaksi') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <x-mary-modal wire:model="transactionModal" title="Form Transaksi Simpanan" class="backdrop-blur-sm" box-class="w-11/12 max-w-3xl">
        <form id="form-transaksi" wire:submit="saveTransaction">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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
                        <x-mary-select 
                            label="Jenis Simpanan" 
                            wire:model.live="jenis_simpanan" 
                            :options="[['id'=>'pokok', 'name'=>'Pokok'], ['id'=>'wajib', 'name'=>'Wajib'], ['id'=>'sukarela', 'name'=>'Sukarela']]" 
                            required 
                        />
                        
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold">Tipe Transaksi</span></label>
                            <select wire:model.live="tipe_transaksi" class="select select-bordered w-full" required>
                                <option value="setor">Setor Dana (Masuk)</option>
                                @if($jenis_simpanan === 'sukarela')
                                    <option value="tarik">Tarik Tunai (Keluar)</option>
                                @else
                                    <option value="tarik" disabled>Tarik Tunai (Terkunci)</option>
                                @endif
                            </select>
                            @if(in_array($jenis_simpanan, ['pokok', 'wajib']))
                                <label class="label"><span class="label-text-alt text-error font-medium">* {{ ucfirst($jenis_simpanan) }} tidak bisa ditarik harian.</span></label>
                            @endif
                        </div>
                    </div>

                    <x-mary-input wire:model="jumlah" label="Nominal Transaksi (Rp)" type="number" placeholder="Contoh: 50000" min="500" required />
                    
                    <x-mary-datetime wire:model="tanggal_transaksi" label="Waktu Transaksi" type="datetime-local" required />
                    
                    <x-mary-input wire:model="keterangan" label="Keterangan (Opsional)" placeholder="Catatan tambahan..." />
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 shadow-inner">
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <x-mary-icon name="o-wallet" class="size-4" /> Info Saldo Saat Ini
                    </h3>
                    
                    @if($selectedAnggotaId)
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm transition-all {{ $jenis_simpanan == 'pokok' ? 'ring-2 ring-primary' : '' }}">
                                <span class="font-bold text-gray-600">Simpanan Pokok</span>
                                <span class="font-black text-lg">Rp {{ number_format($saldoSekarang['pokok'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm transition-all {{ $jenis_simpanan == 'wajib' ? 'ring-2 ring-primary' : '' }}">
                                <span class="font-bold text-gray-600">Simpanan Wajib</span>
                                <span class="font-black text-lg">Rp {{ number_format($saldoSekarang['wajib'], 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm transition-all {{ $jenis_simpanan == 'sukarela' ? 'ring-2 ring-primary' : '' }}">
                                <span class="font-bold text-gray-600">Simpanan Sukarela</span>
                                <span class="font-black text-lg text-green-600">Rp {{ number_format($saldoSekarang['sukarela'], 0, ',', '.') }}</span>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500 leading-relaxed font-medium">
                                    Total akumulasi kewajiban dan aset anggota di atas tersinkronisasi *real-time* dari buku besar simpanan.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center opacity-50">
                            <x-mary-icon name="o-user-circle" class="size-16 mb-2 text-gray-400" />
                            <p class="font-bold">Pilih anggota terlebih dahulu</p>
                            <p class="text-xs">Saldo akan muncul otomatis.</p>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('transactionModal', false)">Batal</button>
            <button type="submit" form="form-transaksi" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="saveTransaction" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="saveTransaction" name="o-check-circle" class="size-4" /> 
                Proses Transaksi
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-modal wire:model="receiptModal" class="backdrop-blur-sm" box-class="w-11/12 max-w-md">
        @if($receiptData)
            <div id="print-area" class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl text-gray-800">
                <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
                    <h2 class="text-xl font-black uppercase tracking-widest">{{ config('app.name', 'Bank Sampah') }}</h2>
                    <p class="text-xs font-bold text-gray-500">Unit Koperasi Simpan Pinjam</p>
                    <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($receiptData->tanggal_transaksi)->format('d M Y - H:i:s') }}</p>
                </div>
                
                <div class="space-y-2 text-sm font-medium">
                    <div class="flex justify-between"><span class="text-gray-500">No. Transaksi</span> <span class="font-mono font-bold">{{ $receiptData->nomor_transaksi }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">No. Anggota</span> <span class="font-bold">{{ $receiptData->anggota->nomor_anggota }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Nama Anggota</span> <span class="font-bold uppercase">{{ $receiptData->anggota->nama }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tipe / Jenis</span> <span class="font-bold uppercase">{{ $receiptData->tipe }} - {{ $receiptData->jenis_simpanan }}</span></div>
                </div>

                <div class="my-4 py-4 border-y border-dashed border-gray-300 text-center bg-gray-50 rounded-lg">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Nominal Transaksi</p>
                    <p class="text-3xl font-black {{ $receiptData->tipe === 'setor' ? 'text-green-600' : 'text-red-600' }}">Rp {{ number_format($receiptData->jumlah, 0, ',', '.') }}</p>
                </div>

                <div class="space-y-2 text-sm font-medium">
                    <div class="flex justify-between"><span class="text-gray-500">Saldo Sebelumnya</span> <span class="font-bold">Rp {{ number_format($receiptData->saldo_sebelum, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Saldo Akhir</span> <span class="font-bold">Rp {{ number_format($receiptData->saldo_sesudah, 0, ',', '.') }}</span></div>
                </div>

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
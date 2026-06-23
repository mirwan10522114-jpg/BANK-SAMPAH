<div class="w-full">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ __('Anggota Koperasi') }}</h1>
            <p class="text-sm font-medium text-gray-500 mt-1">{{ __('Manajemen komprehensif data anggota simpan pinjam.') }}</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="join shadow-sm">
                <div class="join-item">
                    <x-mary-input wire:model.live.debounce.300ms="search" placeholder="Cari ID / NIK / Nama..." icon="o-magnifying-glass" class="input-sm border-gray-300" />
                </div>
                <select wire:model.live="statusFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="pasif">Pasif</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>
            
            <button type="button" wire:click="openAddModal" class="btn btn-primary btn-sm bg-black hover:bg-gray-800 text-white border-none shadow-md">
                <x-mary-icon name="o-plus" class="size-4" />
                {{ __('Tambah Anggota') }}
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
            <div class="flex items-center justify-center size-14 rounded-full bg-blue-100 text-blue-600 shrink-0">
                <x-mary-icon name="o-users" class="size-7" />
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500">{{ __('Total Anggota Aktif') }}</p>
                <p class="text-2xl font-black text-gray-900">{{ $totalAnggotaAktif }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
            <div class="flex items-center justify-center size-14 rounded-full bg-red-100 text-red-600 shrink-0">
                <x-mary-icon name="o-user-minus" class="size-7" />
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500">{{ __('Anggota Pasif/Keluar') }}</p>
                <p class="text-2xl font-black text-gray-900">{{ $totalAnggotaPasifKeluar }}</p>
            </div>
        </div>

        <div wire:click="bukaDetailSimpanan" class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4 transition-all hover:shadow-md hover:border-green-500 cursor-pointer group relative">
            <div class="flex items-center justify-center size-14 rounded-full bg-green-100 text-green-600 shrink-0 group-hover:scale-105 transition-transform">
                <x-mary-icon name="o-wallet" class="size-7" />
            </div>
            <div>
                <p class="text-sm font-bold text-gray-500">{{ __('Total Simpanan Anggota') }}</p>
                <p class="text-2xl font-black text-gray-900">Rp {{ number_format($summaryTotalSimpanan, 0, ',', '.') }}</p>
            </div>
            <div class="absolute top-2 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                <x-mary-icon name="o-arrows-pointing-out" class="size-4 text-green-500" />
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs tracking-wider border-b border-gray-200">
                    <tr>
                        <th>{{ __('No. Anggota') }}</th>
                        <th>{{ __('Nama Anggota') }}</th>
                        <th>{{ __('NIK (KTP)') }}</th>
                        <th>{{ __('Kontak') }}</th>
                        <th>{{ __('Tanggal Bergabung') }}</th>
                        <th>{{ __('Simpanan Pokok') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium">
                    @forelse ($members as $member)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="font-bold text-gray-900">{{ $member->nomor_anggota }}</td>
                            <td class="font-bold text-gray-900 cursor-pointer hover:text-primary hover:underline" wire:click="openDetailModal({{ $member->id }})" title="Lihat detail anggota">{{ $member->nama }}</td>
                            <td class="font-mono text-gray-600">{{ $member->no_ktp }}</td>
                            <td class="text-gray-600">{{ $member->no_telepon }}</td>
                            <td>
                                <div class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($member->tanggal_bergabung)->translatedFormat('d F Y') }}</div>
                                <div class="text-[10px] text-gray-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($member->tanggal_bergabung)->diffForHumans() }}</div>
                            </td>
                            <td class="font-black text-green-600">
                                Rp {{ number_format($member->simpananSaldos->where('jenis_simpanan', 'pokok')->first()->saldo ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if($member->status === 'aktif')
                                    <span class="badge badge-success badge-sm font-bold text-white uppercase px-3">{{ $member->status }}</span>
                                @elseif($member->status === 'keluar')
                                    <span class="badge badge-error badge-sm font-bold text-white uppercase px-3">{{ $member->status }}</span>
                                @else
                                    <span class="badge badge-warning badge-sm font-bold text-white uppercase px-3">{{ $member->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="openEditModal({{ $member->id }})" class="btn btn-xs btn-outline btn-warning border-gray-300 shadow-sm" title="Edit Anggota">
                                        <x-mary-icon name="o-pencil-square" class="size-3" />
                                    </button>
                                    
                                    @if($member->status !== 'keluar')
                                        <button wire:click="openExitModal({{ $member->id }})" class="btn btn-xs btn-error text-white font-bold hover:bg-red-600 shadow-sm" title="Proses Keluar Anggota">
                                            <x-mary-icon name="o-arrow-right-on-rectangle" class="size-3" /> Keluar
                                        </button>
                                    @endif

                                    <button wire:click="openDeleteModal({{ $member->id }})" class="btn btn-xs btn-outline btn-error hover:bg-red-50 border-red-200 shadow-sm" title="Hapus Anggota">
                                        <x-mary-icon name="o-trash" class="size-3" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                                    <x-mary-icon name="o-users" class="size-16 mb-4 text-gray-300" />
                                    <p class="font-bold text-lg">{{ __('Tidak ada data yang ditemukan') }}</p>
                                    <p class="text-sm">{{ __('Coba ubah kata kunci pencarian atau tambah anggota baru.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($members->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                {{ $members->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL BARU: Rincian Total Simpanan Koperasi Keseluruhan --}}
    <x-mary-modal wire:model="detailSimpananModal" title="Rincian Total Simpanan" class="backdrop-blur-sm">
        <div class="bg-white rounded-lg overflow-hidden border border-gray-200 mt-2">
            <table class="table table-zebra w-full text-sm">
                <thead class="bg-gray-50 text-gray-800 font-bold border-b border-gray-200">
                    <tr>
                        <th>{{ __('Jenis Simpanan') }}</th>
                        <th class="text-right">{{ __('Total Saldo') }}</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium">
                    @php $grandTotalKoperasi = 0; @endphp
                    
                    @forelse($rincianSimpananKoperasi ?? [] as $rincian)
                        @php $grandTotalKoperasi += $rincian->total_saldo; @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="font-bold uppercase text-gray-700">
                                @if(strtolower($rincian->jenis_simpanan) === 'sukarela')
                                    {{ __('Simpanan Sukarela') }}
                                @elseif(strtolower($rincian->jenis_simpanan) === 'wajib')
                                    {{ __('Simpanan Wajib') }}
                                @elseif(strtolower($rincian->jenis_simpanan) === 'pokok')
                                    {{ __('Simpanan Pokok') }}
                                @else
                                    {{ $rincian->jenis_simpanan }}
                                @endif
                            </td>
                            <td class="text-right font-black text-green-600">
                                Rp {{ number_format($rincian->total_saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center text-gray-500 py-6">
                                {{ __('Belum ada data simpanan masuk.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 font-black">
                    <tr>
                        <td class="uppercase text-gray-800 font-bold">{{ __('Grand Total') }}</td>
                        <td class="text-right text-gray-900 text-base font-black">
                            Rp {{ number_format($grandTotalKoperasi, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <x-slot:actions>
            <button type="button" class="btn btn-sm btn-ghost bg-gray-100 hover:bg-gray-200 text-gray-800" wire:click="$set('detailSimpananModal', false)">
                {{ __('Tutup') }}
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-modal wire:model="addModal" title="Tambah Anggota Koperasi" class="backdrop-blur-sm">
        <form id="form-tambah-anggota" wire:submit="saveMember">
            <div class="space-y-4">
                <x-mary-input wire:model="nama" label="Nama Lengkap" placeholder="Masukkan nama" required />
                <x-mary-input wire:model="no_ktp" label="Nomor KTP" placeholder="Masukkan 16 digit NIK" required />
                <x-mary-input wire:model="telepon" label="Nomor Telepon" placeholder="Contoh: 08123..." required />
                <x-mary-textarea wire:model="alamat" label="Alamat Lengkap" placeholder="Masukkan alamat domisili" required rows="3"></x-mary-textarea>
                
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 mt-4">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Simpanan Pokok (Otomatis)</p>
                    <p class="text-xl font-black text-gray-900">Rp {{ number_format($simpananPokokDefault, 0, ',', '.') }}</p>
                </div>
            </div>
        </form>
        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('addModal', false)">Batal</button>
            <button type="submit" form="form-tambah-anggota" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="saveMember" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="saveMember" name="o-check" class="size-4" /> 
                Simpan Anggota
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-modal wire:model="editModal" title="Edit Data Anggota" class="backdrop-blur-sm">
        <form id="form-edit-anggota" wire:submit="updateMember">
            <div class="space-y-4">
                <x-mary-input wire:model="nama" label="Nama Lengkap" placeholder="Masukkan nama" required />
                <x-mary-input wire:model="no_ktp" label="Nomor KTP" placeholder="Masukkan 16 digit NIK" required />
                <x-mary-input wire:model="telepon" label="Nomor Telepon" placeholder="Contoh: 08123..." required />
                <x-mary-textarea wire:model="alamat" label="Alamat Lengkap" placeholder="Masukkan alamat domisili" required rows="3"></x-mary-textarea>
            </div>
        </form>
        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('editModal', false)">Batal</button>
            <button type="submit" form="form-edit-anggota" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="updateMember" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="updateMember" name="o-check" class="size-4" /> 
                Perbarui Data
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-modal wire:model="exitModal" class="backdrop-blur-sm" box-class="max-w-md">
        <div class="border-b border-gray-100 pb-3 mb-4 flex items-center gap-2 text-red-600">
             <x-mary-icon name="o-exclamation-circle" class="size-6" />
             <h3 class="font-bold text-lg text-gray-800">Proses Anggota Keluar</h3>
        </div>

        <div class="space-y-4 text-gray-800">
            <p class="text-sm font-medium text-gray-600">Berikut adalah rekapitulasi hak dan kewajiban anggota:</p>

            <div class="p-5 bg-white border border-gray-200 shadow-sm rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-bold text-gray-700 text-sm">Nama Anggota:</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $exitMemberName }}</span>
                </div>

                <div class="flex justify-between items-center mt-2">
                    <span class="font-bold text-green-600 text-sm">(+) Total Simpanan:</span>
                    <span class="font-bold text-green-600 text-sm">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</span>
                </div>
                
                <div class="pl-6 pr-1 mt-1.5 space-y-1.5">
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Pokok:</span>
                        <span>Rp {{ number_format($exitSimpananPokok, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Wajib:</span>
                        <span>Rp {{ number_format($exitSimpananWajib, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-500">
                        <span>Sukarela:</span>
                        <span>Rp {{ number_format($exitSimpananSukarela, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-5">
                    <span class="font-bold text-red-500 text-sm">(-) Sisa Pinjaman:</span>
                    <span class="font-bold text-red-500 text-sm">Rp {{ number_format($sisaPinjaman, 0, ',', '.') }}</span>
                </div>

                <hr class="my-4 border-gray-200">

                <div class="flex justify-between items-center">
                    <span class="font-bold text-gray-900 text-base">Dana Dikembalikan:</span>
                    <span class="font-black text-blue-600 text-lg">Rp {{ number_format($danaKembali, 0, ',', '.') }}</span>
                </div>
            </div>
            
            <div class="text-center mt-3 mb-1">
                <p class="text-[13px] text-red-500 italic font-medium max-w-[90%] mx-auto leading-relaxed">
                    *Tindakan ini akan menonaktifkan anggota dan otomatis memotong saldo Kas Koperasi.
                </p>
            </div>
        </div>

        <x-slot:actions>
            <button type="button" class="btn btn-ghost bg-white border border-gray-200 shadow-sm text-gray-600 hover:bg-gray-50" wire:click="$set('exitModal', false)">
                Batal
            </button>
            <button type="button" wire:click="processExit" wire:loading.attr="disabled" class="btn bg-[#EA580C] hover:bg-[#C2410C] text-white font-bold border-none shadow-md px-6">
                <span wire:loading wire:target="processExit" class="loading loading-spinner loading-xs"></span>
                <span wire:loading.remove wire:target="processExit">Proses Keluar</span>
            </button>
        </x-slot:actions>
    </x-mary-modal>

    <x-mary-modal wire:model="deleteModal" title="Konfirmasi Hapus" class="backdrop-blur-sm">
        <div class="flex flex-col items-center justify-center p-4 text-center">
            <div class="rounded-full bg-red-100 p-3 mb-4 text-red-600">
                <x-mary-icon name="o-trash" class="size-8" />
            </div>
            <h3 class="text-lg font-black text-gray-900 mb-1">Hapus Data Anggota?</h3>
            <p class="text-sm text-gray-500">Anda yakin ingin menghapus <strong>{{ $deleteName }}</strong>? Data akan dipindahkan ke arsip sistem secara aman.</p>
        </div>

        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('deleteModal', false)">Batal</button>
            <button type="button" wire:click="deleteMember" wire:loading.attr="disabled" class="btn btn-error bg-red-600 text-white font-bold border-none shadow-md">
                <span wire:loading wire:target="deleteMember" class="loading loading-spinner loading-xs"></span>
                <x-mary-icon wire:loading.remove wire:target="deleteMember" name="o-trash" class="size-4" /> 
                Ya, Hapus Data
            </button>
        </x-slot:actions>
    </x-mary-modal>

    {{-- ===================== MODAL DETAIL ANGGOTA ===================== --}}
    <x-mary-modal wire:model="detailModal" title="Detail Anggota Koperasi" class="backdrop-blur-sm" box-class="w-11/12 max-w-4xl">
        @if($detailAnggota)
            <div class="space-y-6">
                {{-- Header Info Anggota --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $detailAnggota->nomor_anggota }}</p>
                        <h2 class="text-2xl font-black text-gray-900">{{ $detailAnggota->nama }}</h2>
                        <p class="text-sm text-gray-500 font-medium mt-1">{{ $detailAnggota->no_ktp }} &bull; {{ $detailAnggota->no_telepon }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $detailAnggota->alamat }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        @if($detailAnggota->status === 'aktif')
                            <span class="badge badge-success font-bold text-white uppercase px-3">{{ $detailAnggota->status }}</span>
                        @elseif($detailAnggota->status === 'keluar')
                            <span class="badge badge-error font-bold text-white uppercase px-3">{{ $detailAnggota->status }}</span>
                        @else
                            <span class="badge badge-warning font-bold text-white uppercase px-3">{{ $detailAnggota->status }}</span>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">Bergabung {{ \Carbon\Carbon::parse($detailAnggota->tanggal_bergabung)->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                {{-- Ringkasan Saldo Simpanan --}}
                <div>
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-mary-icon name="o-wallet" class="size-4" /> Saldo Simpanan
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
                            <p class="text-xs font-bold text-gray-500 uppercase">Pokok</p>
                            <p class="text-xl font-black text-gray-900">Rp {{ number_format($detailSaldoSimpanan['pokok'], 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-white border border-gray-200 rounded-xl shadow-sm">
                            <p class="text-xs font-bold text-gray-500 uppercase">Wajib</p>
                            <p class="text-xl font-black text-gray-900">Rp {{ number_format($detailSaldoSimpanan['wajib'], 0, ',', '.') }}</p>
                        </div>
                        <div class="p-4 bg-green-50 border border-green-200 rounded-xl shadow-sm">
                            <p class="text-xs font-bold text-green-600 uppercase">Sukarela</p>
                            <p class="text-xl font-black text-green-700">Rp {{ number_format($detailSaldoSimpanan['sukarela'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Riwayat Transaksi Simpanan Terakhir --}}
                <div>
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-mary-icon name="o-arrows-right-left" class="size-4" /> Riwayat Simpanan Terakhir
                    </h3>
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <table class="table table-sm w-full">
                            <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-xs">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Tipe</th>
                                    <th class="text-right">Nominal</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                @forelse($detailRiwayatSimpanan as $rs)
                                    <tr>
                                        <td class="text-gray-500 text-xs">{{ \Carbon\Carbon::parse($rs->tanggal_transaksi)->format('d/m/Y H:i') }}</td>
                                        <td><span class="badge badge-ghost badge-sm uppercase font-bold">{{ $rs->jenis_simpanan }}</span></td>
                                        <td>
                                            @if($rs->tipe === 'setor')
                                                <span class="text-green-600 font-bold text-xs uppercase">Setor</span>
                                            @else
                                                <span class="text-red-600 font-bold text-xs uppercase">Tarik</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-black {{ $rs->tipe === 'setor' ? 'text-green-600' : 'text-red-600' }}">
                                            Rp {{ number_format($rs->jumlah, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-gray-400 text-sm">Belum ada riwayat simpanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Riwayat Pinjaman & Angsuran --}}
                <div>
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-mary-icon name="o-banknotes" class="size-4" /> Pinjaman & Angsuran
                    </h3>

                    @forelse($detailPinjamans as $pinjaman)
                        <div class="border border-gray-200 rounded-xl p-4 mb-3 bg-white shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                                <div>
                                    <span class="font-mono font-bold text-gray-700 text-sm">{{ $pinjaman->nomor_pinjaman }}</span>
                                    @if($pinjaman->status === 'lunas')
                                        <span class="badge badge-success badge-sm text-white font-bold uppercase ml-2">Lunas</span>
                                    @elseif($pinjaman->status === 'berjalan')
                                        <span class="badge badge-warning badge-sm font-bold uppercase ml-2">Berjalan</span>
                                    @else
                                        <span class="badge badge-ghost badge-sm font-bold uppercase ml-2">{{ ucfirst($pinjaman->status) }}</span>
                                    @endif
                                </div>
                                <div class="text-right text-sm">
                                    <span class="text-gray-500">Sisa: </span>
                                    <span class="font-black {{ $pinjaman->sisa_pinjaman > 0 ? 'text-red-600' : 'text-gray-400' }}">
                                        Rp {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 text-xs text-gray-500 font-medium mb-3">
                                <div>Pokok: <span class="font-bold text-gray-800">Rp {{ number_format($pinjaman->jumlah_pinjaman, 0, ',', '.') }}</span></div>
                                <div>Tenor: <span class="font-bold text-gray-800">{{ $pinjaman->tenor_bulan }} bulan</span></div>
                                <div>Angsuran/bln: <span class="font-bold text-gray-800">Rp {{ number_format($pinjaman->angsuran_per_bulan, 0, ',', '.') }}</span></div>
                            </div>

                            @if($pinjaman->angsurans->count() > 0)
                                <div class="border-t border-dashed border-gray-200 pt-2 mt-2">
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Riwayat Angsuran</p>
                                    <div class="space-y-1">
                                        @foreach($pinjaman->angsurans as $angsuran)
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-500">Angsuran ke-{{ $angsuran->angsuran_ke }} &bull; {{ \Carbon\Carbon::parse($angsuran->tanggal_bayar)->format('d/m/Y') }}</span>
                                                <span class="font-bold text-green-600">Rp {{ number_format($angsuran->jumlah_bayar, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Belum ada angsuran dibayar.</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <x-mary-icon name="o-document-text" class="size-12 mx-auto mb-2 text-gray-300" />
                            <p class="text-sm font-medium">Anggota ini belum pernah mengajukan pinjaman.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <x-slot:actions>
            <button type="button" class="btn btn-ghost" wire:click="$set('detailModal', false)">Tutup</button>
        </x-slot:actions>
    </x-mary-modal>
</div>
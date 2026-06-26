<div class="w-full pb-10">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">{{ __('Pengaturan Koperasi') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('Konfigurasi sistem simpan pinjam dan kas awal.') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 lg:p-8">
                <form wire:submit="simpanPengaturan" class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Setoran Simpanan Pokok (Rp)') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-semibold">Rp</span>
                            </div>
                            <input type="number" wire:model="nominal_simpanan_pokok" class="input input-bordered w-full pl-12 h-11 bg-gray-50 focus:bg-white text-gray-800 font-bold focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors" placeholder="0" required />
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <x-mary-icon name="o-information-circle" class="size-3.5 text-gray-400" />
                            {{ __('Wajib dibayar sekali saat anggota baru mendaftar.') }}
                        </p>
                        @error('nominal_simpanan_pokok') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Setoran Simpanan Wajib (Rp)') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-semibold">Rp</span>
                            </div>
                            <input type="number" wire:model="nominal_simpanan_wajib" class="input input-bordered w-full pl-12 h-11 bg-gray-50 focus:bg-white text-gray-800 font-bold focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors" placeholder="0" required />
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <x-mary-icon name="o-information-circle" class="size-3.5 text-gray-400" />
                            {{ __('Dibayar rutin oleh setiap anggota aktif.') }}
                        </p>
                        @error('nominal_simpanan_wajib') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Biaya Admin Pinjaman (Rp)') }}</label>
                        <div class="relative w-full md:w-1/2">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-semibold">Rp</span>
                            </div>
                            <input type="number" wire:model="biaya_admin_pinjaman" class="input input-bordered w-full pl-12 h-11 bg-gray-50 focus:bg-white text-gray-800 font-bold focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors" placeholder="0" required />
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <x-mary-icon name="o-information-circle" class="size-3.5 text-gray-400" />
                            {{ __('Potongan satu-kali per pengajuan pinjaman (bukan bunga). Pinjaman koperasi ini tanpa bunga.') }}
                        </p>
                        @error('biaya_admin_pinjaman') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <hr class="border-gray-200">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Saldo Awal Kas Koperasi (Rp)') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-semibold">Rp</span>
                            </div>
                            <input type="number" wire:model="saldo_kas_awal" class="input input-bordered w-full pl-12 h-11 bg-gray-50 focus:bg-white text-gray-800 font-bold focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors" placeholder="0" required />
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <x-mary-icon name="o-information-circle" class="size-3.5 text-gray-400" />
                            {{ __('Modal awal sistem sebelum ada transaksi.') }}
                        </p>
                        @error('saldo_kas_awal') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">{{ __('Tanggal Mulai Saldo Awal') }}</label>
                        <input type="date" wire:model="tanggal_saldo_awal" class="input input-bordered w-full md:w-1/2 h-11 bg-gray-50 focus:bg-white text-gray-800 font-bold focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors" />
                        <p class="text-xs text-gray-500 mt-2 flex items-center gap-1.5">
                            <x-mary-icon name="o-information-circle" class="size-3.5 text-gray-400" />
                            {{ __('Titik mulai perhitungan Total Kas & Neraca.') }}
                        </p>
                        @error('tanggal_saldo_awal') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2">
                        <button type="submit" wire:loading.attr="disabled" class="btn h-11 bg-blue-600 hover:bg-blue-700 text-white font-bold border-none shadow-sm rounded-lg px-6 flex items-center gap-2">
                            <span wire:loading wire:target="simpanPengaturan" class="loading loading-spinner loading-sm"></span>
                            <x-mary-icon wire:loading.remove wire:target="simpanPengaturan" name="o-check-circle" class="size-5" />
                            {{ __('Simpan Pengaturan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6 sticky top-6">
                <h3 class="text-sm font-bold text-gray-800 mb-5 border-b border-gray-100 pb-3">{{ __('Status Sistem') }}</h3>
                
                <ul class="space-y-4">
                    <li class="flex items-center gap-3">
                        <x-mary-icon name="c-check-circle" class="size-5 text-green-500 shrink-0" />
                        <span class="text-sm font-semibold text-gray-700 flex-1">{{ __('Modul Simpanan') }}</span>
                        <span class="badge badge-sm badge-ghost font-bold text-xs uppercase">{{ __('Aktif') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-mary-icon name="c-check-circle" class="size-5 text-green-500 shrink-0" />
                        <span class="text-sm font-semibold text-gray-700 flex-1">{{ __('Modul Pinjaman') }}</span>
                        <span class="badge badge-sm badge-ghost font-bold text-xs uppercase">{{ __('Aktif') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-mary-icon name="c-check-circle" class="size-5 text-green-500 shrink-0" />
                        <span class="text-sm font-semibold text-gray-700 flex-1">{{ __('Sistem Pinjaman') }}</span>
                        <span class="badge badge-sm badge-ghost font-bold text-xs uppercase">{{ __('Tanpa Bunga') }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <x-mary-icon name="c-check-circle" class="size-5 text-green-500 shrink-0" />
                        <span class="text-sm font-semibold text-gray-700 flex-1">{{ __('Auto-Generate ID') }}</span>
                        <span class="badge badge-sm badge-ghost font-bold text-xs uppercase">{{ __('Ya') }}</span>
                    </li>
                </ul>

                <div class="mt-8 bg-blue-50 border border-blue-100 rounded-lg p-4 flex gap-3 items-start">
                    <x-mary-icon name="o-chat-bubble-left-ellipsis" class="size-5 text-blue-500 mt-0.5 shrink-0" />
                    <div>
                        <h4 class="text-sm font-bold text-blue-800 mb-1">{{ __('Butuh Bantuan?') }}</h4>
                        <p class="text-xs font-medium text-blue-600/80 leading-relaxed">
                            {{ __('Silakan hubungi tim developer jika Anda ingin merubah struktur dasar dari koperasi atau menambah fitur baru.') }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
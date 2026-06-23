<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiPinjaman as KoperasiPinjamanModel;
use App\Models\KoperasiPinjamanAngsuran;
use App\Models\KoperasiKasTransaksi;
use App\Models\KoperasiSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class Pinjaman extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $statusFilter = '';

    // ===== Form Modal Pengajuan/Pencairan Pinjaman =====
    public bool $pinjamanModal = false;
    public $selectedAnggotaId = null;
    public $jumlah_pinjaman = '';
    public $tenor_bulan = '';
    public $biaya_admin = 0;
    public $tanggal_pengajuan;
    public $keterangan_pinjaman = '';

    // Estimasi angsuran (ditampilkan live, dihitung dari jumlah & tenor)
    public $estimasiAngsuran = 0;

    // ===== Form Modal Pembayaran Angsuran =====
    public bool $angsuranModal = false;
    public $selectedPinjamanId = null;
    public $pinjamanAktif = null; // data pinjaman terpilih (untuk info di modal)
    public $angsuranKe = null;
    public $jumlah_bayar = '';
    public $tanggal_bayar;
    public $keterangan_angsuran = '';

    // ===== State Kuitansi =====
    public bool $receiptModal = false;
    public $receiptType = null; // 'pinjaman' | 'angsuran'
    public $receiptData = null;

    public function mount()
    {
        $this->tanggal_pengajuan = now()->format('Y-m-d');
        $this->tanggal_bayar = now()->format('Y-m-d');
        $this->biaya_admin = (float) (KoperasiSetting::current()->biaya_admin_pinjaman ?? 0);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ===================== PENGAJUAN PINJAMAN =====================

    public function openPinjamanModal()
    {
        $this->resetValidation();
        $this->reset(['selectedAnggotaId', 'jumlah_pinjaman', 'tenor_bulan', 'keterangan_pinjaman']);
        $this->tanggal_pengajuan = now()->format('Y-m-d');
        $this->biaya_admin = (float) (KoperasiSetting::current()->biaya_admin_pinjaman ?? 0);
        $this->estimasiAngsuran = 0;
        $this->pinjamanModal = true;
    }

    // Hitung ulang estimasi angsuran tiap kali jumlah atau tenor berubah
    public function updatedJumlahPinjaman($val)
    {
        $this->hitungEstimasiAngsuran();
    }

    public function updatedTenorBulan($val)
    {
        $this->hitungEstimasiAngsuran();
    }

    private function hitungEstimasiAngsuran()
    {
        if (is_numeric($this->jumlah_pinjaman) && is_numeric($this->tenor_bulan) && $this->tenor_bulan > 0) {
            $this->estimasiAngsuran = KoperasiPinjamanModel::hitungAngsuranPerBulan(
                (float) $this->jumlah_pinjaman,
                (int) $this->tenor_bulan
            );
        } else {
            $this->estimasiAngsuran = 0;
        }
    }

    private function generateNomorPinjaman()
    {
        $prefix = 'PINJ-' . now()->format('Ymd') . '-';
        $last = KoperasiPinjamanModel::where('nomor_pinjaman', 'like', $prefix . '%')
            ->orderBy('nomor_pinjaman', 'desc')
            ->first();

        $nextNum = $last ? ((int) substr($last->nomor_pinjaman, -4)) + 1 : 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    public function savePinjaman()
    {
        $this->validate([
            'selectedAnggotaId' => 'required|exists:koperasi_anggota,id',
            'jumlah_pinjaman' => 'required|numeric|min:1000',
            'tenor_bulan' => 'required|integer|min:1|max:60',
            'biaya_admin' => 'nullable|numeric|min:0',
            'tanggal_pengajuan' => 'required|date',
            'keterangan_pinjaman' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {
            $angsuranPerBulan = KoperasiPinjamanModel::hitungAngsuranPerBulan(
                (float) $this->jumlah_pinjaman,
                (int) $this->tenor_bulan
            );

            $nomorPinjaman = $this->generateNomorPinjaman();

            $pinjaman = KoperasiPinjamanModel::create([
                'nomor_pinjaman' => $nomorPinjaman,
                'koperasi_anggota_id' => $this->selectedAnggotaId,
                'jumlah_pinjaman' => $this->jumlah_pinjaman,
                'tenor_bulan' => $this->tenor_bulan,
                'angsuran_per_bulan' => $angsuranPerBulan,
                'biaya_admin' => $this->biaya_admin ?: 0,
                'tanggal_pengajuan' => $this->tanggal_pengajuan,
                'tanggal_pencairan' => $this->tanggal_pengajuan,
                'status' => 'berjalan',
                'sisa_pinjaman' => $this->jumlah_pinjaman,
                'keterangan' => $this->keterangan_pinjaman,
                'user_id' => Auth::id() ?? 1,
            ]);

            // Catat di Buku Kas Utama Koperasi: pencairan pinjaman = kas keluar
            KoperasiKasTransaksi::create([
                'nomor_referensi' => $nomorPinjaman,
                'sumber' => 'pinjaman',
                'tipe' => 'keluar',
                'jumlah' => $this->jumlah_pinjaman,
                'keterangan' => 'Pencairan Pinjaman a.n ' . KoperasiAnggota::find($this->selectedAnggotaId)->nama,
                'tanggal_transaksi' => $this->tanggal_pengajuan,
                'user_id' => Auth::id() ?? 1,
            ]);

            $this->receiptType = 'pinjaman';
            $this->receiptData = $pinjaman->load(['anggota', 'user']);
        });

        $this->pinjamanModal = false;
        $this->success('Pinjaman berhasil dicairkan.');
        $this->receiptModal = true;
    }

    // ===================== PEMBAYARAN ANGSURAN =====================

    public function openAngsuranModal()
    {
        $this->resetValidation();
        $this->reset(['selectedPinjamanId', 'jumlah_bayar', 'keterangan_angsuran']);
        $this->pinjamanAktif = null;
        $this->angsuranKe = null;
        $this->tanggal_bayar = now()->format('Y-m-d');
        $this->angsuranModal = true;
    }

    // Dipanggil otomatis saat memilih pinjaman di dropdown/choices
    public function updatedSelectedPinjamanId($val)
    {
        if ($val) {
            $pinjaman = KoperasiPinjamanModel::with('anggota')->find($val);
            $this->pinjamanAktif = $pinjaman;
            $this->angsuranKe = $pinjaman ? $pinjaman->angsuranKeBerikutnya() : null;
            $this->jumlah_bayar = $pinjaman ? (float) $pinjaman->angsuran_per_bulan : '';
        } else {
            $this->pinjamanAktif = null;
            $this->angsuranKe = null;
            $this->jumlah_bayar = '';
        }
    }

    public function saveAngsuran()
    {
        $this->validate([
            'selectedPinjamanId' => 'required|exists:koperasi_pinjamans,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'keterangan_angsuran' => 'nullable|string|max:255',
        ]);

        $pinjaman = KoperasiPinjamanModel::find($this->selectedPinjamanId);

        if (!$pinjaman || $pinjaman->status !== 'berjalan') {
            $this->addError('selectedPinjamanId', 'Pinjaman ini sudah tidak berstatus berjalan.');
            return;
        }

        if ($this->jumlah_bayar > $pinjaman->sisa_pinjaman) {
            $this->addError('jumlah_bayar', 'Jumlah bayar melebihi sisa pinjaman (Rp ' . number_format($pinjaman->sisa_pinjaman, 0, ',', '.') . ').');
            return;
        }

        DB::transaction(function () use ($pinjaman) {
            $angsuranKe = $pinjaman->angsuranKeBerikutnya();
            $sisaSetelah = $pinjaman->sisa_pinjaman - $this->jumlah_bayar;

            $angsuran = KoperasiPinjamanAngsuran::create([
                'koperasi_pinjaman_id' => $pinjaman->id,
                'angsuran_ke' => $angsuranKe,
                'jumlah_bayar' => $this->jumlah_bayar,
                'tanggal_bayar' => $this->tanggal_bayar,
                'sisa_pinjaman_setelah' => $sisaSetelah,
                'keterangan' => $this->keterangan_angsuran,
                'user_id' => Auth::id() ?? 1,
            ]);

            // Update sisa pinjaman & status (auto Lunas jika sisa = 0)
            $pinjaman->update([
                'sisa_pinjaman' => $sisaSetelah,
                'status' => $sisaSetelah <= 0 ? 'lunas' : 'berjalan',
            ]);

            // Catat di Buku Kas Utama Koperasi: pembayaran angsuran = kas masuk
            KoperasiKasTransaksi::create([
                'nomor_referensi' => $pinjaman->nomor_pinjaman,
                'sumber' => 'angsuran',
                'tipe' => 'masuk',
                'jumlah' => $this->jumlah_bayar,
                'keterangan' => 'Pembayaran Angsuran ke-' . $angsuranKe . ' a.n ' . $pinjaman->anggota->nama,
                'tanggal_transaksi' => $this->tanggal_bayar,
                'user_id' => Auth::id() ?? 1,
            ]);

            $this->receiptType = 'angsuran';
            $this->receiptData = $angsuran->load(['pinjaman.anggota', 'user']);
        });

        $this->angsuranModal = false;
        $this->success('Pembayaran angsuran berhasil dicatat.');
        $this->receiptModal = true;
    }

    public function render()
    {
        $pinjamans = KoperasiPinjamanModel::with(['anggota', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('anggota', function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_anggota', 'like', '%' . $this->search . '%');
                })->orWhere('nomor_pinjaman', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Anggota aktif untuk dropdown pengajuan pinjaman
        $anggotaAktif = KoperasiAnggota::where('status', 'aktif')
            ->select('id', 'nomor_anggota', 'nama')
            ->get()
            ->map(function ($item) {
                $item->nama_label = $item->nomor_anggota . ' - ' . $item->nama;
                return $item;
            });

        // Pinjaman berjalan untuk dropdown pembayaran angsuran
        $pinjamanBerjalan = KoperasiPinjamanModel::with('anggota')
            ->where('status', 'berjalan')
            ->get()
            ->map(function ($item) {
                $item->pinjaman_label = $item->nomor_pinjaman . ' - ' . $item->anggota->nama
                    . ' (Sisa Rp ' . number_format($item->sisa_pinjaman, 0, ',', '.') . ')';
                return $item;
            });

        return view('pages.admin.koperasi.pinjaman', [
            'pinjamans' => $pinjamans,
            'anggotaAktif' => $anggotaAktif,
            'pinjamanBerjalan' => $pinjamanBerjalan,
        ])->layout('layouts.app', ['title' => __('Pinjaman & Angsuran')]);
    }
}
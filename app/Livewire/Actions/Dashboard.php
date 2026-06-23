<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use App\Models\KoperasiKasTransaksi;
use App\Models\KoperasiSimpananSaldo;
use App\Models\KoperasiPinjaman;
use App\Models\KoperasiAnggota;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Filter Data
    public $periode = '6_bulan';
    public $tanggal_mulai;
    public $tanggal_akhir;

    // Summary Data
    public $totalKas = 0;
    public $totalSimpanan = 0;
    public $sisaPinjaman = 0;

    // Anggota Data
    public $totalAnggota = 0;
    public $anggotaAktif = 0;
    public $anggotaPasif = 0;
    public $anggotaKeluar = 0;

    // Chart Data
    public $chartBulan = [];
    public $chartPemasukan = [];
    public $chartPengeluaran = [];
    public $komposisiSimpanan = [];

    public function mount()
    {
        $this->setDefaultDates();
        $this->loadData();
    }

    public function setDefaultDates()
    {
        $this->tanggal_akhir = now()->format('Y-m-d');
        if ($this->periode == '6_bulan') {
            $this->tanggal_mulai = now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        } elseif ($this->periode == '1_tahun') {
            $this->tanggal_mulai = now()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } elseif ($this->periode == 'bulan_ini') {
            $this->tanggal_mulai = now()->startOfMonth()->format('Y-m-d');
        }
    }

    public function updatedPeriode()
    {
        if ($this->periode != 'custom') {
            $this->setDefaultDates();
            $this->loadData();
        }
    }

    public function terapkanFilter()
    {
        $this->periode = 'custom';
        $this->loadData();
    }

    public function resetFilter()
    {
        $this->periode = '6_bulan';
        $this->setDefaultDates();
        $this->loadData();
    }

    public function loadData()
    {
        $startDate = Carbon::parse($this->tanggal_mulai)->startOfDay();
        $endDate = Carbon::parse($this->tanggal_akhir)->endOfDay();

        // 1. Total Kas (Pemasukan - Pengeluaran) sampai tanggal akhir
        $kasMasuk = KoperasiKasTransaksi::where('tipe', 'masuk')->where('tanggal_transaksi', '<=', $endDate)->sum('jumlah');
        $kasKeluar = KoperasiKasTransaksi::where('tipe', 'keluar')->where('tanggal_transaksi', '<=', $endDate)->sum('jumlah');
        $this->totalKas = $kasMasuk - $kasKeluar;

        // 2. Total Simpanan
        $this->totalSimpanan = KoperasiSimpananSaldo::whereHas('anggota', function ($q) {
            $q->where('status', '!=', 'keluar');
        })->sum('saldo');

        // 3. Sisa Pinjaman (Uang di Peminjam)
        $this->sisaPinjaman = KoperasiPinjaman::where('status', 'berjalan')->sum('sisa_pinjaman');

        // 4. Data Anggota
        $this->totalAnggota = KoperasiAnggota::count();
        $this->anggotaAktif = KoperasiAnggota::where('status', 'aktif')->count();
        $this->anggotaPasif = KoperasiAnggota::where('status', 'pasif')->count();
        $this->anggotaKeluar = KoperasiAnggota::where('status', 'keluar')->count();

        // 5. Data Chart: Komposisi Simpanan
        $simpananGroup = KoperasiSimpananSaldo::whereHas('anggota', function ($q) {
                $q->where('status', '!=', 'keluar');
            })
            ->selectRaw('jenis_simpanan, SUM(saldo) as total')
            ->groupBy('jenis_simpanan')
            ->get()->keyBy('jenis_simpanan');

        $this->komposisiSimpanan = [
            'pokok' => (float) ($simpananGroup['pokok']->total ?? 0),
            'wajib' => (float) ($simpananGroup['wajib']->total ?? 0),
            'sukarela' => (float) ($simpananGroup['sukarela']->total ?? 0),
        ];

        // 6. Data Chart: Arus Kas Trend (Group by Month)
        //
        // PENTING: pengelompokan di query memakai kunci numerik "%Y-%m" (mis. "2026-05"),
        // BUKAN nama bulan ("%b"). DATE_FORMAT(..., "%b") di MySQL selalu menghasilkan
        // singkatan bulan Bahasa Inggris (May, Aug, Oct, Dec) terlepas dari locale aplikasi,
        // sementara label tampilan dibuat dengan translatedFormat() yang memakai Bahasa
        // Indonesia (Mei, Agu, Okt, Des). Kalau dua hal ini dipakai sebagai key yang sama,
        // baris data tidak pernah cocok dengan label bulan yang sudah digenerate, dan
        // Chart.js akan memasangkan nilai ke posisi bulan yang salah.
        $kasData = KoperasiKasTransaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%Y-%m") as bulan_key, tipe, SUM(jumlah) as total')
            ->groupBy('bulan_key', 'tipe')
            ->get();

        $bulanLabels = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        // Generate rentang bulan kosong (kunci numerik) agar grafik tidak terpotong,
        // sekaligus menyiapkan label tampilan Bahasa Indonesia secara terpisah.
        $period = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->startOfMonth());
        foreach ($period as $dt) {
            $key = $dt->format('Y-m');
            $bulanLabels[$key] = $dt->translatedFormat('M y');
            $pemasukanData[$key] = 0;
            $pengeluaranData[$key] = 0;
        }

        foreach ($kasData as $data) {
            // Jaga-jaga ada transaksi di luar rentang bulan yang digenerate (mis. salah input tanggal)
            if (! array_key_exists($data->bulan_key, $pemasukanData)) {
                continue;
            }

            if ($data->tipe === 'masuk') {
                $pemasukanData[$data->bulan_key] = (float) $data->total;
            } else {
                $pengeluaranData[$data->bulan_key] = (float) $data->total;
            }
        }

        $this->chartBulan = array_values($bulanLabels);
        $this->chartPemasukan = array_values($pemasukanData);
        $this->chartPengeluaran = array_values($pengeluaranData);
    }

    public function render()
    {
        return view('pages.admin.koperasi.dashboard')->layout('layouts.app', ['title' => 'Dashboard Koperasi']);
    }
}
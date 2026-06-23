<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use App\Models\KoperasiKasTransaksi;
use App\Models\KoperasiSimpananSaldo;
use App\Models\KoperasiPinjaman;
use App\Models\KoperasiAnggota;
use Carbon\Carbon;
use Mary\Traits\Toast;

class Dashboard extends Component
{
    use Toast;

    public $periode = '6_bulan';
    public $tanggal_mulai;
    public $tanggal_akhir;

    public $totalKas = 0;
    public $totalSimpanan = 0;
    public $sisaPinjaman = 0;

    public $totalAnggota = 0;
    public $anggotaAktif = 0;
    public $anggotaPasif = 0;
    public $anggotaKeluar = 0;

    public $chartBulan = [];
    public $chartPemasukan = [];
    public $chartPengeluaran = [];
    public $komposisiSimpananArray = [0, 0, 0];

    public function mount()
    {
        $this->setDefaultDates();
        $this->loadData();
    }

    public function setDefaultDates()
    {
        $hariIni = now(); // satu instance Carbon, dipakai konsisten

        $this->tanggal_akhir = $hariIni->copy()->format('Y-m-d');

        if ($this->periode === '6_bulan') {
            $this->tanggal_mulai = $hariIni->copy()->subMonths(5)->startOfMonth()->format('Y-m-d');
        } elseif ($this->periode === '1_tahun') {
            // 11 Bulan ke belakang + Bulan ini = 12 Bulan (1 Tahun)
            $this->tanggal_mulai = $hariIni->copy()->subMonths(11)->startOfMonth()->format('Y-m-d');
        } elseif ($this->periode === 'bulan_ini') {
            $this->tanggal_mulai = $hariIni->copy()->startOfMonth()->format('Y-m-d');
        }
        // Untuk 'custom': tanggal_mulai/tanggal_akhir TIDAK disentuh di sini,
        // karena nilainya berasal dari input yang diisi user sendiri.
    }

    // Dipanggil oleh wire:change di Blade setiap dropdown periode berubah.
    // Untuk preset (bukan custom), langsung hitung ulang & muat data — TIDAK
    // butuh tombol "Terapkan" sama sekali.
    public function gantiPeriode()
    {
        if ($this->periode !== 'custom') {
            $this->setDefaultDates();
            $this->loadData();
            $this->success('Memuat data: ' . str_replace('_', ' ', $this->periode));
        }
    }

    // Khusus dipakai saat user benar-benar memilih rentang tanggal manual
    // (periode = 'custom'). TIDAK memaksa overwrite periode preset yang
    // sedang aktif — supaya tidak menimpa tanggal yang baru saja dihitung
    // oleh gantiPeriode() dengan tanggal lama.
    public function terapkanFilterKustom()
    {
        if (!$this->tanggal_mulai || !$this->tanggal_akhir) {
            $this->warning('Pilih rentang tanggal dari dan sampai terlebih dahulu.');
            return;
        }

        if ($this->tanggal_mulai > $this->tanggal_akhir) {
            $this->error('Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
            return;
        }

        // Hanya set ke 'custom' jika memang user mengedit tanggal secara manual
        // (dropdown periode-nya juga "custom"). Kalau dropdown masih di preset
        // (mis. '1_tahun') tapi tombol Terapkan tetap diklik, JANGAN ubah periode
        // — cukup pakai ulang tanggal preset yang sudah benar.
        if ($this->periode !== 'custom') {
            $this->setDefaultDates();
        }

        $this->loadData();
        $this->success('Filter berhasil diterapkan.');
    }

    public function resetFilter()
    {
        $this->periode = '6_bulan';
        $this->setDefaultDates();
        $this->loadData();
        $this->info('Dikembalikan ke 6 Bulan Terakhir.');
    }

    public function loadData()
    {
        $mulai = $this->tanggal_mulai ?: now()->subMonths(5)->startOfMonth()->format('Y-m-d');
        $akhir = $this->tanggal_akhir ?: now()->format('Y-m-d');

        $startDate = Carbon::parse($mulai)->startOfDay();
        $endDate = Carbon::parse($akhir)->endOfDay();

        $kasMasuk = KoperasiKasTransaksi::where('tipe', 'masuk')->where('tanggal_transaksi', '<=', $endDate)->sum('jumlah');
        $kasKeluar = KoperasiKasTransaksi::where('tipe', 'keluar')->where('tanggal_transaksi', '<=', $endDate)->sum('jumlah');
        $this->totalKas = $kasMasuk - $kasKeluar;

        $this->totalSimpanan = KoperasiSimpananSaldo::whereHas('anggota', function ($q) {
            $q->where('status', '!=', 'keluar');
        })->sum('saldo');

        $this->sisaPinjaman = KoperasiPinjaman::where('status', 'berjalan')->sum('sisa_pinjaman');

        $this->totalAnggota = KoperasiAnggota::count();
        $this->anggotaAktif = KoperasiAnggota::where('status', 'aktif')->count();
        $this->anggotaPasif = KoperasiAnggota::where('status', 'pasif')->count();
        $this->anggotaKeluar = KoperasiAnggota::where('status', 'keluar')->count();

        $simpananGroup = KoperasiSimpananSaldo::whereHas('anggota', function ($q) {
                $q->where('status', '!=', 'keluar');
            })
            ->selectRaw('jenis_simpanan, SUM(saldo) as total')
            ->groupBy('jenis_simpanan')
            ->get()->keyBy('jenis_simpanan');

        $this->komposisiSimpananArray = [
            (float) ($simpananGroup['pokok']->total ?? 0),
            (float) ($simpananGroup['wajib']->total ?? 0),
            (float) ($simpananGroup['sukarela']->total ?? 0),
        ];

        $kasData = KoperasiKasTransaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->selectRaw('DATE_FORMAT(tanggal_transaksi, "%Y-%m") as bulan_tahun, tipe, SUM(jumlah) as total')
            ->groupBy('bulan_tahun', 'tipe')
            ->get();

        $pemasukanData = [];
        $pengeluaranData = [];
        $bulanLabels = [];

        $period = \Carbon\CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->startOfMonth());
        foreach ($period as $dt) {
            $key = $dt->format('Y-m');
            $bulanLabels[$key] = $dt->translatedFormat('M y');
            $pemasukanData[$key] = 0;
            $pengeluaranData[$key] = 0;
        }

        foreach ($kasData as $data) {
            $key = $data->bulan_tahun;
            if (isset($pemasukanData[$key])) {
                if ($data->tipe === 'masuk') {
                    $pemasukanData[$key] += (float) $data->total;
                } else {
                    $pengeluaranData[$key] += (float) $data->total;
                }
            }
        }

        $this->chartBulan = array_values($bulanLabels);
        $this->chartPemasukan = array_values($pemasukanData);
        $this->chartPengeluaran = array_values($pengeluaranData);

        $this->dispatch('update-charts',
            labels: $this->chartBulan,
            masuk: $this->chartPemasukan,
            keluar: $this->chartPengeluaran,
            simpanan: $this->komposisiSimpananArray
        );
    }

    public function render()
    {
        return view('pages.admin.koperasi.dashboard')->layout('layouts.app', ['title' => 'Dashboard Koperasi']);
    }
}
<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KoperasiKasTransaksi;
use Carbon\Carbon;
use Mary\Traits\Toast;

class Laporan extends Component
{
    use WithPagination, Toast;

    // Properti Filter
    public $periode = 'bulan_ini';
    public $tanggal_mulai;
    public $tanggal_akhir;
    public $tipe_filter = ''; 

    // Properti Ringkasan Kartu
    public $totalTransaksi = 0;
    public $totalPemasukan = 0;
    public $totalPengeluaran = 0;
    public $saldoAkhir = 0;

    public function mount()
    {
        $this->setDefaultDates();
    }

    public function setDefaultDates()
    {
        $this->tanggal_akhir = now()->format('Y-m-d');
        
        if ($this->periode == '7_hari') {
            $this->tanggal_mulai = now()->subDays(6)->format('Y-m-d');
        } elseif ($this->periode == 'bulan_ini') {
            $this->tanggal_mulai = now()->startOfMonth()->format('Y-m-d');
        } elseif ($this->periode == 'tahun_ini') {
            $this->tanggal_mulai = now()->startOfYear()->format('Y-m-d');
        } else {
            $this->tanggal_mulai = now()->startOfMonth()->format('Y-m-d');
        }
    }

    // Dipicu otomatis setiap kali Dropdown Periode diubah
    public function updatedPeriode($value)
    {
        if ($value !== 'custom') {
            $this->setDefaultDates();
        }
        $this->resetPage(); // Kembali ke halaman 1 saat filter diubah
    }

    // Tombol Filter Ditekan
    public function terapkanFilter()
    {
        // Cegah error: Validasi agar tanggal tidak boleh kosong dan logis
        $this->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ], [
            'tanggal_akhir.after_or_equal' => 'Tanggal Akhir tidak boleh lebih kecil dari Tanggal Mulai.',
            'tanggal_mulai.required' => 'Tanggal Mulai harus diisi.',
            'tanggal_akhir.required' => 'Tanggal Akhir harus diisi.',
        ]);

        $this->periode = 'custom';
        $this->resetPage();
        
        // Notifikasi popup hijau muncul saat berhasil
        $this->success('Filter tanggal berhasil diterapkan.');
    }

    // Tombol Reset Ditekan
    public function resetFilter()
    {
        $this->periode = 'bulan_ini';
        $this->tipe_filter = '';
        $this->setDefaultDates();
        $this->resetPage();
        
        // Notifikasi popup biru muncul
        $this->info('Filter telah dikembalikan ke Bulan Ini.');
    }

    // Tombol Ekspor PDF
    public function eksporPDF()
    {
        // Fitur placeholder mencegah tombol ini error saat ditekan
        $this->warning('Fitur Ekspor PDF sedang dalam tahap pengembangan.');
    }

    public function render()
    {
        // Keamanan tambahan: Jika variabel kosong (misal akibat inspect element), beri nilai default
        $mulai = $this->tanggal_mulai ?: now()->startOfMonth()->format('Y-m-d');
        $akhir = $this->tanggal_akhir ?: now()->format('Y-m-d');

        $startDate = Carbon::parse($mulai)->startOfDay();
        $endDate = Carbon::parse($akhir)->endOfDay();

        // 1. Kalkulasi Data Kartu Ringkasan
        $summaryQuery = KoperasiKasTransaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->when($this->tipe_filter, function($q) {
                $q->where('tipe', $this->tipe_filter); // Filter otomatis pemasukan/pengeluaran
            });
        
        $this->totalTransaksi = $summaryQuery->count();
        $this->totalPemasukan = (clone $summaryQuery)->where('tipe', 'masuk')->sum('jumlah');
        $this->totalPengeluaran = (clone $summaryQuery)->where('tipe', 'keluar')->sum('jumlah');
        $this->saldoAkhir = $this->totalPemasukan - $this->totalPengeluaran;

        // 2. Ambil Data untuk Tabel
        $transactions = KoperasiKasTransaksi::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->when($this->tipe_filter, function($q) {
                $q->where('tipe', $this->tipe_filter);
            })
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(15);

        return view('pages.admin.koperasi.laporan', [
            'transactions' => $transactions
        ])->layout('layouts.app', ['title' => __('Laporan Keuangan')]);
    }
}
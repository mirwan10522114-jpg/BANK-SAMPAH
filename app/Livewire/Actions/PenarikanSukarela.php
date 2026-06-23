<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiPenarikanSukarela;
use App\Models\KoperasiSimpananSaldo;
use App\Models\KoperasiSimpananTransaksi;
use App\Models\KoperasiKasTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class PenarikanSukarela extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $statusFilter = '';

    // Form Pengajuan
    public bool $addModal = false;
    public $selectedAnggotaId;
    public $jumlah;
    public $alasan;
    public $saldoTersedia = 0;

    // Modal Proses
    public bool $processModal = false;
    public $processData;
    public $nama_pengurus = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedAnggotaId($val)
    {
        if ($val) {
            $saldo = KoperasiSimpananSaldo::where('koperasi_anggota_id', $val)
                ->where('jenis_simpanan', 'sukarela')
                ->first();
            $this->saldoTersedia = $saldo ? (float) $saldo->saldo : 0;
        } else {
            $this->saldoTersedia = 0;
        }
    }

    public function openAddModal()
    {
        $this->resetValidation();
        $this->reset(['selectedAnggotaId', 'jumlah', 'alasan', 'saldoTersedia']);
        $this->addModal = true;
    }

    public function simpanPengajuan()
    {
        $this->validate([
            'selectedAnggotaId' => 'required|exists:koperasi_anggota,id',
            'jumlah' => 'required|numeric|min:1000|max:' . $this->saldoTersedia,
            'alasan' => 'required|string',
        ], [
            'jumlah.max' => 'Jumlah penarikan tidak boleh melebihi Saldo Sukarela yang tersedia.'
        ]);

        KoperasiPenarikanSukarela::create([
            'nomor_pengajuan' => 'TARIK-' . now()->format('Ymd') . '-' . rand(1000, 9999),
            'koperasi_anggota_id' => $this->selectedAnggotaId,
            'jumlah' => $this->jumlah,
            'alasan' => $this->alasan,
            'status' => 'menunggu',
            'tanggal_pengajuan' => now(),
        ]);

        $this->addModal = false;
        $this->success('Pengajuan penarikan berhasil dicatat dan menunggu persetujuan.');
    }

    public function openProcessModal($id)
    {
        $this->resetValidation();
        $this->nama_pengurus = '';
        $this->processData = KoperasiPenarikanSukarela::with('anggota')->findOrFail($id);
        $this->processModal = true;
    }

    public function setujuiPengajuan()
    {
        if ($this->processData->status !== 'menunggu') {
            $this->error('Pengajuan ini sudah pernah diproses sebelumnya.');
            $this->processModal = false;
            return;
        }

        $this->validate(
            ['nama_pengurus' => 'required|string|max:255'],
            ['nama_pengurus.required' => 'Nama pengurus wajib diisi sebelum memberikan persetujuan!']
        );

        $this->processData->update([
            'status' => 'disetujui',
            'tanggal_persetujuan' => now(),
            'nama_pengurus' => $this->nama_pengurus,
        ]);

        $this->processModal = false;
        $this->success('Pengajuan berhasil DISETUJUI. Silakan proses pencairan fisik.');
    }

    public function tolakPengajuan()
    {
        if ($this->processData->status !== 'menunggu') {
            $this->error('Pengajuan ini sudah pernah diproses sebelumnya.');
            $this->processModal = false;
            return;
        }

        $this->validate(
            ['nama_pengurus' => 'required|string|max:255'],
            ['nama_pengurus.required' => 'Nama pengurus wajib diisi agar tercatat siapa yang menolak pengajuan ini!']
        );

        $this->processData->update([
            'status' => 'ditolak',
            'tanggal_persetujuan' => now(),
            'nama_pengurus' => $this->nama_pengurus,
        ]);

        $this->processModal = false;
        $this->warning('Pengajuan penarikan telah DITOLAK.');
    }

    public function cairkanDana()
    {
        // Guard: cegah pencairan dobel kalau tombol ter-klik lebih dari sekali
        // atau status sudah berubah sejak modal dibuka.
        if ($this->processData->status !== 'disetujui') {
            $this->error('Pengajuan ini tidak dalam status siap dicairkan.');
            $this->processModal = false;
            return;
        }

        $saldoRekening = KoperasiSimpananSaldo::where('koperasi_anggota_id', $this->processData->koperasi_anggota_id)
            ->where('jenis_simpanan', 'sukarela')
            ->first();

        if (!$saldoRekening || $saldoRekening->saldo < $this->processData->jumlah) {
            $this->error('Gagal mencairkan. Saldo sukarela anggota saat ini tidak mencukupi.');
            return;
        }

        DB::transaction(function () use ($saldoRekening) {
            $saldoSebelum = $saldoRekening->saldo;
            $saldoSesudah = $saldoSebelum - $this->processData->jumlah;

            // 1. Kurangi Saldo Anggota
            $saldoRekening->update(['saldo' => $saldoSesudah]);

            // 2. Catat di Riwayat Simpanan
            $nomorTx = 'SIM-' . now()->format('Ymd') . '-' . rand(1000, 9999);
            KoperasiSimpananTransaksi::create([
                'nomor_transaksi' => $nomorTx,
                'koperasi_anggota_id' => $this->processData->koperasi_anggota_id,
                'jenis_simpanan' => 'sukarela',
                'tipe' => 'tarik',
                'jumlah' => $this->processData->jumlah,
                'saldo_sebelum' => $saldoSebelum,
                'saldo_sesudah' => $saldoSesudah,
                'keterangan' => 'Pencairan Simpanan Sukarela: ' . $this->processData->alasan,
                'tanggal_transaksi' => now(),
                'user_id' => Auth::id() ?? 1,
            ]);

            // 3. Potong Kas Koperasi
            KoperasiKasTransaksi::create([
                'nomor_referensi' => $this->processData->nomor_pengajuan,
                'sumber' => 'simpanan',
                'tipe' => 'keluar',
                'jumlah' => $this->processData->jumlah,
                'keterangan' => 'Pencairan Penarikan a.n ' . ($this->processData->anggota->nama ?? 'Anggota Tidak Ditemukan'),
                'tanggal_transaksi' => now(),
                'user_id' => Auth::id() ?? 1,
            ]);

            // 4. Ubah Status Pengajuan Menjadi Selesai
            $this->processData->update([
                'status' => 'dicairkan',
                'tanggal_pencairan' => now(),
            ]);
        });

        $this->processModal = false;
        $this->success('Dana berhasil DICAIRKAN. Kas koperasi telah dipotong secara otomatis.');
    }

    public function render()
    {
        $query = KoperasiPenarikanSukarela::with('anggota')
            ->when($this->search, function ($q) {
                $q->where(function ($outer) {
                    $outer->whereHas('anggota', function ($sub) {
                        $sub->where('nama', 'like', '%' . $this->search . '%')
                            ->orWhere('nomor_anggota', 'like', '%' . $this->search . '%');
                    })->orWhere('nomor_pengajuan', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        $anggotaAktif = KoperasiAnggota::where('status', 'aktif')
            ->select('id', 'nomor_anggota', 'nama')
            ->get()
            ->map(function ($item) {
                $item->nama_label = $item->nomor_anggota . ' - ' . $item->nama;
                return $item;
            });

        return view('pages.admin.koperasi.penarikan-sukarela', [
            'pengajuans' => $query->paginate(10),
            'anggotaAktif' => $anggotaAktif,
        ])->layout('layouts.app', ['title' => __('Penarikan Simpanan')]);
    }
}
<?php

namespace App\Livewire\Actions;

use App\Models\KoperasiAnggota;
use App\Models\KoperasiKasTransaksi;
use App\Models\KoperasiPenarikanSukarela;
use App\Models\KoperasiSimpananSaldo;
use App\Models\KoperasiSimpananTransaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class PenarikanSukarela extends Component
{
    use Toast, WithPagination;

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

    private function generateNomorPengajuan(): string
    {
        $prefix = 'TARIK-'.now()->format('Ymd').'-';

        // Atomically derive the next sequence so two concurrent submissions
        // never collide on the unique nomor_pengajuan column.
        return DB::transaction(function () use ($prefix): string {
            $last = KoperasiPenarikanSukarela::where('nomor_pengajuan', 'like', $prefix.'%')
                ->orderBy('nomor_pengajuan', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNum = $last ? ((int) substr($last->nomor_pengajuan, -4)) + 1 : 1;

            return $prefix.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
        });
    }

    public function simpanPengajuan()
    {
        $this->validate([
            'selectedAnggotaId' => 'required|exists:koperasi_anggota,id',
            'jumlah' => 'required|numeric|min:1000|max:'.$this->saldoTersedia,
            'alasan' => 'required|string',
        ], [
            'jumlah.max' => 'Jumlah penarikan tidak boleh melebihi Saldo Sukarela yang tersedia.',
        ]);

        KoperasiPenarikanSukarela::create([
            'nomor_pengajuan' => $this->generateNomorPengajuan(),
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

        try {
            DB::transaction(function () {
                // Re-load pengajuan terkunci untuk cegah race antar request.
                $pengajuan = KoperasiPenarikanSukarela::where('id', $this->processData->id)
                    ->lockForUpdate()
                    ->first();

                if (! $pengajuan || $pengajuan->status !== 'disetujui') {
                    throw ValidationException::withMessages([
                        'processData' => 'Pengajuan ini tidak dalam status siap dicairkan.',
                    ]);
                }

                // Kunci baris saldo sukarela sebelum cek & potong. Ini yang
                // menutup race antara jalur ini dan penarikan harian di Simpanan.
                $saldoRekening = KoperasiSimpananSaldo::where('koperasi_anggota_id', $pengajuan->koperasi_anggota_id)
                    ->where('jenis_simpanan', 'sukarela')
                    ->lockForUpdate()
                    ->first();

                if (! $saldoRekening || (float) $saldoRekening->saldo < (float) $pengajuan->jumlah) {
                    throw ValidationException::withMessages([
                        'processData' => 'Gagal mencairkan. Saldo sukarela anggota saat ini tidak mencukupi.',
                    ]);
                }

                $saldoSebelum = $saldoRekening->saldo;
                $saldoSesudah = $saldoSebelum - $pengajuan->jumlah;

                // 1. Kurangi Saldo Anggota
                $saldoRekening->update(['saldo' => $saldoSesudah]);

                // 2. Catat di Riwayat Simpanan (nomor atomik, bukan rand)
                $nomorTx = $this->generateNomorTransaksiSukarela();
                KoperasiSimpananTransaksi::create([
                    'nomor_transaksi' => $nomorTx,
                    'koperasi_anggota_id' => $pengajuan->koperasi_anggota_id,
                    'jenis_simpanan' => 'sukarela',
                    'tipe' => 'tarik',
                    'jumlah' => $pengajuan->jumlah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => 'Pencairan Simpanan Sukarela: '.$pengajuan->alasan,
                    'tanggal_transaksi' => now(),
                    'user_id' => Auth::id() ?? 1,
                ]);

                // 3. Potong Kas Koperasi
                KoperasiKasTransaksi::create([
                    'nomor_referensi' => $pengajuan->nomor_pengajuan,
                    'sumber' => 'simpanan',
                    'tipe' => 'keluar',
                    'jumlah' => $pengajuan->jumlah,
                    'keterangan' => 'Pencairan Penarikan a.n '.($pengajuan->anggota->nama ?? 'Anggota Tidak Ditemukan'),
                    'tanggal_transaksi' => now(),
                    'user_id' => Auth::id() ?? 1,
                ]);

                // 4. Ubah Status Pengajuan Menjadi Selesai
                $pengajuan->update([
                    'status' => 'dicairkan',
                    'tanggal_pencairan' => now(),
                ]);

                // Sinkronkan state Livewire agar modal tertutup benar.
                $this->processData = $pengajuan->fresh();
            });
        } catch (ValidationException $e) {
            $this->error(collect($e->validator->errors()->all())->first());
            $this->processModal = false;

            return;
        }

        $this->processModal = false;
        $this->success('Dana berhasil DICAIRKAN. Kas koperasi telah dipotong secara otomatis.');
    }

    /**
     * Atomically generate the next SIM-... nomor_transaksi (same scheme as
     * the Simpanan component). Kept local to avoid coupling these two classes.
     */
    private function generateNomorTransaksiSukarela(): string
    {
        $prefix = 'SIM-'.now()->format('Ymd').'-';

        return DB::transaction(function () use ($prefix): string {
            $lastTx = KoperasiSimpananTransaksi::where('nomor_transaksi', 'like', $prefix.'%')
                ->orderBy('nomor_transaksi', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNum = $lastTx ? ((int) substr($lastTx->nomor_transaksi, -4)) + 1 : 1;

            return $prefix.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
        });
    }

    public function render()
    {
        $query = KoperasiPenarikanSukarela::with('anggota')
            ->when($this->search, function ($q) {
                $q->where(function ($outer) {
                    $outer->whereHas('anggota', function ($sub) {
                        $sub->where('nama', 'like', '%'.$this->search.'%')
                            ->orWhere('nomor_anggota', 'like', '%'.$this->search.'%');
                    })->orWhere('nomor_pengajuan', 'like', '%'.$this->search.'%');
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
                $item->nama_label = $item->nomor_anggota.' - '.$item->nama;

                return $item;
            });

        return view('pages.admin.koperasi.penarikan-sukarela', [
            'pengajuans' => $query->paginate(10),
            'anggotaAktif' => $anggotaAktif,
        ])->layout('layouts.app', ['title' => __('Penarikan Simpanan')]);
    }
}

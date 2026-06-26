<?php

namespace App\Livewire\Actions;

use App\Models\KoperasiAnggota;
use App\Models\KoperasiKasTransaksi;
use App\Models\KoperasiSimpananSaldo;
use App\Models\KoperasiSimpananTransaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class Simpanan extends Component
{
    use Toast, WithPagination;

    public $search = '';

    public $jenisFilter = '';

    public $tipeFilter = '';

    // Form Modal Transaksi
    public bool $transactionModal = false;

    public $selectedAnggotaId = null;

    public $jenis_simpanan = 'wajib';

    public $tipe_transaksi = 'setor';

    public $jumlah = '';

    public $keterangan = '';

    public $tanggal_transaksi;

    // State Saldo Real-Time
    public $saldoSekarang = [
        'pokok' => 0,
        'wajib' => 0,
        'sukarela' => 0,
    ];

    // State Kuitansi / Struk
    public bool $receiptModal = false;

    public $receiptData = null;

    public function mount()
    {
        $this->tanggal_transaksi = now()->format('Y-m-d\TH:i');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openTransactionModal()
    {
        $this->resetValidation();
        $this->reset(['selectedAnggotaId', 'jumlah', 'keterangan']);
        $this->jenis_simpanan = 'wajib';
        $this->tipe_transaksi = 'setor';
        $this->tanggal_transaksi = now()->format('Y-m-d\TH:i');
        $this->resetSaldoState();
        $this->transactionModal = true;
    }

    // Dipanggil otomatis oleh Livewire saat anggota dipilih (Reaktif)
    public function updatedSelectedAnggotaId($val)
    {
        if ($val) {
            $saldos = KoperasiSimpananSaldo::where('koperasi_anggota_id', $val)->get();
            $this->saldoSekarang = [
                'pokok' => $saldos->where('jenis_simpanan', 'pokok')->first()->saldo ?? 0,
                'wajib' => $saldos->where('jenis_simpanan', 'wajib')->first()->saldo ?? 0,
                'sukarela' => $saldos->where('jenis_simpanan', 'sukarela')->first()->saldo ?? 0,
            ];
        } else {
            $this->resetSaldoState();
        }
    }

    // Memaksa tipe ke 'setor' jika memilih Pokok/Wajib
    public function updatedJenisSimpanan($val)
    {
        if (in_array($val, ['pokok', 'wajib'])) {
            $this->tipe_transaksi = 'setor';
        }
    }

    private function resetSaldoState()
    {
        $this->saldoSekarang = ['pokok' => 0, 'wajib' => 0, 'sukarela' => 0];
    }

    private function generateNomorTransaksi()
    {
        $prefix = 'SIM-'.now()->format('Ymd').'-';

        // Atomically compute the next sequence number. The whole lookup-and-pad
        // runs inside a locked transaction so two concurrent transactions never
        // get the same nomor_transaksi (which would violate the unique column).
        return DB::transaction(function () use ($prefix): string {
            $lastTx = KoperasiSimpananTransaksi::where('nomor_transaksi', 'like', $prefix.'%')
                ->orderBy('nomor_transaksi', 'desc')
                ->lockForUpdate()
                ->first();

            $nextNum = $lastTx ? ((int) substr($lastTx->nomor_transaksi, -4)) + 1 : 1;

            return $prefix.str_pad((string) $nextNum, 4, '0', STR_PAD_LEFT);
        });
    }

    public function saveTransaction()
    {
        $this->validate([
            'selectedAnggotaId' => 'required|exists:koperasi_anggota,id',
            'jenis_simpanan' => 'required|in:pokok,wajib,sukarela',
            'tipe_transaksi' => 'required|in:setor,tarik',
            'jumlah' => 'required|numeric|min:500',
            'tanggal_transaksi' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // LOGIKA ATURAN BISNIS: Validasi Penarikan
        if ($this->tipe_transaksi === 'tarik') {
            if ($this->jenis_simpanan !== 'sukarela') {
                $this->addError('jenis_simpanan', 'Hanya Simpanan Sukarela yang dapat ditarik tunai harian.');

                return;
            }
            if ($this->jumlah > $this->saldoSekarang['sukarela']) {
                $this->addError('jumlah', 'Saldo simpanan sukarela tidak mencukupi untuk penarikan ini.');

                return;
            }
        }

        try {
            DB::transaction(function () {
                // 1. Dapatkan atau buat entri saldo, lalu KUNCI barisnya supaya
                //    penarikan paralel (jalur ini maupun PenarikanSukarela) tidak
                //    lolos cek saldo bersamaan → saldo negatif.
                $saldoRecord = KoperasiSimpananSaldo::firstOrCreate(
                    ['koperasi_anggota_id' => $this->selectedAnggotaId, 'jenis_simpanan' => $this->jenis_simpanan],
                    ['saldo' => 0]
                );
                $saldoRecord = KoperasiSimpananSaldo::where('koperasi_anggota_id', $this->selectedAnggotaId)
                    ->where('jenis_simpanan', $this->jenis_simpanan)
                    ->lockForUpdate()
                    ->first();

                // Re-verify sufficiency against the freshly-locked value.
                if ($this->tipe_transaksi === 'tarik' && $this->jumlah > (float) $saldoRecord->saldo) {
                    throw ValidationException::withMessages([
                        'jumlah' => 'Saldo simpanan sukarela tidak mencukupi untuk penarikan ini.',
                    ]);
                }

                $saldoSebelum = $saldoRecord->saldo;
                $saldoSesudah = $this->tipe_transaksi === 'setor'
                    ? $saldoSebelum + $this->jumlah
                    : $saldoSebelum - $this->jumlah;

                // 2. Catat Histori Transaksi Simpanan
                $nomorTx = $this->generateNomorTransaksi();
                $transaksi = KoperasiSimpananTransaksi::create([
                    'nomor_transaksi' => $nomorTx,
                    'koperasi_anggota_id' => $this->selectedAnggotaId,
                    'jenis_simpanan' => $this->jenis_simpanan,
                    'tipe' => $this->tipe_transaksi,
                    'jumlah' => $this->jumlah,
                    'saldo_sebelum' => $saldoSebelum,
                    'saldo_sesudah' => $saldoSesudah,
                    'keterangan' => $this->keterangan ?: ($this->tipe_transaksi === 'setor' ? 'Setoran Simpanan ' : 'Penarikan Simpanan ').ucfirst($this->jenis_simpanan),
                    'tanggal_transaksi' => $this->tanggal_transaksi,
                    'user_id' => Auth::id() ?? 1,
                ]);

                // 3. Update Saldo Utama
                $saldoRecord->update(['saldo' => $saldoSesudah]);

                // 4. Catat di Buku Kas Utama Koperasi
                KoperasiKasTransaksi::create([
                    'nomor_referensi' => $nomorTx,
                    'sumber' => 'simpanan',
                    'tipe' => $this->tipe_transaksi === 'setor' ? 'masuk' : 'keluar',
                    'jumlah' => $this->jumlah,
                    'keterangan' => $transaksi->keterangan.' a.n '.KoperasiAnggota::find($this->selectedAnggotaId)->nama,
                    'tanggal_transaksi' => $this->tanggal_transaksi,
                    'user_id' => Auth::id() ?? 1,
                ]);

                // Siapkan data untuk kuitansi
                $this->receiptData = $transaksi->load(['anggota', 'user']);
            });
        } catch (ValidationException $e) {
            // Re-throw so Livewire renders the error on the right field.
            throw $e;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan: '.$e->getMessage());

            return;
        }

        $this->transactionModal = false;
        $this->success('Transaksi berhasil diproses.');

        // Buka modal struk kuitansi
        $this->receiptModal = true;
    }

    public function render()
    {
        // Query riwayat transaksi
        $transactions = KoperasiSimpananTransaksi::with(['anggota', 'user'])
            ->when($this->search, function ($q) {
                $q->whereHas('anggota', function ($sub) {
                    $sub->where('nama', 'like', '%'.$this->search.'%')
                        ->orWhere('nomor_anggota', 'like', '%'.$this->search.'%');
                })->orWhere('nomor_transaksi', 'like', '%'.$this->search.'%');
            })
            ->when($this->jenisFilter, fn ($q) => $q->where('jenis_simpanan', $this->jenisFilter))
            ->when($this->tipeFilter, fn ($q) => $q->where('tipe', $this->tipeFilter))
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(15);

        // Ambil data anggota aktif untuk pilihan dropdown auto-complete
        $anggotaAktif = KoperasiAnggota::where('status', 'aktif')
            ->select('id', 'nomor_anggota', 'nama')
            ->get()
            ->map(function ($item) {
                // Modifikasi label agar mudah dicari (ID - Nama)
                $item->nama_label = $item->nomor_anggota.' - '.$item->nama;

                return $item;
            });

        return view('pages.admin.koperasi.simpanan', [
            'transactions' => $transactions,
            'anggotaAktif' => $anggotaAktif,
        ])->layout('layouts.app', ['title' => __('Transaksi Simpanan')]);
    }
}

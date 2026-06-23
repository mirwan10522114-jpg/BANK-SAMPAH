<?php

namespace App\Livewire\Actions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\KoperasiAnggota;
use App\Models\KoperasiSetting;
use App\Models\KoperasiSimpananSaldo;
use App\Models\KoperasiPinjaman;
use App\Models\KoperasiKasTransaksi;
use App\Models\KoperasiAnggotaKeluar;
use App\Models\KoperasiSimpananTransaksi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class Anggota extends Component
{
    use WithPagination, Toast;

    public $search = '';
    public $statusFilter = '';

    // Properti Modal Rincian Total Simpanan Keseluruhan (Kartu Klik)
    public bool $detailSimpananModal = false;
    public $rincianSimpananKoperasi = [];

    // Properti Modal Tambah
    public bool $addModal = false;
    public $nama, $no_ktp, $telepon, $alamat;
    public $simpananPokokDefault = 100000;

    // Properti Modal Edit
    public bool $editModal = false;
    public $editId;

    // Properti Modal Proses Keluar
    public bool $exitModal = false;
    public $exitMemberId, $exitMemberName;
    public $totalSimpanan = 0;
    public $sisaPinjaman = 0;
    public $danaKembali = 0;
    public $exitSimpananPokok = 0;
    public $exitSimpananWajib = 0;
    public $exitSimpananSukarela = 0;

    // Properti Modal Hapus
    public bool $deleteModal = false;
    public $deleteId, $deleteName;

    // Properti Modal Detail Per Anggota
    public bool $detailModal = false;
    public $detailAnggota = null;
    public $detailSaldoSimpanan = ['pokok' => 0, 'wajib' => 0, 'sukarela' => 0];
    public $detailRiwayatSimpanan = [];
    public $detailPinjamans = [];

    public function mount()
    {
        try {
            $setting = KoperasiSetting::first();
            if ($setting) {
                if (isset($setting->nominal_simpanan_pokok) && $setting->nominal_simpanan_pokok > 0) {
                    $this->simpananPokokDefault = (int) $setting->nominal_simpanan_pokok;
                } elseif (isset($setting->simpanan_pokok) && $setting->simpanan_pokok > 0) {
                    $this->simpananPokokDefault = (int) $setting->simpanan_pokok;
                }
            }
        } catch (\Exception $e) {
            $this->simpananPokokDefault = 100000;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /* ===================== DETAIL TOTAL SIMPANAN KOPERASI (KARTU) ===================== */
    public function bukaDetailSimpanan()
    {
        $this->rincianSimpananKoperasi = KoperasiSimpananSaldo::whereHas('anggota', function ($q) {
                $q->where('status', '!=', 'keluar');
            })
            ->selectRaw('jenis_simpanan, SUM(saldo) as total_saldo')
            ->groupBy('jenis_simpanan')
            ->get();

        $this->detailSimpananModal = true;
    }

    /* ===================== TAMBAH ANGGOTA ===================== */
    public function openAddModal()
    {
        $this->resetValidation();
        $this->reset(['nama', 'no_ktp', 'telepon', 'alamat']);
        $this->addModal = true;
    }

    private function generateNomorAnggota(): string
    {
        /*
         * WAJIB pakai withTrashed() karena model KoperasiAnggota menggunakan
         * SoftDeletes. Tanpa withTrashed(), anggota yang sudah dihapus
         * (deleted_at IS NOT NULL) tidak ikut dihitung, sehingga nomor yang
         * pernah dipakai bisa dihasilkan ulang → UniqueConstraintViolationException.
         *
         * Contoh skenario error:
         *   1. KP001 dibuat, lalu dihapus (soft-delete)
         *   2. generateNomorAnggota() tidak menemukan record apapun
         *   3. Mengembalikan 'KP001' → duplicate entry error
         *
         * Dengan withTrashed(), semua nomor yang pernah terpakai ikut
         * dipertimbangkan sehingga nomor baru selalu unik.
         *
         * Sorting dilakukan di DB level (CAST ke UNSIGNED INT) agar efisien
         * dan tidak perlu tarik semua record ke PHP dulu.
         */
        $anggotaTerakhir = KoperasiAnggota::withTrashed()
            ->where('nomor_anggota', 'like', 'KP%')
            ->orderByRaw('CAST(SUBSTRING(nomor_anggota, 3) AS UNSIGNED) DESC')
            ->first();

        if (! $anggotaTerakhir) {
            return 'KP001';
        }

        $angkaTerakhir = (int) substr($anggotaTerakhir->nomor_anggota, 2);
        $angkaBaru = $angkaTerakhir + 1;

        return 'KP' . str_pad($angkaBaru, 3, '0', STR_PAD_LEFT);
    }

    public function saveMember()
    {
        $this->validate([
            'nama'    => 'required|string|max:255',
            /*
             * Validasi unique no_ktp: tambahkan ',NULL,id,deleted_at,NULL'
             * agar rule ini hanya memeriksa record yang belum di-soft-delete.
             * Tanpa ini, mendaftarkan ulang anggota yang pernah dihapus
             * dengan KTP yang sama akan selalu gagal walaupun seharusnya boleh.
             */
            'no_ktp'  => 'required|string|unique:koperasi_anggota,no_ktp,NULL,id,deleted_at,NULL',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'required|string',
        ]);

        DB::transaction(function () {
            $anggota = KoperasiAnggota::create([
                'nomor_anggota'    => $this->generateNomorAnggota(),
                'nama'             => $this->nama,
                'no_ktp'           => $this->no_ktp,
                'no_telepon'       => $this->telepon,
                'alamat'           => $this->alamat,
                'status'           => 'aktif',
                'tanggal_bergabung' => now(),
            ]);

            KoperasiSimpananSaldo::create([
                'koperasi_anggota_id' => $anggota->id,
                'jenis_simpanan'      => 'pokok',
                'saldo'               => $this->simpananPokokDefault,
            ]);

            KoperasiKasTransaksi::create([
                'nomor_referensi'   => 'KAS-IN-' . time(),
                'sumber'            => 'simpanan',
                'tipe'              => 'masuk',
                'jumlah'            => $this->simpananPokokDefault,
                'keterangan'        => 'Simpanan Pokok Anggota Baru a.n ' . $anggota->nama,
                'tanggal_transaksi' => now(),
                'user_id'           => Auth::id() ?? 1,
            ]);
        });

        $this->addModal = false;
        $this->success('Anggota berhasil ditambahkan dan kas telah diperbarui.');
    }

    /* ===================== EDIT ANGGOTA ===================== */
    public function openEditModal($id)
    {
        $this->resetValidation();
        $member = KoperasiAnggota::findOrFail($id);

        $this->editId  = $member->id;
        $this->nama    = $member->nama;
        $this->no_ktp  = $member->no_ktp;
        $this->telepon = $member->no_telepon;
        $this->alamat  = $member->alamat;

        $this->editModal = true;
    }

    public function updateMember()
    {
        $this->validate([
            'nama'    => 'required|string|max:255',
            'no_ktp'  => 'required|string|unique:koperasi_anggota,no_ktp,' . $this->editId . ',id,deleted_at,NULL',
            'telepon' => 'required|string|max:20',
            'alamat'  => 'required|string',
        ]);

        $member = KoperasiAnggota::findOrFail($this->editId);
        $member->update([
            'nama'       => $this->nama,
            'no_ktp'     => $this->no_ktp,
            'no_telepon' => $this->telepon,
            'alamat'     => $this->alamat,
        ]);

        $this->editModal = false;
        $this->success('Data anggota berhasil diperbarui.');
    }

    /* ===================== PROSES KELUAR ===================== */
    public function openExitModal($id)
    {
        $member = KoperasiAnggota::with('simpananSaldos')->findOrFail($id);
        $this->exitMemberId   = $member->id;
        $this->exitMemberName = $member->nama;

        $saldos = $member->simpananSaldos;
        $this->exitSimpananPokok    = (float) ($saldos->where('jenis_simpanan', 'pokok')->first()->saldo ?? 0);
        $this->exitSimpananWajib    = (float) ($saldos->where('jenis_simpanan', 'wajib')->first()->saldo ?? 0);
        $this->exitSimpananSukarela = (float) ($saldos->where('jenis_simpanan', 'sukarela')->first()->saldo ?? 0);

        $this->totalSimpanan = $this->exitSimpananPokok + $this->exitSimpananWajib + $this->exitSimpananSukarela;

        $this->sisaPinjaman = (float) KoperasiPinjaman::where('koperasi_anggota_id', $member->id)
                                    ->whereNotIn('status', ['lunas'])
                                    ->sum('sisa_pinjaman');

        $this->danaKembali = $this->totalSimpanan - $this->sisaPinjaman;

        $this->exitModal = true;
    }

    public function processExit()
    {
        DB::transaction(function () {
            $member = KoperasiAnggota::findOrFail($this->exitMemberId);

            $member->update([
                'status'         => 'keluar',
                'tanggal_keluar' => now(),
            ]);

            KoperasiAnggotaKeluar::create([
                'koperasi_anggota_id' => $member->id,
                'total_simpanan'      => $this->totalSimpanan,
                'sisa_pinjaman'       => $this->sisaPinjaman,
                'dana_dikembalikan'   => $this->danaKembali,
                'tanggal_keluar'      => now(),
            ]);

            if ($this->danaKembali > 0) {
                KoperasiKasTransaksi::create([
                    'nomor_referensi'   => 'KAS-OUT-' . time(),
                    'sumber'            => 'anggota_keluar',
                    'tipe'              => 'keluar',
                    'jumlah'            => $this->danaKembali,
                    'keterangan'        => 'Pengembalian Dana Keluar a.n ' . $member->nama,
                    'tanggal_transaksi' => now(),
                    'user_id'           => Auth::id() ?? 1,
                ]);
            }
        });

        $this->exitModal = false;
        $this->success('Proses keluar selesai. Kas otomatis telah dikurangi.');
    }

    /* ===================== DETAIL ANGGOTA ===================== */
    public function openDetailModal($id)
    {
        $member = KoperasiAnggota::findOrFail($id);
        $this->detailAnggota = $member;

        $saldos = KoperasiSimpananSaldo::where('koperasi_anggota_id', $id)->get();
        $this->detailSaldoSimpanan = [
            'pokok'    => (float) ($saldos->where('jenis_simpanan', 'pokok')->first()->saldo ?? 0),
            'wajib'    => (float) ($saldos->where('jenis_simpanan', 'wajib')->first()->saldo ?? 0),
            'sukarela' => (float) ($saldos->where('jenis_simpanan', 'sukarela')->first()->saldo ?? 0),
        ];

        $this->detailRiwayatSimpanan = KoperasiSimpananTransaksi::where('koperasi_anggota_id', $id)
            ->orderBy('tanggal_transaksi', 'desc')
            ->limit(10)
            ->get();

        $this->detailPinjamans = KoperasiPinjaman::with('angsurans')
            ->where('koperasi_anggota_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->detailModal = true;
    }

    /* ===================== HAPUS ANGGOTA ===================== */
    public function openDeleteModal($id)
    {
        $member = KoperasiAnggota::findOrFail($id);
        $this->deleteId   = $member->id;
        $this->deleteName = $member->nama;
        $this->deleteModal = true;
    }

    public function deleteMember()
    {
        $member = KoperasiAnggota::findOrFail($this->deleteId);
        $member->delete(); // soft-delete

        $this->deleteModal = false;
        $this->success('Data anggota berhasil dihapus ke arsip.');
    }

    /* ===================== RENDER & AUTO-STATUS ===================== */
    public function render()
    {
        $batasPasif = now()->subDays(90);

        $activeMemberIds = KoperasiSimpananTransaksi::where('tanggal_transaksi', '>=', $batasPasif)
            ->pluck('koperasi_anggota_id')
            ->toArray();

        KoperasiAnggota::where('status', 'aktif')
            ->where('tanggal_bergabung', '<', $batasPasif)
            ->whereNotIn('id', $activeMemberIds)
            ->update(['status' => 'pasif']);

        if (! empty($activeMemberIds)) {
            KoperasiAnggota::where('status', 'pasif')
                ->whereIn('id', $activeMemberIds)
                ->update(['status' => 'aktif']);
        }

        $query = KoperasiAnggota::with('simpananSaldos')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama', 'like', '%' . $this->search . '%')
                        ->orWhere('nomor_anggota', 'like', '%' . $this->search . '%')
                        ->orWhere('no_ktp', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc');

        $totalAnggotaAktif      = KoperasiAnggota::where('status', 'aktif')->count();
        $totalAnggotaPasifKeluar = KoperasiAnggota::whereIn('status', ['pasif', 'keluar'])->count();

        $summaryTotalSimpanan = (float) KoperasiSimpananSaldo::whereHas('anggota', function ($q) {
            $q->where('status', '!=', 'keluar');
        })->sum('saldo');

        return view('pages.admin.koperasi.anggota', [
            'members'                => $query->paginate(10),
            'totalAnggotaAktif'      => $totalAnggotaAktif,
            'totalAnggotaPasifKeluar' => $totalAnggotaPasifKeluar,
            'summaryTotalSimpanan'   => $summaryTotalSimpanan,
        ])->layout('layouts.app', ['title' => __('Anggota Koperasi')]);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class AdminController extends BaseController
{
    protected $PesananModel;

    public function __construct()
    {
        $this->PesananModel = new PesananModel();
    }

    public function index()
    {
        $db  = db_connect();

        // ===== Waktu Asia/Jakarta + batas bulan (tanpa startOfMonth) =====
        $now = Time::now('Asia/Jakarta');

        // Awal bulan berjalan: YYYY-mm-01 00:00:00
        $startObj = (clone $now)
            ->setDate($now->getYear(), $now->getMonth(), 1)
            ->setTime(0, 0, 0);

        // Awal bulan berikutnya
        $nextObj  = (clone $startObj)->addMonths(1);

        // Bulan sebelumnya [prevStart, prevNext)
        $prevStartObj = (clone $startObj)->subMonths(1);
        $prevNextObj  = (clone $startObj);

        // String untuk query
        $start    = $startObj->toDateTimeString();
        $next     = $nextObj->toDateTimeString();
        $prevStart = $prevStartObj->toDateTimeString();
        $prevNext = $prevNextObj->toDateTimeString();

        // ===== 1) Data Pelanggan (distinct owner_key pesanan selesai) =====
        $totalPelanggan = (int) $db->table('tb_pesanan')
            ->select('COUNT(DISTINCT owner_key) AS c')
            ->where('status', 'selesai')
            ->get()->getRow('c');

        // (opsional) Data Pelanggan Bulan Ini
        $pelangganBulanIni = (int) $db->table('tb_pesanan')
            ->select('COUNT(DISTINCT owner_key) AS c')
            ->where('status', 'selesai')
            ->where('created_at >=', $start)
            ->where('created_at <',  $next)
            ->get()->getRow('c');

        // ===== 2) Jumlah Produk aktif =====
        $jumlahProduk = (int) $db->table('tb_produk')
            ->where('status', 1)->countAllResults();

        // ===== 3) Paling laris (bulan ini) berdasarkan qty =====
        $rowTop = $db->table('tb_pesanan_item i')
            ->select('i.produk_id, p.nama_produk, SUM(i.qty) AS total_qty')
            ->join('tb_pesanan h', 'h.id_pesanan = i.pesanan_id', 'left')
            ->join('tb_produk p', 'p.id_produk = i.produk_id', 'left')
            ->where('h.created_at >=', $start)
            ->where('h.created_at <',  $next)
            ->where('h.status !=', 'batal') // sesuaikan jika perlu
            ->groupBy('i.produk_id')
            ->orderBy('total_qty', 'DESC')
            ->get()->getRowArray();

        $palingLaris = [
            'nama' => $rowTop['nama_produk'] ?? '—',
            'qty'  => (int)($rowTop['total_qty'] ?? 0),
        ];

        // ===== 4) Omzet bulan ini (gross) dari pesanan SELESAI =====
        $rowOmzet = $db->table('tb_pesanan h')
            ->select('COALESCE(SUM(h.total),0) AS omzet')
            ->where('h.created_at >=', $start)
            ->where('h.created_at <',  $next)
            ->where('h.status', 'selesai')
            ->get()->getRowArray();

        $omzetBulanIni = (float)($rowOmzet['omzet'] ?? 0);

        // ===== Tren vs bulan lalu (opsional) =====
        $rowPrev = $db->table('tb_pesanan')
            ->select('COALESCE(SUM(total),0) AS omzet')
            ->where('created_at >=', $prevStart)
            ->where('created_at <',  $prevNext)
            ->where('status', 'selesai')
            ->get()->getRowArray();

        $omzetPrev = (float)($rowPrev['omzet'] ?? 0);
        $growthPct = $omzetPrev > 0 ? (($omzetBulanIni - $omzetPrev) / $omzetPrev) * 100 : null;

        // ===== Helper format Rupiah =====
        $fmt = static function ($n) {
            return 'Rp ' . number_format($n, 0, ',', '.');
        };

        $bulanLabel = $now->toLocalizedString('MMMM yyyy'); // contoh: Oktober 2025
        $data = [
            'stats' => [
                'pelanggan'       => $totalPelanggan,        // lifetime
                'pelangganBulan'  => $pelangganBulanIni,     // opsional: bulan ini
                'produk'          => $jumlahProduk,
                'palingLaris'     => $palingLaris,
                'omzetBulan'      => $omzetBulanIni,
                'omzetBulanFmt'   => $fmt($omzetBulanIni),
                'growthPct'       => $growthPct,             // bisa null jika bulan lalu 0
                'bulanLabel'      => $bulanLabel,
            ],
            'title' => 'Dashboard Admin | Waroeng Kami',
            'nav_link' => 'Dashboard'
        ];

        return view('admin/dashboard-admin', $data);
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KeranjangModel;
use App\Models\ProdukModel;
use CodeIgniter\HTTP\ResponseInterface;
// new
use Config\Services;
use App\Libraries\CartOwner;
use App\Models\MejaModel;
use App\Models\PesananItemModel;
use App\Models\PesananModel;

class HomepageController extends BaseController
{
    protected $ModelProduk;
    protected $ModelKeranjang;
    protected $ModelMeja;
    protected $ModelPesanan;
    protected $ModelPesananItem;

    public function __construct()
    {
        $this->ModelProduk = new ProdukModel();
        $this->ModelKeranjang = new KeranjangModel();
        $this->ModelMeja = new MejaModel();
        $this->ModelPesanan = new PesananModel();
        $this->ModelPesananItem = new PesananItemModel();
    }
    public function index()
    {
        $q  = trim((string) $this->request->getGet('q'));
        $db = db_connect();

        $tProduk = $db->prefixTable('tb_produk');

        if ($q !== '') {
            // 🔎 Cari SEMUA produk di tb_produk (tanpa join & tanpa limit)
            $products = $db->table($tProduk)
                ->select('id_produk AS id, nama_produk AS nama, deskripsi, harga, gambar, slug, status')
                ->where('status', 'tersedia')
                ->groupStart()
                ->like('nama_produk', $q, 'both')
                ->orLike('deskripsi',  $q, 'both')
                ->groupEnd()
                // kalau tidak ada kolom created_at, hapus 2 baris orderBy di bawah
                ->orderBy('created_at', 'DESC')
                ->orderBy('id_produk', 'DESC')
                ->get()->getResultArray();
        } else {
            // 📋 Default: hanya 3 produk terbaru
            $products = $db->table($tProduk)
                ->select('id_produk AS id, nama_produk AS nama, deskripsi, harga, gambar, slug, status')
                ->where('status', 'tersedia')
                ->orderBy('created_at', 'DESC') // hapus jika kolomnya tidak ada
                ->orderBy('id_produk', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        }

        return view('homepage', [
            'title'    => 'Welcome | Waroeng Kami',
            'nav_link' => 'Homepage',
            'q'        => $q,
            'products' => $products,
        ]);
    }





    // PRODUK
    private function cartOwnerKey(): string
    {
        // Pastikan layanan sesi terinisialisasi (ini otomatis start kalau perlu)
        Services::session();

        // Ambil ID sesi native PHP → stabil & IDE-friendly
        return 'sess:' . session_id();
    }

    public function Data_Produk()
    {
        // 0) Pastikan session aktif (CartOwner::key akan set cookie jika belum ada)
        Services::session();
        $db = db_connect();

        // 1) Katalog produk tersedia
        $produk = $this->ModelProduk
            ->where('status', 'tersedia')
            ->orderBy('favorit', 'DESC')
            ->orderBy('nama_produk', 'ASC')
            ->findAll();

        // 2) Ambil owner_key berbasis cookie (konsisten dengan Tambah_Produk/Data_Keranjang)
        $ownerKey = \App\Libraries\CartOwner::key();

        // 3) Tabel berprefix
        $tKeranjang = $db->prefixTable('tb_keranjang');
        $tProduk    = $db->prefixTable('tb_produk');

        // 4) Agregat khusus owner_key ini
        $agg = $db->table($tKeranjang)
            ->select('COUNT(*) AS row_count,
                  COALESCE(SUM(jumlah), 0)   AS total_qty,
                  COALESCE(SUM(subtotal), 0) AS total_harga')
            ->where('owner_key', $ownerKey)
            ->get()->getRowArray() ?? [];

        $cart_rows  = (int)  ($agg['row_count']   ?? 0);
        $cart_count = (int)  ($agg['total_qty']   ?? 0);   // badge
        $cart_total = (float)($agg['total_harga'] ?? 0.0);

        // 5) Daftar item milik owner_key ini (untuk mini-cart di halaman produk)
        $cart_items = $db->table($tKeranjang . ' k')
            ->select('k.id_keranjang, k.produk_id, k.jumlah, k.harga, k.subtotal,
                  p.nama_produk, p.gambar, p.slug, p.status AS status_produk')
            ->join($tProduk . ' p', 'p.id_produk = k.produk_id', 'left')
            ->where('k.owner_key', $ownerKey)
            ->orderBy('k.id_keranjang', 'DESC')
            ->get()->getResultArray();

        // (opsional) logging kecil
        log_message('debug', 'DATA_PRODUK owner_key={ok}, cart_rows={r}, cart_qty={q}, cart_total={t}', [
            'ok' => $ownerKey,
            'r' => $cart_rows,
            'q' => $cart_count,
            't' => $cart_total
        ]);

        return view('pelanggan/page-produk', [
            'title'          => 'Menu | Waroeng Kami',
            'nav_link'       => 'pesanan',
            'd_produk'       => $produk,

            // Mini-cart info (khusus owner_key ini)
            'cart_items'     => $cart_items,
            'cart_count'     => $cart_count,     // total qty → badge
            'cart_total_qty' => $cart_count,
            'cart_total'     => $cart_total,
            'cart_row_count' => $cart_rows,

            'owner_key_now'  => $ownerKey,       // tampilkan kecil saat debug
        ]);
    }



    /**
     * Format kode: PREFIX + zero-pad number
     */
    private function formatKode(string $prefix, int $number, int $width = 4): string
    {
        return $prefix . str_pad((string)$number, $width, '0', STR_PAD_LEFT);
    }

    /**
     * Preview kode berikutnya dari tb_pesanan (tanpa lock).
     * Jika tabel kosong/kolom kosong → KP0001.
     */
    private function previewKodePesanan(\CodeIgniter\Database\BaseConnection $db, string $prefix = 'KP', int $width = 4): string
    {
        $len = strlen($prefix) + 1;
        $row = $db->query("
        SELECT MAX(CAST(SUBSTRING(kode_pesanan, {$len}) AS UNSIGNED)) AS last_num
        FROM tb_pesanan
        WHERE kode_pesanan IS NOT NULL
          AND kode_pesanan <> ''
          AND kode_pesanan LIKE '{$prefix}%'
    ")->getRowArray();

        $next = (int)($row['last_num'] ?? 0) + 1;
        return $this->formatKode($prefix, $next, $width);
    }

    /**
     * Claim kode + INSERT master pesanan secara atomic (pakai LOCK TABLES).
     * Mengembalikan array [$kode, $idPesanan].
     * NOTE: Tabel tb_pesanan.kode_pesanan sebaiknya UNIQUE untuk ekstra proteksi.
     */
    private function claimKodeAndInsertPesanan(
        \CodeIgniter\Database\BaseConnection $db,
        array $payloadWithoutKode, // data tb_pesanan TANPA 'kode_pesanan'
        string $prefix = 'KP',
        int $width = 4
    ): array {
        $len = strlen($prefix) + 1;

        // Kunci tabel agar anti race-condition
        $db->query('LOCK TABLES tb_pesanan WRITE');

        try {
            $row = $db->query("
            SELECT MAX(CAST(SUBSTRING(kode_pesanan, {$len}) AS UNSIGNED)) AS last_num
            FROM tb_pesanan
            WHERE kode_pesanan IS NOT NULL
              AND kode_pesanan <> ''
              AND kode_pesanan LIKE '{$prefix}%'
        ")->getRowArray();

            $nextNum = (int)($row['last_num'] ?? 0) + 1;
            $kode    = $this->formatKode($prefix, $nextNum, $width);

            // Insert master dengan kode yang baru di-claim
            $insert = array_merge(['kode_pesanan' => $kode], $payloadWithoutKode);
            $db->table('tb_pesanan')->insert($insert);
            $idPesanan = (int) $db->insertID();

            $db->query('UNLOCK TABLES');
            return [$kode, $idPesanan];
        } catch (\Throwable $e) {
            $db->query('UNLOCK TABLES');
            throw $e;
        }
    }

    public function Data_Keranjang()
    {
        // 0) Pastikan sesi & owner_key konsisten dengan Tambah_Produk
        Services::session();                 // aman: start jika belum
        $db       = db_connect();
        $ownerKey = \App\Libraries\CartOwner::key();

        // 1) Table names (prefix aware)
        $tKeranjang = $db->prefixTable('tb_keranjang');
        $tProduk    = $db->prefixTable('tb_produk');
        $tPesanan   = $db->prefixTable('tb_pesanan');

        // 2) Items milik owner_key ini
        $items = $db->table($tKeranjang . ' k')
            ->select('
            k.id_keranjang, k.owner_key, k.produk_id, k.jumlah, k.harga, k.subtotal,
            p.nama_produk, p.gambar, p.slug, p.status AS status_produk
        ')
            ->join($tProduk . ' p', 'p.id_produk = k.produk_id', 'left')
            ->where('k.owner_key', $ownerKey)
            ->orderBy('k.id_keranjang', 'ASC')
            ->get()->getResultArray();

        // 3) Agregat total qty & total harga
        $agg = $db->table($tKeranjang)
            ->select('COALESCE(SUM(jumlah),0) AS total_qty,
                  COALESCE(SUM(subtotal),0) AS total')
            ->where('owner_key', $ownerKey)
            ->get()->getRowArray() ?? [];

        $totalQty = (int)   ($agg['total_qty'] ?? 0);
        $total    = (float) ($agg['total']     ?? 0.0);

        // 4) Preview kode pesanan (opsional)
        $kodePreview = $this->previewKodePesanan($db, 'KP', 4);

        // 5) Meja yang belum dipakai di tb_pesanan
        $mejaM = new \App\Models\MejaModel();

        // Subquery aman: where raw "IS NOT NULL"
        $sub = $db->table($tPesanan)
            ->select('meja_id')
            ->where('meja_id IS NOT NULL', null, false);

        $mejaTersedia = $mejaM->select('id_meja, kode_meja, nama_meja, kapasitas')
            ->where('is_active', 1)
            ->whereNotIn('id_meja', $sub)
            ->orderBy('kode_meja', 'ASC')
            ->findAll();

        // (opsional) logging
        log_message('debug', 'KERANJANG owner_key_now={ok}, items={n}, total_qty={q}, total={t}', [
            'ok' => $ownerKey,
            'n' => count($items),
            'q' => $totalQty,
            't' => $total
        ]);

        return view('pelanggan/page-keranjang', [
            'title'                  => 'Keranjang | Waroeng Kami',
            'nav_link'               => 'pesanan',
            'items'                  => $items,
            'total'                  => $total,
            'total_qty'              => $totalQty,
            'kode_pesanan_view_only' => $kodePreview,
            'meja_tersedia'          => $mejaTersedia,
            'owner_key_now'          => $ownerKey,  // tampilkan kecil saat debug
        ]);
    }




    public function Tambah_Produk()
    {
        // 1) Input & validasi
        $produkId = (int) $this->request->getPost('produk_id');
        $jumlah   = max(1, (int) $this->request->getPost('jumlah')); // default 1
        if ($produkId <= 0) {
            return redirect()->back()->with('error', 'Produk tidak valid.');
        }

        // 2) Owner key stabil (owner-<hex> dari cookie)
        // CartOwner::key() sudah memulai sesi & menyetel cookie bila belum ada.
        $ownerKey = CartOwner::key();
        if ($ownerKey === '') {
            return redirect()->back()->with('error', 'Sesi tidak valid. Coba lagi.');
        }

        // 3) Cek produk tersedia & harga
        $produkM = new ProdukModel();
        $produk  = $produkM->select('id_produk, harga, status')
            ->where('status', 'tersedia')
            ->find($produkId);
        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan / sedang habis.');
        }

        $harga = (float) ($produk['harga'] ?? 0);
        if ($harga <= 0) {
            return redirect()->back()->with('error', 'Harga produk tidak valid.');
        }

        // 4) UPSERT atomic (perlu UNIQUE (owner_key, produk_id))
        $cartM = new KeranjangModel();
        $db    = $cartM->db;

        try {
            $sql = "
            INSERT INTO tb_keranjang (owner_key, produk_id, jumlah, harga, subtotal)
            VALUES (?, ?, ?, ?, ROUND(? * ?, 2))
            ON DUPLICATE KEY UPDATE
                jumlah   = jumlah + VALUES(jumlah),
                harga    = VALUES(harga),
                subtotal = ROUND((jumlah + VALUES(jumlah)) * VALUES(harga), 2)
        ";

            $ok = $db->query($sql, [
                $ownerKey,
                $produkId,
                $jumlah,
                $harga,
                $harga,
                $jumlah,
            ]);

            if ($ok === false) {
                $err = $db->error();
                return redirect()->back()->with('error', 'Gagal menambahkan produk: ' . ($err['message'] ?? 'unknown'));
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->to(base_url('pelanggan/keranjang'))
            ->with('success', 'Berhasil menambahkan produk.');
    }


    public function delete_keranjang($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID keranjang tidak valid.');
        }

        // Pastikan sesi aktif & dapatkan owner_key dari cookie persistent
        Services::session();
        $ownerKey = \App\Libraries\CartOwner::key();

        $cartM = new \App\Models\KeranjangModel();

        // Ambil baris target berdasarkan ID saja
        $row = $cartM->select('id_keranjang, owner_key')
            ->where('id_keranjang', $id)
            ->first();

        if (! $row) {
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan.');
        }

        // Otorisasi: hanya boleh hapus jika owner_key sama
        if ((string)($row['owner_key'] ?? '') !== $ownerKey) {
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus item ini.');
        }

        // Transaksi defensif
        $db = $cartM->db;
        $db->transBegin();

        // Hapus dengan pengaman ganda: id_keranjang + owner_key
        $cartM->where('id_keranjang', $id)
            ->where('owner_key', $ownerKey)
            ->delete();

        // Pastikan tepat 1 baris terhapus
        if (! $db->transStatus() || $db->affectedRows() !== 1) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus item keranjang.');
        }

        $db->transCommit();

        return redirect()->to(base_url('pelanggan/keranjang'))
            ->with('success', 'Item keranjang telah dihapus.');
    }


    public function SuksesPembelian()
    {
        Services::session();

        // Tempdata di-set saat checkout: ['owner_key'=>..., 'kode'=>..., 'total'=>..., ...]
        $data = session()->getTempdata('order_success');
        if (!$data || !is_array($data)) {
            return redirect()->to(base_url('pelanggan/produk'))
                ->with('error', 'Anda belum melakukan pemesanan.');
        }

        // Pengaman: pastikan ringkasan benar-benar milik owner sekarang
        $ownerKeyNow = (string) CartOwner::key();
        if (($data['owner_key'] ?? '') !== $ownerKeyNow) {
            return redirect()->to(base_url('pelanggan/produk'))
                ->with('error', 'Sesi berubah. Tidak dapat menampilkan ringkasan pesanan.');
        }

        // (Opsional) perpanjang TTL (mis. 15 menit lagi)
        // session()->setTempdata('order_success', $data, 900);

        return view('pelanggan/page-sukses', [
            'title'         => 'Pesanan Berhasil | Waroeng Kami',
            'nav_link'      => 'pesanan',
            'order'         => $data,
            'owner_key_now' => $ownerKeyNow, // debug opsional
        ]);
    }









    // (Opsional) Ubah jumlah di halaman keranjang
    public function updateQty()
    {
        $idKeranjang = (int) $this->request->getPost('id_keranjang');
        $aksi        = (string) $this->request->getPost('aksi'); // 'plus' / 'minus'
        $cartM       = new KeranjangModel();

        $row = $cartM->find($idKeranjang);
        if (! $row) return redirect()->back();

        $jumlah = (int) $row['jumlah'];
        $jumlah = $aksi === 'plus'  ? $jumlah + 1 : max(1, $jumlah - 1);

        $harga    = (float) $row['harga'];
        $subtotal = $harga * $jumlah;

        $cartM->update($idKeranjang, ['jumlah' => $jumlah, 'subtotal' => $subtotal]);
        return redirect()->back();
    }

    // (Opsional) Hapus baris dari keranjang
    public function hapus()
    {
        $idKeranjang = (int) $this->request->getPost('id_keranjang');
        (new KeranjangModel())->delete($idKeranjang);
        return redirect()->back();
    }

    public function pesan_sekarang()
    {
        Services::session();
        helper('ordercode'); // pastikan helper aktif
        $db       = db_connect();
        $ownerKey = \App\Libraries\CartOwner::key();

        $nama   = trim((string)$this->request->getPost('username'));
        $alamat = trim((string)$this->request->getPost('alamat'));

        if ($nama === '' || $alamat === '') {
            return redirect()->back()->with('error', 'Lengkapi data pemesan terlebih dahulu.');
        }

        // Input sederhana
        $makanDitempat = (int)($this->request->getPost('makan_ditempat') ?? 0);
        $mejaIdInput   = (int)($this->request->getPost('meja_id') ?? 0);

        // Ambil item keranjang khusus milik owner_key ini
        $rows = $db->table('tb_keranjang k')
            ->select('k.id_keranjang, k.produk_id, k.jumlah, k.harga, k.subtotal, p.nama_produk')
            ->join('tb_produk p', 'p.id_produk = k.produk_id', 'left')
            ->where('k.owner_key', $ownerKey)
            ->orderBy('k.id_keranjang', 'ASC')
            ->get()->getResultArray();

        if (empty($rows)) {
            return redirect()->to(base_url('pelanggan/keranjang'))
                ->with('error', 'Keranjang kosong, tidak ada item untuk diproses.');
        }

        // Hitung total dan siapkan batch item
        $grandTotal = 0.0;
        $totalQty   = 0;
        $itemsBatch = [];

        foreach ($rows as $r) {
            $qty      = max(1, (int)$r['jumlah']);
            $harga    = (float)$r['harga'];
            $subtotal = (float)($r['subtotal'] ?: ($qty * $harga));

            $totalQty   += $qty;
            $grandTotal += $subtotal;

            $itemsBatch[] = [
                'produk_id'   => (int)$r['produk_id'],
                'nama_produk' => (string)$r['nama_produk'],
                'qty'         => $qty,
                'harga'       => $harga,
                'subtotal'    => $subtotal,
            ];
        }

        // Payload utama untuk header pesanan
        $payload = [
            'owner_key'      => $ownerKey,
            'nama_pelanggan' => $nama,
            'alamat'         => $alamat,
            'makan_ditempat' => $makanDitempat,
            'meja_id'        => $mejaIdInput ?: null,
            'total'          => $grandTotal,
            'status'         => 'menunggu_bayar',
            'created_at'     => date('Y-m-d H:i:s'),
        ];

        try {
            // Dapatkan kode pesanan dan ID otomatis via helper ordercode
            [$kode, $idPesanan] = claim_next_kode_from_pesanan($db, $payload, 'KP', 4);

            // Mulai transaksi
            $db->transStart();

            // Insert detail item
            foreach ($itemsBatch as &$it) {
                $it['pesanan_id'] = $idPesanan;
                $it['created_at'] = date('Y-m-d H:i:s');
            }
            $db->table('tb_pesanan_item')->insertBatch($itemsBatch);

            // Kosongkan keranjang milik owner_key ini
            $db->table('tb_keranjang')->where('owner_key', $ownerKey)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal disimpan.');
            }

            // ===== Tempdata untuk page sukses & riwayat =====
            $ttlSeconds = 1800; // 30 menit

            $success = [
                'id'          => $idPesanan,
                'kode'        => $kode,
                'total'       => $grandTotal,
                'total_qty'   => $totalQty,
                'item_count'  => count($itemsBatch),
                'waktu'       => date('Y-m-d H:i:s'),
                'expires_at'  => time() + $ttlSeconds,
                'owner_key'   => $ownerKey,
            ];

            session()->setTempdata('order_success', $success, $ttlSeconds);
            session()->setTempdata('riwayat_access', [
                'owner_key'  => $ownerKey,
                'expires_at' => time() + $ttlSeconds,
            ], $ttlSeconds);

            // ===== Langsung tampilkan halaman sukses =====
            return view('pelanggan/page-sukses', [
                'title'          => 'Pesanan Berhasil | Waroeng Kami',
                'nav_link'       => 'pesanan',
                'order'          => $success,
                'owner_key_now'  => $ownerKey
            ]);
        } catch (\Throwable $e) {
            if ($db->transStatus() === false) {
                $db->transRollback();
            }

            log_message('error', 'Gagal membuat pesanan: {msg}', ['msg' => $e->getMessage()]);
            return redirect()->to(base_url('pelanggan/keranjang'))
                ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }


    public function RiwayatTemp()
    {
        Services::session();
        $db = db_connect();

        // Ambil tempdata yang di-set saat checkout
        $access = session()->getTempdata('riwayat_access');
        if (!$access || empty($access['owner_key']) || empty($access['expires_at'])) {
            return redirect()->to(base_url('pelanggan/produk'))
                ->with('error', 'Akses riwayat sudah berakhir. Silakan lakukan pemesanan lagi.');
        }

        // Ambil owner_key dari tempdata & sesi aktif
        $ownerKeyFromTemp = (string)$access['owner_key'];
        $ownerKeyNow      = (string)\App\Libraries\CartOwner::key();

        // Pengaman utama — hanya pemilik yang sama boleh melihat
        if ($ownerKeyNow !== $ownerKeyFromTemp) {
            return redirect()->to(base_url('pelanggan/produk'))
                ->with('error', 'Sesi berubah. Anda tidak berhak melihat riwayat ini.');
        }

        $remain = max(0, (int)$access['expires_at'] - time());

        // Ambil pesanan terbaru milik owner_key ini
        $latest = $db->table('tb_pesanan')
            ->select('id_pesanan, kode_pesanan, total, status, created_at AS tgl', false)
            ->where('owner_key', $ownerKeyFromTemp)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id_pesanan', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $orders = [];
        if ($latest) {
            // Ambil item pesanan
            $items = $db->table('tb_pesanan_item')
                ->select('nama_produk, qty')
                ->where('pesanan_id', (int)$latest['id_pesanan'])
                ->orderBy('id_pesanan_item', 'ASC')
                ->get()
                ->getResultArray();

            // Buat label "Nama × qty"
            $labels = [];
            $totalQty = 0;
            foreach ($items as $it) {
                $qty = (int)($it['qty'] ?? 0);
                $totalQty += $qty;
                $labels[] = trim((string)$it['nama_produk']) . ' ×' . $qty;
            }

            $produkLabel = implode(', ', $labels);

            // Tambahkan ringkasan ke data pesanan
            $latest['total_qty']    = $totalQty;
            $latest['item_count']   = count($items);
            $latest['produk_list']  = $labels;
            $latest['produk_label'] = $produkLabel;

            $orders = [$latest];
        }

        return view('pelanggan/riwayat-temp', [
            'title'         => 'Riwayat Pesanan (Sementara) | Waroeng Kami',
            'nav_link'      => 'pesanan',
            'orders'        => $orders,
            'remain'        => $remain,
            'owner_key_now' => $ownerKeyNow,
        ]);
    }



    // AKHIR PRODUK
    public function tentang_kami()
    {
        $data = [
            'title' => 'Homepage | Tentang Kami',
            "nav_link" => 'tentang'
        ];
        return view('pelanggan/page-tentang-kami', $data);
    }
}

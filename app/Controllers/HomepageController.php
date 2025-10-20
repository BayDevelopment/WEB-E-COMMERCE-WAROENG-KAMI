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

        if ($q !== '') {
            // 🔎 MODE PENCARIAN: by nama_produk dari tb_pesanan_item
            // Ambil produk "tersedia" yang PERNAH dipesan dengan nama mirip q,
            // urutkan dari pesanan TERBARU (ps.created_at), batasi 3 hasil.
            $products = $db->table('tb_produk pr')
                ->select('pr.id_produk AS id, pr.nama_produk AS nama, pr.deskripsi, pr.harga, pr.gambar, pr.slug, pr.status')
                ->join('tb_pesanan_item pi', 'pi.produk_id = pr.id_produk', 'inner')
                ->join('tb_pesanan ps', 'ps.id_pesanan = pi.pesanan_id', 'inner')
                ->where('pr.status', 'tersedia')
                ->like('pi.nama_produk', $q) // cari by nama_produk di pesanan
                ->groupBy('pr.id_produk')    // distinct produk
                ->orderBy('MAX(ps.created_at)', 'DESC', false) // pesanan terbaru dulu
                ->limit(3)
                ->get()->getResultArray();
        } else {
            // 📋 MODE DEFAULT: tampilkan semua produk tersedia (tanpa limit)
            $builder = $db->table('tb_produk')
                ->select('id_produk AS id, nama_produk AS nama, deskripsi, harga, gambar, slug, status')
                ->where('status', 'tersedia');

            // Kalau TIDAK ada kolom created_at di tb_produk, hapus baris di bawah
            $builder->orderBy('created_at', 'DESC');
            $builder->orderBy('id_produk', 'DESC');

            $products = $builder->get()->getResultArray();
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
        // Produk tersedia
        $produk = $this->ModelProduk
            ->where('status', 'tersedia')
            ->orderBy('favorit', 'DESC')
            ->orderBy('nama_produk', 'ASC')
            ->findAll();

        // Pastikan ownerKey konsisten (jangan bikin baru kalau mau baca baris lama!)
        $ownerKey = (string) ($this->cartOwnerKey() ?? '');
        if ($ownerKey === '') {
            // Catatan: kalau kamu bikin baru di sini, jelas tidak akan match data lama.
            log_message('warning', 'owner_key kosong saat baca keranjang. Tidak dibuat baru agar tidak menutupi data lama.');
        }

        $db = \Config\Database::connect();

        // Pakai prefixTable supaya aman dari dbprefix
        $tKeranjang = $db->prefixTable('tb_keranjang');
        $tProduk    = $db->prefixTable('tb_produk');

        // --- Query utama: by owner_key ---
        $builder = $db->table($tKeranjang . ' k')
            ->select('
            k.id_keranjang  AS keranjang_id,
            k.owner_key,
            k.produk_id,
            k.jumlah,
            k.harga        AS harga_item,
            k.subtotal     AS subtotal_item,
            p.nama_produk,
            p.gambar,
            p.status       AS status_produk
        ')
            ->join($tProduk . ' p', 'p.id_produk = k.produk_id', 'left');

        if ($ownerKey !== '') {
            $builder->where('k.owner_key', $ownerKey);
        }

        // Lihat SQL yang dikompilasi (debug)
        $sqlCompiled = $builder->getCompiledSelect();
        log_message('debug', 'SQL (by owner_key): {sql}', ['sql' => $sqlCompiled]);

        $cart_items = $builder->orderBy('k.id_keranjang', 'DESC')->get()->getResultArray();

        // Jika kosong, lakukan DIAGNOSIS:
        if (empty($cart_items)) {
            // 1) Apakah ada baris apapun di tabel keranjang?
            $allCount = (int) $db->table($tKeranjang)->countAllResults();
            log_message('debug', 'DIAG keranjang: countAll={c}', ['c' => $allCount]);

            // 2) Ambil owner_key yang tersedia (sample)
            $owners = $db->table($tKeranjang)
                ->select('owner_key, COUNT(*) as c')
                ->groupBy('owner_key')
                ->orderBy('c', 'DESC')
                ->limit(5)
                ->get()->getResultArray();
            log_message('debug', 'DIAG owner_key sample: {owners}', ['owners' => json_encode($owners)]);

            // 3) Coba ambil TANPA filter owner_key: apakah join/tabelnya benar?
            $probe = $db->table($tKeranjang . ' k')
                ->select('k.id_keranjang, k.owner_key, k.produk_id, k.jumlah, k.harga, k.subtotal')
                ->join($tProduk . ' p', 'p.id_produk = k.produk_id', 'left')
                ->orderBy('k.id_keranjang', 'DESC')
                ->limit(5)
                ->get()->getResultArray();
            log_message('debug', 'DIAG sample rows no-filter: {rows}', ['rows' => json_encode($probe)]);

            // 4) Kalau ternyata owner_key di DB beda dengan $ownerKey yang aktif,
            //    berarti masalah ada di cara kamu membentuk/menyimpan owner_key saat insert.
        }

        // Ringkasan
        $row_count   = count($cart_items);
        $total_qty   = 0;
        $total_harga = 0;

        foreach ($cart_items as $row) {
            $qty      = (int) ($row['jumlah'] ?? 0);
            $harga    = (int) ($row['harga_item'] ?? 0);
            $subtotal = (int) ($row['subtotal_item'] ?? ($qty * $harga));
            $total_qty   += $qty;
            $total_harga += $subtotal;
        }

        // Badge: pilih row_count (jumlah baris) atau total_qty (total porsi)
        $cart_count = $total_qty;

        return view('pelanggan/page-produk', [
            'title'          => 'Menu | Waroeng Kami',
            'nav_link'       => 'pesanan',
            'd_produk'       => $produk,
            'cart_items'     => $cart_items,
            'cart_count'     => $cart_count,
            'cart_total_qty' => $total_qty,
            'cart_total'     => $total_harga,
            'cart_row_count' => $row_count,
            'owner_key_now'  => $ownerKey, // bisa kamu tampilkan sementara di view untuk cek
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
        $db = db_connect();

        // Ambil isi keranjang + info produk (nama & gambar)
        $items = $db->table('tb_keranjang k')
            ->select('k.id_keranjang, k.produk_id, k.jumlah, k.harga, k.subtotal,
                  p.nama_produk, p.gambar, p.slug')
            ->join('tb_produk p', 'p.id_produk = k.produk_id', 'left')
            ->orderBy('k.id_keranjang', 'ASC')
            ->get()->getResultArray();

        // Hitung total kuantitas & total harga
        $totalQty = 0;
        $total    = 0.0;
        foreach ($items as $it) {
            $totalQty += (int)($it['jumlah'] ?? 0);
            $subtotal = isset($it['subtotal'])
                ? (float)$it['subtotal']
                : ((float)($it['harga'] ?? 0) * (int)($it['jumlah'] ?? 0));
            $total += $subtotal;
        }

        // 🔑 Preview kode pesanan
        $kodePreview = $this->previewKodePesanan($db, 'KP', 4);

        // === AMBIL MEJA YANG BELUM ADA DI TB_PESANAN ===
        $mejaM = new MejaModel();

        $mejaTersedia = $mejaM->select('id_meja, kode_meja, nama_meja, kapasitas')
            ->where('is_active', 1)
            ->where('id_meja NOT IN (SELECT meja_id FROM tb_pesanan WHERE meja_id IS NOT NULL)', null, false)
            ->orderBy('kode_meja', 'ASC')
            ->findAll();
        // === END ===

        $data = [
            'title'                  => 'Keranjang | Waroeng Kami',
            'nav_link'               => 'pesanan',
            'items'                  => $items,
            'total'                  => $total,
            'total_qty'              => $totalQty,
            'kode_pesanan_view_only' => $kodePreview,
            'meja_tersedia'          => $mejaTersedia, // dikirim ke view
        ];

        return view('pelanggan/page-keranjang', $data);
    }



    public function Tambah_Produk()
    {
        // --- Ambil input aman ---
        $produkId = (int) $this->request->getPost('produk_id');
        $jumlah   = max(1, (int) $this->request->getPost('jumlah')); // default 1

        if ($produkId <= 0) {
            return redirect()->back()->with('error', 'Produk tidak valid.');
        }

        // --- Init service & model ---
        Services::session();            // pastikan sesi aktif
        $ownerKey = CartOwner::key();   // KONSISTEN dipakai di semua aksi cart


        $produkM = new ProdukModel();
        $cartM   = new KeranjangModel();
        $db      = db_connect();

        // --- Validasi produk tersedia ---
        $produk = $produkM->where('status', 'tersedia')->find($produkId);
        if (! $produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan / sedang habis.');
        }

        $harga = (float) ($produk['harga'] ?? 0);

        // --- Transaksi untuk konsistensi ---
        $db->transStart();

        // Cek apakah item untuk owner ini sudah ada
        $exist = $cartM
            ->where(['owner_key' => $ownerKey, 'produk_id' => $produkId])
            ->first();

        if ($exist) {
            // Merge jumlah
            $newJumlah   = (int) $exist['jumlah'] + $jumlah;
            $newSubtotal = $harga * $newJumlah;

            $cartM->update($exist['id_keranjang'], [
                'jumlah'   => $newJumlah,
                'harga'    => $harga,        // snapshot harga saat ini (opsional)
                'subtotal' => $newSubtotal,
            ]);
        } else {
            // Insert item baru milik owner_key ini
            $cartM->insert([
                'owner_key' => $ownerKey,
                'produk_id' => $produkId,
                'jumlah'    => $jumlah,
                'harga'     => $harga,
                'subtotal'  => $harga * $jumlah,
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal menambahkan ke keranjang.');
        }

        return redirect()
            ->to(base_url('pelanggan/keranjang'))
            ->with('success', 'Berhasil menambahkan produk.');
    }
    public function delete_keranjang($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID keranjang tidak valid.');
        }

        // Pastikan sesi aktif & owner_key konsisten
        Services::session();
        $ownerKey = \App\Libraries\CartOwner::key();

        $cartM = new KeranjangModel();

        // Ambil baris target
        $row = $cartM->find($id);
        if (! $row) {
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan.');
        }

        // Jika ada konsep user login, siapkan pengecekan alternatif
        $userId = (int) (session('id_user') ?? 0);
        $isOwnerBySession = ((string)($row['owner_key'] ?? '') === $ownerKey);
        $isOwnerByUser    = ($userId > 0 && (int)($row['user_id'] ?? 0) === $userId);

        if (! $isOwnerBySession && ! $isOwnerByUser) {
            // Debug opsional (sementara):
            // log_message('debug', 'DELETE DENIED. row_owner={row}, sess_owner={sess}', ['row'=>$row['owner_key']??null,'sess'=>$ownerKey]);
            return redirect()->back()->with('error', 'Anda tidak berhak menghapus item ini.');
        }

        // Transaksi
        $cartM->db->transBegin();

        // Hapus defensif: cocokkan id + (owner_key atau user_id yang sah)
        $builder = $cartM->where('id_keranjang', $id);
        if ($isOwnerBySession) {
            $builder->where('owner_key', $ownerKey);
        } else {
            $builder->where('user_id', $userId);
        }
        $builder->delete();

        if (! $cartM->db->transStatus() || $cartM->db->affectedRows() < 1) {
            $cartM->db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus item keranjang.');
        }

        $cartM->db->transCommit();
        return redirect()->to(base_url('pelanggan/keranjang'))
            ->with('success', 'Item keranjang telah dihapus.');
    }

    public function SuksesPembelian()
    {
        Services::session(); // jaga-jaga

        // Ambil dari Tempdata (bukan Flashdata)
        $data = session()->getTempdata('order_success');

        if (!$data) {
            return redirect()->to(base_url('pelanggan/produk'))
                ->with('error', 'Anda belum melakukan pemesanan.');
        }

        // (Opsional) Sliding TTL: perpanjang sesuai sisa waktu yang kamu simpan
        // Kalau kamu ingin memperpanjang 30 menit setiap kali dibuka:
        // session()->setTempdata('order_success', $data, 1800);

        return view('pelanggan/page-sukses', [
            'title'    => 'Pesanan Berhasil | Waroeng Kami',
            'nav_link' => 'pesanan',
            'order'    => $data,
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
        helper('ordercode');
        $db       = db_connect();
        $ownerKey = CartOwner::key();

        $nama   = trim((string)$this->request->getPost('username'));
        $alamat = trim((string)$this->request->getPost('alamat'));
        if ($nama === '' || $alamat === '') {
            return redirect()->back()->with('error', 'Lengkapi data pemesan terlebih dahulu.');
        }

        // Ambil input makan di tempat dan meja_id langsung tanpa validasi rumit
        $makanDitempat = (int)($this->request->getPost('makan_ditempat') ?? 0);
        $mejaIdInput   = (int)($this->request->getPost('meja_id') ?? 0);

        // Ambil item keranjang
        $rows = $db->table('tb_keranjang k')
            ->select('k.id_keranjang, k.produk_id, k.jumlah, k.harga, k.subtotal, p.nama_produk')
            ->join('tb_produk p', 'p.id_produk = k.produk_id', 'left')
            ->where('k.owner_key', $ownerKey)
            ->orderBy('k.id_keranjang', 'ASC')
            ->get()->getResultArray();

        if (empty($rows)) {
            return redirect()->to(base_url('pelanggan/keranjang'))
                ->with('error', 'Tidak ada item yang diproses.');
        }

        $grandTotal = 0.0;
        $totalQty   = 0;
        $itemsBatch = [];

        foreach ($rows as $r) {
            $qty      = max(1, (int)$r['jumlah']);
            $harga    = (float)$r['harga'];
            $subtotal = isset($r['subtotal']) ? (float)$r['subtotal'] : $qty * $harga;

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

        // Payload utama
        $payload = [
            'owner_key'      => $ownerKey,
            'nama_pelanggan' => $nama,
            'alamat'         => $alamat,
            'makan_ditempat' => $makanDitempat,
            'meja_id'        => $mejaIdInput ?: null,
            'total'          => $grandTotal,
            'status'         => 'menunggu_bayar',
        ];

        try {
            [$kode, $idPesanan] = claim_next_kode_from_pesanan($db, $payload, 'KP', 4);

            $db->transStart();
            foreach ($itemsBatch as &$it) {
                $it['pesanan_id'] = $idPesanan;
            }
            $db->table('tb_pesanan_item')->insertBatch($itemsBatch);
            $db->table('tb_keranjang')->where('owner_key', $ownerKey)->delete();
            $db->transComplete();

            $ttlSeconds = 1800;
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

            return redirect()->to(base_url('pelanggan/success'));
        } catch (\Throwable $e) {
            if ($db->transStatus() === false) {
                $db->transRollback();
            }
            return redirect()->to(base_url('pelanggan/keranjang'))
                ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }



    public function RiwayatTemp()
    {
        $access = session()->getTempdata('riwayat_access');
        if (!$access || empty($access['owner_key'])) {
            return redirect()->to(base_url('pelanggan/produk'))
                ->with('error', 'Akses riwayat sudah berakhir. Silakan lakukan pemesanan lagi.');
        }

        $ownerKey = $access['owner_key'];
        $remain   = max(0, (int)$access['expires_at'] - time());
        $db = db_connect();

        // Ambil hanya pesanan terbaru
        $latest = $db->table('tb_pesanan')
            ->select('id_pesanan, kode_pesanan, total, status, created_at AS tgl', false)
            ->where('owner_key', $ownerKey)
            ->orderBy('created_at', 'DESC')
            ->orderBy('id_pesanan', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $orders = [];
        if ($latest) {
            // Ambil ringkas item (qty & nama)
            $items = $db->table('tb_pesanan_item')
                ->select('nama_produk, qty')
                ->where('pesanan_id', (int)$latest['id_pesanan'])
                ->orderBy('id_pesanan_item', 'ASC') // sesuaikan dengan PK item mu
                ->get()->getResultArray();

            // Buat label: "Nama ×qty"
            $labels = [];
            $totalQty = 0;
            foreach ($items as $it) {
                $qty = (int)($it['qty'] ?? 0);
                $totalQty += $qty;
                $labels[] = trim((string)$it['nama_produk']) . ' ×' . $qty;
            }

            // Kalau itemnya banyak dan mau dipersingkat (opsional)
            // $maxShow = 5;
            // $more = max(0, count($labels) - $maxShow);
            // $produkLabel = implode(', ', array_slice($labels, 0, $maxShow)) . ($more ? " +$more lainnya" : '');

            $produkLabel = implode(', ', $labels);

            // Tambahkan ke data pesanan
            $latest['total_qty']    = $totalQty;
            $latest['item_count']   = count($items);
            $latest['produk_list']  = $labels;       // array, kalau mau di-list satu-satu di view
            $latest['produk_label'] = $produkLabel;  // string, langsung tampil

            $orders = [$latest];
        }

        return view('pelanggan/riwayat-temp', [
            'title'    => 'Riwayat Pesanan (Sementara) | Waroeng Kami',
            'nav_link' => 'pesanan',
            'orders'   => $orders, // 0/1 item
            'remain'   => $remain,
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

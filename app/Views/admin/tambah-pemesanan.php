<?= $this->extend('templates/admin/main') ?>
<?= $this->section('admin_content') ?>

<style>
    /* ========= Base (Light only) ========= */
    :root {
        --page-bg: #ffffff;
        --text: #111315;
        --muted: #6b7280;
        --card-bg: #ffffff;
        --border: #e7e9ee;
        --shadow-1: 0 6px 18px rgba(16, 24, 40, .06), 0 1px 2px rgba(16, 24, 40, .04);
        --shadow-2: 0 14px 32px rgba(16, 24, 40, .10), 0 3px 6px rgba(16, 24, 40, .06);
        --radius: 16px;
        /* contextual accents */
        --accent-primary: #3b82f6;
        /* primary */
        --accent-success: #22c55e;
        /* success */
        --accent-warning: #f59e0b;
        /* warning */
        --accent-danger: #ef4444;
        /* danger */
    }

    body {
        background: var(--page-bg);
        color: var(--text);
    }

    .breadcrumb {
        background: transparent;
        margin-bottom: 1rem;
    }

    /* ========= Modern Stat Cards ========= */
    .dashboard-cards>[class*="col-"] {
        display: flex;
    }

    .card.dash-card {
        background: var(--card-bg) !important;
        /* netral putih */
        color: var(--text) !important;
        /* abaikan .text-white bawaan */
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-1);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        display: flex;
        flex-direction: column;
        width: 100%;
        min-height: 180px;
        position: relative;
        overflow: hidden;
    }

    .card.dash-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-2);
        border-color: rgba(59, 130, 246, .25);
    }

    .card.dash-card .card-body {
        flex: 1 1 auto;
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: .35rem;
    }

    .card.dash-card .card-footer {
        border-top: 1px solid var(--border);
        background: linear-gradient(180deg, #fff, #fafafa);
    }

    /* Aksen strip kiri sesuai konteks (ambil dari kelas bg-*) */
    .card.dash-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        opacity: .95;
        background: linear-gradient(180deg, var(--_accent), rgba(0, 0, 0, 0));
    }

    .card.dash-card.bg-primary {
        --_accent: var(--accent-primary);
    }

    .card.dash-card.bg-success {
        --_accent: var(--accent-success);
    }

    .card.dash-card.bg-warning {
        --_accent: var(--accent-warning);
    }

    .card.dash-card.bg-danger {
        --_accent: var(--accent-danger);
    }

    /* Tipografi angka/label yang modern */
    .card.dash-card .fw-semibold {
        font-weight: 700;
        font-size: .9rem;
        letter-spacing: .2px;
        color: var(--muted);
    }

    .card.dash-card .fs-2 {
        font-size: 1.8rem !important;
        line-height: 1.1;
        font-weight: 800;
        color: var(--text);
    }

    .card.dash-card .fs-4 {
        font-size: 1.35rem !important;
        line-height: 1.15;
        font-weight: 800;
        color: var(--text);
    }

    .card.dash-card .fs-6.fw-bold {
        font-weight: 800;
        color: var(--text);
    }

    .card.dash-card .small {
        color: var(--muted);
    }

    /* Link footer */
    .card.dash-card .card-footer a.small {
        color: #0f172a;
    }

    .card.dash-card .card-footer a.small:hover {
        text-decoration: underline;
    }

    /* Hapus efek warna teks dari .text-white pada isi kartu berwarna */
    .card.dash-card.text-white,
    .card.dash-card [class*="text-white"] {
        color: inherit !important;
    }

    /* Chart canvas & table halus */
    #myAreaChart,
    #myBarChart {
        max-height: 320px;
    }
</style>

<div class="container-fluid px-4 mb-4">
    <h1 class="mt-4 mb-1"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <div class="w-100 mb-3">
        <a href="<?= base_url('admin/pemesanan') ?>" class="btn btn-theme rounded-pill py-2 ms-auto"><span><i class="fa-solid fa-angle-left"></i></span> Kembali</a>
    </div>


    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards">
        <div class="cards-mobile-wrap page-root mb-2">
            <form id="filterPemesanan" class="row g-2 align-items-end mb-3">
                <div class="col-md-6">
                    <label class="form-label mb-0">Keyword</label>
                    <div class="input-group">
                        <input type="text" id="fKeyword" class="form-control" placeholder="Cari nama produk…">
                        <button type="button" id="btnSearch" class="btn btn-dark">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-3">
                    <label class="form-label mb-0">Kategori</label>
                    <select id="fKategori" class="form-select">
                        <option value="">Semua</option>
                        <?php if (!empty($kategori_list)): ?>
                            <?php foreach ($kategori_list as $kategori): ?>
                                <option value="<?= esc($kategori) ?>"><?= esc($kategori) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="col-md-3 d-grid d-md-flex gap-2">
                    <button type="button" id="btnReset" class="btn btn-outline-secondary w-100">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
        <div class="cards-mobile-wrap page-root">
            <div class="row">
                <div class="col-12">
                    <div class="row g-3 cards-mobile">
                        <?php if (!empty($d_produk) && is_array($d_produk)): ?>
                            <?php foreach ($d_produk as $p): ?>
                                <div class="col-lg-4">
                                    <div class="product-card shadow-sm tl-card-white">
                                        <div class="product-media position-relative">
                                            <img
                                                class="product-thumb"
                                                src="<?= !empty($p['gambar'])
                                                            ? base_url('assets/uploads/produk/' . esc($p['gambar']))
                                                            : base_url('assets/img/box.png') ?>"
                                                alt="<?= esc($p['nama_produk']) ?>"
                                                loading="lazy">

                                            <?php if ((int)($p['favorit'] ?? 0) === 1): ?>
                                                <span class="badge-favorit tl-badge-contrast" aria-label="Menu Favorit">
                                                    <i class="bi bi-star-fill me-1" aria-hidden="true"></i> Favorit
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="product-body mb-2 p-3">
                                            <div class="d-flex align-items-start justify-content-between">
                                                <h6 class="product-title mb-1 tl-text-yellow-dark mb-0">
                                                    <?= esc($p['nama_produk']) ?>
                                                </h6>
                                                <span class="product-price tl-text-yellow fw-semibold">
                                                    Rp <?= number_format((float)$p['harga'], 0, ',', '.') ?>
                                                </span>
                                            </div>

                                            <?php if (!empty($p['deskripsi'])): ?>
                                                <p class="product-desc mb-2 small text-muted">
                                                    <?= esc($p['deskripsi']) ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="d-grid gap-2 mt-3">
                                                <!-- Tombol langsung tambah -->
                                                <form action="<?= site_url('admin/pemesanan/tambah') ?>" method="post" class="m-0">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="produk_id" value="<?= (int)$p['id_produk'] ?>">
                                                    <input type="hidden" name="jumlah" value="1">
                                                    <button type="submit"
                                                        class="btn btn-add-to-cart rounded-pill py-2 w-100 d-flex align-items-center justify-content-center gap-2"
                                                        aria-label="Tambah ke pesanan langsung">
                                                        <i class="bi bi-tag"></i> <span>Tambah</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div id="emptyResult" class="cover-image-empty text-center py-5">
                                <img src="<?= base_url('assets/img/box.png') ?>" alt="IMG-Waroeng-Kami" class="size-img-empty mb-3">
                                <h6 class="text-capitalize mb-1">Ups, produk tidak ditemukan 😢</h6>
                                <p class="text-muted text-capitalize mb-0">Silakan hubungi pemilik toko atau kasir</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tombol keranjang mengambang -->
                <!-- Tombol Floating -->
                <div class="floating-cart-wrapper">
                    <button id="toggleCart" class="btn btn-primary rounded-circle position-relative">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <?php if (!empty($cart_count) && $cart_count > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= $cart_count ?>
                                <span class="visually-hidden">items in cart</span>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- Keranjang -->
                    <div class="floating-cart-content">
                        <form action="<?= site_url('admin/pemesanan/checkout') ?>" method="post" class="card border-0 shadow-sm">
                            <?= csrf_field() ?>
                            <!-- Data Pemesan -->
                            <div class="card-body">

                                <!-- Kode Pesanan -->
                                <div class="mb-3">
                                    <label for="kode" class="form-label">Kode Pesanan</label>
                                    <input type="text" class="form-control" id="kode"
                                        value="<?= esc($kode_pesanan) ?>" readonly>
                                    <!-- penting: tidak ada name agar tidak ikut ke POST -->
                                </div>

                                <!-- Username -->
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username / Inisial</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        placeholder="Masukkan username atau inisial..." required>
                                </div>

                                <!-- Alamat -->
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                        placeholder="Tulis alamat pengantaran..." required></textarea>
                                </div>

                                <!-- Pilihan makan -->
                                <div class="mb-3">
                                    <label class="form-label d-block">Pilihan</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="makan_ditempat" id="makan_ditempat1" value="1">
                                        <label class="form-check-label" for="makan_ditempat1">Makan di Tempat</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="makan_ditempat" id="makan_ditempat0" value="0" checked>
                                        <label class="form-check-label" for="makan_ditempat0">Dibungkus / Take Away</label>
                                    </div>
                                </div>

                                <!-- Pilih Meja -->
                                <div class="mb-3" id="blok-meja" style="display: none;">
                                    <label for="meja_id" class="form-label">Pilih Meja</label>
                                    <select class="form-select" id="meja_id" name="meja_id">
                                        <option value="">— Pilih Meja —</option>
                                        <?php foreach (($mejaList ?? []) as $m): ?>
                                            <option value="<?= esc($m['id_meja']) ?>">
                                                <?= esc($m['kode_meja']) ?> — Kapasitas <?= esc($m['kapasitas']) ?> org
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="form-text text-muted">Hanya menampilkan meja yang belum digunakan.</div>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label mb-0">Metode Pembayaran</label>
                                    <select id="fPembayaran" name="pembayaran" class="form-select" required>
                                        <option value="">Pilih Metode...</option>
                                        <option value="cash">Cash / Tunai</option>
                                        <option value="qris">QRIS / Scan</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label mb-0">Status Pembayaran</label>
                                    <select id="fStatus" name="status" class="form-select" required>
                                        <option value="">Pilih Status...</option>
                                        <option value="baru">Baru</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="batal">Batal</option>
                                    </select>
                                </div>

                            </div>



                            <!-- Daftar Item -->
                            <div class="list-group list-group-flush scroll-200">
                                <?php foreach ($items as $it): ?>
                                    <?php $itemId = $it['id'] ?? $it['id_keranjang'] ?? $it['produk_id'] ?? null; ?>
                                    <div class="list-group-item">
                                        <div class="d-flex align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= !empty($it['gambar']) ? base_url('assets/uploads/produk/' . $it['gambar']) : base_url('assets/img/box.png') ?>"
                                                    alt="<?= esc($it['nama_produk']) ?>"
                                                    style="width:64px;height:64px;object-fit:cover;border-radius:10px;">
                                                <div>
                                                    <div class="fw-semibold mb-1"><?= esc($it['nama_produk']) ?></div>
                                                    <div class="small text-muted">
                                                        Rp <?= number_format((float)$it['harga'], 0, ',', '.') ?> / porsi
                                                    </div>
                                                    <div class="small">
                                                        Qty: <span class="fw-semibold"><?= (int)$it['jumlah'] ?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <div class="fw-bold mb-2">
                                                    Rp <?= number_format(((float)($it['subtotal'] ?? ($it['harga'] * $it['jumlah']))), 0, ',', '.') ?>
                                                </div>

                                                <?php if ($itemId !== null): ?>
                                                    <a href="#"
                                                        class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                                        onclick="confirmDeleteKeranjang('<?= esc((int)$itemId) ?>')"
                                                        title="Hapus">
                                                        <i class="bi bi-trash"></i> Hapus
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge text-bg-secondary">ID tidak ditemukan</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Ringkasan & Aksi -->
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div class="fw-bold">Total (<?= (int)$total_qty ?> porsi)</div>
                                <div class="fw-bold">Rp <?= number_format((float)$total, 0, ',', '.') ?></div>
                            </div>

                            <div class="card-body pt-0">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success py-2 rounded-pill" type="submit">
                                        Pesan Sekarang
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- empty dari js -->
            <div id="emptyResult" class="cover-image-empty text-center py-5">
                <img src="<?= base_url('assets/img/box.png') ?>" alt="IMG-Waroeng-Kami" class="size-img-empty mb-3">
                <h6 class="text-capitalize mb-1">Ups, produk tidak ditemukan 😢</h6>
                <p class="text-muted text-capitalize mb-0">Silakan hubungi pemilik toko atau kasir</p>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('fKeyword');
        const kategoriSelect = document.getElementById('fKategori');
        const btnSearch = document.getElementById('btnSearch');
        const btnReset = document.getElementById('btnReset');
        const productCards = document.querySelectorAll('.product-card');
        const emptyDiv = document.getElementById('emptyResult');

        function filterProducts() {
            const keyword = searchInput.value.toLowerCase();
            const kategori = kategoriSelect.value.toLowerCase();
            let anyVisible = false;

            productCards.forEach(card => {
                const nama = card.querySelector('.product-title').textContent.toLowerCase();
                const kategoriCard = card.dataset.kategori?.toLowerCase() ?? '';
                const matchesKeyword = nama.includes(keyword);
                const matchesKategori = !kategori || kategoriCard === kategori;

                const show = matchesKeyword && matchesKategori;
                card.parentElement.style.display = show ? '' : 'none';

                if (show) anyVisible = true;
            });

            // Tampilkan div empty jika tidak ada yang cocok
            emptyDiv.style.display = anyVisible ? 'none' : '';
        }

        btnSearch.addEventListener('click', filterProducts);
        searchInput.addEventListener('keyup', filterProducts);
        kategoriSelect.addEventListener('change', filterProducts);

        btnReset.addEventListener('click', function() {
            searchInput.value = '';
            kategoriSelect.value = '';
            filterProducts();
        });

        // inisialisasi
        filterProducts();
    });
    const toggleCartBtn = document.getElementById('toggleCart');
    const floatingCartContent = document.querySelector('.floating-cart-content');

    toggleCartBtn.addEventListener('click', () => {
        floatingCartContent.classList.toggle('show');
    });

    document.addEventListener('DOMContentLoaded', function() {
        const makanRadios = document.querySelectorAll('input[name="makan_ditempat"]');
        const blokMeja = document.getElementById('blok-meja');

        function toggleMeja() {
            const makanDiTempat = document.getElementById('makan_ditempat1').checked;
            if (makanDiTempat) {
                blokMeja.style.display = 'block';
            } else {
                blokMeja.style.display = 'none';
            }
        }

        // Jalankan saat page load
        toggleMeja();

        // Tambahkan event listener ke semua radio button
        makanRadios.forEach(radio => {
            radio.addEventListener('change', toggleMeja);
        });
    });
</script>
<?= $this->endSection() ?>
<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>
<style>
    /* === Dark → text kuning === */
    :root {
        --wk-accent-yellow: #f5d20bff;
    }

    /* Global: mayoritas teks jadi kuning saat dark */
    [data-bs-theme="dark"] body {
        color: var(--wk-accent-yellow) !important;
    }

    /* Judul, paragraf, link, breadcrumb, dsb. */
    [data-bs-theme="dark"] h1,
    [data-bs-theme="dark"] h2,
    [data-bs-theme="dark"] h3,
    [data-bs-theme="dark"] h4,
    [data-bs-theme="dark"] h5,
    [data-bs-theme="dark"] h6,
    [data-bs-theme="dark"] p,
    [data-bs-theme="dark"] a,
    [data-bs-theme="dark"] .breadcrumb,
    [data-bs-theme="dark"] .product-title,
    [data-bs-theme="dark"] .product-desc,
    [data-bs-theme="dark"] .wk-title,
    [data-bs-theme="dark"] .wk-text-white {
        color: var(--wk-accent-yellow) !important;
    }

    /* KECUALI: biarkan komponen ini tetap pakai warna body agar kontras terjaga */
    [data-bs-theme="dark"] .btn,
    [data-bs-theme="dark"] .btn *,
    [data-bs-theme="dark"] .btn-add-to-cart,
    [data-bs-theme="dark"] .badge,
    [data-bs-theme="dark"] .cart-badge,
    [data-bs-theme="dark"] .qty-group .btn-qty,
    [data-bs-theme="dark"] .input-group-text,
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .toast,
    [data-bs-theme="dark"] .modal-content {
        color: var(--bs-body-color) !important;
    }

    /* Opsional: utilitas opt-in kalau mau target elemen tertentu saja */
    [data-bs-theme="dark"] .tl-text-yellow-dark {
        color: var(--wk-accent-yellow) !important;
    }

    /* Shim dark mode untuk CSS lama yang hard-coded */
    [data-bs-theme="dark"] body {
        background-color: #0f1115 !important;
        color: #e5e7eb !important;
    }

    /* Semua kontainer/kartu/list yang biasanya putih → ikut var Bootstrap */
    [data-bs-theme="dark"] .bg-white,
    [data-bs-theme="dark"] .wk-section,
    [data-bs-theme="dark"] .container,
    [data-bs-theme="dark"] .card,
    [data-bs-theme="dark"] .wk-card,
    [data-bs-theme="dark"] .product-card,
    [data-bs-theme="dark"] .list-group-item,
    [data-bs-theme="dark"] .offcanvas,
    [data-bs-theme="dark"] .dropdown-menu {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* Input, input-group, badge, dsb. */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* Teks & border utilitas */
    [data-bs-theme="dark"] .text-dark {
        color: var(--bs-body-color) !important;
    }

    [data-bs-theme="dark"] .text-muted {
        color: var(--bs-secondary-color) !important;
    }

    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .btn-outline-wk {
        border-color: var(--bs-border-color) !important;
    }

    /* Button custom */
    [data-bs-theme="dark"] .btn-wk {
        background: #f59e0b !important;
        color: #0b0f17 !important;
        border: none !important;
    }

    /* Kartu produk spesifik (kalau ada kelas ini di page-produk) */
    [data-bs-theme="dark"] .wk-grid .product-card .desc,
    [data-bs-theme="dark"] .wk-desc {
        color: var(--bs-secondary-color) !important;
    }

    /* Mobile-only: ≤576px */
    @media (max-width: 576px) {
        .cards-mobile {
            margin-bottom: 100px !important;
        }
    }
</style>

<!-- Global Page Loader -->
<div id="pageLoader" hidden aria-hidden="true">
    <div class="pl-backdrop"></div>
    <div class="pl-card">
        <div class="pl-spinner" aria-label="Loading"></div>
        <div class="pl-text">Memuat...</div>
        <div class="pl-progress"><span class="pl-bar"></span></div>
    </div>
</div>

<nav aria-label="breadcrumb page-root" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Produk Kami</li>
    </ol>
</nav>

<div class="cards-mobile-wrap page-root">
    <div class="row g-3 cards-mobile">
        <?php if (!empty($d_produk)): ?>
            <?php foreach ($d_produk as $p): ?>
                <div class="col-12">
                    <!-- tambahkan tl-card-white agar kartu putih saat LIGHT -->
                    <div class="product-card shadow-sm tl-card-white">
                        <div class="product-media">
                            <img
                                class="product-thumb"
                                src="<?= !empty($p['gambar'])
                                            ? base_url('assets/uploads/produk/' . esc($p['gambar']))
                                            : base_url('assets/img/box.png') ?>"
                                alt="<?= esc($p['nama_produk']) ?>">

                            <?php if ((int)($p['favorit'] ?? 0) === 1): ?>
                                <!-- opsional: tl-badge-contrast biar badge tetap kebaca di light -->
                                <span class="badge-favorit tl-badge-contrast" aria-label="Menu Favorit">
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    Favorit
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-body mb-2 p-3">
                            <div class="d-flex align-items-start justify-content-between">
                                <!-- judul jadi kuning saat LIGHT -->
                                <h6 class="product-title mb-1 tl-text-yellow-dark"><?= esc($p['nama_produk']) ?></h6>
                                <!-- harga sudah kuning by design; kalau mau pastikan kuning di LIGHT: -->
                                <span class="product-price tl-text-yellow">
                                    Rp <?= number_format((float)$p['harga'], 0, ',', '.') ?>
                                </span>
                            </div>

                            <?php if (!empty($p['deskripsi'])): ?>
                                <p class="product-desc mb-2"><?= esc($p['deskripsi']) ?></p>
                            <?php endif; ?>

                            <div class="d-grid gap-2 mt-2">
                                <form action="<?= site_url('pelanggan/produk') ?>" method="post" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="produk_id" value="<?= (int)$p['id_produk'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <button class="btn btn-add-to-cart rounded-pill py-2 w-100 d-flex align-items-center justify-content-center gap-2"
                                        type="submit" aria-label="Tambah ke keranjang">
                                        <span>Tambah</span>
                                        <i class="bi bi-tag"></i>
                                    </button>
                                </form>

                                <form action="<?= site_url('pelanggan/produk') ?>" method="post" class="m-0 form-add-to-cart">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="produk_id" value="<?= (int)$p['id_produk'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <input type="hidden" name="stay" value="1">
                                    <button class="btn btn-add-to-cart rounded-pill py-2 w-100 d-flex align-items-center justify-content-center gap-2"
                                        type="submit" aria-label="Masukkan ke keranjang">
                                        <i class="bi bi-cart-plus me-1" aria-hidden="true"></i> Keranjangi
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="cover-image-empty">
                <img src="<?= base_url('assets/img/box.png') ?>" alt="IMG-Waroeng-Kami" class="size-img-empty">
                <h6 class="text-capitalize">Ups, Produk tidak ditemukan nih</h6>
                <p class="text-capitalize">Silakan hubungi pemilik toko atau kasir</p>
            </div>
        <?php endif; ?>
    </div>

    <?php $count = (int)($cart_count ?? 0); ?>
    <a href="<?= base_url('pelanggan/keranjang'); ?>" class="btn-cart-fab"
        aria-label="Buka keranjang" <?= $count === 0 ? 'data-empty="1"' : '' ?>>
        <i class="bi bi-cart3"></i>
        <?php if ($count > 0): ?>
            <span class="cart-badge"><?= $count ?></span>
        <?php endif; ?>
    </a>

</div>


<?= $this->endSection() ?>
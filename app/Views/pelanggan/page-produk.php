<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>
<style>
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
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Produk Kami</li>
    </ol>
</nav>

<div class="cards-mobile-wrap">
    <div class="row g-3 cards-mobile">
        <?php if (!empty($d_produk)): ?>
            <?php foreach ($d_produk as $p): ?>
                <div class="col-12">
                    <div class="product-card shadow-sm border-0">
                        <div class="product-media">
                            <img
                                class="product-thumb"
                                src="<?= !empty($p['gambar'])
                                            ? base_url('assets/img/' . esc($p['gambar']))
                                            : base_url('assets/img/box.png') ?>"
                                alt="<?= esc($p['nama_produk']) ?>">

                            <?php if ((int)($p['favorit'] ?? 0) === 1): ?>
                                <span class="badge-favorit" aria-label="Menu Favorit">
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    Favorit
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-body mb-2 p-3">
                            <div class="d-flex align-items-start justify-content-between">
                                <h6 class="product-title mb-1 wk-yellow-on-dark"><?= esc($p['nama_produk']) ?></h6>
                                <span class="product-price">Rp <?= number_format((float)$p['harga'], 0, ',', '.') ?></span>
                            </div>

                            <?php if (!empty($p['deskripsi'])): ?>
                                <p class="product-desc mb-2"><?= esc($p['deskripsi']) ?></p>
                            <?php endif; ?>

                            <!-- Stack aksi: tombol vertikal rapi & full width -->
                            <div class="d-grid gap-2 mt-2">

                                <!-- Tambah (submit ke keranjang, default jumlah=1) -->
                                <form action="<?= base_url('pelanggan/produk') ?>" method="post" class="m-0">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="produk_id" value="<?= (int)$p['id_produk'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <button class="btn btn-add-to-cart rounded-pill py-2 w-100 d-flex align-items-center justify-content-center gap-2"
                                        type="submit" aria-label="Tambah ke keranjang">
                                        <span>Tambah</span>
                                        <i class="fa-solid fa-tags"></i>
                                    </button>
                                </form>

                                <form action="<?= base_url('pelanggan/produk') ?>" method="post" class="m-0 form-add-to-cart">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="produk_id" value="<?= (int)$p['id_produk'] ?>">
                                    <input type="hidden" name="jumlah" value="1">
                                    <input type="hidden" name="stay" value="1"><!-- ← tetap di page produk -->
                                    <button class="btn btn-add-to-cart rounded-pill py-2 w-100 d-flex align-items-center justify-content-center gap-2" type="submit" aria-label="Masukkan ke keranjang">
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
<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.product-card').forEach((card) => {
        const grp = card.querySelector('.qty-group');
        if (!grp) return;
        const input = grp.querySelector('.qty-input');
        const minus = grp.querySelector('.btn-qty.minus');
        const plus = grp.querySelector('.btn-qty.plus');
        const form = card.querySelector('form');
        const hidden = form?.querySelector('.jumlah-hidden');

        const clamp = (n) => Math.max(0, Math.min(999, Number(n) || 0));
        const sync = () => {
            const v = clamp(input.value);
            input.value = v;
            if (hidden) hidden.value = v;
        };

        minus?.addEventListener('click', () => {
            input.value = clamp(input.value) - 1;
            sync();
        });
        plus?.addEventListener('click', () => {
            input.value = clamp(input.value) + 1;
            sync();
        });
        input?.addEventListener('input', sync);

        sync(); // init
    });
</script>
<?= $this->endSection() ?>
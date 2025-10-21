<?= $this->extend('templates/pelanggan/main') ?>

<?= $this->section('public_content') ?>
<style>
    /* ================== 1) BRAND & THEME VARS ================== */
    :root {
        --wk-primary: #FFC107;
        --wk-primary-600: #E0AA06;
        --wk-accent: #ff7043;
        --wk-radius: 14px;
    }

    /* LIGHT (OFF) → putih bersih */
    body[data-bs-theme="light"] {
        --bs-body-bg: #ffffff;
        --bs-body-color: #111827;
        --bs-secondary-color: #6b7280;
        --bs-border-color: #e5e7eb;
        --bs-secondary-bg: #ffffff;

        /* WK palette */
        --wk-dark: #212529;
        /* teks utama */
        --wk-muted: #6c757d;
        /* teks sekunder */
        --wk-soft: #f8f9fa;
        /* pill/soft bg */
        --wk-card: #ffffff;
        /* kartu/list/search bg */

        background: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
    }

    /* DARK (ON) → gelap total */
    body.is-dark[data-bs-theme="dark"] {
        color-scheme: dark;
        --bs-body-bg: #0b0f17;
        --bs-body-color: #e5e7eb;
        --bs-secondary-color: #cbd5e1;
        --bs-border-color: #1f2937;
        --bs-secondary-bg: #0b0f17;

        /* WK palette */
        --wk-dark: #e5e7eb;
        --wk-muted: #9ca3af;
        --wk-soft: #0f1422;
        --wk-card: #121725;

        background: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
    }

    /* Haluskan transisi & cegah scroll horizontal */
    html {
        background: #ffffff;
    }

    body {
        transition: background-color .25s, color .25s, border-color .25s, filter .25s;
        overflow-x: hidden;
    }

    body.is-dark[data-bs-theme="dark"]~html {
        background: #0b0f17;
    }

    /* fallback */

    /* ================== 2) MOBILE-FIRST LAYOUT (UNTUK SEMUA DEVICE) ================== */
    /* Semua wrapper transparan supaya ikut body */
    .app-shell,
    header,
    main,
    footer,
    .container,
    .container-fluid,
    section {
        background: transparent !important;
    }

    /* “Satu kolom mobile” di SEMUA resolusi */
    .container-narrow {
        width: 100%;
        max-width: 430px;
        /* lebar mobile */
        margin: 0 auto;
        padding: 0 12px 96px;
        /* extra bottom buat ruang navbar bawah */
    }

    /* Section spacing konsisten mobile */
    .wk-section {
        padding: 10px 0;
        margin-bottom: 30px;
    }

    /* Judul & teks */
    .wk-title {
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: 10px;
        color: var(--wk-dark);
    }

    .wk-muted {
        color: var(--wk-muted);
    }

    /* ================== 3) KOMPONEN “WK” ================== */
    /* Search */
    .wk-search {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--wk-card);
        color: var(--wk-dark);
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 999px;
        padding: 8px 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
    }

    .wk-search i {
        color: var(--wk-muted);
    }

    .wk-search input {
        border: 0;
        outline: 0;
        width: 100%;
        background: transparent;
        color: var(--wk-dark);
    }

    .wk-search input::placeholder {
        color: var(--wk-muted);
    }

    /* autofill sering memutihkan */
    body.is-dark .wk-search input:-webkit-autofill {
        -webkit-text-fill-color: var(--wk-dark) !important;
        -webkit-box-shadow: 0 0 0 1000px var(--wk-card) inset !important;
        transition: background-color 9999s ease-out 0s !important;
    }

    /* Chips/Pill */
    .wk-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: var(--wk-soft);
        color: var(--wk-dark);
        border: 1px solid rgba(0, 0, 0, .06);
        white-space: nowrap;
    }

    .wk-chips {
        display: flex;
        gap: 8px;
        overflow: auto;
        padding-bottom: 4px;
    }

    .wk-chips::-webkit-scrollbar {
        height: 6px;
    }

    .wk-chips::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, .1);
        border-radius: 8px;
    }

    /* List produk (selalu vertikal, mobile) */
    .wk-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    /* Kartu produk */
    .wk-card {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: var(--wk-card);
        color: var(--wk-dark);
        border-radius: var(--wk-radius);
        border: 1px solid rgba(0, 0, 0, .06);
        box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
    }

    .wk-img {
        width: 90px;
        height: 90px;
        flex: 0 0 90px;
        border-radius: 10px;
        object-fit: cover;
        background: #eee;
    }

    body.is-dark .wk-img {
        background: #1f2430 !important;
    }

    .wk-info {
        flex: 1 1 auto;
        min-width: 0;
    }

    .wk-name {
        font-weight: 600;
        font-size: .98rem;
        margin-bottom: 4px;
        color: var(--wk-dark);
    }

    .wk-desc {
        font-size: .85rem;
        color: var(--wk-muted);
        margin: 0 0 6px;
    }

    .wk-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: 6px;
    }

    .wk-price {
        font-weight: 700;
        color: var(--wk-dark);
    }

    /* Tombol */
    .btn-wk {
        background: var(--wk-primary);
        color: #1f1f1f;
        border: 0;
        font-weight: 600;
        border-radius: 999px;
        padding: .45rem .9rem;
    }

    .btn-wk:hover {
        background: var(--wk-primary-600);
        color: #1f1f1f;
    }

    .btn-outline-wk {
        border: 1px solid var(--wk-primary);
        color: var(--wk-primary);
        background: transparent;
        border-radius: 999px;
        padding: .4rem .9rem;
        font-weight: 600;
    }

    .btn-outline-wk:hover {
        background: var(--wk-primary);
        color: #1f1f1f;
    }

    /* ================== 4) BOOTSTRAP COMPONENT COLORS (ikut tema) ================== */
    .card,
    .list-group,
    .list-group-item,
    .list-group-flush .list-group-item,
    .dropdown-menu,
    .offcanvas,
    .modal-content,
    .toast,
    .popover,
    .breadcrumb,
    .navbar,
    .navbar.bg-body-tertiary,
    .navbar.bg-native {
        background-color: var(--bs-secondary-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* Table transparan biar nyatu */
    .table {
        color: var(--bs-body-color) !important;
    }

    .table>:not(caption)>*>* {
        background-color: transparent !important;
        color: inherit !important;
    }

    .table-bordered,
    .table-bordered>:not(caption)>* {
        border-color: var(--bs-border-color) !important;
    }

    .table-striped>tbody>tr:nth-of-type(odd) {
        background-color: rgba(255, 255, 255, .03) !important;
    }

    /* Form */
    .form-control,
    .form-select,
    .input-group-text {
        background: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    .form-label,
    .form-check-label,
    .form-text,
    .text-muted {
        color: var(--bs-secondary-color) !important;
    }

    /* Carousel (teks tetap terbaca) */
    .carousel-caption {
        background: rgba(0, 0, 0, .45);
        color: #f3f4f6;
        border-radius: 12px;
        padding: .75rem 1rem;
    }

    body.is-dark .carousel-item img {
        filter: brightness(.72) contrast(1.05) saturate(.95);
    }

    body.is-dark .carousel-control-prev-icon,
    body.is-dark .carousel-control-next-icon {
        filter: invert(1) grayscale(1) drop-shadow(0 2px 4px rgba(0, 0, 0, .5));
    }

    /* ================== 5) FOOTER (warna saja; layout ikut HTML) ================== */
    body[data-bs-theme="light"] .app-bottom-nav {
        background: #ffffff !important;
        border-top: 1px solid #e5e7eb !important;
    }

    body.is-dark[data-bs-theme="dark"] .app-bottom-nav {
        background: #0b0f17 !important;
        border-top: 1px solid #1f2937 !important;
    }

    .app-bottom-nav .nav-link {
        color: var(--bs-secondary-color) !important;
        font-weight: 600;
    }

    .app-bottom-nav .nav-link.active {
        color: var(--wk-primary) !important;
    }

    .app-bottom-nav .nav-link i {
        color: currentColor;
    }

    /* ================== 6) “Babat putih” utilitas/inline ================== */
    body.is-dark .bg-white,
    body.is-dark .bg-light,
    body.is-dark .bg-body,
    body.is-dark .bg-body-tertiary {
        background-color: var(--bs-secondary-bg) !important;
    }

    body.is-dark .text-dark {
        color: var(--bs-body-color) !important;
    }

    body.is-dark .border,
    body.is-dark [class*="border-"] {
        border-color: var(--bs-border-color) !important;
    }

    /* inline style putih */
    body.is-dark [style*="background:#fff"],
    body.is-dark [style*="background: #fff"],
    body.is-dark [style*="background-color:#fff"],
    body.is-dark [style*="background-color: #fff"],
    body.is-dark [style*="#fafafa"],
    body.is-dark [style*="rgb(255,255,255)"],
    body.is-dark [style*="rgb(250, 250, 250)"] {
        background-color: var(--bs-secondary-bg) !important;
        background: var(--bs-secondary-bg) !important;
        color: var(--bs-body-color) !important;
    }

    /* ================== 7) PENGUNCI MOBILE UNTUK SEMUA BREAKPOINT ================== */
    /* Paksa layout tetap “mobile” meski layar lebar */
    @media (min-width:576px) {
        .container-narrow {
            max-width: 430px;
        }
    }

    @media (min-width:768px) {
        .container-narrow {
            max-width: 430px;
        }

        .wk-list {
            max-width: 430px;
            margin: 0 auto;
        }
    }

    @media (min-width:992px) {
        .container-narrow {
            max-width: 430px;
        }

        .wk-list {
            max-width: 430px;
        }
    }

    @media (min-width:1200px) {
        .container-narrow {
            max-width: 430px;
        }

        .wk-list {
            max-width: 430px;
        }
    }

    @media (max-width: 576px) {
        .wk-section {
            margin-bottom: 30px;
        }

        .container-narrow {
            margin-bottom: -80px;
        }
    }
</style>



<!-- HERO / CAROUSEL -->
<div id="carouselExampleCaptions" class="carousel slide carousel-fade mb-3"
    data-bs-ride="carousel" data-bs-interval="4000" data-bs-pause="hover" data-bs-touch="true">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0"
            class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1"
            aria-label="Slide 2"></button>
    </div>
    <div class="carousel-inner rounded-4 overflow-hidden">
        <div class="carousel-item active">
            <img src="<?= base_url('assets/img/bahan-makanan.png') ?>" class="d-block w-100" alt="Bahan Makanan">
            <div class="carousel-caption d-none d-md-block">
                <h5 class="text-uppercase">Kualitas rasa dimulai dari bahan makanan yang baik.</h5>
                <p class="text-capitalize">Cita rasa terbaik dari bahan pilihan berkualitas.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?= base_url('assets/img/restaurant.png') ?>" class="d-block w-100" alt="Restoran Nyaman">
            <div class="carousel-caption d-none d-md-block">
                <h5 class="text-uppercase">Dengan tempat yang nyaman</h5>
                <p class="text-capitalize">Makan enak, suasana tenang, hati pun senang.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
    </button>
</div>

<!-- SEARCH -->
<section class="wk-section">
    <div class="container-narrow">
        <form class="wk-search" method="get" action="<?= base_url('/') ?>">
            <i class="bi bi-search"></i>
            <input
                type="text"
                name="q"
                inputmode="search"
                minlength="1"
                maxlength="80"
                placeholder="Cari menu favoritmu…"
                value="<?= esc($q ?? '') ?>">
            <button class="btn btn-wk" type="submit">Cari</button>
        </form>
    </div>
</section>



<!-- PRODUK (LIST VERTIKAL) -->
<section class="wk-section">
    <div class="container-narrow">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="wk-title mb-0 wk-text-contrast">Menu Populer</div>
            <span class="wk-badge">Terbaru</span>
        </div>

        <div class="wk-list">
            <?php foreach (($products ?? []) as $p): ?>
                <div class="wk-card">
                    <img class="wk-img" src="<?= base_url('assets/img/' . esc($p['gambar'] ?? base_url('assets/img/placeholder.jpg'))) ?>"
                        alt="<?= esc($p['nama']) ?>">
                    <div class="wk-info">
                        <div class="wk-name"><?= esc($p['nama']) ?></div>
                        <?php if (!empty($p['deskripsi'])): ?>
                            <p class="wk-desc mb-1"><?= esc($p['deskripsi']) ?></p>
                        <?php endif; ?>

                        <div class="wk-cta">
                            <div class="wk-price">Rp <?= number_format((float)($p['harga'] ?? 0), 0, ',', '.') ?></div>
                            <form method="post" action="<?= site_url('pelanggan/produk') ?>">
                                <?= csrf_field() ?>
                                <input type="hidden" name="produk_id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="jumlah" value="1">
                                <button type="submit" class="btn btn-wk">
                                    <i class="bi bi-cart-plus"></i> Tambah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($products)): ?>
                <div class="text-center wk-muted py-4">Produk belum tersedia.</div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-3">
            <a href="<?= base_url('pelanggan/produk') ?>" class="btn btn-wk">
                Lihat Semua Menu
            </a>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
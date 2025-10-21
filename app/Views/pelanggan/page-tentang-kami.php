<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>

<!-- ====== PAGE CSS (khusus halaman ini) ====== -->
<style>
    /* Phone frame: tetap mobile di semua device */
    .about-shell {
        width: 100%;
        max-width: 430px;
        /* kunci tampilan mobile */
        margin: 0 auto;
        min-height: 100svh;
        background: transparent;
        /* biar ikut tema dari body */
    }

    .about-card {
        background: var(--bs-secondary-bg);
        color: var(--bs-body-color);
        border: 1px solid var(--bs-border-color);
        border-radius: 16px;
        padding: 14px;
    }

    body[data-bs-theme="light"] .about-card.tl-card-white {
        background: #fff !important;
        color: #0f172a !important;
        border: 1px solid rgba(0, 0, 0, .06) !important;
    }

    .about-hero {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
    }

    .about-hero .cover {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
        filter: brightness(.95);
        border-radius: 14px;
    }

    .about-hero .brand {
        position: absolute;
        left: 12px;
        bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(0, 0, 0, .45);
        color: #fff;
        padding: 10px 12px;
        border-radius: 12px;
        backdrop-filter: blur(6px);
    }

    .about-hero .brand img {
        width: 38px;
        height: 38px;
        object-fit: contain;
    }

    .about-hero .brand .title {
        margin: 0;
        font-weight: 700;
        line-height: 1.1;
    }

    .about-hero .brand .tag {
        margin: 0;
        font-size: .82rem;
        opacity: .9;
    }

    /* Section heading */
    .sec-title {
        font-weight: 800;
        margin: 6px 0 8px;
    }

    .muted {
        color: var(--bs-secondary-color);
        margin: 0;
    }

    /* Grid kecil rapi */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    /* Stats chip */
    .stat {
        display: grid;
        gap: 4px;
        text-align: center;
        padding: 10px;
        border-radius: 14px;
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
    }

    .stat .num {
        font-weight: 800;
        font-size: 1.05rem;
    }

    .stat .lbl {
        font-size: .82rem;
        color: var(--bs-secondary-color);
    }

    /* Timeline */
    .timeline {
        margin: 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 10px;
    }

    .timeline li {
        display: grid;
        gap: 2px;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid var(--bs-border-color);
        background: var(--bs-secondary-bg);
    }

    .timeline .t-year {
        font-weight: 700;
    }

    .timeline .t-text {
        color: var(--bs-secondary-color);
        margin: 0;
    }

    /* Team */
    .team {
        display: grid;
        gap: 10px;
    }

    .member {
        display: grid;
        grid-template-columns: 54px 1fr;
        gap: 10px;
        align-items: center;
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 10px;
        background: var(--bs-secondary-bg);
    }

    .member img {
        width: 54px;
        height: 54px;
        object-fit: cover;
        border-radius: 12px;
    }

    .member .name {
        font-weight: 700;
        margin: 0;
    }

    .member .role {
        color: var(--bs-secondary-color);
        margin: 0;
        font-size: .9rem;
    }

    /* Maps */
    .map-embed {
        border: 0;
        width: 100%;
        height: 220px;
        border-radius: 14px;
        outline: 1px solid var(--bs-border-color);
    }

    /* Spasi bawah jika ada bottom-nav */
    @media (max-width: 576px) {
        .about-shell {
            padding-bottom: calc(var(--bottom-nav-h, 64px) + 16px);
        }
    }

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

    /* ====== DARK MODE TYPO COLORS (PATCH) ====== */
    /* Palet teks saat dark */
    :root {
        --wk-accent-yellow: #f5d20b;
        /* kuning utama */
        --wk-text-on-dark: #e5e7eb;
        /* putih kebiruan utk paragraf */
        --wk-muted-on-dark: #9ca3af;
        /* abu-abu untuk teks sekunder */
    }

    /* 1) Basis: body & paragraf default = putih */
    body[data-bs-theme="dark"] {
        color: var(--wk-text-on-dark) !important;
    }

    /* 2) Heading, judul seksi, & teks beraksen = kuning */
    body[data-bs-theme="dark"] h1,
    body[data-bs-theme="dark"] h2,
    body[data-bs-theme="dark"] h3,
    body[data-bs-theme="dark"] h4,
    body[data-bs-theme="dark"] h5,
    body[data-bs-theme="dark"] h6,
    body[data-bs-theme="dark"] .sec-title,
    body[data-bs-theme="dark"] .tl-text-yellow-dark,
    /* util opt-in yg sudah kamu pakai */
    body[data-bs-theme="dark"] .tl-text-yellow {
        /* util kuning umum */
        color: var(--wk-accent-yellow) !important;
    }

    /* 3) Paragraf, deskripsi, label sekunder = muted putih */
    body[data-bs-theme="dark"] .muted,
    body[data-bs-theme="dark"] .t-text,
    body[data-bs-theme="dark"] .member .role,
    body[data-bs-theme="dark"] .timeline .t-text,
    body[data-bs-theme="dark"] .stat .lbl {
        color: var(--wk-muted-on-dark) !important;
    }

    /* 4) Link: kuning biar konsisten, hover sedikit lebih terang */
    body[data-bs-theme="dark"] a {
        color: var(--wk-accent-yellow) !important;
        text-decoration: none;
    }

    body[data-bs-theme="dark"] a:hover {
        filter: brightness(1.08);
    }

    /* 5) Breadcrumb: item aktif & separator muted, link kuning */
    body[data-bs-theme="dark"] .breadcrumb {
        color: var(--wk-text-on-dark) !important;
    }

    body[data-bs-theme="dark"] .breadcrumb .breadcrumb-item+.breadcrumb-item::before {
        color: var(--wk-muted-on-dark) !important;
    }

    body[data-bs-theme="dark"] .breadcrumb .breadcrumb-item a {
        color: var(--wk-accent-yellow) !important;
    }

    body[data-bs-theme="dark"] .breadcrumb .breadcrumb-item.active {
        color: var(--wk-muted-on-dark) !important;
    }

    /* 6) Komponen interaktif tetap ikut body (jangan kuning) */
    body[data-bs-theme="dark"] .btn,
    body[data-bs-theme="dark"] .btn *,
    body[data-bs-theme="dark"] .btn-add-to-cart,
    body[data-bs-theme="dark"] .badge,
    body[data-bs-theme="dark"] .cart-badge,
    body[data-bs-theme="dark"] .qty-group .btn-qty,
    body[data-bs-theme="dark"] .input-group-text,
    body[data-bs-theme="dark"] .form-control,
    body[data-bs-theme="dark"] .form-select,
    body[data-bs-theme="dark"] .toast,
    body[data-bs-theme="dark"] .modal-content {
        color: var(--bs-body-color) !important;
        /* biar kontras terhadap bg komponen */
    }

    /* 7) Kartu/section tetap pakai token Bootstrap (sudah aman di CSS kamu),
      tapi pastikan tidak “ikut kuning” */
    body[data-bs-theme="dark"] .about-card,
    body[data-bs-theme="dark"] .member,
    body[data-bs-theme="dark"] .timeline li,
    body[data-bs-theme="dark"] .stat {
        color: var(--wk-text-on-dark) !important;
    }

    /* 8) Utilitas tambahan (opsional) */
    body[data-bs-theme="dark"] .tl-text-white-dark {
        color: var(--wk-text-on-dark) !important;
    }

    /* ===== Mobile only (yang sudah kamu punya) ===== */
    @media (max-width: 576px) {
        .about-shell {
            margin-bottom: 0 !important;
        }
    }

    /* ===== Tablet kecil & ke atas → tetap “mobile look” ===== */
    :root {
        --phone-max: 430px;
        /* lebar frame HP */
        --bottom-nav-h: 64px;
        /* kalau pakai bottom nav */
    }

    /* ≥577px (semua tablet/desktop) → kunci center dengan lebar HP */
    @media (min-width: 577px) {
        .about-shell {
            max-width: var(--phone-max);
            margin: 0 auto 14px !important;
            /* center + beri napas bawah */
            padding-bottom: calc(var(--bottom-nav-h, 64px) + 16px);
            /* konten aman dari bottom-nav jika ada */
            border-radius: 16px;
        }
    }

    /* ≥768px (tablet/PC) → beri “frame” & background halaman */
    @media (min-width: 768px) {
        body {
            background: #eef1f6;
            /* latar belakang halaman (di luar frame) */
            padding: 20px 0;
            /* ruang atas-bawah */
        }

        .about-shell {
            box-shadow: 0 8px 28px rgba(0, 0, 0, .08);
            /* efek device frame */
        }
    }

    /* (Opsional) ≥1200px tetap center rapi */
    @media (min-width: 1200px) {
        .about-shell {
            margin: 0 auto 10px !important;
        }
    }
</style>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
    </ol>
</nav>

<!-- Global Page Loader -->
<div id="pageLoader" hidden aria-hidden="true">
    <div class="pl-backdrop"></div>
    <div class="pl-card">
        <div class="pl-spinner" aria-label="Loading"></div>
        <div class="pl-text">Memuat...</div>
        <div class="pl-progress"><span class="pl-bar"></span></div>
    </div>
</div>

<div class="about-shell page-root">
    <!-- HERO -->
    <section class="about-hero about-card tl-card-white mb-3">
        <img class="cover" src="<?= base_url('assets/img/hero-about.jpg') ?>" alt="Tentang Kami | Waroeng Kami" loading="lazy" decoding="async" width="430" height="160">
        <div class="brand">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo">
            <div>
                <h5 class="title tl-text-yellow-dark">Waroeng Kami</h5>
                <p class="tag">Sejak 2021 • “Enak • Cepat • Bersih”</p>
            </div>
        </div>
    </section>

    <!-- DESKRIPSI SINGKAT -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Siapa Kami?</h6>
        <p class="muted">
            Kami adalah tim kecil yang fokus menghadirkan pengalaman kuliner cepat saji yang higienis,
            rasa konsisten, dan harga ramah. Mengusung konsep <em>mobile-first ordering</em>, pelanggan
            bisa pesan dari ponsel, ambil di gerai, atau makan di tempat—semuanya cepat & rapi.
        </p>
    </section>

    <!-- MISI & VISI -->
    <section class="grid-2 mb-3">
        <div class="about-card tl-card-white">
            <h6 class="sec-title tl-text-yellow-dark mb-1">Misi</h6>
            <ul class="muted m-0 ps-3">
                <li>Kualitas rasa konsisten setiap hari</li>
                <li>Layanan cepat & ramah</li>
                <li>Harga jujur, porsi pas</li>
            </ul>
        </div>
        <div class="about-card tl-card-white">
            <h6 class="sec-title tl-text-yellow-dark mb-1">Visi</h6>
            <p class="muted mb-0">Menjadi gerai favorit keluarga di Serang yang mudah diakses, modern, dan peduli kebersihan.</p>
        </div>
    </section>

    <!-- STATS -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Fakta Singkat</h6>
        <div class="grid-2">
            <div class="stat">
                <div class="num tl-text-yellow">+4</div>
                <div class="lbl">Tahun Berjalan</div>
            </div>
            <div class="stat">
                <div class="num tl-text-yellow">50+</div>
                <div class="lbl">Menu & Variasi</div>
            </div>
            <div class="stat">
                <div class="num tl-text-yellow">10K+</div>
                <div class="lbl">Porsi Terjual</div>
            </div>
            <div class="stat">
                <div class="num tl-text-yellow">4.8/5</div>
                <div class="lbl">Rating Pelanggan</div>
            </div>
        </div>
    </section>

    <!-- TIMELINE -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Sejarah Singkat</h6>
        <ul class="timeline">
            <li>
                <div class="t-year tl-text-yellow">2021</div>
                <p class="t-text">Mulai beroperasi dari dapur kecil, fokus menu ayam & nasi.</p>
            </li>
            <li>
                <div class="t-year tl-text-yellow">2023</div>
                <p class="t-text">Implementasi sistem pemesanan online dan QR di gerai.</p>
            </li>
            <li>
                <div class="t-year tl-text-yellow">2025</div>
                <p class="t-text">Scale-up kitchen & integrasi dashboard produksi harian.</p>
            </li>
        </ul>
    </section>

    <!-- TEAM -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Tim Inti</h6>
        <div class="team">
            <div class="member">
                <img src="<?= base_url('assets/img/woman.png') ?>" alt="Foto Tim 1" loading="lazy" decoding="async" width="54" height="54">
                <div>
                    <p class="name">Salma C. N. Rahmawati</p>
                    <p class="role">Founder & Operasional</p>
                </div>
            </div>
            <div class="member">
                <img src="<?= base_url('assets/img/boy.png') ?>" alt="Foto Tim 2" loading="lazy" decoding="async" width="54" height="54">
                <div>
                    <p class="name">Rafie</p>
                    <p class="role">Tech & Customer Experience</p>
                </div>
            </div>
            <div class="member">
                <img src="<?= base_url('assets/img/woman.png') ?>" alt="Foto Tim 3" loading="lazy" decoding="async" width="54" height="54">
                <div>
                    <p class="name">Najwa</p>
                    <p class="role">Kitchen Lead</p>
                </div>
            </div>
        </div>
    </section>

    <!-- KONTAK & CTA -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Hubungi Kami</h6>
        <p class="muted">Ada pertanyaan, kritik, atau pesanan event? Silakan kontak kami:</p>
        <div class="d-grid gap-2">
            <a class="btn btn-wk d-flex align-items-center justify-content-center gap-2" href="https://wa.me/6281234567890" target="_blank" rel="noopener">
                <i class="bi bi-whatsapp"></i> WhatsApp
            </a>
            <a class="btn btn-outline-wk d-flex align-items-center justify-content-center gap-2" href="tel:+6281234567890">
                <i class="bi bi-telephone"></i> Telepon
            </a>
            <a class="btn btn-outline-wk d-flex align-items-center justify-content-center gap-2" href="mailto:cs@waroengkami.id">
                <i class="bi bi-envelope"></i> Email
            </a>
        </div>
    </section>

    <!-- LOKASI -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Lokasi Gerai</h6>
        <p class="muted mb-2">Jl. Contoh No. 123, Kota Serang, Banten 42111</p>
        <!-- Ganti src berikut dengan src Maps asli tokomu bila sudah ada -->
        <iframe class="map-embed"
            title="Lokasi Waroeng Kami"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            src="https://maps.google.com/maps?q=Kota%20Serang&t=&z=14&ie=UTF8&iwloc=&output=embed">
        </iframe>
    </section>

    <!-- CATATAN JAM OPERASIONAL (opsional) -->
    <section class="about-card tl-card-white mb-3">
        <h6 class="sec-title tl-text-yellow-dark">Jam Operasional</h6>
        <ul class="m-0 ps-3">
            <li>Senin–Jumat: 10.00 – 21.00</li>
            <li>Sabtu–Minggu: 10.00 – 22.00</li>
        </ul>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Tidak wajib—hanya placeholder bila ingin interaksi khusus halaman
    // Contoh: scrolling ke section via anchor, atau analytics.
</script>
<?= $this->endSection() ?>
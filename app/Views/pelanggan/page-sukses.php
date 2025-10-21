<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>

<style>
    /* =======================
   ANDROID-LIKE MOBILE FRAME
   ======================= */
    .android-shell {
        display: grid;
        place-content: start center;
        min-height: 100%;
        padding: 16px 12px 40px;
        background:
            radial-gradient(1200px 800px at 50% -200px, rgba(0, 0, 0, .05), transparent 60%),
            linear-gradient(180deg, rgba(0, 0, 0, .03), transparent 55%);
        border-radius: 15px;
    }

    .android-frame {
        width: 100%;
        max-width: 430px;
        /* ~ Pixel 7 width */
        border-radius: 20px;
        background: #fff;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, .08),
            0 2px 8px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    /* Inner spacing */
    .android-body {
        padding: 16px;
    }

    .android-section {
        padding: 16px;
    }

    .android-section+.android-section {
        padding-top: 0;
    }

    /* Badge & accents */
    :root {
        --brand-accent: #ffd24d;
        --brand-accent-ink: #1f1f1f;
        --wk-accent-yellow: #f5d20bff;
        /* kuning untuk dark text */
    }

    .success-badge {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        background: var(--brand-accent);
        color: var(--brand-accent-ink);
        box-shadow: 0 10px 30px rgba(0, 0, 0, .12);
    }

    /* Card look */
    .soft-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    .order-kode {
        font-feature-settings: "tnum" 1, "cv01" 1;
        letter-spacing: .5px;
    }

    /* Steps */
    .step {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
    }

    .step .num {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: grid;
        place-items: center;
        font-weight: 700;
        background: rgba(0, 0, 0, .06);
    }

    /* Buttons: full width like Android */
    .btn-block {
        width: 100%;
    }

    /* Breadcrumb → compact */
    .breadcrumb {
        margin-bottom: 8px;
        font-size: .9rem;
    }

    .breadcrumb .breadcrumb-item+.breadcrumb-item::before {
        padding-right: .35rem;
    }

    /* Responsive */
    @media (min-width: 576px) {

        .android-body,
        .android-section {
            padding: 18px;
        }
    }

    @media (min-width: 992px) {
        .android-shell {
            padding-top: 24px;
        }
    }

    /* Mobile-only helper */
    @media (max-width: 576px) {
        .cards-mobile {
            margin-bottom: 100px !important;
        }
    }

    /* =======================
   DARK MODE THEME SYSTEM
   ======================= */

    /* 1) Variabel Bootstrap untuk dark (body full dark, card sedikit lebih terang) */
    [data-bs-theme="dark"] {
        /* Body benar-benar gelap */
        --bs-body-bg: #0f1115;
        /* body/background utama */
        --bs-body-color: #e5e7eb;
        /* fallback teks non-kuning */
        --bs-secondary-color: #9aa4b2;
        /* teks sekunder */
        --bs-border-color: #1f2937;
        /* border */
        --bs-secondary-bg: #151a21;
        /* bg kontainer/card */
        --bs-tertiary-bg: #10151b;
        /* bg elemen nested */
    }

    /* 2) Body full dark */
    [data-bs-theme="dark"] body {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        /* akan ditimpa ke kuning di rule global text */
    }

    /* 3) Android shell & frame penyesuaian dark */
    [data-bs-theme="dark"] .android-shell {
        background:
            radial-gradient(1200px 800px at 50% -200px, rgba(255, 255, 255, .04), transparent 60%),
            linear-gradient(180deg, rgba(255, 255, 255, .03), transparent 55%);
    }

    [data-bs-theme="dark"] .android-frame {
        background: var(--bs-secondary-bg);
        box-shadow:
            0 10px 30px rgba(0, 0, 0, .5),
            0 2px 8px rgba(0, 0, 0, .4);
    }

    /* 4) Global: mayoritas teks kuning saat dark */
    [data-bs-theme="dark"] body,
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

    /* 5) Pengecualian: biar kontras & aksesibilitas komponen UI */
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
        /* bukan kuning */
    }

    /* 6) Kontainer/kartu/list → bg berbeda dari body + border sesuai var */
    [data-bs-theme="dark"] .bg-white,
    [data-bs-theme="dark"] .wk-section,
    [data-bs-theme="dark"] .container,
    [data-bs-theme="dark"] .card,
    [data-bs-theme="dark"] .wk-card,
    [data-bs-theme="dark"] .product-card,
    [data-bs-theme="dark"] .soft-card,
    [data-bs-theme="dark"] .list-group-item,
    [data-bs-theme="dark"] .offcanvas,
    [data-bs-theme="dark"] .dropdown-menu {
        background-color: var(--bs-secondary-bg) !important;
        /* beda dari body */
        color: var(--bs-body-color) !important;
        /* fallback non-kuning */
        border-color: var(--bs-border-color) !important;
    }

    /* 7) Input, input-group, badge, dsb. mengikuti palet dark */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: var(--bs-tertiary-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* 8) Utilitas teks */
    [data-bs-theme="dark"] .text-dark {
        color: var(--bs-body-color) !important;
    }

    [data-bs-theme="dark"] .text-muted {
        color: var(--bs-secondary-color) !important;
    }

    /* 9) Border & outline */
    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .btn-outline-wk {
        border-color: var(--bs-border-color) !important;
    }

    /* 10) Tombol custom kuning */
    [data-bs-theme="dark"] .btn-wk {
        background: #f59e0b !important;
        color: #0b0f17 !important;
        border: none !important;
    }

    /* 11) Deskripsi/teks sekunder di kartu produk */
    [data-bs-theme="dark"] .wk-grid .product-card .desc,
    [data-bs-theme="dark"] .wk-desc {
        color: var(--bs-secondary-color) !important;
    }

    /* 12) Opsional: utilitas opt-in kalau mau target elemen tertentu jadi kuning */
    [data-bs-theme="dark"] .tl-text-yellow-dark {
        color: var(--wk-accent-yellow) !important;
    }
</style>


<div class="android-shell">
    <div class="android-frame mb-3">

        <div class="android-body">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('pelanggan/produk') ?>">Produk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pesanan Berhasil</li>
                </ol>
            </nav>
        </div>

        <!-- Hero -->
        <section class="android-section text-center">
            <div class="d-flex flex-column align-items-center gap-3">
                <div class="success-badge">
                    <i class="bi bi-check2-circle" style="font-size:1.75rem;"></i>
                </div>
                <div>
                    <h5 class="mb-1">Pesanan Berhasil Dikirim 🎉</h5>
                    <p class="text-muted mb-0">Terima kasih! Silakan lanjutkan pembayaran untuk memproses pesanan Anda.</p>
                </div>
            </div>
        </section>

        <!-- Ringkasan + Instruksi -->
        <section class="android-section">
            <div class="soft-card p-3">
                <?php
                $kode       = esc($order['kode']  ?? '—');
                $total      = (float)($order['total'] ?? 0);
                $waktu      = esc($order['waktu'] ?? date('Y-m-d H:i:s'));
                $totalQty   = (int)($order['total_qty'] ?? 0);     // total porsi
                $itemCount  = (int)($order['item_count'] ?? 0);    // jumlah baris item
                ?>


                <!-- Kode & Total -->
                <div class="mb-3">
                    <div class="text-uppercase small text-muted">Kode Pesanan</div>
                    <div class="h5 fw-bold order-kode mt-1 mb-3"><?= $kode ?></div>

                    <div class="d-flex justify-content-between flex-wrap gap-3">
                        <div>
                            <div class="text-uppercase small text-muted">Total Pembelian</div>
                            <div class="h6 fw-semibold mt-1">Rp <?= number_format($total, 0, ',', '.') ?></div>
                            <div class="small text-muted mt-1">
                                <?= $itemCount ?> item • <?= $totalQty ?> porsi
                            </div>
                        </div>
                        <div class="text-start">
                            <div class="text-uppercase small text-muted">Waktu</div>
                            <div class="h6 fw-semibold mt-1"><?= esc(waktu_indonesia($waktu)) ?></div>
                        </div>
                    </div>

                </div>

                <!-- Instruksi -->
                <div class="p-3 rounded-4" style="background:rgba(0,0,0,.03);">
                    <div class="mb-2 fw-semibold">Instruksi Pembayaran</div>
                    <div class="step">
                        <div class="num">1</div>
                        <div>Gunakan <strong>Kode Pesanan</strong> saat konfirmasi.</div>
                    </div>
                    <div class="step mt-2">
                        <div class="num">2</div>
                        <div>Lakukan pembayaran sesuai nominal di atas.</div>
                    </div>
                    <div class="step mt-2">
                        <div class="num">3</div>
                        <div>Upload bukti bayar di <em>Riwayat Pesanan</em> (jika tersedia) atau kirim ke admin.</div>
                    </div>
                    <div class="mt-3 small text-muted">Pesanan diproses setelah pembayaran terverifikasi.</div>
                </div>

                <!-- Aksi -->
                <div class="mt-3 d-grid gap-2">
                    <a href="<?= base_url('pelanggan/produk') ?>"
                        class="btn btn-add-to-cart rounded-pill py-2 btn-block d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-bag-plus"></i> Beli Lagi
                    </a>
                    <a href="<?= base_url('pelanggan/riwayat') ?>"
                        class="btn btn-outline-secondary rounded-pill py-2 btn-block d-inline-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-clock-history"></i> Lihat Riwayat
                    </a>
                </div>

                <!-- Info -->
                <div class="alert alert-info border-0 mt-3 mb-0" role="alert">
                    Simpan <strong>kode pesanan</strong> Anda. Bila butuh bantuan, hubungi admin dan sertakan kode agar cepat ditangani.
                </div>
            </div>
        </section>

    </div>
</div>

<?= $this->endSection() ?>
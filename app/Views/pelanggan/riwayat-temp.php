<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>

<style>
    /* ======================================
   ANDROID-LIKE MOBILE FRAME (Mobile Look)
   ====================================== */

    /* Shell: full height, center, lembut */
    .android-shell {
        display: grid;
        place-content: start center;
        min-height: 100vh;
        /* full screen */
        padding: 16px 12px 40px;
        background:
            radial-gradient(1200px 800px at 50% -200px, rgba(0, 0, 0, .05), transparent 60%),
            linear-gradient(180deg, rgba(0, 0, 0, .03), transparent 55%);
        border-radius: 15px;
    }

    /* Frame: lebar terkunci ala mobile */
    .android-frame {
        width: 100%;
        max-width: 430px;
        /* ~ Pixel 7 width */
        margin: 0 auto;
        border-radius: 20px;
        background: #fff;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, .08),
            0 2px 8px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    /* Spacing dasar */
    .android-body {
        padding: 16px;
    }

    .android-section {
        padding: 16px;
    }

    .android-section+.android-section {
        padding-top: 0;
    }

    /* Komponen kartu halus */
    .soft-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    /* Badge & aksen + warna tema */
    :root {
        --brand-accent: #ffd24d;
        --brand-accent-ink: #1f1f1f;
        --wk-accent-yellow: #f5d20bff;
        /* kuning untuk teks utama saat dark */
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

    /* Teks utilitas */
    .muted {
        color: #6c757d;
    }

    .mono {
        font-feature-settings: "tnum" 1, "cv01" 1;
        letter-spacing: .3px;
    }

    .order-kode {
        font-feature-settings: "tnum" 1, "cv01" 1;
        letter-spacing: .5px;
    }

    /* Badge lembut */
    .badge-soft {
        background: rgba(0, 0, 0, .06);
        border-radius: 10px;
        padding: .25rem .5rem;
    }

    /* Button block ala Android */
    .btn-block {
        width: 100%;
    }

    /* Breadcrumb ringkas */
    .breadcrumb {
        margin-bottom: 8px;
        font-size: .9rem;
    }

    .breadcrumb .breadcrumb-item+.breadcrumb-item::before {
        padding-right: .35rem;
    }

    /* Daftar/list vertikal */
    .list-group {
        display: flex;
        flex-direction: column;
        /* stack */
        gap: 8px;
        /* jarak antar item */
    }

    .list-group-item {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        padding: 12px 18px;
        /* sedikit lebih lega */
    }

    /* Baris ringkasan pesanan */
    .order-row {
        display: flex;
        align-items: center;
        gap: 12px;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .order-row .col-left {
        flex: 1 1 auto;
        min-width: 0;
    }

    .order-row .col-right {
        margin-left: auto;
        text-align: right;
        min-width: 160px;
    }

    /* List produk per baris */
    .produk-lines {
        margin: 6px 0 0;
        padding-left: 1.1rem;
    }

    .produk-lines li {
        line-height: 1.25rem;
        margin: 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Truncate nama panjang */
    .text-truncate {
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* Responsive */
    @media (min-width: 576px) {

        .android-body,
        .android-section {
            padding: 18px;
        }
    }

    @media (min-width: 768px) {
        .android-shell {
            padding: 24px 0 60px;
        }

        .list-group {
            gap: 10px;
        }
    }

    @media (min-width: 992px) {
        .android-shell {
            padding-top: 24px;
        }
    }

    /* Mobile helper */
    @media (max-width: 576px) {
        .cards-mobile {
            margin-bottom: 100px !important;
        }
    }

    /* ===========================
   DARK MODE THEME SYSTEM
   =========================== */

    /* 1) Variabel Bootstrap untuk dark:
   - Body sangat gelap
   - Card/kontainer sedikit lebih terang (depth)
*/
    [data-bs-theme="dark"] {
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
        /* bg elemen nested/input */
    }

    /* 2) Body full dark */
    [data-bs-theme="dark"] body {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        /* akan ditimpa rule teks kuning di bawah */
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

    /* 4) Global: mayoritas teks KUNING di dark */
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

    /* 5) Pengecualian: komponen UI tetap pakai body-color (bukan kuning) agar readable */
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

    /* 7) Input, input-group, dsb. mengikuti palet dark (lebih gelap daripada card) */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: var(--bs-tertiary-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* 8) Utilitas teks sekunder */
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

    /* 10) Tombol aksen kuning */
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

    /* 12) Utilitas opt-in: paksa kuning pada elemen tertentu */
    [data-bs-theme="dark"] .tl-text-yellow-dark {
        color: var(--wk-accent-yellow) !important;
    }

    /* ==== Card refinement (ringan, aman untuk light & dark) ==== */
    /* Berlaku untuk: card bootstrap, soft-card custom, product-card, dan item list */
    .card,
    .soft-card,
    .product-card,
    .list-group-item {
        background-color: var(--bs-secondary-bg) !important;
        /* sedikit beda dari body */
        border: 1px solid var(--bs-border-color) !important;
        /* garis halus */
    }

    /* Hover/focus halus (opsional) */
    .card:hover,
    .soft-card:hover,
    .product-card:hover,
    .list-group-item:hover {
        box-shadow:
            0 6px 20px rgba(0, 0, 0, .12),
            0 2px 8px rgba(0, 0, 0, .06);
        border-color: rgba(245, 158, 11, .25);
        /* kuning lembut */
    }

    /* Jika mau border hanya saat DARK, pakai blok ini, dan hapus rule umum di atas:
[data-bs-theme="dark"] .card,
[data-bs-theme="dark"] .soft-card,
[data-bs-theme="dark"] .product-card,
[data-bs-theme="dark"] .list-group-item {
  background-color: var(--bs-secondary-bg) !important;
  border: 1px solid var(--bs-border-color) !important;
}
*/
</style>


<div class="android-shell">
    <div class="android-frame">
        <div class="android-body">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Pesanan</li>
                </ol>
            </nav>

            <div class="alert alert-warning border-0 soft-card p-3" role="alert">
                Akses halaman ini akan berakhir dalam <strong id="cd">--:--</strong>. Setelah waktu habis, halaman ini tidak dapat diakses kembali.
            </div>

            <div class="soft-card p-3">
                <h6 class="mb-3">Daftar Pesanan Anda</h6>

                <?php if (empty($orders)): ?>
                    <div class="text-center text-muted py-4">Belum ada pesanan.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($orders as $o): ?>
                            <div class="list-group-item">
                                <div class="order-row">
                                    <div class="col-left">
                                        <div class="fw-semibold mono"><?= esc($o['kode_pesanan']) ?></div>
                                        <div class="small muted">
                                            <?= esc(waktu_indonesia($o['tgl'])) ?> <br> • <?= (int)($o['item_count'] ?? 0) ?> item • <?= (int)($o['total_qty'] ?? 0) ?> porsi
                                        </div>

                                        <?php if (!empty($o['produk_list'])): ?>
                                            <ul class="produk-lines">
                                                <?php foreach ($o['produk_list'] as $pl): ?>
                                                    <li><?= esc($pl) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>

                                    <div class="col-right">
                                        <div class="fw-semibold">Rp <?= number_format((float)$o['total'], 0, ',', '.') ?></div>
                                        <span class="badge-soft small"><?= esc($o['status']) ?></span>
                                    </div>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    </div>
                <?php endif; ?>

                <div class="d-grid mt-3">
                    <a href="<?= base_url('pelanggan/produk') ?>" class="btn btn-add-to-cart rounded-pill btn-block py-2 mb-2"><span><i class="bi bi-bag-plus"></i></span> Belanja Lagi</a>
                    <a href="<?= base_url('pelanggan/success') ?>" class="btn btn-outline-secondary rounded-pill btn-block py-2"><span><i class="fa-solid fa-angle-left"></i></span> Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<script>
    (function() {
        const cd = document.getElementById('cd');
        if (!cd) return;
        let remain = <?= (int)($remain ?? 0) ?>; // detik
        function pad(n) {
            return String(n).padStart(2, '0');
        }

        function tick() {
            if (remain <= 0) {
                cd.textContent = '00:00';
                // Optional UX: disable klik, lalu arahkan balik setelah 2 detik
                setTimeout(() => {
                    window.location.href = <?= json_encode(base_url('pelanggan/produk')) ?>;
                }, 2000);
                return;
            }
            const m = Math.floor(remain / 60),
                s = remain % 60;
            cd.textContent = pad(m) + ':' + pad(s);
            remain--;
            setTimeout(tick, 1000);
        }
        tick();
    })();
</script>
<?= $this->endSection() ?>
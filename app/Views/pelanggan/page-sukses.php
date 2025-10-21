<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>

<style>
    /* ====== MOBILE-IN-DESKTOP SHELL (simulate Android on wide screens) ====== */
    .android-shell {
        display: grid;
        place-content: start center;
        min-height: 100%;
        padding: 16px 12px 40px;
        background:
            radial-gradient(1200px 800px at 50% -200px, rgba(0, 0, 0, .05), transparent 60%),
            linear-gradient(180deg, rgba(0, 0, 0, .03), transparent 55%);
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

    /* Breadcrumb → compact & single-line inside frame */
    .breadcrumb {
        margin-bottom: 8px;
        font-size: .9rem;
    }

    .breadcrumb .breadcrumb-item+.breadcrumb-item::before {
        padding-right: .35rem;
    }

    /* Responsive rules:
     - On phones: frame fills width (<=430px)
     - On tablets/PC: centered, tetap 430px (rasa Android) */
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
<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>

<style>
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
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08), 0 2px 8px rgba(0, 0, 0, .05);
        overflow: hidden
    }

    .android-body {
        padding: 16px
    }

    .soft-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08)
    }

    .order-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center
    }

    .muted {
        color: #6c757d
    }

    .mono {
        font-feature-settings: "tnum" 1, "cv01" 1;
        letter-spacing: .3px
    }

    .badge-soft {
        background: rgba(0, 0, 0, .06);
        border-radius: 10px;
        padding: .25rem .5rem
    }

    .btn-block {
        width: 100%
    }

    /* Selalu pakai layout "mobile" di semua device */
    .android-shell {
        min-height: 100vh;
        /* full-height biar center rapi */
        display: grid;
        place-content: start center;
        padding: 16px 12px 40px;
    }

    .android-frame {
        width: 100%;
        max-width: 430px;
        /* kunci lebar “mobile” */
        margin: 0 auto;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08), 0 2px 8px rgba(0, 0, 0, .05);
        overflow: hidden;
    }

    /* Daftar (riwayat / produk) selalu vertikal */
    .list-group {
        display: flex;
        flex-direction: column;
        /* tumpuk ke bawah */
        gap: 8px;
        /* jarak antar item */
    }

    .list-group-item {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .06);
        padding: 12px 14px;
    }

    /* Baris ringkasan pesanan tetap dua kolom, tapi boleh wrap jika panjang */
    .order-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        /* biar baris turun kalau kepanjangan */
    }

    /* Nama produk panjang → rapi satu baris + tooltip */
    .text-truncate {
        max-width: 100%;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }

    /* Tombol full width */
    .btn-block {
        width: 100%;
    }

    /* (Opsional) di layar besar, tambah napas dikit tapi tetap “mobile look” */
    @media (min-width: 768px) {
        .android-shell {
            padding: 24px 0 60px;
        }

        .list-group {
            gap: 10px;
        }
    }

    /* List produk per baris */
    .produk-lines {
        margin: 6px 0 0;
        padding-left: 1.1rem;
        /* bullet standar, rapi */
    }

    .produk-lines li {
        line-height: 1.25rem;
        margin: 2px 0;
        white-space: nowrap;
        /* tetap satu baris */
        overflow: hidden;
        text-overflow: ellipsis;
        /* kalau kepanjangan kasih ... */
    }

    .order-row .col-left {
        flex: 1 1 auto;
        min-width: 0;
    }

    .order-row .col-right {
        text-align: right;
        min-width: 140px;
    }

    .order-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .order-row .col-left {
        flex: 1 1 auto;
        min-width: 0;
    }

    .order-row .col-right {
        margin-left: auto;
        /* dorong ke paling kanan */
        text-align: right;
        min-width: 160px;
        /* jaga lebar kanan */
    }

    .list-group-item {
        padding: 12px 18px;
    }

    /* tambah padding kanan biar mepet frame */
    .price {
        font-feature-settings: "tnum" 1;
        /* angka rata */
        letter-spacing: .2px;
        display: block;
    }
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
                                            <?= esc($o['tgl']) ?> • <?= (int)($o['item_count'] ?? 0) ?> item • <?= (int)($o['total_qty'] ?? 0) ?> porsi
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
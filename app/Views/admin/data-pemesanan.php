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

    <div class="d-flex w-100 mb-3">
        <a href="<?= base_url('admin/pemesanan/tambah') ?>" class="btn btn-theme rounded-pill py-2 ms-auto"><span><i class="fa-solid fa-file-circle-plus"></i></span> Tambah</a>
    </div>

    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards mb-4">
        <!-- Card Data Akun -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body" id="filterCard">
                    <form method="get" action="<?= current_url() ?>" class="row g-2 align-items-end mb-3">
                        <div class="col-md-6">
                            <label class="form-label mb-0">Keyword</label>
                            <input type="text" name="keyword" value="<?= esc($keyword) ?>" placeholder="Cari nama / produk..." class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label mb-0">Kategori</label>
                            <select name="kategori" class="form-select">
                                <option value="">Semua</option>
                                <?php if (!empty($kategori_list)): ?>
                                    <?php foreach ($kategori_list as $k): ?>
                                        <option value="<?= esc($k) ?>" <?= $selected_kategori === $k ? 'selected' : '' ?>>
                                            <?= esc($k) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-3 d-grid d-md-flex gap-2">
                            <button type="submit" class="btn btn-dark">
                                <i class="fa-solid fa-magnifying-glass"></i> Cari
                            </button>
                            <a href="<?= current_url() ?>" class="btn btn-outline-secondary">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset
                            </a>
                        </div>
                    </form>

                </div>

                <div class="card-body" id="tableCard">
                    <?php if (!empty($rows) && is_array($rows)): ?>
                        <div class="table-responsive">
                            <div class="table-responsive shadow-sm rounded-4">
                                <table class="table align-middle mb-0" id="tablePesanan">
                                    <thead class="table-light text-center">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Produk</th>
                                            <th>Kuantitas</th>
                                            <th>Harga Satuan</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-dark">
                                        <?php if (!empty($rows) && is_array($rows)): ?>
                                            <?php $no = 1; ?>
                                            <?php foreach ($rows as $row): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td class="text-start">
                                                        <div class="d-flex align-items-center gap-2">
                                                            <img src="<?= base_url('assets/uploads/produk/' . ($row['gambar'] ?? 'no-image.png')) ?>"
                                                                alt="Produk"
                                                                class="rounded-3 border"
                                                                style="width: 45px; height: 45px; object-fit: cover;">
                                                            <div>
                                                                <span class="fw-medium"><?= esc($row['nama_produk']) ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($row['qty']) ?></td>
                                                    <td class="text-end">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                                    <td class="text-end fw-bold text-success">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fa-regular fa-box-open fa-lg mb-2"></i><br>
                                                    Belum ada data pesanan.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    <?php else: ?>
                        <div class="text-center py-4">
                            <img src="<?= base_url('assets/img/box.png') ?>" alt="IMG-Waroeng-Kami" class="mb-2" width="100">
                            <h6>Ups, tidak ada produk yang tersedia!</h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
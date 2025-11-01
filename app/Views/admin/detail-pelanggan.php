<?= $this->extend('templates/admin/main') ?>
<?= $this->section('admin_content') ?>

<style>
    /* ========= Base Light ========= */
    :root {
        --page-bg: #ffffff;
        --text: #111315;
        --muted: #6b7280;
        --card-bg: #ffffff;
        --border: #e7e9ee;
        --shadow-1: 0 6px 18px rgba(16, 24, 40, .06), 0 1px 2px rgba(16, 24, 40, .04);
        --shadow-2: 0 14px 32px rgba(16, 24, 40, .10), 0 3px 6px rgba(16, 24, 40, .06);
        --radius: 16px;
        --accent-primary: #3b82f6;
        --accent-success: #22c55e;
        --accent-warning: #f59e0b;
        --accent-danger: #ef4444;
    }

    /* ========= Base Dark ========= */
    @media (prefers-color-scheme: dark) {
        :root {
            --page-bg: #0f172a;
            --text: #f1f5f9;
            --muted: #94a3b8;
            --card-bg: #1e293b;
            --border: #334155;
            --shadow-1: 0 6px 18px rgba(0, 0, 0, .4);
            --shadow-2: 0 14px 32px rgba(0, 0, 0, .6);
        }

        .table {
            color: var(--text);
        }

        .table thead {
            background: #273549;
        }

        .breadcrumb-item.active {
            color: var(--muted);
        }
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
        color: var(--text) !important;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow-1);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    .card.dash-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-2);
        border-color: rgba(59, 130, 246, .35);
    }

    .card.dash-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 6px;
        opacity: .95;
        background: linear-gradient(180deg, var(--_accent), rgba(0, 0, 0, 0));
    }

    .card.dash-card.bg-warning {
        --_accent: var(--accent-warning);
    }

    .card-body {
        padding: 1.5rem;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
    }

    .detail-header h5 {
        font-weight: 700;
    }

    .btn-theme {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border: none;
        transition: 0.3s;
    }

    .btn-theme:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
    }

    .table th {
        background-color: var(--card-bg);
        color: var(--text);
        border-color: var(--border);
    }

    .table td {
        border-color: var(--border);
    }

    .info-list li {
        margin-bottom: .25rem;
        font-size: .95rem;
    }

    .info-list strong {
        color: var(--muted);
        width: 160px;
        display: inline-block;
    }

    .card-footer a {
        color: var(--accent-primary);
        text-decoration: none;
    }

    .card-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-1"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <div class="d-flex w-100 mb-3">
        <a href="<?= base_url('admin/pelanggan') ?>" class="btn btn-theme rounded-pill py-2 ms-auto">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning">
                <div class="card-body">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Detail Pelanggan</h5>
                    </div>

                    <!-- Info pelanggan -->
                    <div class="row mb-4 info-list text-capitalize">
                        <div class="col-md-4 mb-2"><strong>Kode Pesanan:</strong> <?= esc($pelanggan['kode_pesanan']) ?></div>
                        <div class="col-md-4 mb-2"><strong>Nama Pelanggan:</strong> <?= esc($pelanggan['nama_pelanggan']) ?></div>
                        <div class="col-md-4 mb-2"><strong>Alamat:</strong> <?= esc($pelanggan['alamat'] ?? '-') ?></div>
                        <div class="col-md-4 mb-2"><strong>Makan di Tempat:</strong> <?= $pelanggan['makan_ditempat'] ? 'Ya' : 'Tidak' ?></div>
                        <div class="col-md-4 mb-2"><strong>Nomor Meja:</strong> <?= esc($pelanggan['meja_id'] ?? '-') ?></div>
                        <div class="col-md-4 mb-2"><strong>Total Pembelian:</strong> Rp <?= number_format($pelanggan['total'] ?? 0, 0, ',', '.') ?></div>
                        <div class="col-md-4 mb-2"><strong>Status:</strong>
                            <span class="badge bg-<?= $pelanggan['status'] == 'selesai' ? 'success' : ($pelanggan['status'] == 'proses' ? 'warning text-dark' : 'secondary') ?>">
                                <?= ucfirst($pelanggan['status']) ?>
                            </span>
                        </div>
                    </div>

                    <hr>

                    <!-- Daftar Produk -->
                    <h6 class="fw-semibold mb-3 text-uppercase">Daftar Produk</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Jumlah</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($produkDetail)): ?>
                                    <?php $no = 1;
                                    foreach ($produkDetail as $prod): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= esc($prod['nama_produk']) ?></td>
                                            <td class="text-center"><?= esc($prod['jumlah']) ?></td>
                                            <td class="text-end">Rp <?= number_format($prod['harga'], 0, ',', '.') ?></td>
                                            <td class="text-end fw-semibold">Rp <?= number_format($prod['subtotal'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- Footer total -->
                                    <tr class="fw-bold bg-light">
                                        <td colspan="2" class="text-end">Total</td>
                                        <td class="text-center"><?= $total_qty ?? 0 ?></td>
                                        <td></td>
                                        <td class="text-end">Rp <?= number_format($total_harga ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Tidak ada produk pada pesanan ini.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
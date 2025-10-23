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

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>

    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards">
        <!-- Pelanggan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-card bg-primary position-relative h-100">
                <div class="card-body">
                    <div class="fw-semibold">Data Pelanggan</div>
                    <div class="fs-2">
                        <?= esc(number_format((int)($stats['pelanggan'] ?? 0))) ?>
                    </div>
                    <?php if (isset($stats['pelangganBulan'])): ?>
                        <div class="small">+ <?= esc(number_format((int)$stats['pelangganBulan'])) ?> bulan ini</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Produk -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body">
                    <div class="fw-semibold">Jumlah Produk</div>
                    <div class="fs-2">
                        <?= esc(number_format((int)($stats['produk'] ?? 0))) ?>
                    </div>
                    <div class="small">Produk aktif di katalog</div>
                </div>
            </div>
        </div>

        <!-- Paling Laris -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-card bg-success position-relative h-100">
                <div class="card-body">
                    <div class="fw-semibold">Paling Laris (<?= esc($stats['bulanLabel'] ?? 'bulan ini') ?>)</div>
                    <div class="fs-6 fw-bold mb-1"><?= esc($stats['palingLaris']['nama'] ?? '—') ?></div>
                    <div class="small"><?= esc((int)($stats['palingLaris']['qty'] ?? 0)) ?> terjual</div>
                </div>
            </div>
        </div>

        <!-- Omzet Bulan Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card dash-card bg-danger position-relative h-100">
                <div class="card-body">
                    <div class="fw-semibold">Omzet Bulan Ini</div>
                    <div class="fs-4"><?= esc($stats['omzetBulanFmt'] ?? 'Rp 0') ?></div>
                    <?php if (array_key_exists('growthPct', $stats) && $stats['growthPct'] !== null): ?>
                        <?php $up = ($stats['growthPct'] >= 0); ?>
                        <div class="small" style="opacity:.9">
                            <i class="fas fa-arrow-<?= $up ? 'up' : 'down' ?>"></i>
                            <?= number_format(abs((float)$stats['growthPct']), 1) ?>% vs bulan lalu
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('templates/admin/main') ?>
<?= $this->section('admin_content') ?>

<style>
    /* ======= Floating Card ======= */
    #cardAksiFloating {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        width: 280px;
        max-width: 90%;
        z-index: 1050;
        display: none;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
        border-radius: 12px;
        background-color: #ffffff;
        color: #111315;
    }

    #cardAksiFloating.dark {
        background-color: #1f2937;
        color: #f3f4f6;
    }

    #cardAksiFloating .card-body {
        padding: 1rem;
        position: relative;
    }

    #cardAksiFloating .btn-close-floating {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        cursor: pointer;
        background: transparent;
        border: none;
        font-size: 1.2rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #cardAksiFloating {
            width: 90%;
            bottom: 0.5rem;
            right: 0.5rem;
        }
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-1"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <div class="row dashboard-cards">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body" id="filterCard">
                    <form method="get" action="<?= current_url() ?>" class="row g-2 align-items-end mb-3">
                        <div class="col-md-6">
                            <label class="form-label mb-0">Keyword</label>
                            <input type="text" name="keyword" value="<?= esc($keyword) ?>" placeholder="Cari nama pelanggan..." class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label mb-0">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Semua</option>
                                <?php if (!empty($status_list)): ?>
                                    <?php foreach ($status_list as $s): ?>
                                        <option value="<?= esc($s) ?>" <?= $selected_status === $s ? 'selected' : '' ?>>
                                            <?= ucfirst(esc($s)) ?>
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
                        <div class="table-responsive rounded-4 shadow-sm">
                            <table id="tablePelanggan" class="table mb-0 text-capitalize align-middle border rounded-4">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Pesanan</th>
                                        <th>Nama Pelanggan</th>
                                        <th>Alamat</th>
                                        <th>Makan di Tempat</th>
                                        <th>Nomor Meja</th>
                                        <th>Total Pembelian</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php $no = 1;
                                    foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td class="fw-semibold text-primary"><?= esc($row['kode_pesanan']) ?></td>
                                            <td><?= esc($row['nama_pelanggan']) ?></td>
                                            <td><?= esc($row['alamat'] ?? '-') ?></td>
                                            <td>
                                                <?php if ($row['makan_ditempat'] == 1): ?>
                                                    <span class="badge bg-success">Ya</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Tidak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= esc($row['meja_id'] ?? '-') ?></td>
                                            <td class="text-end fw-bold">Rp <?= number_format($row['total'] ?? 0, 0, ',', '.') ?></td>
                                            <td>
                                                <?php $status = strtolower($row['status'] ?? ''); ?>
                                                <span class="badge bg-<?= $status === 'selesai' ? 'success' : ($status === 'proses' ? 'warning text-dark' : ($status === 'batal' ? 'danger' : 'secondary')) ?>">
                                                    <?= ucfirst($status) ?>
                                                </span>
                                            </td>
                                            <td class="text-nowrap">
                                                <!-- Tombol Detail -->
                                                <a href="<?= site_url('admin/pelanggan/detail/' . esc($row['kode_pesanan'])) ?>" class="btn btn-sm btn-theme rounded-pill px-3 me-2">
                                                    <i class="fa-solid fa-eye"></i> Lihat
                                                </a>

                                                <!-- Tombol Hapus -->
                                                <a href="javascript:void(0)" onclick="confirmDeletePelanggan('<?= $row['id_pesanan'] ?>')" class="btn btn-sm btn-danger rounded-pill px-3 me-2" title="Hapus">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>

                                                <!-- Tombol Gear (Floating Card) -->
                                                <button type="button" class="btn btn-secondary btn-gear rounded-pill py-2" data-kode="<?= esc($row['kode_pesanan']) ?>">
                                                    <i class="fa-solid fa-gear"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <img src="<?= base_url('assets/img/box.png') ?>" alt="Empty" class="mb-2" width="100">
                            <h6>Ups, tidak ada pesanan yang tersedia!</h6>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Card -->
<div id="cardAksiFloating" class="card shadow-lg rounded-4">
    <button id="closeFloatingCard" class="btn-close-floating">&times;</button>
    <div class="card-body text-center" id="cardContentFloating"></div>
</div>

<!-- CSRF Meta -->
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<meta name="csrf-name" content="<?= csrf_token() ?>">

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $(function() {
        let selectedId = null;
        const csrfName = $('meta[name="csrf-name"]').attr('content');
        const csrfHash = $('meta[name="csrf-token"]').attr('content');

        $('.btn-gear').on('click', function(e) {
            e.stopPropagation();
            const kode = $(this).data('kode');
            if (selectedId === kode) {
                $('#cardAksiFloating').fadeOut();
                selectedId = null;
                return;
            }
            selectedId = kode;

            // Generate tombol aksi + CSRF
            let html = '';
            ['baru', 'selesai', 'batal'].forEach(s => {
                html += `<form action="/admin/pelanggan/status" method="post" class="d-inline-block m-1">
                        <input type="hidden" name="${csrfName}" value="${csrfHash}">
                        <input type="hidden" name="kode_pesanan" value="${kode}">
                        <input type="hidden" name="status" value="${s}">
                        <button type="submit" class="btn ${s==='batal'?'btn-danger':'btn-theme'}">${s.charAt(0).toUpperCase()+s.slice(1)}</button>
                    </form>`;
            });
            html += `<hr><a href="/admin/pelanggan/cetak/${kode}" target="_blank" class="btn btn-success mt-2"><i class="fa-solid fa-clipboard"></i> Struk</a>`;

            $('#cardContentFloating').html(html);

            // Dark mode support
            if ($('body').hasClass('dark')) $('#cardAksiFloating').addClass('dark');
            else $('#cardAksiFloating').removeClass('dark');

            $('#cardAksiFloating').fadeIn();
        });

        $('#closeFloatingCard').on('click', function() {
            $('#cardAksiFloating').fadeOut();
            selectedId = null;
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#cardAksiFloating, .btn-gear').length) {
                $('#cardAksiFloating').fadeOut();
                selectedId = null;
            }
        });
    });
</script>
<?= $this->endSection() ?>
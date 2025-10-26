<!DOCTYPE html>
<html lang="en">

<!-- header -->
<?= $this->include('templates/admin/header') ?>

<body class="sb-nav-fixed">
    <?php
    $flashSuccess = session()->getFlashdata('success') ?? null;
    $flashError   = session()->getFlashdata('error')   ?? null;
    $flashWarn    = session()->getFlashdata('warning') ?? null;
    ?>

    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <!-- Navbar Brand-->
        <a class="navbar-brand d-flex flex-column align-items-center text-center" href="<?= base_url('/') ?>">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="IMG-Waroeng-Kami" class="logo-brand-admin d-block">
            <span class="brand-panel-label text-warning">PANEL</span>
        </a>


        <!-- Sidebar Toggle-->
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" type="button">
            <i class="fas fa-bars"></i>
        </button>
        <!-- Navbar-->
        <ul class="navbar-nav ms-auto me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle p-0" href="#" id="navbarDropdown"
                    role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?= base_url('assets/uploads/avatars/' . esc($admin['avatar'])) ?>"
                        alt="User" class="logo-admin-show rounded-circle">
                </a>

                <ul class="dropdown-menu dropdown-menu-end wk-dropdown" aria-labelledby="navbarDropdown">
                    <li class="dropdown-header">Account</li>
                    <li>
                        <a class="dropdown-item <?= ($nav_link === 'Profile') ? 'active' : '' ?>" href="<?= base_url('admin/profile') ?>">
                            <span class="item-icon"><i class="fas fa-cog"></i></span>
                            <span class="item-label">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item <?= ($nav_link === 'Activity') ? 'active' : '' ?>" href="<?= base_url('admin/activity') ?>">
                            <span class="item-icon"><i class="fas fa-stream"></i></span>
                            <span class="item-label">Activity Log</span>
                            <!-- <span class="item-meta badge">12</span> -->
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <!-- 1) Trigger (ganti href jadi tombol modal) -->
                        <a class="dropdown-item text-danger" href="#"
                            data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <span class="item-icon"><i class="fas fa-sign-out-alt"></i></span>
                            <span class="item-label">Logout</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <!-- sidebar -->
        <?= $this->include('templates/admin/sidebar') ?>
        <div id="layoutSidenav_content">
            <!-- render content -->
            <?= $this->renderSection('admin_content') ?>

            <!-- footer -->
            <?= $this->include('templates/admin/footer') ?>
        </div>
    </div>

    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- datatables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- Buttons + JSZip for Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.1.1/js/buttons.html5.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
    <script>
        // sweet alert combine
        document.addEventListener('DOMContentLoaded', () => {
            const msgSuccess = <?= json_encode($flashSuccess) ?>;
            const msgError = <?= json_encode($flashError) ?>;
            const msgWarn = <?= json_encode($flashWarn) ?>;

            if (!msgSuccess && !msgError && !msgWarn) return;

            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: t => {
                    t.onmouseenter = Swal.stopTimer;
                    t.onmouseleave = Swal.resumeTimer;
                }
            });

            if (msgSuccess) Toast.fire({
                icon: "success",
                title: msgSuccess
            });
            if (msgError) Toast.fire({
                icon: "error",
                title: msgError
            });
            if (msgWarn) Toast.fire({
                icon: "warning",
                title: msgWarn
            });
        });

        // togle mode dark or light
        (function() {
            var saved = localStorage.getItem('theme');
            var root = document.documentElement;
            var body = document.body;

            // fallback ke prefers-color-scheme kalau belum ada preferensi
            if (!saved) {
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                saved = prefersDark ? 'dark' : 'light';
            }

            applyTheme(saved);
            requestAnimationFrame(updateToggleLabel);

            // event klik
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('themeToggle');
                if (btn) btn.addEventListener('click', function() {
                    var now = (document.body.classList.contains('dark') || root.classList.contains('theme-dark')) ? 'light' : 'dark';
                    applyTheme(now);
                    localStorage.setItem('theme', now);
                    updateToggleLabel();
                });
            });

            function applyTheme(mode) {
                var isDark = mode === 'dark';
                // kompatibel: body.dark & html.theme-dark
                body.classList.toggle('dark', isDark);
                root.classList.toggle('theme-dark', isDark);
                // optional: untuk debugging/styling selektor attr
                root.setAttribute('data-theme', isDark ? 'dark' : 'light');
            }

            function updateToggleLabel() {
                var btn = document.getElementById('themeToggle');
                if (!btn) return;
                var isDark = document.body.classList.contains('dark') || document.documentElement.classList.contains('theme-dark');
                btn.querySelector('.icon').textContent = isDark ? '☀️' : '🌙';
                btn.querySelector('.label').textContent = isDark ? 'Light' : 'Dark';
                btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                btn.setAttribute('title', isDark ? 'Switch to Light' : 'Switch to Dark');
            }
        })();

        // modal logout
        (function() {
            const form = document.getElementById('logoutForm');
            const btn = document.getElementById('logoutConfirmBtn');
            const spin = document.getElementById('logoutSpinner');
            const txt = document.getElementById('logoutBtnText');

            if (!form) return;

            form.addEventListener('submit', function() {
                btn.disabled = true;
                spin.classList.remove('d-none');
                txt.textContent = 'Keluar...';
            }, {
                once: true
            });
        })();

        // datatables
        $(function() {
            const dt = $('#tableActivity').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 20, 50],
                order: [
                    [1, 'asc']
                ], // sort default: Nama

                columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    }, // No
                    {
                        targets: 5,
                        orderable: false,
                        searchable: false
                    } // Aksi
                    // Tidak ada render khusus untuk Status
                ],

                dom: "<'row mb-2'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-2'<'col-sm-5'i><'col-sm-7'p>>",

                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel me-1"></i> Excel',
                    className: 'btn btn-success',
                    title: 'Activity Log - Waroeng Kami',
                    filename: 'activity_log_' + new Date().toISOString().slice(0, 10).replace(/-/g, ''),
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4], // exclude Aksi
                        format: {
                            body: function(data) {
                                return $('<div>').html(data).text().trim(); // strip HTML
                            }
                        }
                    }
                }],

                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Tidak ada data",
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "›",
                        previous: "‹"
                    }
                }
            });

            // Auto-number kolom No
            dt.on('order.dt search.dt draw.dt', function() {
                let i = 1;
                dt.column(0, {
                        search: 'applied',
                        order: 'applied',
                        page: 'current'
                    })
                    .nodes().each(function(cell) {
                        cell.innerHTML = i++ + '.';
                    });
            }).draw();

            // Keyword filter
            function applyKeyword() {
                const kw = $('#fKeyword').val().trim();
                dt.search(kw).draw();
            }
            $('#btnSearch').on('click', applyKeyword);
            $('#fKeyword').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    applyKeyword();
                }
            });

            // Status exact match filter
            $('#fStatus').on('change', function() {
                const v = $(this).val();
                dt.column(4).search(v ? '^' + $.fn.dataTable.util.escapeRegex(v) + '$' : '', true, false).draw();
            });

            // Reset filter
            $('#btnReset').on('click', function() {
                $('#fKeyword').val('');
                $('#fStatus').val('');
                dt.search('');
                dt.columns().search('');
                dt.draw();
            });
        });
    </script>

    <!-- letakkan di akhir <body> -->
    <button id="themeToggle" class="theme-fab " type="button" aria-label="Toggle theme">
        <span class="icon sun" aria-hidden="true">☀️</span>
        <span class="icon moon" aria-hidden="true">🌙</span>
    </button>
    <!-- 2) Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3 
    bg-light text-dark dark-mode:bg-dark dark-mode:text-light">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width:44px;height:44px;background:rgba(220,53,69,.1);">
                                <i class="fas fa-sign-out-alt text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fs-6">Yakin mau keluar?</div>
                            <div class="text-secondary small">Kamu akan dibawa ke halaman login.</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light dark-mode:btn-dark" data-bs-dismiss="modal">Batal</button>

                    <form id="logoutForm" action="<?= site_url('auth/logout') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger" id="logoutConfirmBtn">
                            <span class="me-2" id="logoutBtnText">Logout</span>
                            <span class="spinner-border spinner-border-sm d-none" id="logoutSpinner" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
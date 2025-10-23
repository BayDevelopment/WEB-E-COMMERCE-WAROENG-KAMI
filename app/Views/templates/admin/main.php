<!DOCTYPE html>
<html lang="en">

<!-- header -->
<?= $this->include('templates/admin/header') ?>

<body class="sb-nav-fixed">
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
                    <img src="<?= base_url('assets/img/boy.png') ?>"
                        alt="User" class="logo-admin-show rounded-circle">
                </a>

                <ul class="dropdown-menu dropdown-menu-end wk-dropdown" aria-labelledby="navbarDropdown">
                    <li class="dropdown-header">Account</li>
                    <li>
                        <a class="dropdown-item" href="#!">
                            <span class="item-icon"><i class="fas fa-cog"></i></span>
                            <span class="item-label">Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#!">
                            <span class="item-icon"><i class="fas fa-stream"></i></span>
                            <span class="item-label">Activity Log</span>
                            <!-- <span class="item-meta badge">12</span> -->
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="#!">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
    <script>
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
    </script>

    <!-- letakkan di akhir <body> -->
    <button id="themeToggle" class="theme-fab" type="button" aria-label="Toggle theme">
        <span class="icon sun" aria-hidden="true">☀️</span>
        <span class="icon moon" aria-hidden="true">🌙</span>
    </button>
</body>

</html>
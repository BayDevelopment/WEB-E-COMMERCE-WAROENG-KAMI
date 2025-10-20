<!doctype html>
<html lang="id">

<?= $this->include('templates/pelanggan/header') ?>

<body id="public_content" class="wk-scope" data-bs-theme="light">
    <?php
    $flashSuccess = session()->getFlashdata('success') ?? null;
    $flashError   = session()->getFlashdata('error')   ?? null;
    $flashWarn    = session()->getFlashdata('warning') ?? null;
    ?>

    <div class="app-shell">
        <header>
            <nav class="navbar bg-native">
                <div class="container">
                    <!-- Brand -->
                    <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="logo-size-navbar me-2">
                    </a>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="switchCheckDefault"><i class="fa-solid fa-moon"></i></label>
                        <input class="form-check-input" type="checkbox" role="switch" id="switchCheckDefault">
                    </div>
                </div>
            </nav>
        </header>

        <main class="container py-4">
            <?= $this->renderSection('public_content') ?>
        </main>

        <!-- Bottom Navbar ala aplikasi -->
        <!-- Footer: Bottom Navbar Mobile -->
        <?= $this->renderSection('scripts') ?>
        <?= $this->include('templates/pelanggan/footer') ?>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        // dark mode
        document.addEventListener('DOMContentLoaded', function() {
            const bodyEl = document.body;
            const switchInput = document.getElementById('switchCheckDefault');

            const pref = localStorage.getItem('wk_scope_theme');
            const preferSystemDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDarkInit = pref ? (pref === 'dark') : preferSystemDark;

            applyTheme(isDarkInit);
            if (switchInput) switchInput.checked = isDarkInit;

            if (switchInput) {
                switchInput.addEventListener('change', function() {
                    const on = this.checked;
                    applyTheme(on);
                    localStorage.setItem('wk_scope_theme', on ? 'dark' : 'light');
                });
            }

            function applyTheme(isDark) {
                bodyEl.classList.toggle('is-dark', isDark);
                bodyEl.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
                /* >>> penting: ubah background root (html) juga <<< */
                document.documentElement.style.backgroundColor = isDark ? '#0b0f17' : '#ffffff';
            }
        });
    </script>
</body>

</html>
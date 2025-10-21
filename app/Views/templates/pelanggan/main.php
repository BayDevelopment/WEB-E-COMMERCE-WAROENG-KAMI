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
                    <div class="form-check form-switch theme-switch">
                        <label class="form-check-label" for="switchCheckDefault" role="switch" aria-checked="false" tabindex="0">
                            <i id="themeIcon" class="fa-solid fa-cloud-sun"></i>
                            <i id="themeStar" class="fa-solid fa-star ms-1 d-none" style="font-size:.75em;"></i>
                        </label>
                        <input class="form-check-input visually-hidden" type="checkbox" role="switch" id="switchCheckDefault">
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
            const icon = document.getElementById('themeIcon');
            const star = document.getElementById('themeStar');

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

            function setIcon(isDark) {
                if (!icon) return;
                if (isDark) {
                    // Bulan + bintang (tampilkan star kecil)
                    icon.className = 'fa-solid fa-moon';
                    if (star) star.classList.remove('d-none');
                    icon.setAttribute('aria-label', 'Mode Gelap');
                    icon.setAttribute('title', 'Mode Gelap');
                } else {
                    // Matahari + awan (sembunyikan star)
                    icon.className = 'fa-solid fa-cloud-sun';
                    if (star) star.classList.add('d-none');
                    icon.setAttribute('aria-label', 'Mode Terang');
                    icon.setAttribute('title', 'Mode Terang');
                }
            }

            function applyTheme(isDark) {
                bodyEl.classList.toggle('is-dark', isDark);
                bodyEl.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
                // >>> penting: ubah background root (html) juga <<<
                document.documentElement.style.backgroundColor = isDark ? '#0b0f17' : '#ffffff';
                // set ikon sesuai tema
                setIcon(isDark);
            }
        });

        // loading
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('pageLoader');

            // ---- Fade-in konten saat page siap ----
            // Tambahkan class pf-ready setelah layout stabil sedikit
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    document.body.classList.add('pf-ready');
                });
            });

            // ---- Loader helpers (fade) ----
            function showLoader() {
                if (!loader) return;
                loader.hidden = false; // tampilkan elemen
                // next frame → apply class supaya CSS transition jalan
                requestAnimationFrame(() => {
                    loader.classList.remove('is-hiding');
                    loader.classList.add('is-visible');
                });
                document.body.style.pointerEvents = 'none';
            }

            function hideLoader() {
                if (!loader) return;
                loader.classList.remove('is-visible');
                loader.classList.add('is-hiding');
                // setelah transisi, benar2 sembunyikan
                const done = () => {
                    loader.hidden = true;
                    loader.classList.remove('is-hiding');
                    loader.removeEventListener('transitionend', done);
                    document.body.style.pointerEvents = '';
                };
                // fallback timeout biar aman
                loader.addEventListener('transitionend', done, {
                    once: true
                });
                setTimeout(done, 250);
            }

            // Tampilkan saat form submit
            document.addEventListener('submit', (e) => {
                const form = e.target;
                if (form.matches('form')) showLoader();
            }, true);

            // Tampilkan saat klik link internal (kecuali yang dikecualikan)
            document.addEventListener('click', (e) => {
                const a = e.target.closest('a');
                if (!a) return;
                if (
                    a.target === '_blank' ||
                    a.hasAttribute('download') ||
                    a.getAttribute('href')?.startsWith('#') ||
                    a.dataset.noLoader === 'true'
                ) return;
                try {
                    const url = new URL(a.href, location.href);
                    if (url.origin !== location.origin) return; // external skip
                } catch {
                    return;
                }
                showLoader();
            });

            // Saat benar2 meninggalkan halaman
            window.addEventListener('beforeunload', showLoader);

            // Bila kembali dari cache (back/forward), sembunyikan loader cepat
            window.addEventListener('pageshow', (evt) => {
                if (evt.persisted) hideLoader();
            });

            // Expose opsional
            window.AppLoader = {
                show: showLoader,
                hide: hideLoader
            };
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= esc($title) ?></title>
    <link href="<?= base_url('assets/css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
</head>
<!-- CSS tambahan -->
<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

    body {
        font-family: "Poppins", sans-serif;
    }

    /* Bikin gambar kiri full-cover, rapi di layar besar */
    .login-hero {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /* Optional efek gelap tipis biar kontras */
        position: relative;
    }

    .login-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, .08);
        /* tipis aja */
    }

    /* Sedikit polishing input fokus */
    .form-control:focus {
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        border-color: #86b7fe;
    }

    /* efek fade untuk semua elemen yang dikasih class .fade-in */
    .fade-in {
        opacity: 0;
        transform: translateY(10px);
        animation: fadeInUp 0.8s ease-out forwards;
    }

    /* keyframes: naik dikit sambil muncul */
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }

        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* bisa juga buat background image (lebih halus di kiri) */
    .fade-bg {
        animation: fadeBg 1s ease-out forwards;
        opacity: 0;
    }

    @keyframes fadeBg {
        0% {
            opacity: 0;
            transform: scale(1.02);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .form-blocker {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, .6);
        backdrop-filter: blur(1px);
        display: none;
        border-radius: .75rem;
    }

    .form-blocker.show {
        display: block;
    }

    .pos-rel {
        position: relative;
    }
</style>

<body class="bg-primary">
    <?php
    $s = session();
    $flashSuccess = $s->getFlashdata('success');
    $flashError   = $s->getFlashdata('error');
    $flashWarn    = null; // gak dipakai
    ?>
    <!-- Layout Login Responsive: kiri gambar (hanya lg+), kanan form -->
    <div id="layoutAuthentication" class="bg-light">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container-fluid p-0">
                    <div class="row g-0 min-vh-100">

                        <!-- KIRI: Gambar (disembunyikan di <lg) -->
                        <div class="col-lg-7 d-none d-lg-flex p-0">
                            <div class="login-hero w-100 h-100"
                                style="background-image:url('<?= base_url('assets/img/side-img.jpg') ?>');"
                                role="img" aria-label="IMG-Waroeng-Kami">
                            </div>
                        </div>

                        <!-- KANAN: Form -->
                        <div class="col-12 col-lg-5 d-flex align-items-center justify-content-center p-4 p-lg-5 fade-bg">
                            <div class="w-100" style="max-width: 440px;">
                                <div class="card shadow-lg border-0 rounded-4">
                                    <div class="card-header bg-white border-0 pt-4">
                                        <h3 class="text-center fw-semibold mb-1">Login</h3>
                                        <p class="text-center text-muted mb-0 small">Masuk ke dashboard kamu</p>
                                    </div>
                                    <div class="card-body p-4">
                                        <?php $errors = session('errors') ?? []; ?>
                                        <!-- FORM kamu (boleh pakai yang ada) -->
                                        <form id="loginForm" action="<?= site_url('auth/login') ?>" method="post" novalidate>
                                            <?= csrf_field() ?>

                                            <div class="form-floating mb-3">
                                                <input
                                                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                                    id="inputEmail"
                                                    name="email"
                                                    type="email"
                                                    placeholder="name@example.com"
                                                    value="<?= old('email') ?>"
                                                    autocomplete="email"
                                                    required />
                                                <label for="inputEmail">Email address</label>
                                                <?php if (isset($errors['email'])): ?>
                                                    <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input
                                                    class="form-control <?= isset($errors['password_hash']) ? 'is-invalid' : '' ?>"
                                                    id="inputPassword"
                                                    name="password_hash"
                                                    type="password"
                                                    placeholder="Password"
                                                    autocomplete="current-password"
                                                    required />
                                                <label for="inputPassword">Password</label>
                                                <?php if (isset($errors['password_hash'])): ?>
                                                    <div class="invalid-feedback"><?= esc($errors['password_hash']) ?></div>
                                                <?php endif; ?>
                                            </div>

                                            <div class="d-grid mt-3">
                                                <button id="btnSubmit" type="submit" class="btn btn-warning py-2 fw-semibold rounded-pill py-lg-2">
                                                    Login
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>

                                <!-- Footer kecil (opsional) -->
                                <div class="text-center text-muted small mt-4">
                                    Copyright &copy; Waroeng Kami 2025
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/js/scripts.js') ?>"></script>
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

        (function() {
            const form = document.getElementById('loginForm');
            const btn = document.getElementById('btnSubmit');

            // bikin overlay blocker di sekitar form (opsional)
            // kasih class .pos-rel ke wrapper card kamu kalau mau overlay nempel rapi
            let blocker;

            function ensureBlocker() {
                if (blocker) return;
                blocker = document.createElement('div');
                blocker.className = 'form-blocker';
                // taruh di parent terdekat biar nutup area form
                (form.closest('.card') || form).classList.add('pos-rel');
                (form.closest('.card') || form).appendChild(blocker);
            }

            function cloneIntoHidden(field) {
                if (!field.name) return;
                if (field.type === 'file') return; // biarkan file tetap enabled kalau ada
                if (field.disabled) return;

                const addHidden = (name, value) => {
                    const h = document.createElement('input');
                    h.type = 'hidden';
                    h.name = name;
                    h.value = value;
                    form.appendChild(h);
                };

                if ((field.type === 'checkbox' || field.type === 'radio')) {
                    if (field.checked) addHidden(field.name, field.value || 'on');
                    return;
                }

                if (field.tagName === 'SELECT' && field.multiple) {
                    [...field.options].forEach(opt => {
                        if (opt.selected) addHidden(field.name, opt.value);
                    });
                    return;
                }

                addHidden(field.name, field.value ?? '');
            }

            function lockUI() {
                // ubah isi tombol → spinner + loading
                if (!btn.dataset.origHtml) btn.dataset.origHtml = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading...';
                btn.disabled = true;
                btn.setAttribute('aria-busy', 'true');

                // duplikasi semua input bernama → hidden, lalu disable aslinya
                const fields = form.querySelectorAll('input, select, textarea');
                fields.forEach(el => cloneIntoHidden(el));
                fields.forEach(el => {
                    if (el.type !== 'file') el.disabled = true;
                });

                // tampilkan overlay
                ensureBlocker();
                blocker.classList.add('show');
            }

            function unlockUI() {
                // restore kalau perlu (misal user back navigation/bfcache)
                if (btn.dataset.origHtml) btn.innerHTML = btn.dataset.origHtml;
                btn.disabled = false;
                btn.removeAttribute('aria-busy');
                if (blocker) blocker.classList.remove('show');

                // re-enable inputs yang tadi kita disable
                form.querySelectorAll('input[disabled], select[disabled], textarea[disabled]').forEach(el => {
                    if (el.type !== 'file') el.disabled = false;
                });
                // hapus hidden clones biar gak numpuk
                form.querySelectorAll('input[type="hidden"]').forEach(h => {
                    // jangan hapus hidden yang memang dari server (CSRF). Deteksi kasar: name=<?= csrf_token() ?>
                    // Kalau mau aman, kasih data-flag di hidden yang kita buat:
                });
            }

            // prevent double submit
            let submitted = false;
            form.addEventListener('submit', function(e) {
                if (submitted) {
                    e.preventDefault();
                    return false;
                }
                submitted = true;
                lockUI();
                // biarkan form submit normal
            });

            // kalau user balik dari bfcache (Safari/Firefox), restore UI
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    submitted = false;
                    unlockUI();
                }
            });

        })();
    </script>
</body>

</html>
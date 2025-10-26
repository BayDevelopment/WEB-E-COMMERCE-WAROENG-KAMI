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
    <h1 class="mt-4 mb-1"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <div class="w-100 mb-3">
        <a href="<?= base_url('admin/activity') ?>" class="btn btn-theme rounded-pill py-2 ms-auto"><span><i class="fa-solid fa-angle-left"></i></span> Kembali</a>
    </div>


    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards">
        <!-- Card Data Akun -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body">
                    <?php $errors = session('errors') ?? []; ?>
                    <form action="<?= site_url('admin/activity/tambah') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- NAMA LENGKAP -->
                        <div class="form-floating mb-3">
                            <input
                                class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>"
                                id="inputNamaLengkap"
                                name="nama_lengkap"
                                type="text"
                                value="<?= old('nama_lengkap') ?>"
                                autocomplete="name"
                                required />
                            <label for="inputNamaLengkap">Nama Lengkap</label>
                            <?php if (isset($errors['nama_lengkap'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['nama_lengkap']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- EMAIL -->
                        <div class="form-floating mb-3">
                            <input
                                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                id="inputEmail"
                                name="email"
                                type="email"
                                value="<?= old('email') ?>"
                                autocomplete="email"
                                required />
                            <label for="inputEmail">Email</label>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- PASSWORD + toggle eye -->
                        <div class="form-floating mb-3 position-relative">
                            <input
                                class="form-control <?= isset($errors['password_hash']) ? 'is-invalid' : '' ?>"
                                id="inputPassword"
                                name="password_hash"
                                type="password"
                                autocomplete="new-password"
                                required />
                            <label for="inputPassword">Password</label>
                            <?php if (isset($errors['password_hash'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['password_hash']) ?></div>
                            <?php endif; ?>
                            <button type="button"
                                class="btn btn-sm btn-theme rounded-pill py-2 position-absolute top-50 end-0 translate-middle-y me-2"
                                id="btnTogglePass" aria-label="Tampilkan/Sembunyikan Password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>

                        <!-- ROLE -->
                        <div class="form-floating mb-3">
                            <select
                                class="form-select <?= isset($errors['role']) ? 'is-invalid' : '' ?>"
                                id="inputRole"
                                name="role"
                                required>
                                <option value="" disabled <?= old('role') ? '' : 'selected' ?>>Pilih role...</option>
                                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="karyawan" <?= old('role') === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                            </select>
                            <label for="inputRole">Role</label>
                            <?php if (isset($errors['role'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- JK -->
                        <div class="form-floating mb-3">
                            <select
                                class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>"
                                id="inputJK"
                                name="jenis_kelamin"
                                required>
                                <option value="" disabled <?= old('jenis_kelamin') ? '' : 'selected' ?>>-- Jenis Kelamin --</option>
                                <option value="pria" <?= old('jenis_kelamin') === 'pria' ? 'selected' : '' ?>>Pria</option>
                                <option value="wanita" <?= old('jenis_kelamin') === 'wanita' ? 'selected' : '' ?>>Wanita</option>
                            </select>
                            <label for="inputJK">Jenis Kelamin</label>
                            <?php if (isset($errors['jenis_kelamin'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['jenis_kelamin']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- NO TELP -->
                        <div class="form-floating mb-3">
                            <input
                                class="form-control <?= isset($errors['no_telp']) ? 'is-invalid' : '' ?>"
                                id="inputNoTelp"
                                name="no_telp"
                                type="tel"
                                value="<?= old('no_telp') ?>"
                                inputmode="tel"
                                pattern="^[0-9+\s()-]{8,20}$"
                                required />
                            <label for="inputNoTelp">No. Telp</label>
                            <?php if (isset($errors['no_telp'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['no_telp']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- AVATAR (preview by jenis_kelamin) -->
                        <!-- AVATAR UPLOADER -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block mb-2" for="inputAvatar">Avatar</label>

                            <div class="wk-avatar-uploader d-flex align-items-center gap-3">
                                <img
                                    id="avatarPreview"
                                    src="<?php
                                            $jk = old('jenis_kelamin');
                                            echo old('avatar_preview')
                                                ?: ($jk === 'wanita'
                                                    ? base_url('assets/img/women.png')
                                                    : ($jk === 'pria'
                                                        ? base_url('assets/img/boy.png')
                                                        : base_url('assets/img/avatar-default.png')));
                                            ?>"
                                    alt="Preview Avatar"
                                    class="wk-avatar-img">

                                <div class="wk-upload-box flex-grow-1" id="wkDropZone" role="group" aria-label="Unggah avatar">
                                    <input
                                        class="form-control d-none <?= isset($errors['avatar']) ? 'is-invalid' : '' ?>"
                                        id="inputAvatar"
                                        name="avatar"
                                        type="file"
                                        accept="image/*"
                                        aria-describedby="avatarHelp" />

                                    <div class="wk-upload-actions">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnChooseAvatar">
                                            <i class="fa-solid fa-upload me-1"></i> Pilih Gambar
                                        </button>
                                        <span class="wk-file-name text-truncate" id="wkFileName">Belum ada file</span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnClearAvatar" aria-label="Hapus pilihan avatar">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>

                                    <div class="wk-upload-hint small" id="avatarHelp">
                                        Drag & drop ke kotak ini atau klik “Pilih Gambar”. Maks 1MB. Format: JPG, PNG.
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($errors['avatar'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['avatar']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid gap-2 py-3">
                            <button type="submit" id="btnSubmit" class="btn btn-theme">
                                <span class="btn-text">Simpan</span>
                                <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    // show password 
    document.addEventListener('DOMContentLoaded', () => {
        // Toggle password
        const pass = document.getElementById('inputPassword');
        const btn = document.getElementById('btnTogglePass');
        if (pass && btn) {
            btn.addEventListener('click', () => {
                const newType = pass.type === 'password' ? 'text' : 'password';
                pass.type = newType;
                btn.querySelector('i')?.classList.toggle('fa-eye-slash');
            });
        }

        // Preview avatar dari file
        const inputAvatar = document.getElementById('inputAvatar');
        const avatarPrev = document.getElementById('avatarPreview');

        let userPickedAvatar = false; // flag: user sudah pilih file

        if (inputAvatar && avatarPrev) {
            inputAvatar.addEventListener('change', (e) => {
                const f = e.target.files?.[0];
                if (!f) return;
                if (!/^image\//.test(f.type)) {
                    e.target.value = '';
                    return alert('File harus gambar.');
                }
                if (f.size > 1 * 1024 * 1024) {
                    e.target.value = '';
                    return alert('Maksimal 1MB.');
                }
                avatarPrev.src = URL.createObjectURL(f);
                userPickedAvatar = true;
            });
        }

        // Update preview berdasar jenis kelamin (ID yang benar: inputJK)
        const jkSelect = document.getElementById('inputJK');

        function setGenderPreview() {
            if (!jkSelect || !avatarPrev) return;
            // hanya ganti kalau user BELUM memilih file avatar
            if (userPickedAvatar) return;

            const val = jkSelect.value;
            if (val === 'wanita') {
                avatarPrev.src = '<?= base_url('assets/img/woman.png') ?>';
            } else if (val === 'pria') {
                avatarPrev.src = '<?= base_url('assets/img/boy.png') ?>';
            } else {
                avatarPrev.src = '<?= base_url('assets/img/user.png') ?>';
            }
        }

        if (jkSelect) {
            // sinkron saat halaman pertama kali tampil
            setGenderPreview();
            // sinkron tiap user ganti pilihan
            jkSelect.addEventListener('change', setGenderPreview);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('btnSubmit');
        const btnText = submitBtn.querySelector('.btn-text');
        const spinner = submitBtn.querySelector('.spinner-border');

        form.addEventListener('submit', function(e) {
            // Ambil semua elemen form kecuali CSRF dan hidden
            const elements = form.querySelectorAll('input, select, textarea, button');

            elements.forEach(el => {
                if (el.type === 'hidden') return; // biarkan CSRF & hidden field

                // Tombol selain submit → disable
                if (el.tagName === 'BUTTON' && el !== submitBtn) {
                    el.disabled = true;
                    return;
                }

                // Input text / email / tel / password / textarea → readonly
                if (['text', 'email', 'tel', 'password'].includes(el.type) || el.tagName === 'TEXTAREA') {
                    el.readOnly = true;
                }

                // Select → nonaktifkan pointer events tapi tetap submit value
                if (el.tagName === 'SELECT') {
                    el.classList.add('disabled-select');
                    el.style.pointerEvents = 'none';
                }

                // File input → nonaktifkan klik tapi tetap submit value
                if (el.type === 'file') {
                    el.style.pointerEvents = 'none';
                }
            });

            // Tombol submit → tampilkan spinner + disable
            btnText.textContent = 'Loading...';
            spinner.classList.remove('d-none');
            submitBtn.disabled = true;
        });
    });
</script>



<?= $this->endSection() ?>
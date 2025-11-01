<?= $this->extend('templates/admin/main') ?>
<?= $this->section('admin_content') ?>

<style>
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

    body {
        background: var(--page-bg);
        color: var(--text);
    }

    .breadcrumb {
        background: transparent;
        margin-bottom: 1rem;
    }

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

    .card.dash-card .fw-semibold {
        font-weight: 700;
        font-size: .9rem;
        color: var(--muted);
    }

    .card.dash-card .fs-2 {
        font-size: 1.8rem !important;
        line-height: 1.1;
        font-weight: 800;
        color: var(--text);
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4 mb-1"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <div class="w-100 mb-3">
        <a href="<?= base_url('admin/activity') ?>" class="btn btn-theme rounded-pill py-2 ms-auto">
            <i class="fa-solid fa-angle-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row dashboard-cards">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body">
                    <?php $errors = session('errors') ?? []; ?>
                    <form action="<?= site_url('admin/activity/update/' . $activity['id_admin']) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- NAMA LENGKAP -->
                        <div class="form-floating mb-3">
                            <input
                                class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>"
                                id="inputNamaLengkap"
                                name="nama_lengkap"
                                type="text"
                                value="<?= old('nama_lengkap', $activity['nama_lengkap']) ?>"
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
                                value="<?= old('email', $activity['email']) ?>"
                                required />
                            <label for="inputEmail">Email</label>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- PASSWORD (optional, kosongkan jika tidak ubah) -->
                        <div class="form-floating mb-3 position-relative">
                            <input
                                class="form-control <?= isset($errors['password_hash']) ? 'is-invalid' : '' ?>"
                                id="inputPassword"
                                name="password_hash"
                                type="password" />
                            <label for="inputPassword">Password (kosongkan jika tidak diubah)</label>
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
                                <option value="" disabled>Pilih role...</option>
                                <option value="admin" <?= old('role', $activity['role']) === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="karyawan" <?= old('role', $activity['role']) === 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                            </select>
                            <label for="inputRole">Role</label>
                            <?php if (isset($errors['role'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['role']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- JENIS KELAMIN -->
                        <div class="form-floating mb-3">
                            <select
                                class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>"
                                id="inputJK"
                                name="jenis_kelamin"
                                required>
                                <option value="" disabled>-- Jenis Kelamin --</option>
                                <option value="pria" <?= old('jenis_kelamin', $activity['jenis_kelamin']) === 'pria' ? 'selected' : '' ?>>Pria</option>
                                <option value="wanita" <?= old('jenis_kelamin', $activity['jenis_kelamin']) === 'wanita' ? 'selected' : '' ?>>Wanita</option>
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
                                value="<?= old('no_telp', $activity['no_telp']) ?>"
                                pattern="^[0-9+\s()-]{8,20}$"
                                required />
                            <label for="inputNoTelp">No. Telp</label>
                            <?php if (isset($errors['no_telp'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['no_telp']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- AVATAR -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block mb-2" for="inputAvatar">Avatar</label>

                            <div class="wk-avatar-uploader d-flex align-items-center gap-3">
                                <img
                                    id="avatarPreview"
                                    src="<?= base_url('assets/uploads/avatars/' . ($activity['avatar'] ?? 'avatar-default.png')) ?>"
                                    alt="Preview Avatar"
                                    class="wk-avatar-img">

                                <div class="wk-upload-box flex-grow-1">
                                    <input
                                        class="form-control d-none <?= isset($errors['avatar']) ? 'is-invalid' : '' ?>"
                                        id="inputAvatar"
                                        name="avatar"
                                        type="file"
                                        accept="image/*" />

                                    <div class="wk-upload-actions">
                                        <button type="button" class="btn btn-sm btn-primary" id="btnChooseAvatar">
                                            <i class="fa-solid fa-upload me-1"></i> Pilih Gambar
                                        </button>
                                        <span class="wk-file-name text-truncate" id="wkFileName">
                                            <?= $activity['avatar'] ? esc($activity['avatar']) : 'Belum ada file' ?>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-secondary d-none" id="btnClearAvatar">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                    <div class="wk-upload-hint small">
                                        Drag & drop atau klik “Pilih Gambar”. Maks 1MB. Format: JPG, PNG.
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($errors['avatar'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['avatar']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2 py-3">
                            <button type="submit" id="btnSubmit" class="btn btn-theme">
                                <span class="btn-text">Simpan Perubahan</span>
                                <span class="spinner-border spinner-border-sm ms-2 d-none"></span>
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

        // Avatar preview
        const inputAvatar = document.getElementById('inputAvatar');
        const avatarPrev = document.getElementById('avatarPreview');
        const btnChoose = document.getElementById('btnChooseAvatar');

        btnChoose?.addEventListener('click', () => inputAvatar.click());
        inputAvatar?.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            if (!/^image\//.test(file.type)) {
                alert('File harus gambar.');
                e.target.value = '';
                return;
            }
            if (file.size > 1024 * 1024) {
                alert('Ukuran maksimal 1MB.');
                e.target.value = '';
                return;
            }
            avatarPrev.src = URL.createObjectURL(file);
            document.getElementById('wkFileName').textContent = file.name;
        });

        // Gender auto-preview
        const jkSelect = document.getElementById('inputJK');

        function setGenderPreview() {
            const val = jkSelect.value;
            if (!inputAvatar.value) {
                if (val === 'wanita') avatarPrev.src = '<?= base_url('assets/img/woman.png') ?>';
                else if (val === 'pria') avatarPrev.src = '<?= base_url('assets/img/boy.png') ?>';
                else avatarPrev.src = '<?= base_url('assets/img/avatar-default.png') ?>';
            }
        }
        jkSelect?.addEventListener('change', setGenderPreview);
    });
</script>
<?= $this->endSection() ?>
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
    <h1 class="mt-4"><?= esc($breadcrumb) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active"><?= esc($breadcrumb) ?></li>
    </ol>

    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards">
        <!-- Pelanggan -->
        <!-- Card Data Akun -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fw-semibold">Data Akun</div>
                        <!-- Action buttons -->
                        <div class="d-flex gap-2">
                            <button id="btnShowEdit" class="btn btn-sm btn-theme rounded-pill py-2">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <button id="btnShowPass" class="btn btn-sm btn-theme rounded-pill py-2">
                                <i class="fas fa-key me-1"></i>Password
                            </button>
                        </div>
                    </div>

                    <?php
                    // $admin = data admin dari controller (array)
                    // contoh: ['email'=>..., 'role'=>..., 'is_active'=>1, 'nama_lengkap'=>..., 'no_telp'=>..., 'avatar'=>..., 'last_login_at'=>..., 'login_attempts'=>...]
                    $admin = $admin ?? [];
                    ?>

                    <div class="mt-3 d-flex align-items-center gap-3">
                        <img src="<?= base_url('assets/uploads/avatars/' . esc($admin['avatar'])) ?>" class="rounded-circle border" alt="avatar" width="72" height="72" style="object-fit:cover;">
                        <div>
                            <div class="h6 mb-1"><?= esc($admin['nama_lengkap'] ?? '-') ?></div>
                            <div class="small text-muted mb-1"><?= esc($admin['email'] ?? '-') ?></div>
                            <div class="small">
                                <span class="badge bg-dark me-1"><?= esc($admin['role'] ?? '-') ?></span>
                                <?php if (($admin['is_active'] ?? 0) == 1): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="row gy-2 small">
                        <div class="col-5 text-muted">Nama Lengkap</div>
                        <div class="col-7 fw-medium"><?= esc($admin['nama_lengkap'] ?? '-') ?></div>

                        <div class="col-5 text-muted">No. Telepon</div>
                        <div class="col-7 fw-medium"><?= esc($admin['no_telp'] ?? '-') ?></div>

                        <div class="col-5 text-muted">Email</div>
                        <div class="col-7 fw-medium"><?= esc($admin['email'] ?? '-') ?></div>

                        <div class="col-5 text-muted">Role</div>
                        <div class="col-7 fw-medium"><?= esc($admin['role'] ?? '-') ?></div>

                        <div class="col-5 text-muted">Status</div>
                        <div class="col-7 fw-medium"><?= (($admin['is_active'] ?? 0) == 1 ? 'Aktif' : 'Nonaktif') ?></div>

                        <div class="col-5 text-muted">Login Terakhir</div>
                        <div class="col-7 fw-medium">
                            <?= !empty($admin['last_login_at']) ? date('d M Y H:i', strtotime($admin['last_login_at'])) : '-' ?>
                        </div>

                        <div class="col-5 text-muted">Percobaan Login</div>
                        <div class="col-7 fw-medium"><?= (int)($admin['login_attempts'] ?? 0) ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card dash-card position-relative h-100 d-none" id="accountCard">
                <div class="card-body">

                    <!-- Panel Edit Akun -->
                    <div id="panelEditAkun" class="d-none">
                        <form id="formEditAkun" class="wk-form" action="<?= site_url('admin/profile/update') ?>" method="post" enctype="multipart/form-data" novalidate>
                            <?= csrf_field() ?>

                            <?php
                            $errors = session('errors') ?? [];
                            $flashError = session('error') ?? null;
                            $flashSuccess = session('success') ?? null;

                            // helper ringkas
                            $isInvalid = function (string $name) use ($errors) {
                                return isset($errors[$name]) ? ' is-invalid' : '';
                            };
                            $errText   = function (string $name) use ($errors) {
                                return isset($errors[$name]) ? esc($errors[$name]) : '';
                            };

                            // helper value dgn fallback old() → data db
                            $val = function (string $name, $fallback) {
                                $o = old($name);
                                return (isset($o) && $o !== '') ? $o : $fallback;
                            };
                            ?>

                            <?php if ($flashError): ?>
                                <div class="alert alert-danger mb-3"><?= esc($flashError) ?></div>
                            <?php endif; ?>

                            <?php if ($flashSuccess): ?>
                                <div class="alert alert-success mb-3"><?= esc($flashSuccess) ?></div>
                            <?php endif; ?>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill wk-badge">Akun</span>
                                    <h5 class="m-0">Edit Akun</h5>
                                </div>
                                <!-- Tutup: hide card saja, TIDAK hapus state -->
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                    data-close-panel="#panelEditAkun" data-clear="0">Tutup</button>
                            </div>

                            <div class="wk-body">
                                <!-- Nama Lengkap -->
                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text"
                                        name="nama_lengkap"
                                        class="form-control<?= $isInvalid('nama_lengkap') ?>"
                                        value="<?= esc($val('nama_lengkap', $admin['nama_lengkap'] ?? '')) ?>"
                                        minlength="3" maxlength="50" required>
                                    <div class="invalid-feedback"><?= $errText('nama_lengkap') ?: 'IN-INVALID' ?></div>
                                </div>

                                <!-- No. Telepon -->
                                <div class="mb-3">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="tel"
                                        name="no_telp"
                                        class="form-control<?= $isInvalid('no_telp') ?>"
                                        value="<?= esc($val('no_telp', $admin['no_telp'] ?? '')) ?>"
                                        pattern="^[0-9+\-\s]+$" minlength="8" maxlength="13"
                                        placeholder="+62 8xx xxxx xxxx">
                                    <div class="invalid-feedback"><?= $errText('no_telp') ?: 'IN-INVALID' ?></div>
                                    <small class="form-text">Hanya angka, spasi, + atau - (8–13 karakter).</small>
                                </div>

                                <!-- Email -->
                                <div class="mb-3">
                                    <label class="form-label">Email (opsional)</label>
                                    <input type="email"
                                        name="email"
                                        class="form-control<?= $isInvalid('email') ?>"
                                        value="<?= esc($val('email', $admin['email'] ?? '')) ?>"
                                        autocomplete="email">
                                    <div class="invalid-feedback"><?= $errText('email') ?: 'IN-INVALID' ?></div>
                                    <div class="form-text">Ubah email akan diverifikasi &amp; harus unik.</div>
                                </div>

                                <!-- Avatar -->
                                <div class="mb-1">
                                    <label class="form-label">Avatar</label>
                                    <input type="file"
                                        name="avatar"
                                        class="form-control<?= $isInvalid('avatar') ?>"
                                        accept="image/jpg,image/jpeg,image/png">
                                    <div class="invalid-feedback"><?= $errText('avatar') ?: 'IN-INVALID' ?></div>
                                    <div class="form-text">Maks 1 MB, JPG/PNG.</div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <!-- Batal: tutup & HAPUS state -->
                                <button type="button" class="btn btn-outline-secondary rounded-pill py-2"
                                    data-close-panel="#panelEditAkun" data-clear="1">Batal</button>
                                <button id="btnSaveAkun" type="submit" class="btn btn-theme rounded-pill py-2">Simpan</button>
                            </div>
                        </form>
                    </div>

                    <!-- Panel Ganti Password -->
                    <div id="panelGantiPassword" class="d-none">
                        <form id="formGantiPassword" class="wk-form" action="<?= site_url('admin/profile/change-password') ?>" method="post" novalidate>
                            <?= csrf_field() ?>

                            <?php
                            // ambil errors & flash dari session
                            $errors  = session('errors') ?? [];
                            $errMsg  = session('error') ?? null;
                            $okMsg   = session('success') ?? null;
                            // helper kecil
                            $isInvalid = fn($name) => isset($errors[$name]) ? ' is-invalid' : '';
                            $errText   = fn($name) => isset($errors[$name]) ? esc($errors[$name]) : '';
                            ?>

                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge rounded-pill wk-badge">Keamanan</span>
                                    <h5 class="m-0">Ganti Password</h5>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-close-panel="#panelGantiPassword" data-clear="0">Tutup</button>
                            </div>

                            <div class="wk-body">
                                <!-- Password lama -->
                                <div class="mb-3">
                                    <label class="form-label">Password Lama</label>
                                    <input
                                        type="password"
                                        name="password_lama"
                                        class="form-control<?= $isInvalid('password_lama') ?>"
                                        placeholder="Masukkan password lama"
                                        minlength="6"
                                        required>
                                    <div class="invalid-feedback"><?= $errText('password_lama') ?></div>
                                </div>

                                <!-- Password baru -->
                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input
                                        type="password"
                                        name="password_hash"
                                        class="form-control<?= $isInvalid('password_hash') ?>"
                                        placeholder="Masukkan password baru"
                                        minlength="6"
                                        required>
                                    <div class="invalid-feedback"><?= $errText('password') ?></div>
                                </div>

                                <!-- Konfirmasi -->
                                <div class="mb-1">
                                    <label class="form-label">Ulangi Password Baru</label>
                                    <input
                                        type="password"
                                        name="password_confirm"
                                        class="form-control<?= $isInvalid('password_confirm') ?>"
                                        placeholder="Ulangi password baru"
                                        minlength="6"
                                        required>
                                    <div class="invalid-feedback"><?= $errText('password_confirm') ?></div>
                                </div>

                                <div class="form-text">Minimal 8 karakter. Password disimpan dalam hash.</div>
                            </div>

                            <div class="d-grid gap-2 pt-5">
                                <button type="button" class="btn btn-outline-secondary rounded-pill py-2" data-close-panel="#panelGantiPassword" data-clear="1">Batal</button>
                                <button id="btnSavePass" type="submit" class="btn btn-theme rounded-pill py-2">Update Password</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    (() => {
        /**
         * Freeze form on submit tanpa kehilangan nilai.
         * - Buat "mirror" hidden input untuk setiap field yang akan di-disable
         * - Disable semua field (biar ga bisa diutak-atik)
         * - Tampilkan overlay + kunci tombol submit
         */
        function attachFreezeOnSubmit(form) {
            if (!form) return;

            // bikin overlay tipis biar UX-nya jelas lagi proses
            const overlay = document.createElement('div');
            overlay.className = 'form-blocker';
            overlay.style.cssText = `
      position: absolute; inset: 0; background: rgba(255,255,255,.4);
      backdrop-filter: blur(1px);
      pointer-events: none; opacity: 0; transition: opacity .15s ease;
      border-radius: inherit;
    `;
            // bungkus form content biar overlay nempel rapi
            form.style.position = 'relative';
            form.appendChild(overlay);

            form.addEventListener('submit', (ev) => {
                // Hindari double-freeze
                if (form.dataset.freezing === '1') return;
                form.dataset.freezing = '1';

                // 1) Buat mirror hidden untuk tiap elemen yang bakal kita disable
                const mirrors = [];
                const els = Array.from(form.elements);

                els.forEach(el => {
                    // Skip yang ga bernama atau sudah hidden / submit / button
                    if (!el.name) return;
                    if (el.type === 'hidden' || el.type === 'submit' || el.type === 'button') return;

                    // NOTE: file input ga bisa di-mirror (value file read-only).
                    // Kita biarin gak di-disable supaya file tetap terkirim.
                    if (el.type === 'file') return;

                    // Checkbox / radio → tulis hanya yang checked
                    if (el.type === 'checkbox' || el.type === 'radio') {
                        if (!el.checked) return;
                        const hid = document.createElement('input');
                        hid.type = 'hidden';
                        hid.name = el.name;
                        hid.value = el.value;
                        // Tandai supaya gampang dibersihkan jika perlu
                        hid.dataset.mirror = '1';
                        form.appendChild(hid);
                        mirrors.push(hid);
                        return;
                    }

                    // Select multiple → mirror tiap option yang selected
                    if (el.tagName === 'SELECT' && el.multiple) {
                        Array.from(el.selectedOptions).forEach(opt => {
                            const hid = document.createElement('input');
                            hid.type = 'hidden';
                            hid.name = el.name;
                            hid.value = opt.value;
                            hid.dataset.mirror = '1';
                            form.appendChild(hid);
                            mirrors.push(hid);
                        });
                        return;
                    }

                    // Input/select/textarea biasa
                    const hid = document.createElement('input');
                    hid.type = 'hidden';
                    hid.name = el.name;
                    hid.value = el.value;
                    hid.dataset.mirror = '1';
                    form.appendChild(hid);
                    mirrors.push(hid);
                });

                // 2) Disable semua kontrol untuk ngunci UI (kecuali file)
                els.forEach(el => {
                    if (el.type === 'file') return; // biarin aktif biar file kebawa
                    if (el.type === 'hidden') return;
                    el.setAttribute('disabled', 'disabled');
                    // Tambah aria state nice-to-have
                    el.setAttribute('aria-disabled', 'true');
                });

                // 3) Kunci tombol submit + kasih spinner dikit
                const submits = els.filter(e => e.type === 'submit' || e.matches('[type="submit"], .btn[type="submit"]'));
                submits.forEach(btn => {
                    btn.dataset._origHtml = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Menyimpan...';
                });

                // 4) Tampilkan overlay
                overlay.style.pointerEvents = 'auto';
                overlay.style.opacity = '1';

                // Catatan:
                // - Kita TIDAK panggil preventDefault() → biarkan submit normal berjalan.
                // - Jika server balikin validasi & stay di halaman yang sama, value tetap terkirim
                //   karena mirror hidden sudah disisipkan sebelum disable.
                // - Kalau kamu pakai JS submit custom, panggil fungsi ini manual sebelum fetch().
            });
        }

        // Pasang ke kedua form kamu
        attachFreezeOnSubmit(document.querySelector('#formEditAkun'));
        attachFreezeOnSubmit(document.querySelector('#formGantiPassword'));
    })();

    (() => {
        const KEY = 'wk_account_activePanel'; // simpan selector panel aktif
        const card = document.getElementById('accountCard');
        const PANELS = ['#panelEditAkun', '#panelGantiPassword'];

        function hideAllPanels() {
            PANELS.forEach(sel => document.querySelector(sel)?.classList.add('d-none'));
        }

        function showCard() {
            card?.classList.remove('d-none');
        }

        function hideCard() {
            card?.classList.add('d-none');
        }

        function openPanel(sel, {
            persist = true
        } = {}) {
            if (!document.querySelector(sel)) return;
            showCard();
            hideAllPanels();
            document.querySelector(sel).classList.remove('d-none');
            if (persist) {
                try {
                    localStorage.setItem(KEY, sel);
                } catch (e) {}
            }
        }

        function clearPersist() {
            try {
                localStorage.removeItem(KEY);
            } catch (e) {}
        }

        // Trigger: tombol spesifik
        document.getElementById('btnShowEdit')?.addEventListener('click', (e) => {
            e.preventDefault();
            openPanel('#panelEditAkun', {
                persist: true
            });
        });
        document.getElementById('btnShowPass')?.addEventListener('click', (e) => {
            e.preventDefault();
            openPanel('#panelGantiPassword', {
                persist: true
            });
        });

        // Trigger: atribut data-panel (opsional, fleksibel)
        document.addEventListener('click', (e) => {
            const t = e.target.closest('[data-panel]');
            if (!t) return;
            e.preventDefault();
            const sel = t.getAttribute('data-panel');
            openPanel(sel, {
                persist: true
            });
        });

        // Tombol close di dalam panel
        document.querySelectorAll('[data-close-panel]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const sel = btn.getAttribute('data-close-panel');
                const clear = btn.getAttribute('data-clear') === '1';

                // selalu tutup panel yang dimaksud
                document.querySelector(sel)?.classList.add('d-none');

                if (clear) {
                    // BATAL: hapus state + sembunyikan card
                    clearPersist();
                    hideCard();
                } else {
                    // TUTUP: sembunyikan card saja, tapi state dipertahankan
                    hideCard();
                }
            });
        });

        // Restore saat load: kalau ada state, buka lagi
        (function restore() {
            let sel = null;
            try {
                sel = localStorage.getItem(KEY);
            } catch (e) {}
            if (sel && document.querySelector(sel)) {
                openPanel(sel, {
                    persist: false
                }); // jangan re-write key
            } else {
                hideCard(); // default tertutup
            }
        })();
    })();
</script>

<?= $this->endSection() ?>
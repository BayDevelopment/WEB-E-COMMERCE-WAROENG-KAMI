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

    <div class="d-flex w-100 mb-3">
        <a href="<?= base_url('admin/activity/tambah') ?>" class="btn btn-theme rounded-pill py-2 ms-auto"><span><i class="fa-solid fa-file-circle-plus"></i></span> Tambah</a>
    </div>


    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards">
        <!-- Card Data Akun -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body" id="filterCard">
                    <form id="filterActivity" class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label mb-0">Keyword</label>
                            <div class="input-group">
                                <input type="text" id="fKeyword" class="form-control" placeholder="Cari nama / email…">
                                <button type="button" id="btnSearch" class="btn btn-dark">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-0">Status</label>
                            <select id="fStatus" class="form-select">
                                <option value="">Semua</option>
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-grid d-md-flex gap-2">
                            <button type="button" id="btnReset" class="btn btn-outline-secondary w-100">
                                <i class="fa-solid fa-rotate-left me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-body" id="tableCard">
                    <?php if (!empty($rows) && is_array($rows)): ?>
                        <table class="table text-dark" id="tableActivity">
                            <thead>
                                <tr>
                                    <th scope="col">No</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($rows as $d_a): ?>
                                    <tr>
                                        <th scope="row"><?= $no++ ?>.</th>
                                        <td><?= esc($d_a['nama_lengkap'] ?? '-') ?></td>
                                        <td><?= esc($d_a['email'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?= esc($d_a['role'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php $isActive = (int) ($d_a['is_active'] ?? 0); ?>
                                            <span class="badge bg-<?= $isActive === 1 ? 'success' : 'danger' ?>">
                                                <?= $isActive === 1 ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="#" class="btn btn-primary rounded-pill py-2" title="Edit">
                                                <i class="fa-solid fa-file-pen"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="cover-img-empty text-center py-4">
                            <img src="<?= base_url('assets/img/box.png') ?>" alt="IMG-Waroeng-Kami" class="size-img-empty mb-2">
                            <h6>Ups, Tidak ada activity yang terdeteksi!</h6>
                        </div>
                    <?php endif; ?>
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
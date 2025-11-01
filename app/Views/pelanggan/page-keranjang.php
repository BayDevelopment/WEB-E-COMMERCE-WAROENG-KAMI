<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>
<style>
    /* Scroll vertikal otomatis jika tinggi > 200px */
    .scroll-200 {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
    }

    .scroll-200::-webkit-scrollbar {
        width: 8px;
    }

    .scroll-200::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, .06);
        border-radius: 8px;
    }

    .scroll-200::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, .25);
        border-radius: 8px;
    }

    @media (max-width: 576px) {
        .cards-mobile {
            margin-bottom: 70px;
        }
    }

    /* === Dark → text kuning === */
    :root {
        --wk-accent-yellow: #f5d20bff;
    }

    /* Global: mayoritas teks jadi kuning saat dark */
    [data-bs-theme="dark"] body {
        color: var(--wk-accent-yellow) !important;
    }

    /* Judul, paragraf, link, breadcrumb, dsb. */
    [data-bs-theme="dark"] h1,
    [data-bs-theme="dark"] h2,
    [data-bs-theme="dark"] h3,
    [data-bs-theme="dark"] h4,
    [data-bs-theme="dark"] h5,
    [data-bs-theme="dark"] h6,
    [data-bs-theme="dark"] p,
    [data-bs-theme="dark"] a,
    [data-bs-theme="dark"] .breadcrumb,
    [data-bs-theme="dark"] .product-title,
    [data-bs-theme="dark"] .product-desc,
    [data-bs-theme="dark"] .wk-title,
    [data-bs-theme="dark"] .wk-text-white {
        color: var(--wk-accent-yellow) !important;
    }

    /* KECUALI: biarkan komponen ini tetap pakai warna body agar kontras terjaga */
    [data-bs-theme="dark"] .btn,
    [data-bs-theme="dark"] .btn *,
    [data-bs-theme="dark"] .btn-add-to-cart,
    [data-bs-theme="dark"] .badge,
    [data-bs-theme="dark"] .cart-badge,
    [data-bs-theme="dark"] .qty-group .btn-qty,
    [data-bs-theme="dark"] .input-group-text,
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .toast,
    [data-bs-theme="dark"] .modal-content {
        color: var(--bs-body-color) !important;
    }

    /* Opsional: utilitas opt-in kalau mau target elemen tertentu saja */
    [data-bs-theme="dark"] .tl-text-yellow-dark {
        color: var(--wk-accent-yellow) !important;
    }

    /* Shim dark mode untuk CSS lama yang hard-coded */
    [data-bs-theme="dark"] body {
        background-color: #0f1115 !important;
        color: #e5e7eb !important;
    }

    /* Semua kontainer/kartu/list yang biasanya putih → ikut var Bootstrap */
    [data-bs-theme="dark"] .bg-white,
    [data-bs-theme="dark"] .wk-section,
    [data-bs-theme="dark"] .container,
    [data-bs-theme="dark"] .card,
    [data-bs-theme="dark"] .wk-card,
    [data-bs-theme="dark"] .product-card,
    [data-bs-theme="dark"] .list-group-item,
    [data-bs-theme="dark"] .offcanvas,
    [data-bs-theme="dark"] .dropdown-menu {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* Input, input-group, badge, dsb. */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select,
    [data-bs-theme="dark"] .input-group-text {
        background-color: var(--bs-body-bg) !important;
        color: var(--bs-body-color) !important;
        border-color: var(--bs-border-color) !important;
    }

    /* Teks & border utilitas */
    [data-bs-theme="dark"] .text-dark {
        color: var(--bs-body-color) !important;
    }

    [data-bs-theme="dark"] .text-muted {
        color: var(--bs-secondary-color) !important;
    }

    [data-bs-theme="dark"] .border,
    [data-bs-theme="dark"] .btn-outline-wk {
        border-color: var(--bs-border-color) !important;
    }

    /* Button custom */
    [data-bs-theme="dark"] .btn-wk {
        background: #f59e0b !important;
        color: #0b0f17 !important;
        border: none !important;
    }

    /* Kartu produk spesifik (kalau ada kelas ini di page-produk) */
    [data-bs-theme="dark"] .wk-grid .product-card .desc,
    [data-bs-theme="dark"] .wk-desc {
        color: var(--bs-secondary-color) !important;
    }

    /* Mobile-only: ≤576px */
    @media (max-width: 576px) {
        .cards-mobile {
            margin-bottom: 100px !important;
        }
    }

    /* Overlay ringan di atas form saat submit */
    form[data-submitting="1"] {
        position: relative;
    }

    form[data-submitting="1"]::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, .05);
        backdrop-filter: blur(1px);
        border-radius: .75rem;
        /* menyesuaikan card kamu */
    }
</style>

<!-- Global Page Loader -->
<div id="pageLoader" hidden aria-hidden="true">
    <div class="pl-backdrop"></div>
    <div class="pl-card">
        <div class="pl-spinner" aria-label="Loading"></div>
        <div class="pl-text">Memuat...</div>
        <div class="pl-progress"><span class="pl-bar"></span></div>
    </div>
</div>

<nav aria-label="breadcrumb page-root" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('pelanggan/produk') ?>">Produk</a></li>
        <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
    </ol>
</nav>

<div class="cards-mobile-wrap mb-3 page-root">
    <div class="row g-3 cards-mobile mb-3">

        <?php if (!empty($items)): ?>
            <!-- FORM PEMESAN + DAFTAR ITEM -->
            <div class="col-12">
                <form action="<?= site_url('pelanggan/keranjang/checkout') ?>" method="post" class="card border-0 shadow-sm">
                    <?= csrf_field() ?>

                    <!-- Data Pemesan -->
                    <div class="card-body">

                        <!-- Kode Pesanan -->
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Pesanan</label>
                            <input type="text" class="form-control" id="kode"
                                value="<?= esc($kode_pesanan_view_only) ?>" readonly>
                            <!-- penting: tidak ada name agar tidak ikut ke POST -->
                        </div>

                        <!-- Username -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username / Inisial</label>
                            <input type="text" class="form-control" id="username" name="username"
                                placeholder="Masukkan username atau inisial..." required>
                        </div>

                        <!-- Alamat -->
                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                placeholder="Tulis alamat pengantaran..." required></textarea>
                        </div>

                        <!-- Pilihan makan -->
                        <div class="mb-3">
                            <label class="form-label d-block">Pilihan</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="makan_ditempat" id="makan_ditempat1" value="1">
                                <label class="form-check-label" for="makan_ditempat1">Makan di Tempat</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="makan_ditempat" id="makan_ditempat0" value="0" checked>
                                <label class="form-check-label" for="makan_ditempat0">Dibungkus / Take Away</label>
                            </div>
                        </div>

                        <!-- Pilih Meja -->
                        <div class="mb-3" id="blok-meja" style="display: none;">
                            <label for="meja_id" class="form-label">Pilih Meja</label>
                            <select class="form-select" id="meja_id" name="meja_id">
                                <option value="">— Pilih Meja —</option>
                                <?php foreach (($meja_tersedia ?? []) as $m): ?>
                                    <option value="<?= esc($m['id_meja']) ?>">
                                        <?= esc($m['kode_meja']) ?> — Kapasitas <?= esc($m['kapasitas']) ?> org
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text text-muted">Hanya menampilkan meja yang belum digunakan.</div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label mb-0">Metode Pembayaran</label>
                            <select id="fPembayaran" name="pembayaran" class="form-select" required>
                                <option value="">Pilih Metode...</option>
                                <option value="cash">Cash / Tunai</option>
                                <option value="qris">QRIS / Scan</option>
                            </select>
                        </div>

                    </div>



                    <!-- Daftar Item -->
                    <div class="list-group list-group-flush scroll-200">
                        <?php foreach ($items as $it): ?>
                            <?php $itemId = $it['id'] ?? $it['id_keranjang'] ?? $it['produk_id'] ?? null; ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= !empty($it['gambar']) ? base_url('assets/uploads/produk/' . $it['gambar']) : base_url('assets/img/box.png') ?>"
                                            alt="<?= esc($it['nama_produk']) ?>"
                                            style="width:64px;height:64px;object-fit:cover;border-radius:10px;">
                                        <div>
                                            <div class="fw-semibold mb-1"><?= esc($it['nama_produk']) ?></div>
                                            <div class="small text-muted">
                                                Rp <?= number_format((float)$it['harga'], 0, ',', '.') ?> / porsi
                                            </div>
                                            <div class="small">
                                                Qty: <span class="fw-semibold"><?= (int)$it['jumlah'] ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <div class="fw-bold mb-2">
                                            Rp <?= number_format(((float)($it['subtotal'] ?? ($it['harga'] * $it['jumlah']))), 0, ',', '.') ?>
                                        </div>

                                        <?php if ($itemId !== null): ?>
                                            <a href="#"
                                                class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1"
                                                onclick="return confirmDelete('<?= base_url('pelanggan/keranjang/delete/' . (int)$itemId) ?>')"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        <?php else: ?>
                                            <span class="badge text-bg-secondary">ID tidak ditemukan</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Ringkasan & Aksi -->
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div class="fw-bold">Total (<?= (int)$total_qty ?> porsi)</div>
                        <div class="fw-bold">Rp <?= number_format((float)$total, 0, ',', '.') ?></div>
                    </div>

                    <div class="card-body pt-0">
                        <div class="d-grid gap-2">
                            <button class="btn btn-add-to-cart py-2 rounded-pill" type="submit">
                                <span><i class="fa-regular fa-file"></i></span> Pesan Sekarang
                            </button>
                            <a href="<?= base_url('pelanggan/produk') ?>"
                                class="btn btn-add-to-cart rounded-pill py-2">
                                <span><i class="fa-solid fa-tags"></i></span> Tambah Pesanan
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /FORM -->
        <?php else: ?>
            <!-- State kosong -->
            <div class="col-12">
                <div class="cover-image-empty text-center">
                    <img src="<?= base_url('assets/img/box.png') ?>" alt="IMG-Waroeng-Kami" class="size-img-empty">
                    <h6 class="mb-2">Ups, Keranjang masih kosong</h6>
                    <a href="<?= base_url('pelanggan/produk') ?>"
                        class="btn btn-add-to-cart rounded-pill px-4 py-2 d-inline-flex align-items-center justify-content-center gap-2"
                        aria-label="Pilih produk">
                        <i class="bi bi-bag-plus" aria-hidden="true"></i>
                        <span>Pilih Produk</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Reusable: panggil dengan confirmDelete('.../delete/123')
    window.confirmDelete = function(url) {
        if (!window.Swal) {
            // Fallback kalau SweetAlert2 gagal load
            if (confirm('Apakah Anda yakin? Data akan dihapus permanen.')) {
                window.location.href = url;
            }
            return false;
        }

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data akan dihapus secara permanen dan tidak dapat dipulihkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true,
            focusCancel: true
        }).then((res) => {
            if (res.isConfirmed) {
                window.location.href = url;
            }
        });

        return false; // cegah <a href="#"> melakukan navigasi default
    };

    document.addEventListener('DOMContentLoaded', function() {
        const radioMakan = document.querySelectorAll('input[name="makan_ditempat"]');
        const blokMeja = document.getElementById('blok-meja');

        radioMakan.forEach(radio => {
            radio.addEventListener('change', () => {
                if (document.getElementById('makan_ditempat1').checked) {
                    blokMeja.style.display = '';
                } else {
                    blokMeja.style.display = 'none';
                    document.getElementById('meja_id').value = '';
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="pelanggan/keranjang/checkout"]');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            // Cegah double-submit (jaga2 user spam enter/klik)
            if (form.dataset.submitting === '1') {
                e.preventDefault();
                return;
            }
            form.dataset.submitting = '1';

            // 1) Clone nilai ke hidden input sebelum disable
            const fields = form.querySelectorAll('input[name], textarea[name], select[name]');
            fields.forEach(function(el) {
                // Biarkan hidden yang sudah ada (mis. CSRF) apa adanya
                if (el.type === 'hidden') return;

                // Hanya ambil yang "bernilai" untuk tipe checkbox/radio
                if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;

                // SELECT multiple: kirim semua yang terseleksi
                if (el.tagName === 'SELECT' && el.multiple) {
                    Array.from(el.selectedOptions).forEach(function(opt) {
                        const h = document.createElement('input');
                        h.type = 'hidden';
                        h.name = el.name;
                        h.value = opt.value;
                        form.appendChild(h);
                    });
                } else {
                    const h = document.createElement('input');
                    h.type = 'hidden';
                    h.name = el.name;
                    h.value = el.value;
                    form.appendChild(h);
                }
            });

            // 2) Disable semua kontrol form supaya tidak bisa diubah/klik lagi
            const disableTargets = form.querySelectorAll('input, textarea, select, button');
            disableTargets.forEach(function(el) {
                // biarkan hidden tetap aktif (CSRF dsb.)
                if (el.type !== 'hidden') el.disabled = true;
                // tampilan non-interaktif ringan
                el.classList.add('pe-none');
            });

            // 3) Ganti tombol submit jadi "Loading..."
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Loading...';
                submitBtn.disabled = true;
            }

            // 4) (Opsional) matikan link aksi lain di area form supaya tidak diklik saat submit
            const links = form.querySelectorAll('a');
            links.forEach(a => {
                a.classList.add('disabled', 'pe-none', 'opacity-50');
                a.setAttribute('aria-disabled', 'true');
                a.addEventListener('click', ev => ev.preventDefault(), {
                    once: true
                });
            });

            // Biarkan form lanjut submit normal (POST)
            // Tidak perlu e.preventDefault()
        });
    });
</script>
<?= $this->endSection() ?>
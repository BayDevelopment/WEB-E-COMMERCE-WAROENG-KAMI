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
        <a href="<?= base_url('admin/produk') ?>" class="btn btn-theme rounded-pill py-2 ms-auto"><span><i class="fa-solid fa-angle-left"></i></span> Kembali</a>
    </div>


    <!-- ROW: 4 Modern Stat Cards -->
    <div class="row dashboard-cards">
        <!-- Card Data Akun -->
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card dash-card bg-warning position-relative h-100">
                <div class="card-body">
                    <?php $errors = session('errors') ?? []; ?>
                    <form action="<?= site_url('admin/produk/edit/' . esc($produk['id_produk'])) ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <!-- NAMA PRODUK -->
                        <div class="form-floating mb-3">
                            <input
                                class="form-control <?= isset($errors['nama_produk']) ? 'is-invalid' : '' ?>"
                                id="inputNamaProduk"
                                name="nama_produk"
                                type="text"
                                value="<?= old('nama_produk', $produk['nama_produk']) ?>"
                                required />
                            <label for="inputNamaProduk">Nama Produk</label>
                            <?php if (isset($errors['nama_produk'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['nama_produk']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- SLUG (otomatis, readonly, tanpa name) -->
                        <div class="form-group mb-3">
                            <label for="slug">Slug (otomatis)</label>
                            <input
                                type="text"
                                id="slug"
                                class="form-control"
                                value="<?= esc($produk['slug']) ?>"
                                readonly>
                        </div>

                        <!-- DESKRIPSI -->
                        <div class="form-floating mb-3">
                            <textarea
                                class="form-control <?= isset($errors['deskripsi']) ? 'is-invalid' : '' ?>"
                                id="inputDeskripsi"
                                name="deskripsi"
                                style="height: 100px"><?= old('deskripsi', $produk['deskripsi']) ?></textarea>
                            <label for="inputDeskripsi">Deskripsi</label>
                            <?php if (isset($errors['deskripsi'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['deskripsi']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- KATEGORI -->
                        <div class="form-floating mb-3">
                            <select
                                class="form-select <?= isset($errors['kategori']) ? 'is-invalid' : '' ?>"
                                id="inputKategori"
                                name="kategori"
                                required>
                                <option value="" disabled>Pilih kategori...</option>
                                <option value="makanan" <?= old('kategori', $produk['kategori']) === 'makanan' ? 'selected' : '' ?>>Makanan</option>
                                <option value="minuman" <?= old('kategori', $produk['kategori']) === 'minuman' ? 'selected' : '' ?>>Minuman</option>
                            </select>
                            <label for="inputKategori">Kategori</label>
                            <?php if (isset($errors['kategori'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['kategori']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- HARGA -->
                        <div class="form-floating mb-3">
                            <input
                                class="form-control <?= isset($errors['harga']) ? 'is-invalid' : '' ?>"
                                id="inputHarga"
                                name="harga"
                                type="number"
                                step="0.01"
                                value="<?= old('harga', $produk['harga']) ?>"
                                required />
                            <label for="inputHarga">Harga</label>
                            <?php if (isset($errors['harga'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['harga']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- STATUS -->
                        <div class="form-floating mb-3">
                            <select
                                class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>"
                                id="inputStatus"
                                name="status"
                                required>
                                <option value="tersedia" <?= old('status', $produk['status']) === 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                                <option value="habis" <?= old('status', $produk['status']) === 'habis' ? 'selected' : '' ?>>Habis</option>
                            </select>
                            <label for="inputStatus">Status</label>
                            <?php if (isset($errors['status'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['status']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- FAVORIT -->
                        <div class="form-check mb-3">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="inputFavorit"
                                name="favorit"
                                value="1"
                                <?= old('favorit', $produk['favorit']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="inputFavorit">Tandai sebagai produk favorit</label>
                        </div>

                        <!-- LEVEL PEDAS -->
                        <div class="form-floating mb-3">
                            <select
                                class="form-select <?= isset($errors['level_pedas']) ? 'is-invalid' : '' ?>"
                                id="inputLevelPedas"
                                name="level_pedas">
                                <option value="" disabled>Pilih level pedas...</option>
                                <option value="tidak" <?= old('level_pedas', $produk['level_pedas']) === 'tidak' ? 'selected' : '' ?>>Tidak Pedas</option>
                                <option value="sedang" <?= old('level_pedas', $produk['level_pedas']) === 'sedang' ? 'selected' : '' ?>>Sedang</option>
                                <option value="pedas" <?= old('level_pedas', $produk['level_pedas']) === 'pedas' ? 'selected' : '' ?>>Pedas</option>
                                <option value="sesuai-pembeli" <?= old('level_pedas', $produk['level_pedas']) === 'sesuai-pembeli' ? 'selected' : '' ?>>Sesuai Pembeli</option>
                            </select>
                            <label for="inputLevelPedas">Level Pedas</label>
                            <?php if (isset($errors['level_pedas'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['level_pedas']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- GAMBAR -->
                        <div class="mb-4">
                            <label for="inputGambar" class="form-label fw-bold">Gambar Produk</label>
                            <div class="border border-2 rounded-3 p-4 text-center position-relative" id="uploadArea">
                                <input
                                    class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    type="file"
                                    id="inputGambar"
                                    name="gambar"
                                    accept=".jpg,.jpeg,.png" />

                                <div id="previewArea" class="<?= $produk['gambar'] ? 'd-none' : '' ?>">
                                    <i class="bi bi-cloud-arrow-up fs-1 text-secondary mb-2"></i>
                                    <p class="mb-1 text-muted">Klik atau seret gambar ke sini</p>
                                    <small class="text-secondary">Ukuran maks. <strong>1 MB</strong> • Format: JPG, PNG</small>
                                </div>

                                <img
                                    id="previewImage"
                                    src="<?= base_url('assets/uploads/produk/' . esc($produk['gambar'])) ?>"
                                    alt="Preview"
                                    class="img-fluid rounded mt-2 <?= $produk['gambar'] ? '' : 'd-none' ?>"
                                    style="max-height: 200px;">
                            </div>
                            <?php if (isset($errors['gambar'])): ?>
                                <div class="invalid-feedback d-block"><?= esc($errors['gambar']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- TOMBOL SIMPAN -->
                        <div class="d-grid gap-2 py-3">
                            <button type="submit" id="btnSubmit" class="btn btn-theme">
                                <span class="btn-text">Perbarui</span>
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
    document.addEventListener("DOMContentLoaded", function() {
        const inputNama = document.getElementById("inputNamaProduk");
        const slugInput = document.getElementById("slug");
        const form = document.querySelector("form");
        const btnSubmit = document.getElementById("btnSubmit");
        const btnText = btnSubmit.querySelector(".btn-text");
        const spinner = btnSubmit.querySelector(".spinner-border");
        const inputFile = document.getElementById("inputGambar");
        const previewArea = document.getElementById("previewArea");
        const previewImage = document.getElementById("previewImage");

        // 🔹 Generate slug otomatis dari nama produk
        inputNama.addEventListener("input", function() {
            let slug = this.value.toLowerCase()
                .replace(/ /g, "-")
                .replace(/[^\w-]+/g, "");
            slugInput.value = slug;
        });

        // 🔹 Preview gambar modern
        inputFile.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (!file) return;

            if (!["image/jpeg", "image/png"].includes(file.type)) {
                alert("Format harus JPG atau PNG!");
                inputFile.value = "";
                return;
            }

            if (file.size > 1024 * 1024) {
                alert("Ukuran gambar maksimal 1 MB!");
                inputFile.value = "";
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                previewImage.src = event.target.result;
                previewImage.classList.remove("d-none");
                previewArea.classList.add("d-none");
            };
            reader.readAsDataURL(file);
        });

        // 🔹 Disable input & tampilkan loading saat submit
        form.addEventListener("submit", function() {
            btnSubmit.disabled = true;
            btnText.textContent = "Loading...";
            spinner.classList.remove("d-none");

            // Nonaktifkan semua input tapi biar tetap terkirim
            form.querySelectorAll("input, select, textarea").forEach(el => {
                el.readOnly = true;
                el.style.opacity = "0.7";
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const kategoriSelect = document.getElementById('inputKategori');
        const levelPedasDiv = document.getElementById('inputLevelPedas').closest('.form-floating');

        function toggleLevelPedas() {
            if (kategoriSelect.value === 'minuman') {
                levelPedasDiv.style.display = 'none';
                document.getElementById('inputLevelPedas').value = '';
            } else {
                levelPedasDiv.style.display = '';
            }
        }

        // Jalankan saat pertama kali halaman edit dibuka
        toggleLevelPedas();

        // Jalankan setiap kali kategori diubah
        kategoriSelect.addEventListener('change', toggleLevelPedas);
    });
</script>


</script>



<?= $this->endSection() ?>
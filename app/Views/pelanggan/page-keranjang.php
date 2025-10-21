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
</style>
<nav aria-label="breadcrumb " class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('pelanggan/produk') ?>">Produk</a></li>
        <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
    </ol>
</nav>

<div class="cards-mobile-wrap">
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

                    </div>



                    <!-- Daftar Item -->
                    <div class="list-group list-group-flush scroll-200">
                        <?php foreach ($items as $it): ?>
                            <?php $itemId = $it['id'] ?? $it['id_keranjang'] ?? $it['produk_id'] ?? null; ?>
                            <div class="list-group-item">
                                <div class="d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= !empty($it['gambar']) ? base_url('assets/img/' . $it['gambar']) : base_url('assets/img/box.png') ?>"
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
                                                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-1"
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
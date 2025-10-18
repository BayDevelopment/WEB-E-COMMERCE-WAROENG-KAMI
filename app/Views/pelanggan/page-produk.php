<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>

<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Produk Kami</li>
    </ol>
</nav>

<?php
// contoh data (hapus jika sudah dari DB)
$products = $products ?? [
    ['id' => 1, 'nama' => 'Nasi Goreng Spesial', 'deskripsi' => 'Nasi gurih, ayam suwir, telur, sayur', 'harga' => 28000, 'gambar' => base_url('assets/img/nasi-goreng.jpeg'), 'qty' => 0],
    ['id' => 2, 'nama' => 'Ayam Bakar Madu', 'deskripsi' => 'Bumbu madu, daging empuk, sambal', 'harga' => 35000, 'gambar' => base_url('assets/img/ayam-bakar-madu.jpg'), 'qty' => 0],
    ['id' => 3, 'nama' => 'Es Teh Manis', 'deskripsi' => 'Teh premium, gula cair, es batu', 'harga' => 8000, 'gambar' => base_url('assets/img/esteh.jpeg'), 'qty' => 0],
];
?>

<div class="cards-mobile-wrap mb-3">
    <div class="row g-3 cards-mobile">
        <?php foreach ($products as $p): ?>
            <div class="col-12">
                <div class="product-card shadow-sm border-0">
                    <img class="product-thumb" src="<?= esc($p['gambar']) ?>" alt="<?= esc($p['nama']) ?>">

                    <div class="product-body mb-2">
                        <div class="d-flex align-items-start justify-content-between">
                            <h6 class="product-title mb-1"><?= esc($p['nama']) ?></h6>
                            <span class="product-price">Rp <?= number_format($p['harga'], 0, ',', '.') ?></span>
                        </div>
                        <p class="product-desc mb-2"><?= esc($p['deskripsi']) ?></p>

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="qty-group" data-id="<?= (int)$p['id'] ?>">
                                <button class="btn-qty minus" type="button" aria-label="Kurangi">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <input class="qty-input" type="text" value="<?= (int)$p['qty'] ?>" inputmode="numeric" pattern="[0-9]*" aria-label="Jumlah">
                                <button class="btn-qty plus" type="button" aria-label="Tambah">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>

                            <form action="<?= base_url('pesanan/tambah') ?>" method="post" class="m-0">
                                <input type="hidden" name="produk_id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="jumlah" value="<?= (int)$p['qty'] ?>" class="jumlah-hidden">
                                <button class="btn btn-add-to-cart" type="submit">
                                    Tambah
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php $count = (int)($cart_count ?? 0); ?>
    <a href="<?= base_url('keranjang'); ?>"
        class="btn-cart-fab"
        aria-label="Buka keranjang"
        <?= $count === 0 ? 'data-empty="1"' : '' ?>>
        <i class="bi bi-cart3"></i>
        <span class="cart-badge"><?= $count ?></span>
    </a>


</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    document.querySelectorAll('.product-card').forEach((card) => {
        const grp = card.querySelector('.qty-group');
        if (!grp) return;
        const input = grp.querySelector('.qty-input');
        const minus = grp.querySelector('.btn-qty.minus');
        const plus = grp.querySelector('.btn-qty.plus');
        const form = card.querySelector('form');
        const hidden = form?.querySelector('.jumlah-hidden');

        const clamp = (n) => Math.max(0, Math.min(999, Number(n) || 0));
        const sync = () => {
            const v = clamp(input.value);
            input.value = v;
            if (hidden) hidden.value = v;
        };

        minus?.addEventListener('click', () => {
            input.value = clamp(input.value) - 1;
            sync();
        });
        plus?.addEventListener('click', () => {
            input.value = clamp(input.value) + 1;
            sync();
        });
        input?.addEventListener('input', sync);

        sync(); // init
    });
</script>
<?= $this->endSection() ?>
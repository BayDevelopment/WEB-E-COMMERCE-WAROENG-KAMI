<?= $this->extend('templates/pelanggan/main') ?>
<?= $this->section('public_content') ?>
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb modern-breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pilih Tempat</li>
    </ol>
</nav>

<!-- Global Page Loader -->
<div id="pageLoader" hidden aria-hidden="true">
    <div class="pl-backdrop"></div>
    <div class="pl-card">
        <div class="pl-spinner" aria-label="Loading"></div>
        <div class="pl-text">Memuat...</div>
        <div class="pl-progress"><span class="pl-bar"></span></div>
    </div>
</div>

<div class="cards-mobile-wrap page-root">
    <div class="row g-3 cards-mobile">
        <div class="col-lg-4">
            <div class="modern-card card-with-photo is-empty">
                <img class="card-photo" src="<?= base_url('assets/img/restaurant.png') ?>" alt="Ilustrasi meja 1">
                <div class="card-overlay"></div>

                <div class="no-tempat">1</div>
                <div class="status-tempat">Kosong</div>

                <!-- Glass panel info -->
                <div class="card-info">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-2"></i>
                        <span>2–4 Orang</span>
                    </div>
                    <button class="btn btn-reserve" type="button">
                        Pilih
                        <i class="bi bi-chevron-right ms-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('templates/pelanggan/main') ?>

<?= $this->section('public_content') ?>
<div id="carouselExampleCaptions" class="carousel slide carousel-fade"
    data-bs-ride="carousel"
    data-bs-interval="4000"
    data-bs-pause="hover"
    data-bs-touch="true">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="<?= base_url('assets/img/bahan-makanan.png') ?>" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5 class="text-uppercase">Kualitas rasa dimulai dari bahan makanan yang baik.</h5>
                <p class="text-capitalize">Menghadirkan cita rasa terbaik dari bahan makanan pilihan yang berkualitas.</p>
            </div>
        </div>
        <div class="carousel-item">
            <img src="<?= base_url('assets/img/restaurant.png') ?>" class="d-block w-100" alt="...">
            <div class="carousel-caption d-none d-md-block">
                <h5 class="text-uppercase">dengan tempat yang nyaman</h5>
                <p class="text-capitalize">Some representative placeholder content for the second slide.</p>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<?= $this->endSection() ?>
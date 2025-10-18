<!doctype html>
<html lang="id">

<?= $this->include('templates/pelanggan/header') ?>

<body>
    <div class="app-shell">
        <header>
            <nav class="navbar bg-native">
                <div class="container">
                    <!-- Brand -->
                    <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="logo-size-navbar me-2">
                    </a>
                    <div class="form-check form-switch">
                        <label class="form-check-label" for="switchCheckDefault"><i class="fa-solid fa-moon"></i></label>
                        <input class="form-check-input" type="checkbox" role="switch" id="switchCheckDefault">
                    </div>
                </div>
            </nav>
        </header>

        <main class="container py-4">
            <?= $this->renderSection('public_content') ?>
        </main>

        <!-- Bottom Navbar ala aplikasi -->
        <!-- Footer: Bottom Navbar Mobile -->
        <?= $this->renderSection('scripts') ?>
        <?= $this->include('templates/pelanggan/footer') ?>


    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
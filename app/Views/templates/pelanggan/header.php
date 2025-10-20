<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>

    <meta name="description" content="<?= esc($seo['desc'] ?? 'Pesan makanan favorit dengan cepat. Menu lengkap, harga ramah, dan pengiriman tepat waktu di Waroeng Kami.') ?>">
    <meta name="keywords" content="<?= esc($seo['keywords'] ?? 'waroeng kami, kuliner cilegon, nasi goreng, ayam bakar, es teh') ?>">
    <meta name="author" content="Bayu Albar Ladici">

    <!-- Canonical & hreflang -->
    <link rel="canonical" href="<?= esc($seo['canonical'] ?? current_url(true)) ?>">
    <link rel="alternate" href="<?= esc($seo['canonical'] ?? current_url(true)) ?>" hreflang="id-ID">
    <link rel="alternate" href="<?= esc($seo['alt_en'] ?? current_url(true)) ?>" hreflang="en" />
    <link rel="alternate" href="<?= esc($seo['canonical'] ?? current_url(true)) ?>" hreflang="x-default" />

    <!-- Open Graph (Facebook, WhatsApp, LinkedIn) -->
    <meta property="og:type" content="<?= esc($seo['og_type'] ?? 'website') ?>">
    <meta property="og:site_name" content="Waroeng Kami">
    <meta property="og:title" content="<?= esc($seo['title'] ?? 'Waroeng Kami — Enak & Praktis') ?>">
    <meta property="og:description" content="<?= esc($seo['desc'] ?? 'Pesan makanan favorit dengan cepat. Menu lengkap, harga ramah, dan pengiriman tepat waktu.') ?>">
    <meta property="og:url" content="<?= esc($seo['canonical'] ?? current_url(true)) ?>">
    <meta property="og:image" content="<?= esc($seo['image'] ?? base_url('assets/og/wk-1200x630.jpg')) ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="id_ID">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($seo['title'] ?? 'Waroeng Kami — Enak & Praktis') ?>">
    <meta name="twitter:description" content="<?= esc($seo['desc'] ?? 'Pesan makanan favorit dengan cepat. Menu lengkap, harga ramah, dan pengiriman tepat waktu.') ?>">
    <meta name="twitter:image" content="<?= esc($seo['image'] ?? base_url('assets/img/logo.png')) ?>">

    <!-- Robots -->
    <meta name="robots" content="<?= esc($seo['robots'] ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1') ?>">

    <!-- Theme color (auto light/dark) -->
    <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0b0f17" media="(prefers-color-scheme: dark)">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Eksternal CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/my_style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- FAVICON DASAR -->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/img/logo.png') ?>">
</head>
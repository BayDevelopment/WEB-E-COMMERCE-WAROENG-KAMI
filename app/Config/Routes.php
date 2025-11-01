<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// public
$routes->get('/', 'HomepageController::index');
$routes->get('pelanggan/produk', 'HomepageController::Data_Produk');
$routes->post('pelanggan/produk', 'HomepageController::Tambah_Produk');
$routes->get('pelanggan/keranjang', 'HomepageController::Data_Keranjang');
$routes->get('pelanggan/keranjang/delete/(:num)', 'HomepageController::delete_keranjang/$1');
$routes->post('pelanggan/keranjang/checkout', 'HomepageController::pesan_sekarang');
$routes->get('pelanggan/success', 'HomepageController::SuksesPembelian');
$routes->get('pelanggan/riwayat', 'HomepageController::RiwayatTemp');
$routes->get('tentang-kami', 'HomepageController::tentang_kami');


$routes->group('auth', ['filter' => 'guest'], static function ($routes) {
    $routes->get('login',  'AuthController::index');
    $routes->post('login', 'AuthController::doLogin');
});

// logout
$routes->post('auth/logout', 'AuthController::logout');

// Admin
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'AdminController::index');
    $routes->get('profile', 'AdminController::profile');
    $routes->post('profile/update', 'AdminController::profile_aksi');
    $routes->post('profile/change-password', 'AdminController::reset_password');
    $routes->get('activity', 'AdminController::activity_log');
    $routes->get('activity/tambah', 'AdminController::page_tambahActivity');
    $routes->post('activity/tambah', 'AdminController::Aksi_tambahActivity');
    $routes->get('activity/edit/(:num)', 'AdminController::page_editActivity/$1');
    $routes->post('activity/update/(:num)', 'AdminController::aksi_editActivity/$1');
    $routes->get('activity/hapus/(:num)', 'AdminController::hapusActivity/$1');
    $routes->get('produk', 'AdminController::page_produk');
    $routes->get('produk/tambah', 'AdminController::page_tambah_produk');
    $routes->post('produk/tambah', 'AdminController::tambah_produk');
    $routes->get('produk/edit/(:num)', 'AdminController::page_edit_produk/$1');
    $routes->post('produk/edit/(:num)', 'AdminController::edit_produk/$1');
    $routes->get('produk/hapus/(:num)', 'AdminController::delete_produk/$1');

    // pelanggan
    $routes->get('pelanggan', 'AdminController::data_pelanggan');
    $routes->get('pelanggan/detail/(:segment)', 'AdminController::detail_pelanggan/$1');
    $routes->post('pelanggan/status', 'AdminController::status');
    $routes->get('pelanggan/hapus/(:num)', 'AdminController::delete_pelanggan/$1');
    $routes->get('pelanggan/cetak/(:segment)', 'AdminController::cetak_struk/$1');

    // pemesanan
    $routes->get('pemesanan', 'AdminController::data_pemesanan');
    $routes->get('pemesanan/tambah', 'AdminController::page_tambah_pemesanan');
    $routes->post('pemesanan/tambah', 'AdminController::tambah_pemesanan');
    $routes->post('pemesanan/checkout', 'AdminController::checkout');
    $routes->get('pemesanan/hapus/(:num)', 'AdminController::kurangi_item_keranjang/$1');

    // Laporan
    $routes->get('laporan', 'AdminController::Laporan');
});

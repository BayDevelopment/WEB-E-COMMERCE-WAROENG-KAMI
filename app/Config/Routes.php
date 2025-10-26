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
});

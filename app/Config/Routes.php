<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomepageController::index');
$routes->get('pelanggan/produk', 'HomepageController::Data_Produk');
$routes->post('pelanggan/produk', 'HomepageController::Tambah_Produk');
$routes->get('pelanggan/keranjang', 'HomepageController::Data_Keranjang');
$routes->get('pelanggan/keranjang/delete/(:num)', 'HomepageController::delete_keranjang/$1');
$routes->post('pelanggan/keranjang/checkout', 'HomepageController::pesan_sekarang');
$routes->get('pelanggan/success', 'HomepageController::SuksesPembelian');
$routes->get('pelanggan/riwayat', 'HomepageController::RiwayatTemp');
$routes->get('pelanggan/pilih-tempat', 'HomepageController::tempat_pelanggan');
$routes->get('tentang-kami', 'HomepageController::tentang_kami');

// Admin
$routes->get('admin/dashboard', 'AdminController::index');

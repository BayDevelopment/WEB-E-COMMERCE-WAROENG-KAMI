<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomepageController::index');
$routes->get('pelanggan/produk', 'HomepageController::Data_Produk');
$routes->get('pelanggan/pilih-tempat', 'HomepageController::tempat_pelanggan');

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class HomepageController extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Homepage | Welcome',
            "nav_link" => 'Homepage'

        ];
        return view('homepage', $data);
    }
    public function Data_Produk()
    {
        $data = [
            'title' => 'Homepage | Data Produk',
            "nav_link" => 'pesanan'

        ];
        return view('pelanggan/page-produk', $data);
    }
    public function tempat_pelanggan()
    {
        $data = [
            'title' => 'Homepage | Pilih Tempat',
            "nav_link" => 'pesanan'
        ];
        return view('pelanggan/page-tempat', $data);
    }
    public function tentang_kami()
    {
        $data = [
            'title' => 'Homepage | Tentang Kami',
            "nav_link" => 'tentang'
        ];
        return view('pelanggan/page-tentang-kami', $data);
    }
}

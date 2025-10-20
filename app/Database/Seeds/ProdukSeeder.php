<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class ProdukSeeder extends Seeder
{
    private function slugify(string $text): string
    {
        // Hilangkan karakter non-alfanumerik kecuali spasi & dash
        $text = trim($text);
        $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
        $text = trim($text, '-');
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = strtolower($text);
        $text = preg_replace('~[^-a-z0-9]+~', '', $text);
        return $text ?: 'item';
    }

    public function run()
    {
        $now = Time::now('Asia/Jakarta');

        $items = [
            ['nama_produk' => 'Nasi Ayam Bakar Madu',              'deskripsi' => 'Nasi dengan ayam bakar madu',                    'harga' => 22000, 'gambar' => 'ayam-bakar-madu.jpg',   'status' => 'tersedia', 'favorit' => 1, 'level_pedas' => 'sedang'],
            ['nama_produk' => 'Es Teh',           'deskripsi' => 'Es dengan teh asli penggunungan',                       'harga' => 5000, 'gambar' => 'esteh.jpeg', 'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => null],
            ['nama_produk' => 'Nasi Goreng',           'deskripsi' => 'Nasi Goreng Spesial dengan nasi premium',                         'harga' => 18000, 'gambar' => 'nasi-goreng.jpeg', 'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'sesuai-pembeli'],
            // ['nama_produk' => 'Nasi Dendeng Balado',       'deskripsi' => 'Dendeng tipis balado merah',                     'harga' => 30000, 'gambar' => 'img/produk/nasi-dendeng.jpg',     'status' => 'tersedia', 'favorit' => 1, 'level_pedas' => 'pedas'],
            // ['nama_produk' => 'Nasi Ayam Pop',             'deskripsi' => 'Ayam pop, sambal, sayur',                        'harga' => 26000, 'gambar' => 'img/produk/nasi-ayam-pop.jpg',    'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'tidak'],
            // ['nama_produk' => 'Nasi Ikan Asam Padeh',      'deskripsi' => 'Ikan kuah asam padeh segar',                     'harga' => 29000, 'gambar' => 'img/produk/nasi-asam-padeh.jpg',  'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'sedang'],
            // ['nama_produk' => 'Nasi Telur Dadar',          'deskripsi' => 'Telur dadar Padang tebal',                       'harga' => 20000, 'gambar' => 'img/produk/nasi-telur-dadar.jpg', 'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'sesuai-pembeli'],
            // ['nama_produk' => 'Nasi Perkedel',             'deskripsi' => 'Perkedel kentang + sambal ijo',                  'harga' => 20000, 'gambar' => 'img/produk/nasi-perkedel.jpg',    'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'tidak'],
            // ['nama_produk' => 'Nasi Kikil Gulai',          'deskripsi' => 'Kikil empuk gulai santan',                       'harga' => 29000, 'gambar' => 'img/produk/nasi-kikil.jpg',       'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'sedang'],
            // ['nama_produk' => 'Nasi Tunjang',              'deskripsi' => 'Tulang lunak bumbu gulai',                       'harga' => 32000, 'gambar' => 'img/produk/nasi-tunjang.jpg',     'status' => 'tersedia', 'favorit' => 1, 'level_pedas' => 'sedang'],
            // ['nama_produk' => 'Nasi Ayam Bakar Madu',      'deskripsi' => 'Ayam bakar madu, sambal, lalap',                 'harga' => 27000, 'gambar' => 'img/produk/nasi-ayam-bakar.jpg',  'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'tidak'],
            // ['nama_produk' => 'Nasi Gulai Nangka',         'deskripsi' => 'Gulai nangka muda, khas Minang',                 'harga' => 18000, 'gambar' => 'img/produk/gulai-nangka.jpg',     'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'sedang'],
            // ['nama_produk' => 'Nasi Paru Balado',          'deskripsi' => 'Paru goreng balado krispi',                      'harga' => 31000, 'gambar' => 'img/produk/paru-balado.jpg',      'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'pedas'],
            // ['nama_produk' => 'Nasi Ayam Cabe Ijo',        'deskripsi' => 'Ayam suwir cabe ijo',                            'harga' => 26000, 'gambar' => 'img/produk/ayam-cabe-ijo.jpg',    'status' => 'habis',    'favorit' => 0, 'level_pedas' => 'sedang'],
            // ['nama_produk' => 'Nasi Ikan Panggang',        'deskripsi' => 'Ikan panggang bumbu kuning',                     'harga' => 27500, 'gambar' => 'img/produk/ikan-panggang.jpg',    'status' => 'tersedia', 'favorit' => 0, 'level_pedas' => 'sesuai-pembeli'],
        ];

        // Lengkapi slug & timestamp
        $rows = [];
        foreach ($items as $it) {
            $rows[] = [
                'nama_produk' => $it['nama_produk'],
                'slug'        => $this->slugify($it['nama_produk']),
                'deskripsi'   => $it['deskripsi'],
                'harga'       => $it['harga'],
                'gambar'      => $it['gambar'],
                'status'      => $it['status'],
                'favorit'     => $it['favorit'],
                'level_pedas' => $it['level_pedas'],
                'created_at'  => $now->toDateTimeString(),
                'updated_at'  => $now->toDateTimeString(),
            ];
        }

        $this->db->table('tb_produk')->insertBatch($rows);
    }
}

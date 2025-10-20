<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MejaSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        // Matikan FK agar truncate aman jika ada relasi
        $db->query('SET FOREIGN_KEY_CHECKS=0;');
        $db->table('tb_meja')->truncate();

        // Helper format kode: MJ001, MJ002, ...
        $fmt = static function (int $n): string {
            return 'MJ' . str_pad((string)$n, 3, '0', STR_PAD_LEFT);
        };

        // Data sample
        $rows = [
            ['kode_meja' => $fmt(1),  'nama_meja' => 'Meja Depan A',     'kapasitas' => 4, 'is_active' => 1, 'keterangan' => 'Dekat pintu masuk'],
            ['kode_meja' => $fmt(2),  'nama_meja' => 'Meja Depan B',     'kapasitas' => 4, 'is_active' => 1, 'keterangan' => null],
            ['kode_meja' => $fmt(3),  'nama_meja' => 'Meja Family A',    'kapasitas' => 6, 'is_active' => 1, 'keterangan' => 'Area tengah'],
            ['kode_meja' => $fmt(4),  'nama_meja' => 'Meja Family B',    'kapasitas' => 6, 'is_active' => 1, 'keterangan' => null],
            ['kode_meja' => $fmt(5),  'nama_meja' => 'Meja Jendela A',   'kapasitas' => 2, 'is_active' => 1, 'keterangan' => 'View luar'],
            ['kode_meja' => $fmt(6),  'nama_meja' => 'Meja Jendela B',   'kapasitas' => 2, 'is_active' => 1, 'keterangan' => null],
            ['kode_meja' => $fmt(7),  'nama_meja' => 'Meja Bar A',       'kapasitas' => 1, 'is_active' => 1, 'keterangan' => 'Single seat'],
            ['kode_meja' => $fmt(8),  'nama_meja' => 'Meja Bar B',       'kapasitas' => 1, 'is_active' => 1, 'keterangan' => null],
            ['kode_meja' => $fmt(9),  'nama_meja' => 'Meja VIP A',       'kapasitas' => 8, 'is_active' => 1, 'keterangan' => 'Ruang VIP'],
            ['kode_meja' => $fmt(10), 'nama_meja' => 'Meja VIP B',       'kapasitas' => 8, 'is_active' => 0, 'keterangan' => 'Perlu perbaikan kursi'],
            ['kode_meja' => $fmt(11), 'nama_meja' => 'Meja Outdoor A',   'kapasitas' => 4, 'is_active' => 1, 'keterangan' => 'Area merokok'],
            ['kode_meja' => $fmt(12), 'nama_meja' => 'Meja Outdoor B',   'kapasitas' => 4, 'is_active' => 1, 'keterangan' => null],
        ];

        $db->table('tb_meja')->insertBatch($rows);

        $db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
}

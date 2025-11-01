<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKategoriTbProduk extends Migration
{
    public function up()
    {
        $fields = [
            'kategori' => [
                'type'       => 'ENUM',
                'constraint' => ['makanan', 'minuman'],
                'default'    => 'makanan', // ✅ default harus salah satu dari constraint
                'null'       => false,
                'after'      => 'nama_produk', // ✅ letakkan setelah kolom 'nama_produk'
            ],
        ];

        $this->forge->addColumn('tb_produk', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_produk', 'kategori');
    }
}

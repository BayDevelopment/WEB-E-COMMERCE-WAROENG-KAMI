<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ProdukMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_produk'   => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => false],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 140, 'null' => false],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'harga'       => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'gambar'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'      => ['type' => 'ENUM', 'constraint' => ['tersedia', 'habis'], 'default' => 'tersedia', 'null' => false],
            'favorit'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'null' => false],
            'level_pedas' => ['type' => 'ENUM', 'constraint' => ['tidak', 'sedang', 'pedas', 'sesuai-pembeli'], 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],

        ]);

        $this->forge->addKey('id_produk', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('nama_produk', false, false, 'idx_nama_produk');

        $this->forge->createTable('tb_produk', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_produk', true);
    }
}

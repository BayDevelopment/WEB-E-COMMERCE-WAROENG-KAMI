<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KeranjangMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_keranjang' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'produk_id'    => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => false],
            'jumlah'       => ['type' => 'INT', 'constraint' => 10, 'null' => false, 'default' => 1], // <-- porsi yang dipesan
            'harga'        => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],       // snapshot harga saat tambah
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => false],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_keranjang', true);
        $this->forge->addUniqueKey('produk_id'); // satu baris per produk → mudah merge jumlah
        $this->forge->createTable('tb_keranjang', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_keranjang', true);
    }
}

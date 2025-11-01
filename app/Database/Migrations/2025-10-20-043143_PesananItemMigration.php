<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PesananItemMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_pesanan_item' => ['type' => 'INT',    'constraint' => 11,  'unsigned' => true, 'auto_increment' => true],
            'pesanan_id'      => ['type' => 'BIGINT', 'constraint' => 20,  'unsigned' => true, 'null' => false], // <- DISAMAKAN
            'produk_id'       => ['type' => 'INT',    'constraint' => 11,  'unsigned' => true, 'null' => false],
            'nama_produk'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'qty'             => ['type' => 'INT',    'constraint' => 11,  'unsigned' => true, 'default' => 1],
            'harga'           => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'subtotal'        => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => '0.00'],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_pesanan_item', true);
        $this->forge->addKey('pesanan_id');
        $this->forge->addKey('produk_id');

        $this->forge->addForeignKey(
            'pesanan_id',
            'tb_pesanan',
            'id_pesanan',
            'CASCADE',
            'CASCADE',
            'fk_pesanan_item_pesanan'
        );
        // $this->forge->addForeignKey(
        //     'produk_id',
        //     'tb_produk',
        //     'id_produk',
        //     'RESTRICT',
        //     'CASCADE',
        //     'fk_pesanan_item_produk'
        // );

        $this->forge->createTable('tb_pesanan_item', true, [
            'ENGINE'          => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE'         => 'utf8mb4_unicode_ci',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tb_pesanan_item', true);
    }
}

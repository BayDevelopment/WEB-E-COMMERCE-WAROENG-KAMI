<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOwnerKeyToKeranjang extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_keranjang', [
            'owner_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
                'after'      => 'id_keranjang',
            ],
        ]);
        $this->db->query('CREATE INDEX idx_owner_key ON tb_keranjang(owner_key)');
        // (opsional) cegah duplikasi item yang sama:
        // $this->db->query('CREATE UNIQUE INDEX uniq_owner_produk ON tb_keranjang(owner_key, produk_id)');
    }

    public function down()
    {
        // rollback
        $this->forge->dropColumn('tb_keranjang', 'owner_key');
        // index akan ikut hilang saat kolom di-drop pada sebagian besar engine;
        // kalau tidak, drop index manual.
    }
}

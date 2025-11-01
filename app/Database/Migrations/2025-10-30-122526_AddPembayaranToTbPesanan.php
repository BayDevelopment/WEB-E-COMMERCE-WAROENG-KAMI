<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPembayaranToTbPesanan extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tb_pesanan', [
            'pembayaran' => [
                'type' => 'ENUM',
                'constraint' => ['cash', 'qris'],
                'default' => 'cash',
                'after' => 'total', // letakkan setelah kolom total
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tb_pesanan', 'pembayaran');
    }
}

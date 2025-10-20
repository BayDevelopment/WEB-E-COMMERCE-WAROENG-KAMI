<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class PesananMigration extends Migration
{
    public function up()
    {
        // 1) Buat tabel tanpa FK dulu
        $this->forge->addField([
            'id_pesanan'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'auto_increment' => true],
            'kode_pesanan'   => ['type' => 'VARCHAR', 'constraint' => 24],
            'owner_key'      => ['type' => 'VARCHAR', 'constraint' => 64],

            'nama_pelanggan' => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'alamat'         => ['type' => 'TEXT', 'null' => true],
            'makan_ditempat' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'meja_id'        => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => true, 'null' => true], // NULL agar SET NULL valid

            'total'          => ['type' => 'DECIMAL', 'constraint' => '14,2', 'default' => '0.00'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['baru', 'selesai', 'batal'], 'default' => 'baru'],

            // pakai TIMESTAMP + RawSql agar MySQL tidak meng-quote
            'created_at'     => ['type' => 'TIMESTAMP', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id_pesanan', true);
        $this->forge->addUniqueKey('kode_pesanan');
        $this->forge->addKey('owner_key');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->addKey('meja_id');

        $this->forge->createTable('tb_pesanan', true, [
            'ENGINE' => 'InnoDB',
            'COMMENT' => 'Master pesanan (relasi meja)',
        ]);

        // 2) Tambahkan FOREIGN KEY via RAW SQL (pasti ON DELETE SET NULL, ON UPDATE CASCADE)
        $this->db->query("
            ALTER TABLE `tb_pesanan`
            ADD CONSTRAINT `fk_pesanan_meja`
            FOREIGN KEY (`meja_id`) REFERENCES `tb_meja`(`id_meja`)
            ON DELETE SET NULL
            ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        // Drop FK dulu (jika ada), lalu tabel
        // Aman kalau FK belum ada: bungkus try-catch ringan
        try {
            $this->db->query("ALTER TABLE `tb_pesanan` DROP FOREIGN KEY `fk_pesanan_meja`");
        } catch (\Throwable $e) {
            // abaikan
        }
        $this->forge->dropTable('tb_pesanan', true);
    }
}

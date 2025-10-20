<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class MejaMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_meja' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_meja' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_meja' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kapasitas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 1,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'keterangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            // gunakan TIMESTAMP + RawSql agar default CURRENT_TIMESTAMP tidak di-quote
            'created_at' => [
                'type'    => 'TIMESTAMP',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_meja', true);
        $this->forge->addUniqueKey('kode_meja');
        $this->forge->addKey('is_active');

        $this->forge->createTable('tb_meja', true, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => 'Daftar meja dine-in',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tb_meja', true);
    }
}

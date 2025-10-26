<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TbAdminMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_admin' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // CORE AUTH
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 191,
            ],
            'password_hash' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role' => [
                // kalau pakai driver non-MySQL, ENUM bisa diubah ke VARCHAR(20)
                'type'       => "ENUM('admin','karyawan')",
                'default'    => 'admin',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],

            // PROFIL OPSIONAL
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['pria', 'wanita'],
                'null'       => false,
                'default'    => 'pria', // pilih salah satu: 'pria' atau 'wanita'
                'comment'    => 'pria/wanita',
            ],

            // AUDIT BASIC
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'login_attempts' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'default'    => 0,
            ],

            // TIMESTAMPS & SOFT DELETE
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false
            ],
            'updated_at' => [
                'type'      => 'DATETIME',
                'null'      => false
            ]
        ]);

        $this->forge->addKey('id_admin', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addKey('role');
        $this->forge->addKey('is_active');

        $this->forge->createTable('tb_admin', true, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => 'Table admin/seller mikro: email-only login + manual reset',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tb_admin', true);
    }
}

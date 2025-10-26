<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $now = Time::now('Asia/Jakarta');

        $data = [
            [
                'email'         => 'root@denokshop.my.id',
                'password_hash' => password_hash('@bayudev123', PASSWORD_ARGON2ID),
                'role'          => 'admin',
                'is_active'     => 1,
                'nama_lengkap'  => 'Bayudev',
                'no_telp'       => '081200000001',
                'jenis_kelamin'       => 'pria',
                'avatar'        => 'boy.png',
                'last_login_at' => Time::now('Asia/Jakarta'),
                'login_attempts' => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]
        ];

        // insert batch
        $this->db->table('tb_admin')->insertBatch($data);
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class TbAdminModel extends Model
{
    protected $table            = 'tb_admin';
    protected $primaryKey       = 'id_admin';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email',    'password_hash',    'role',    'is_active',    'nama_lengkap',    'no_telp',    'avatar', 'jenis_kelamin',    'last_login_at',    'login_attempts'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';
}

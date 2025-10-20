<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananItemModel extends Model
{
    protected $table            = 'tb_pesanan_item';
    protected $primaryKey       = 'id_pesanan_item';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['pesanan_id',    'produk_id',    'nama_produk',    'qty',    'harga',    'subtotal'];


    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';
}

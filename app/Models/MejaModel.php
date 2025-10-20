<?php

namespace App\Models;

use CodeIgniter\Model;

class MejaModel extends Model
{
    protected $table            = 'tb_meja';
    protected $primaryKey       = 'id_meja';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_meja',    'nama_meja',    'kapasitas',    'is_active',    'keterangan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    protected function getMejaTersedia(): array
    {
        // Status pesanan yang dianggap “masih memakai meja”
        $aktifStatuses = ['menunggu_bayar', 'menunggu_konfirmasi', 'diproses', 'siap_saji', 'sedang_makan'];

        $mejaM = new MejaModel();

        // Ambil meja aktif yang TIDAK muncul pada tb_pesanan aktif (dine-in)
        $mejaTersedia = $mejaM->select('tb_meja.id_meja, tb_meja.kode_meja, tb_meja.nama_meja, tb_meja.kapasitas')
            ->where('tb_meja.is_active', 1)
            ->where(
                "NOT EXISTS (
                SELECT 1 FROM tb_pesanan p
                WHERE p.makan_ditempat = 1
                  AND p.meja_id = tb_meja.id_meja
                  AND p.status NOT IN (" . implode(',', array_map(fn($s) => $mejaM->db->escape($s), $aktifStatuses)) . ")
            )",
                null,
                false // jangan auto-escape subquery
            )
            ->orderBy('tb_meja.kode_meja', 'ASC')
            ->findAll();

        return $mejaTersedia;
    }
}

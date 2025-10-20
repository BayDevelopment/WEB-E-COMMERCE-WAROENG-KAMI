<?php

use CodeIgniter\Database\BaseConnection;

/** "KP0001" formatter */
function oc_format(string $prefix, int $num, int $width = 4): string
{
    return $prefix . str_pad((string)$num, $width, '0', STR_PAD_LEFT);
}

/** Preview kode berikutnya (tanpa lock) */
function peek_next_kode_from_pesanan(BaseConnection $db, string $prefix = 'KP', int $width = 4): string
{
    $len = strlen($prefix) + 1; // MySQL SUBSTRING 1-based
    $sql = "
        SELECT MAX(CAST(SUBSTRING(kode_pesanan, ?) AS UNSIGNED)) AS last_num
        FROM tb_pesanan
        WHERE kode_pesanan IS NOT NULL
          AND kode_pesanan <> ''
          AND kode_pesanan LIKE ?
    ";
    $row = $db->query($sql, [$len, "{$prefix}%"])->getRowArray();
    $next = (int)($row['last_num'] ?? 0) + 1;
    return oc_format($prefix, $next, $width);
}

/**
 * Klaim kode + INSERT master secara atomic.
 * @return array{0:string $kode, 1:int $idPesanan}
 */
function claim_next_kode_from_pesanan(
    BaseConnection $db,
    array $payloadWithoutKode,
    string $prefix = 'KP',
    int $width = 4
): array {
    $len = strlen($prefix) + 1;

    $db->query('LOCK TABLES tb_pesanan WRITE');
    try {
        $sql = "
            SELECT MAX(CAST(SUBSTRING(kode_pesanan, ?) AS UNSIGNED)) AS last_num
            FROM tb_pesanan
            WHERE kode_pesanan IS NOT NULL
              AND kode_pesanan <> ''
              AND kode_pesanan LIKE ?
        ";
        $row = $db->query($sql, [$len, "{$prefix}%"])->getRowArray();
        $nextNum = (int)($row['last_num'] ?? 0) + 1;
        $kode    = oc_format($prefix, $nextNum, $width);

        $insert  = array_merge(['kode_pesanan' => $kode], $payloadWithoutKode);
        $db->table('tb_pesanan')->insert($insert);
        $idPesanan = (int)$db->insertID();

        $db->query('UNLOCK TABLES');
        return [$kode, $idPesanan];
    } catch (\Throwable $e) {
        $db->query('UNLOCK TABLES');
        throw $e;
    }
}

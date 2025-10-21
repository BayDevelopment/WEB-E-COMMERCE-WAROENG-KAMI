<?php

if (!function_exists('waktu_indonesia')) {
    /**
     * Format waktu ke gaya Indonesia.
     * 
     * @param string|int|null $datetime  Timestamp, string tanggal, atau null (otomatis waktu sekarang)
     * @param bool $fullFormat           true → "Rabu, 22 Oktober 2025 21:10 WIB"
     *                                   false → "22-10-2025 21:10 WIB"
     * @return string
     */
    function waktu_indonesia($datetime = null, bool $fullFormat = true): string
    {
        // Zona waktu Indonesia barat (WIB)
        date_default_timezone_set('Asia/Jakarta');

        if ($datetime === null) {
            $timestamp = time();
        } elseif (is_numeric($datetime)) {
            $timestamp = (int)$datetime;
        } else {
            $timestamp = strtotime($datetime);
        }

        // Nama hari dan bulan Indonesia
        $hari = [
            'Sunday'    => 'Minggu',
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
        ];
        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $hariNama  = $hari[date('l', $timestamp)];
        $tgl       = date('j', $timestamp);
        $bln       = $bulan[(int)date('n', $timestamp)];
        $thn       = date('Y', $timestamp);
        $jam       = date('H:i', $timestamp);

        if ($fullFormat) {
            return sprintf('%s, %d %s %s %s WIB', $hariNama, $tgl, $bln, $thn, $jam);
        }

        // versi pendek: 22-10-2025 21:10 WIB
        return date('d-m-Y H:i', $timestamp) . ' WIB';
    }
}

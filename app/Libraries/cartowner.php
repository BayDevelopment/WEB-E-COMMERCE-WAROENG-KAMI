<?php

// App/Libraries/CartOwner.php
namespace App\Libraries;

class CartOwner
{
    public static function key(): string
    {
        $sess = session();
        // Jika sudah ada, pakai terus
        $ok = (string) ($sess->get('owner_key') ?? '');
        if ($ok !== '') return $ok;

        // Belum ada → inisialisasi (hindari ketergantungan penuh pada session_id yang bisa ganti)
        $seed = session_id();
        if (!$seed) {
            @session_start();
            $seed = session_id();
        }
        if (!$seed) {
            $seed = bin2hex(random_bytes(16));
        } // fallback

        $ok = 'sess:' . $seed;
        $sess->set('owner_key', $ok);
        return $ok;
    }
}

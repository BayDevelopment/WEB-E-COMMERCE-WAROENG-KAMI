<?php

namespace App\Libraries;

use Config\Services;
use Config\App;

class CartOwner
{
    const COOKIE_NAME = 'wk_owner';
    const COOKIE_TTL  = 60 * 60 * 24 * 30; // 30 hari

    public static function key(): string
    {
        // optional: aktifkan sesi (tidak dipakai untuk owner_key, hanya amankan request)
        $session = Services::session();
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $session->start();
        }

        $req  = Services::request();
        $resp = Services::response();

        // baca cookie persistent
        $cid = (string) ($req->getCookie(self::COOKIE_NAME) ?? '');

        if ($cid === '') {
            $cid = bin2hex(random_bytes(16));

            /** @var \Config\App $app */
            $app = config(App::class);

            // 🔧 ambil properti via array → IDE friendly, no red underlines
            $cfg            = get_object_vars($app);
            $cookieDomain   = (string)($cfg['cookieDomain']   ?? '');
            $cookiePath     = (string)($cfg['cookiePath']     ?? '/');
            $cookiePrefix   = (string)($cfg['cookiePrefix']   ?? '');
            $cookieSecure   = (bool)  ($cfg['cookieSecure']   ?? false);
            $cookieSameSite = (string)($cfg['cookieSameSite'] ?? 'Lax');

            // set cookie (bentuk array → aman di semua CI4)
            $resp->setCookie([
                'name'     => self::COOKIE_NAME,
                'value'    => $cid,
                'expire'   => self::COOKIE_TTL,   // detik
                'domain'   => $cookieDomain,
                'path'     => $cookiePath ?: '/',
                'prefix'   => $cookiePrefix,
                'secure'   => $cookieSecure,
                'httponly' => true,
                'samesite' => $cookieSameSite,
            ]);
        }

        // owner_key final berbasis cookie (tanpa “:” → lolos alpha_dash)
        return 'owner-' . $cid;
    }
}

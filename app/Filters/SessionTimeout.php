<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SessionTimeout implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $resp    = service('response');
        $app     = config('App');

        // ✅ ambil path via getUri(), bukan $req->uri
        $uriPath = trim($request->getUri()->getPath(), '/');
        $isAuth  = strpos($uriPath, 'auth') === 0;  // whitelist auth/*

        if ($isAuth) return;

        $sess     = session();
        $isLogged = $sess->get('isLoggedInAdmin') === true;

        if (! $isLogged) return;

        $req = service('request');
        $exp = (int) ($req->getCookie('admin_exp') ?? 0);

        // cookie ADA & expired -> logout + redirect
        if ($exp && $exp < time()) {
            $sess->remove(['isLoggedInAdmin', 'admin_id', 'admin_role', 'admin_name']);
            $sess->regenerate(true);

            $cookiePath   = $app->cookiePath   ?? '/';
            $cookieDomain = $app->cookieDomain ?? '';

            $redirect = redirect()
                ->to(site_url('auth/login'))
                ->with('error', 'Sesi berakhir (18 jam). Silakan login lagi.');
            // ⚠️ urutan: name, domain, path
            $redirect->deleteCookie('admin_exp', $cookieDomain, $cookiePath);
            $redirect->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->setHeader('Pragma', 'no-cache');

            return $redirect;
        }

        // cookie TIDAK ADA (0) tapi user sudah login -> refresh, jangan tendang
        if (! $exp) {
            $expireSec = 18 * 60 * 60;
            $expiresAt = time() + $expireSec;

            $cookiePath   = $app->cookiePath   ?? '/';
            $cookieDomain = $app->cookieDomain ?? '';
            $secure       = $req->isSecure();

            $resp->setCookie(
                'admin_exp',
                (string) $expiresAt,
                $expireSec,
                $cookieDomain,  // domain
                $cookiePath,    // path
                '',             // prefix
                $secure,        // secure
                true,           // httpOnly
                'Lax'           // sameSite
            );
        }
    }


    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}

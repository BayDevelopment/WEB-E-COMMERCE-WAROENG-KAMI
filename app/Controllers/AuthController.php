<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TbAdminModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class AuthController extends BaseController
{
    protected $AdminModel;
    public function __construct()
    {
        $this->AdminModel = new TbAdminModel();
    }
    public function index()
    {
        if (session('isLoggedInAdmin')) {
            return redirect()->to(site_url('admin/dashboard')); // <- penting: return
        }

        return view('auth', ['title' => 'Login | Waroeng Kami']);
    }

    public function doLogin()
    {
        $rules = [
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|max_length[50]',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email nggak valid.',
                    'max_length'  => 'Email terlalu panjang.',
                ],
            ],
            'password_hash' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[6]|max_length[50]',
                'errors' => [
                    'required'   => 'Password wajib diisi.',
                    'min_length' => 'Password minimal 6 karakter.',
                    'max_length' => 'Password terlalu panjang.',
                ],
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Periksa kembali isianmu ya.');
        }

        $email = trim((string) $this->request->getPost('email'));
        $pass  = (string) $this->request->getPost('password_hash');

        // Throttle login
        $attemptKey = 'auth_attempts_' . sha1($this->request->getIPAddress() . '|' . strtolower($email));
        $attempts   = (int) (session()->get($attemptKey) ?? 0);
        if ($attempts >= 7) {
            usleep(300000);
            return redirect()->back()
                ->withInput()
                ->with('errors', ['email' => 'Terlalu banyak percobaan. Coba lagi nanti.'])
                ->with('error', 'Terlalu banyak percobaan. Coba lagi nanti.');
        }

        $model = new TBAdminModel();
        $user  = $model->where('email', $email)->first();

        $valid = $user && (int)$user['is_active'] === 1 && password_verify($pass, $user['password_hash']);

        if (! $valid) {
            session()->set($attemptKey, $attempts + 1);

            if ($user) {
                $model->update($user['id_admin'], [
                    'login_attempts' => min(255, (int)($user['login_attempts'] ?? 0) + 1),
                ]);
            }

            usleep(300000);
            return redirect()->back()
                ->withInput()
                ->with('errors', [
                    'email'         => 'Email atau password salah.',
                    'password_hash' => 'Email atau password salah.',
                ])
                ->with('error', 'Email atau password salah.');
        }

        // Login berhasil
        session()->remove($attemptKey);
        $nowJakarta = Time::now('Asia/Jakarta')->toDateTimeString();
        $model->update($user['id_admin'], [
            'last_login_at'  => $nowJakarta,
            'login_attempts' => 0,
        ]);

        session()->regenerate(true);
        session()->set([
            'isLoggedInAdmin' => true,
            'admin_id'        => $user['id_admin'],
            'admin_role'      => $user['role'],
            'admin_name'      => ($user['nama_lengkap'] ?: $user['email'])
        ]);

        // Cek role → admin bisa ke dashboard, selain admin dikembalikan ke login
        if ($user['role'] === 'admin') {
            return redirect()->to(site_url('admin/dashboard'))
                ->with('success', 'Selamat, Login Berhasil')
                ->withCookies();
        } else {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Ups, akses ditolak. Hanya admin yang bisa masuk dashboard.');
        }
    }



    public function logout()
    {
        helper('cookie');
        $resp = service('response');
        $sess = session();

        // hapus session
        $sess->remove(['isLoggedInAdmin', 'admin_id', 'admin_role', 'admin_name']);
        $sess->regenerate(true);

        // hapus cookie kadaluarsa
        $app          = config('App');
        $cookiePath   = $app->cookiePath ?? '/';
        $cookieDomain = $app->cookieDomain ?? '';
        $resp->deleteCookie('admin_exp', $cookiePath, $cookieDomain);

        $resp->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache');

        $sess->setFlashdata('success', 'Selamat, berhasil logout!');
        return redirect()->to(site_url('auth/login'))->withCookies();
    }
}

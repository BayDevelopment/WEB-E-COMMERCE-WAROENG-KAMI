<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PesananModel;
use App\Models\TbAdminModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

class AdminController extends BaseController
{
    protected $PesananModel;
    protected $AdminModel;

    public function __construct()
    {
        $this->PesananModel = new PesananModel();
        $this->AdminModel = new TbAdminModel();
    }

    public function index()
    {
        $db = db_connect();

        // ✅ ambil dari session (HARUS pakai string key)
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }
        // ===== Waktu Asia/Jakarta + batas bulan (tanpa startOfMonth) =====
        $now = Time::now('Asia/Jakarta');

        // Awal bulan berjalan: YYYY-mm-01 00:00:00
        $startObj = (clone $now)
            ->setDate($now->getYear(), $now->getMonth(), 1)
            ->setTime(0, 0, 0);

        // Awal bulan berikutnya
        $nextObj  = (clone $startObj)->addMonths(1);

        // Bulan sebelumnya [prevStart, prevNext)
        $prevStartObj = (clone $startObj)->subMonths(1);
        $prevNextObj  = (clone $startObj);

        // String untuk query
        $start    = $startObj->toDateTimeString();
        $next     = $nextObj->toDateTimeString();
        $prevStart = $prevStartObj->toDateTimeString();
        $prevNext = $prevNextObj->toDateTimeString();

        // ===== 1) Data Pelanggan (distinct owner_key pesanan selesai) =====
        $totalPelanggan = (int) $db->table('tb_pesanan')
            ->select('COUNT(DISTINCT owner_key) AS c')
            ->where('status', 'selesai')
            ->get()->getRow('c');

        // (opsional) Data Pelanggan Bulan Ini
        $pelangganBulanIni = (int) $db->table('tb_pesanan')
            ->select('COUNT(DISTINCT owner_key) AS c')
            ->where('status', 'selesai')
            ->where('created_at >=', $start)
            ->where('created_at <',  $next)
            ->get()->getRow('c');

        // ===== 2) Jumlah Produk aktif =====
        $jumlahProduk = (int) $db->table('tb_produk')
            ->where('status', 1)->countAllResults();

        // ===== 3) Paling laris (bulan ini) berdasarkan qty =====
        $rowTop = $db->table('tb_pesanan_item i')
            ->select('i.produk_id, p.nama_produk, SUM(i.qty) AS total_qty')
            ->join('tb_pesanan h', 'h.id_pesanan = i.pesanan_id', 'left')
            ->join('tb_produk p', 'p.id_produk = i.produk_id', 'left')
            ->where('h.created_at >=', $start)
            ->where('h.created_at <',  $next)
            ->where('h.status', 'selesai')         // ⬅️ ini yang penting
            ->groupBy('i.produk_id, p.nama_produk')
            ->orderBy('total_qty', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $palingLaris = [
            'nama' => $rowTop['nama_produk'] ?? '—',
            'qty'  => (int)($rowTop['total_qty'] ?? 0),
        ];

        // ===== 4) Omzet bulan ini (gross) dari pesanan SELESAI =====
        $rowOmzet = $db->table('tb_pesanan h')
            ->select('COALESCE(SUM(h.total),0) AS omzet')
            ->where('h.created_at >=', $start)
            ->where('h.created_at <',  $next)
            ->where('h.status', 'selesai')
            ->get()->getRowArray();

        $omzetBulanIni = (float)($rowOmzet['omzet'] ?? 0);

        // ===== Tren vs bulan lalu (opsional) =====
        $rowPrev = $db->table('tb_pesanan')
            ->select('COALESCE(SUM(total),0) AS omzet')
            ->where('created_at >=', $prevStart)
            ->where('created_at <',  $prevNext)
            ->where('status', 'selesai')
            ->get()->getRowArray();

        $omzetPrev = (float)($rowPrev['omzet'] ?? 0);
        $growthPct = $omzetPrev > 0 ? (($omzetBulanIni - $omzetPrev) / $omzetPrev) * 100 : null;

        // ===== Helper format Rupiah =====
        $fmt = static function ($n) {
            return 'Rp ' . number_format($n, 0, ',', '.');
        };

        $bulanLabel = $now->toLocalizedString('MMMM yyyy'); // contoh: Oktober 2025
        $data = [
            'stats' => [
                'pelanggan'       => $totalPelanggan,        // lifetime
                'pelangganBulan'  => $pelangganBulanIni,     // opsional: bulan ini
                'produk'          => $jumlahProduk,
                'palingLaris'     => $palingLaris,
                'omzetBulan'      => $omzetBulanIni,
                'omzetBulanFmt'   => $fmt($omzetBulanIni),
                'growthPct'       => $growthPct,             // bisa null jika bulan lalu 0
                'bulanLabel'      => $bulanLabel,
            ],
            'title' => 'Dashboard Admin | Waroeng Kami',
            'nav_link' => 'Dashboard',
            'breadcumb' => 'Dashboard Admin',
            'admin' => $admin
        ];

        return view('admin/dashboard-admin', $data);
    }

    public function profile()
    {
        $adminId = session('admin_id'); // set ini saat login

        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        $data = [
            'title'     => 'Profile Saya | Waroeng Kami',
            'nav_link'  => 'Profile',
            'breadcrumb' => 'Profile', // typo kamu: breadcumb
            'admin'     => $admin,         // <-- kirim ke view
        ];

        return view('admin/profile', $data);
    }
    public function profile_aksi()
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        // ------- Validasi dasar (tanpa avatar) -------
        $rules = [
            'nama_lengkap' => 'required|min_length[3]|max_length[50]',
            'no_telp'      => 'permit_empty|min_length[8]|max_length[13]|regex_match[/^[0-9+\-\s]+$/]',
            'email'        => 'permit_empty|valid_email|is_unique[tb_admin.email,id_admin,' . $adminId . ']',
        ];
        $messages = [
            'nama_lengkap' => [
                'required'    => 'Nama lengkap wajib diisi',
                'min_length'  => 'Minimal 3 karakter',
            ],
            'email' => [
                'valid_email' => 'Format email tidak valid',
                'is_unique'   => 'Email sudah dipakai akun lain',
            ],
            'no_telp' => [
                'regex_match' => 'No. telepon hanya boleh angka, spasi, + atau -',
            ],
        ];

        // ------- Validasi avatar jika diupload -------
        $file = $this->request->getFile('avatar');
        $hasAvatar = $file && $file->isValid() && !$file->hasMoved();

        if ($hasAvatar) {
            $rules['avatar'] = 'is_image[avatar]|max_size[avatar,1024]|mime_in[avatar,image/jpg,image/jpeg,image/png]';
            $messages['avatar'] = [
                'is_image' => 'File avatar harus gambar',
                'max_size' => 'Ukuran avatar maks 1MB',
                'mime_in'  => 'Format avatar harus JPG/PNG',
            ];
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // ------- Build data update -------
        $payload = [
            'nama_lengkap' => trim($this->request->getPost('nama_lengkap')),
            'no_telp'      => trim($this->request->getPost('no_telp')),
        ];

        $email = trim((string) $this->request->getPost('email'));
        if ($email !== '') {
            $payload['email'] = $email;
        }

        // ------- Proses avatar (opsional) -------
        if ($hasAvatar) {
            // buat nama file acak, pindahkan
            $newName = $file->getRandomName();

            // simpan ke public/uploads/avatars (web-accessible)
            $target  = FCPATH . 'assets/uploads/avatars';

            if (!is_dir($target)) {
                @mkdir($target, 0775, true);
            }

            if (!$file->hasMoved()) {
                $file->move($target, $newName);
            }

            // OPTIONAL: resize/fit kalau mau
            // service('image')->withFile($target.'/'.$newName)->fit(512, 512, 'center')->save($target.'/'.$newName);

            // hapus avatar lama (kalau ada)
            if (!empty($admin['avatar'])) {
                $old = rtrim($target, '/\\') . DIRECTORY_SEPARATOR . $admin['avatar'];
                if (is_file($old)) @unlink($old);
            }

            // simpan hanya nama file di DB
            $payload['avatar'] = $newName;
        }

        // ------- Simpan -------
        try {
            $this->AdminModel->update($adminId, $payload);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan profil: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/profile'))->with('success', 'Profil berhasil diperbarui.');
    }

    public function reset_password()
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        // Validasi input
        $rules = [
            'password_lama'    => 'required|min_length[8]',
            'password_hash'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password_hash]',
        ];
        $messages = [
            'password_lama' => [
                'required'   => 'Password lama wajib diisi',
                'min_length' => 'Minimal 8 karakter',
            ],
            'password_hash' => [
                'required'   => 'Password baru wajib diisi',
                'min_length' => 'Minimal 8 karakter',
            ],
            'password_confirm' => [
                'required' => 'Konfirmasi password wajib diisi',
                'matches'  => 'Konfirmasi tidak cocok dengan password baru',
            ],
        ];


        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $old = (string) $this->request->getPost('password_lama');
        $new = (string) $this->request->getPost('password_hash');

        // Verifikasi password lama
        if (!password_verify($old, $admin['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Password lama salah.');
        }

        // Kalau sama dengan lama, tolak
        if (password_verify($new, $admin['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Password baru tidak boleh sama dengan yang lama.');
        }

        // Hash & update
        $hash = password_hash($new, PASSWORD_DEFAULT);

        try {
            $this->AdminModel->update($adminId, [
                'password_hash'  => $hash,
                'login_attempts' => 0,                 // reset percobaan
            ]);

            // Demi keamanan, regenerasi session ID
            session()->regenerate(true);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal update password: ' . $e->getMessage());
        }

        return redirect()->to(site_url('admin/profile'))->with('success', 'Password berhasil diperbarui.');
    }

    public function activity_log()
    {
        // 1) Ambil dari session
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        // 2) Profil yang login
        $me = $this->AdminModel->find($adminId);
        if (!$me) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        // 3) Hanya Admin & Admin Tertinggi yang boleh akses
        $allowedRoles = ['admin', 'karyawan'];
        $isAllowed = in_array(
            mb_strtolower((string)($me['role'] ?? '')),
            array_map('mb_strtolower', $allowedRoles),
            true
        );

        if (!$isAllowed) {
            return redirect()->to(site_url('admin/dashboard'))
                ->with('error', 'Anda tidak memiliki akses ke Activity Log.');
        }

        // 4) Ambil SELURUH data admin (hanya 2 role di atas)
        // asumsi $adminId = session('admin_id'); dan $allowedRoles sudah didefinisikan
        $rows = $this->AdminModel
            ->select('id_admin, nama_lengkap, email, role, is_active, no_telp, avatar, last_login_at, login_attempts')
            ->whereIn('role', $allowedRoles)
            ->where('id_admin !=', (int) $adminId)
            ->orderBy('last_login_at IS NULL', 'ASC', false)
            ->orderBy('last_login_at', 'DESC')
            ->findAll();


        // 5) Kirim ke view
        return view('admin/activity-log', [
            'title'      => 'Activity Log | Waroeng Kami',
            'nav_link'   => 'Activity',
            'breadcrumb' => 'Activity Log',
            'admin'      => $me,    // yang login
            'rows'       => $rows,  // seluruh admin (Admin & Admin Tertinggi)
        ]);
    }

    public function page_tambahActivity()
    {
        $adminId = session('admin_id'); // set ini saat login

        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }
        $data = [
            'title' => 'Tambah Activity | Waroeng Kami',
            'nav_link' => 'Tambah',
            'breadcrumb' => 'Tambah Activity',
            'admin' => $admin
        ];
        return view('admin/tambah-activity', $data);
    }

    public function Aksi_tambahActivity()
    {
        // 1) Validasi input
        $rules = [
            'nama_lengkap' => [
                'label'  => 'Nama Lengkap',
                'rules'  => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required'   => 'Nama lengkap wajib diisi.',
                    'min_length' => 'Nama minimal 3 karakter.',
                    'max_length' => 'Nama terlalu panjang.',
                ],
            ],
            'email' => [
                'label'  => 'Email',
                'rules'  => 'required|valid_email|max_length[100]|is_unique[tb_admin.email]',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.',
                    'is_unique'   => 'Email sudah terdaftar.',
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
            'role' => [
                'label'  => 'Role',
                'rules'  => 'required|in_list[admin,karyawan]',
                'errors' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list'  => 'Role tidak valid.',
                ],
            ],
            'no_telp' => [
                'label'  => 'No. Telp',
                'rules'  => 'required|regex_match[/^[0-9+\s()-]{8,20}$/]',
                'errors' => [
                    'required'    => 'No. Telp wajib diisi.',
                    'regex_match' => 'Format No. Telp tidak valid.',
                ],
            ],
            // jenis_kelamin dipakai hanya untuk fallback avatar (tidak disimpan ke DB krn tidak ada di allowedFields)
            'jenis_kelamin' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'permit_empty|in_list[pria,wanita]',
            ],
            // avatar opsional
            'avatar' => [
                'label'  => 'Avatar',
                'rules'  => 'if_exist|uploaded[avatar]|max_size[avatar,1024]|is_image[avatar]|ext_in[avatar,jpg,png]',
                'errors' => [
                    'uploaded' => 'Avatar gagal diunggah.',
                    'max_size' => 'Avatar maksimal 1MB.',
                    'is_image' => 'File avatar harus berupa gambar.',
                    'ext_in'   => 'Format avatar harus jpg, jpeg, png, atau webp.',
                ],
            ],
        ];

        // Catatan kecil: kalau avatar bener-bener opsional TANPA harus upload, pakai validator custom di bawah ini.
        // Karena rule uploaded[avatar] bikin file wajib diupload saat field ada.
        // Kita deteksi manual nanti pakai ->getFile()

        // Override ringan: hapus 'uploaded[avatar]' bila tidak ada file yang dipilih
        $file = $this->request->getFile('avatar');
        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            unset($rules['avatar']); // biar nggak wajib
        }

        if (! $this->validate($rules)) {
            // dd($this->validator->getErrors());
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Periksa kembali input Anda.');
        }

        // 2) Ambil input yang sudah valid
        $nama   = trim((string) $this->request->getPost('nama_lengkap'));
        $email  = strtolower(trim((string) $this->request->getPost('email')));
        $pass   = (string) $this->request->getPost('password_hash');
        $role   = (string) $this->request->getPost('role');
        $telp   = trim((string) $this->request->getPost('no_telp'));
        $jk     = (string) $this->request->getPost('jenis_kelamin'); // hanya untuk default avatar

        // 3) Siapkan avatar
        $avatarName = null;

        // Folder simpan (public/uploads/avatars). Pastikan folder writeable & ada .htaccess sesuai kebutuhan.
        $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'avatars';
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            // Validasi manual lagi untuk batas ukuran & mime (jaga-jaga kalau rules avatar di-unset)
            if ($avatarFile->getSize() > 1024 * 1024) { // 1MB
                return redirect()->back()->withInput()->with('errors', [
                    'avatar' => 'Avatar maksimal 1MB.',
                ]);
            }
            $mime = $avatarFile->getMimeType();
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                return redirect()->back()->withInput()->with('errors', [
                    'avatar' => 'Format avatar harus jpg, jpeg, png, atau webp.',
                ]);
            }

            $newName = $avatarFile->getRandomName();
            $avatarFile->move($targetDir, $newName);
            $avatarName = 'uploads/avatars/' . $newName; // simpan path relatif dari public
        } else {
            // Fallback default berdasarkan jenis kelamin
            if ($jk === 'wanita') {
                $avatarName = 'women.png';
            } elseif ($jk === 'pria') {
                $avatarName = 'boy.png';
            } else {
                $avatarName = 'user.png';
            }
        }

        // 4) Hash password
        $hash = password_hash($pass, PASSWORD_DEFAULT);

        // 5) Susun data sesuai allowedFields
        $data = [
            'email'          => $email,
            'password_hash'  => $hash,
            'role'           => $role,
            'is_active'      => 1,              // aktif
            'nama_lengkap'   => $nama,
            'no_telp'        => $telp,
            'avatar'         => $avatarName,
            'last_login_at'  => null,           // belum pernah login
            'login_attempts' => 0,              // reset percobaan
        ];

        // 6) Simpan
        $model = new TBAdminModel();

        try {
            $model->insert($data);

            return redirect()
                ->to(site_url('admin/activity')) // ganti tujuan sesuai routing kamu
                ->with('success', 'Admin berhasil ditambahkan.');
        } catch (\Throwable $e) {
            // Hapus file yg udah telanjur diupload kalau gagal insert
            if (!empty($newName) && is_file($targetDir . DIRECTORY_SEPARATOR . $newName)) {
                @unlink($targetDir . DIRECTORY_SEPARATOR . $newName);
            }

            log_message('error', 'Gagal tambah admin: {msg}', ['msg' => $e->getMessage()]);

            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }
}

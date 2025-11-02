<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KeranjangModel;
use App\Models\MejaModel;
use App\Models\PesananItemModel;
use App\Models\PesananModel;
use App\Models\ProdukModel;
use App\Models\TbAdminModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use Config\Services;

class AdminController extends BaseController
{
    protected $PesananModel;
    protected $AdminModel;
    protected $ProdukModel;
    protected $PesananItemModel;
    protected $MejaModel;

    public function __construct()
    {
        $this->PesananModel = new PesananModel();
        $this->AdminModel = new TbAdminModel();
        $this->ProdukModel = new ProdukModel();
        $this->PesananItemModel = new PesananItemModel();
        $this->MejaModel = new MejaModel();
    }

    public function index()
    {
        $db = db_connect();

        // Ambil admin dari session
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        // Waktu Asia/Jakarta
        $now = Time::now('Asia/Jakarta');
        $startObj = (clone $now)->setDate($now->getYear(), $now->getMonth(), 1)->setTime(0, 0, 0);
        $nextObj  = (clone $startObj)->addMonths(1);
        $prevStartObj = (clone $startObj)->subMonths(1);
        $prevNextObj  = (clone $startObj);

        $start = $startObj->toDateTimeString();
        $next = $nextObj->toDateTimeString();
        $prevStart = $prevStartObj->toDateTimeString();
        $prevNext = $prevNextObj->toDateTimeString();

        // 1) Data Pelanggan
        $totalPelanggan = (int) $db->table('tb_pesanan')
            ->select('COUNT(DISTINCT owner_key) AS c')
            ->where('status', 'selesai')
            ->get()->getRow('c');

        $pelangganBulanIni = (int) $db->table('tb_pesanan')
            ->select('COUNT(DISTINCT owner_key) AS c')
            ->where('status', 'selesai')
            ->where('created_at >=', $start)
            ->where('created_at <',  $next)
            ->get()->getRow('c');

        // 2) Jumlah Produk aktif
        $jumlahProduk = (int) $db->table('tb_produk')
            ->where('status', 1)
            ->countAllResults();

        // 3) Paling laris bulan ini
        $rowTop = $db->table('tb_pesanan_item i')
            ->select('i.produk_id, p.nama_produk, SUM(i.qty) AS total_qty')
            ->join('tb_pesanan h', 'h.id_pesanan = i.pesanan_id', 'left')
            ->join('tb_produk p', 'p.id_produk = i.produk_id', 'left')
            ->where('h.created_at >=', $start)
            ->where('h.created_at <',  $next)
            ->where('h.status', 'selesai')
            ->groupBy('i.produk_id, p.nama_produk')
            ->orderBy('total_qty', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $palingLaris = [
            'nama' => $rowTop['nama_produk'] ?? '—',
            'qty'  => (int)($rowTop['total_qty'] ?? 0),
        ];

        // 4) Omzet bulan ini
        $rowOmzet = $db->table('tb_pesanan h')
            ->select('COALESCE(SUM(h.total),0) AS omzet')
            ->where('h.created_at >=', $start)
            ->where('h.created_at <',  $next)
            ->where('h.status', 'selesai')
            ->get()->getRowArray();
        $omzetBulanIni = (float)($rowOmzet['omzet'] ?? 0);

        // Tren vs bulan lalu
        $rowPrev = $db->table('tb_pesanan')
            ->select('COALESCE(SUM(total),0) AS omzet')
            ->where('created_at >=', $prevStart)
            ->where('created_at <',  $prevNext)
            ->where('status', 'selesai')
            ->get()->getRowArray();
        $omzetPrev = (float)($rowPrev['omzet'] ?? 0);
        $growthPct = $omzetPrev > 0 ? (($omzetBulanIni - $omzetPrev) / $omzetPrev) * 100 : null;

        // 5) Chart Pemesanan per Tahun
        $yearData = $db->table('tb_pesanan')
            ->select('YEAR(created_at) AS tahun, COUNT(*) AS total')
            ->where('status', 'selesai')
            ->groupBy('tahun')
            ->orderBy('tahun', 'ASC')
            ->get()
            ->getResultArray();

        $chartLabels = [];
        $chartData   = [];
        foreach ($yearData as $y) {
            $chartLabels[] = $y['tahun'];
            $chartData[]   = (int) $y['total'];
        }

        // 6) Chart Omzet / Keuntungan per Tahun
        $yearProfitData = $db->table('tb_pesanan')
            ->select('YEAR(created_at) AS tahun, COALESCE(SUM(total),0) AS omzet')
            ->where('status', 'selesai')
            ->groupBy('tahun')
            ->orderBy('tahun', 'ASC')
            ->get()
            ->getResultArray();

        $profitLabels = [];
        $profitData   = [];
        foreach ($yearProfitData as $y) {
            $profitLabels[] = $y['tahun'];
            $profitData[]   = (float) $y['omzet'];
        }

        // Helper Rupiah
        $fmt = static fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $bulanLabel = $now->toLocalizedString('MMMM yyyy');

        $data = [
            'stats' => [
                'pelanggan'       => $totalPelanggan,
                'pelangganBulan'  => $pelangganBulanIni,
                'produk'          => $jumlahProduk,
                'palingLaris'     => $palingLaris,
                'omzetBulan'      => $omzetBulanIni,
                'omzetBulanFmt'   => $fmt($omzetBulanIni),
                'growthPct'       => $growthPct,
                'bulanLabel'      => $bulanLabel,
            ],
            'chartLabels' => $chartLabels,
            'chartData'   => $chartData,
            'profitLabels' => $profitLabels,
            'profitData'   => $profitData,
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

    public function page_editActivity($id = null)
    {
        $adminId = session('admin_id'); // Pastikan ini diset saat login
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        // Ambil data activity berdasarkan ID
        $activity = $this->AdminModel->find($id);
        if (!$activity) {
            return redirect()->to(site_url('admin/activity'))
                ->with('error', 'Data Activity tidak ditemukan!');
        }

        $data = [
            'title'      => 'Edit Activity | Waroeng Kami',
            'nav_link'   => 'Edit',
            'breadcrumb' => 'Edit Activity',
            'admin'      => $admin,
            'activity'   => $activity
        ];

        return view('admin/edit-activity', $data);
    }

    public function aksi_editActivity($id)
    {
        // Pastikan admin login
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login kembali.');
        }

        // Ambil data admin login & data activity yang akan diedit
        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan.');
        }

        $activity = $this->AdminModel->find($id);
        if (!$activity) {
            return redirect()->to(site_url('admin/activity'))
                ->with('error', 'Data activity tidak ditemukan.');
        }

        $validation = \Config\Services::validation();

        // === Custom rule unik email tapi boleh sama dengan miliknya sendiri ===
        $emailBaru = $this->request->getPost('email');
        $emailLama = $activity['email'];

        // Cek apakah email baru digunakan oleh admin lain
        $emailExists = $this->AdminModel
            ->where('email', $emailBaru)
            ->where('id_admin !=', $id)
            ->first();

        if ($emailExists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Email tersebut sudah digunakan oleh akun lain.');
        }

        // === Validasi Form (Bahasa Indonesia) ===
        $rules = [
            'nama_lengkap'   => [
                'rules'  => 'required|min_length[3]|max_length[50]',
                'errors' => [
                    'required'    => 'Nama lengkap wajib diisi.',
                    'min_length'  => 'Nama lengkap minimal 3 karakter.',
                    'max_length'  => 'Nama lengkap maksimal 50 karakter.'
                ]
            ],
            'email'          => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'Email wajib diisi.',
                    'valid_email' => 'Format email tidak valid.'
                ]
            ],
            'role'           => [
                'rules'  => 'required|in_list[admin,karyawan]',
                'errors' => [
                    'required' => 'Role wajib dipilih.',
                    'in_list'  => 'Role yang dipilih tidak valid.'
                ]
            ],
            'jenis_kelamin'  => [
                'rules'  => 'required|in_list[pria,wanita]',
                'errors' => [
                    'required' => 'Jenis kelamin wajib dipilih.',
                    'in_list'  => 'Jenis kelamin tidak valid.'
                ]
            ],
            'no_telp'        => [
                'rules'  => 'required|numeric|min_length[8]|max_length[13]',
                'errors' => [
                    'required'   => 'Nomor telepon wajib diisi.',
                    'numeric'    => 'Nomor telepon hanya boleh berisi angka.',
                    'min_length' => 'Nomor telepon minimal 8 digit.',
                    'max_length' => 'Nomor telepon maksimal 13 digit.'
                ]
            ],
            'avatar'         => [
                'rules'  => 'permit_empty|max_size[avatar,1024]|is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]',
                'errors' => [
                    'max_size' => 'Ukuran gambar maksimal 1MB.',
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in'  => 'Format gambar hanya boleh JPG, JPEG, atau PNG.'
                ]
            ],
        ];

        // Jika password diisi → tambahkan rule
        if ($this->request->getPost('password_hash')) {
            $rules['password_hash'] = [
                'rules'  => 'min_length[6]',
                'errors' => [
                    'min_length' => 'Password minimal 6 karakter.'
                ]
            ];
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // === Data Update ===
        $dataUpdate = [
            'nama_lengkap'  => $this->request->getPost('nama_lengkap'),
            'email'         => $emailBaru,
            'role'          => $this->request->getPost('role'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'no_telp'       => $this->request->getPost('no_telp'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        // === Update Password jika diisi (gunakan Argon2ID) ===
        $password = $this->request->getPost('password_hash');
        if (!empty($password)) {
            $dataUpdate['password_hash'] = password_hash($password, PASSWORD_ARGON2ID);
        }

        // === Upload Avatar (opsional) ===
        $avatarFile = $this->request->getFile('avatar');
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            $newName = $avatarFile->getRandomName();
            $avatarFile->move(FCPATH . 'assets/uploads/avatars/', $newName);

            // Hapus avatar lama jika bukan file default (boy.png, woman.png, user.png)
            if (!empty($activity['avatar']) && file_exists(FCPATH . 'assets/uploads/avatars/' . $activity['avatar'])) {
                $defaultAvatars = ['boy.png', 'woman.png', 'user.png'];
                if (!in_array($activity['avatar'], $defaultAvatars)) {
                    unlink(FCPATH . 'assets/uploads/avatars/' . $activity['avatar']);
                }
            }



            $dataUpdate['avatar'] = $newName;
        }

        // Simpan ke database
        $this->AdminModel->update($id, $dataUpdate);

        // Redirect dengan SweetAlert
        return redirect()->to(site_url('admin/activity'))
            ->with('success', 'Data activity berhasil diperbarui!');
    }


    public function hapusActivity($id = null)
    {
        $model = new TBAdminModel();

        if (empty($id) || !is_numeric($id)) {
            return redirect()
                ->back()
                ->with('error', 'ID tidak valid.');
        }

        // Cek apakah data ada
        $admin = $model->find($id);
        if (!$admin) {
            return redirect()
                ->back()
                ->with('error', 'Activity Log tidak ditemukan.');
        }

        try {
            // Hapus avatar kalau bukan default
            if (
                !empty($admin['avatar']) &&
                !in_array($admin['avatar'], ['user.png', 'boy.png', 'women.png'])
            ) {
                $avatarPath = FCPATH . $admin['avatar'];

                if (is_file($avatarPath)) {
                    @unlink($avatarPath);
                }
            }

            // Hapus data dari DB
            $model->delete($id);

            return redirect()
                ->to(site_url('admin/activity'))
                ->with('success', 'Activity Log berhasil dihapus');
        } catch (\Throwable $e) {
            log_message('error', 'Gagal hapus Activity: {msg}', ['msg' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    public function page_produk()
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

        // ✅ Ambil semua data produk
        $data_produk = $this->ProdukModel->findAll();

        // ✅ Ambil kategori unik dari produk
        $kategori_list = $this->ProdukModel
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori', 'ASC')
            ->findAll();

        // Siapkan data untuk dikirim ke view
        $data = [
            'title'      => 'Data Produk | Waroeng Kami',
            'breadcrumb' => 'Data Produk',
            'nav_link'   => 'produk',
            'rows'       => $data_produk ?? [],
            'kategori_list' => array_column($kategori_list, 'kategori'), // hanya ambil kolom kategori
            'admin'      => $admin
        ];

        return view('admin/data-produk', $data);
    }
    public function page_tambah_produk()
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        $data = [
            'title' => 'Tambah Produk | Waroeng Kami',
            'breadcrumb' => 'Tambah Produk',
            'nav_link' => 'produk',
            'admin' => $admin
        ];
        return view('admin/tambah-produk', $data);
    }

    public function tambah_produk()
    {
        $produkModel = new ProdukModel();

        // Validasi input (tanpa slug)
        $validationRules = [
            'nama_produk' => [
                'rules' => 'required|min_length[3]|max_length[100]',
                'errors' => [
                    'required' => 'Nama produk wajib diisi.',
                    'min_length' => 'Nama produk minimal 3 karakter.',
                    'max_length' => 'Nama produk maksimal 100 karakter.',
                ],
            ],
            'deskripsi' => [
                'rules' => 'permit_empty|max_length[500]',
                'errors' => [
                    'max_length' => 'Deskripsi maksimal 500 karakter.',
                ],
            ],
            'kategori' => [
                'rules' => 'required|in_list[makanan,minuman]',
                'errors' => [
                    'required' => 'Kategori wajib dipilih.',
                    'in_list' => 'Kategori tidak valid.',
                ],
            ],
            'harga' => [
                'rules' => 'required|numeric|greater_than[0]',
                'errors' => [
                    'required' => 'Harga wajib diisi.',
                    'numeric' => 'Harga harus berupa angka.',
                    'greater_than' => 'Harga harus lebih dari 0.',
                ],
            ],
            'gambar' => [
                'rules' => 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]|max_size[gambar,1028]',
                'errors' => [
                    'uploaded' => 'Gambar wajib diunggah.',
                    'is_image' => 'File harus berupa gambar.',
                    'mime_in' => 'Format gambar harus JPG atau PNG.',
                    'max_size' => 'Ukuran gambar maksimal 1 MB.',
                ],
            ],
            'status' => [
                'rules' => 'required|in_list[tersedia,habis]',
                'errors' => [
                    'required' => 'Status wajib dipilih.',
                    'in_list' => 'Status tidak valid.',
                ],
            ],
            'level_pedas' => [
                'rules' => 'permit_empty|in_list[tidak,sedang,pedas,sesuai-pembeli]',
                'errors' => [
                    'in_list' => 'Level pedas tidak valid.',
                ],
            ],
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Ambil data dari form
        $nama_produk  = $this->request->getPost('nama_produk');
        $deskripsi    = $this->request->getPost('deskripsi');
        $kategori     = $this->request->getPost('kategori');
        $harga        = $this->request->getPost('harga');
        $status       = $this->request->getPost('status');
        $favorit      = $this->request->getPost('favorit') ? 1 : 0;
        $level_pedas  = $this->request->getPost('level_pedas');

        // 🔹 Buat slug otomatis dari nama_produk
        // helper('text');
        $slug = url_title($nama_produk, '-', true);

        // Pastikan slug unik
        $existing = $produkModel->where('slug', $slug)->first();
        if ($existing) {
            $slug .= '-' . uniqid(); // tambahkan ID unik jika sudah ada
        }

        // Upload gambar
        $gambarFile = $this->request->getFile('gambar');
        $gambarName = $gambarFile->getRandomName();

        if ($gambarFile->isValid() && !$gambarFile->hasMoved()) {
            $gambarFile->move(FCPATH . 'assets/uploads/produk', $gambarName);
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal mengunggah gambar.');
        }

        // Simpan ke database
        $data = [
            'nama_produk' => $nama_produk,
            'slug'        => $slug,
            'deskripsi'   => $deskripsi,
            'kategori'    => $kategori,
            'harga'       => $harga,
            'gambar'      => $gambarName,
            'status'      => $status,
            'favorit'     => $favorit,
            'level_pedas' => $level_pedas,
        ];

        if ($produkModel->insert($data)) {
            return redirect()->to(site_url('admin/produk'))->with('success', 'Produk berhasil ditambahkan.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data produk.');
        }
    }

    public function page_edit_produk($id = null)
    {
        $adminId = session('admin_id');

        // 🔹 Cek apakah sesi admin masih ada
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        // 🔹 Ambil data admin
        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan.');
        }

        // 🔹 Ambil data produk berdasarkan ID
        $produk = $this->ProdukModel->find($id);
        if (!$produk) {
            throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
        }

        // 🔹 Kirim data ke view
        $data = [
            'title' => 'Edit Produk | Waroeng Kami',
            'breadcrumb' => 'Edit Produk',
            'nav_link' => 'produk',
            'admin' => $admin,
            'produk' => $produk
        ];

        return view('admin/edit-produk', $data);
    }
    public function edit_produk($id)
    {
        // 🔒 Pastikan user login
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        // 🔍 Cek produk
        $produk = $this->ProdukModel->find($id);
        if (!$produk) {
            return redirect()->to(site_url('admin/produk'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        // 🔹 Ambil inputan form
        $nama_produk = $this->request->getPost('nama_produk');
        $deskripsi   = $this->request->getPost('deskripsi');
        $kategori    = $this->request->getPost('kategori');
        $harga       = $this->request->getPost('harga');
        $status      = $this->request->getPost('status');
        $favorit     = $this->request->getPost('favorit') ? 1 : 0;
        $level_pedas = $this->request->getPost('level_pedas');
        $gambarBaru  = $this->request->getFile('gambar');

        // 🔸 Rules dasar (tanpa upload wajib)
        $rules = [
            'nama_produk' => 'required|min_length[3]|max_length[100]',
            'deskripsi'   => 'permit_empty|max_length[500]',
            'kategori'    => 'required|in_list[makanan,minuman]',
            'harga'       => 'required|numeric',
            'status'      => 'required|in_list[tersedia,habis]',
        ];

        // 🔸 Tambahkan rules file HANYA jika ada gambar baru
        if ($gambarBaru && $gambarBaru->isValid() && !$gambarBaru->hasMoved()) {
            $rules['gambar'] = 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]|max_size[gambar,1024]';
        }

        // 🔹 Validasi form
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 🔹 Buat slug otomatis
        $slug = url_title($nama_produk, '-', true);

        // 🔹 Siapkan data update
        $dataUpdate = [
            'nama_produk' => $nama_produk,
            'slug'        => $slug,
            'deskripsi'   => $deskripsi,
            'kategori'    => $kategori,
            'harga'       => $harga,
            'status'      => $status,
            'favorit'     => $favorit,
            'level_pedas' => $level_pedas,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        // 🔹 Jika upload gambar baru
        if ($gambarBaru && $gambarBaru->isValid() && !$gambarBaru->hasMoved()) {
            $namaFileBaru = $gambarBaru->getRandomName();
            $gambarBaru->move(FCPATH . 'assets/uploads/produk/', $namaFileBaru);

            // 🔸 Hapus gambar lama jika bukan default
            if (!empty($produk['gambar']) && file_exists(FCPATH . 'assets/uploads/produk/' . $produk['gambar'])) {
                if (!in_array($produk['gambar'], ['default.png', 'woman.png', 'boy.png', 'user.png'])) {
                    unlink(FCPATH . 'assets/uploads/produk/' . $produk['gambar']);
                }
            }

            // 🔸 Simpan nama gambar baru
            $dataUpdate['gambar'] = $namaFileBaru;
        }

        // 🔹 Update ke database
        $this->ProdukModel->update($id, $dataUpdate);

        // 🔹 Redirect sukses
        return redirect()->to(site_url('admin/produk'))
            ->with('success', 'Produk berhasil diperbarui.');
    }
    public function delete_produk($id)
    {
        // 🔒 Pastikan user login
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        // 🔍 Cek produk berdasarkan ID
        $produk = $this->ProdukModel->find($id);
        if (!$produk) {
            return redirect()->to(site_url('admin/produk'))
                ->with('error', 'Produk tidak ditemukan.');
        }

        // 🚫 Jangan hapus gambar dari folder — hanya hapus data di database
        // (kalau kamu ingin gambar tetap tersimpan di server)

        // 🗑️ Hapus data dari database saja
        $this->ProdukModel->delete($id);

        // 🔁 Redirect ke halaman produk
        return redirect()->to(site_url('admin/produk'))
            ->with('success', 'Produk berhasil dihapus (gambar tetap tersimpan).');
    }


    // pelanggan 
    public function data_pelanggan()
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        $db = db_connect();
        $tPesanan   = $db->prefixTable('tb_pesanan');
        $tKeranjang = $db->prefixTable('tb_keranjang');
        $tProduk    = $db->prefixTable('tb_produk');

        // Filter (optional)
        $keyword = $this->request->getGet('keyword') ?? '';
        $status  = $this->request->getGet('status') ?? '';

        $builder = $db->table($tPesanan)->select('*');
        if ($keyword) $builder->like('nama_pelanggan', $keyword);
        if ($status)  $builder->where('status', $status);

        $pelanggan = $builder->orderBy('id_pesanan', 'DESC')->get()->getResultArray();

        // Ambil produk detail per pesanan
        foreach ($pelanggan as &$p) {
            $produk = $db->table($tKeranjang . ' k')
                ->select('p.nama_produk, k.jumlah, k.harga, k.subtotal')
                ->join($tProduk . ' p', 'p.id_produk = k.produk_id', 'left')
                ->where('k.owner_key', $p['kode_pesanan'])
                ->get()
                ->getResultArray();
            $p['produkDetail'] = $produk;
        }

        $status_list = array_unique(array_column($pelanggan, 'status'));

        $data = [
            'title'           => 'Data Pelanggan | Waroeng Kami',
            'breadcrumb'      => 'Data Pelanggan',
            'nav_link'        => 'pelanggan',
            'admin'           => $admin,
            'rows'            => $pelanggan,
            'status_list'     => $status_list,
            'keyword'         => $keyword,
            'selected_status' => $status,
        ];

        return view('admin/data-pelanggan', $data);
    }

    public function detail_pelanggan($kode_pesanan = null)
    {
        if (!$kode_pesanan) {
            return redirect()->to(site_url('admin/data-pelanggan'))
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        $db = db_connect();
        $tPesanan     = $db->prefixTable('tb_pesanan');
        $tPesananItem = $db->prefixTable('tb_pesanan_item');

        // Ambil data pesanan berdasarkan ID
        $pesanan = $db->table($tPesanan)
            ->where('kode_pesanan', $kode_pesanan)
            ->get()
            ->getRowArray();

        if (!$pesanan) {
            return redirect()->to(site_url('admin/pelanggan'))
                ->with('error', 'Data tidak ditemukan.');
        }

        // Ambil item pesanan
        $produk = $db->table($tPesananItem)
            ->select('nama_produk, qty as jumlah, harga, subtotal')
            ->where('pesanan_id', $pesanan['id_pesanan'])
            ->orderBy('nama_produk', 'ASC')
            ->get()
            ->getResultArray();

        // Hitung total qty & total harga
        $total_qty   = 0;
        $total_harga = 0;
        foreach ($produk as $item) {
            $total_qty   += (int)$item['jumlah'];
            $total_harga += (float)$item['subtotal'];
        }

        $data = [
            'title'        => 'Detail Pelanggan | Waroeng Kami',
            'breadcrumb'   => 'Detail Pelanggan',
            'nav_link'     => 'pelanggan',
            'admin'        => $admin,
            'pelanggan'    => $pesanan,
            'produkDetail' => $produk,
            'total_qty'    => $total_qty,
            'total_harga'  => $total_harga,
        ];

        return view('admin/detail-pelanggan', $data);
    }



    // data pemesanan
    public function data_pemesanan()
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        $db = db_connect();
        $tPesananItem = $db->prefixTable('tb_pesanan_item');
        $tProduk      = $db->prefixTable('tb_produk');

        // Ambil filter dari query param
        $keyword = $this->request->getGet('keyword') ?? '';
        $kategori = $this->request->getGet('kategori') ?? '';

        $builder = $db->table($tPesananItem . ' pi')
            ->select('pi.*, p.gambar, p.slug, p.kategori, p.level_pedas')
            ->join($tProduk . ' p', 'p.id_produk = pi.produk_id', 'left');

        if ($keyword) {
            $builder->like('pi.nama_produk', $keyword);
        }

        if ($kategori) {
            $builder->where('p.kategori', $kategori);
        }

        $pemesanan = $builder->orderBy('pi.id_pesanan_item', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil daftar kategori unik untuk dropdown
        $kategori_list = array_unique(array_column($pemesanan, 'kategori'));

        $data = [
            'title'             => 'Data Pemesanan | Waroeng Kami',
            'breadcrumb'        => 'Data Pemesanan',
            'nav_link'          => 'pemesanan',
            'admin'             => $admin,
            'rows'              => $pemesanan,
            'kategori_list'     => $kategori_list,
            'keyword'           => $keyword,
            'selected_kategori' => $kategori,
        ];

        return view('admin/data-pemesanan', $data);
    }

    public function page_tambah_pemesanan()
    {
        $session = session();
        $adminId = $session->get('admin_id');

        // --- 1. Pastikan admin login ---
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        // --- 2. Ambil data admin ---
        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        $db = db_connect();

        // --- 3. Ambil daftar meja & produk ---
        $mejaList   = $this->MejaModel->where('is_active', 1)->orderBy('kode_meja', 'ASC')->findAll();
        $produkList = $this->ProdukModel->where('status', 'tersedia')->orderBy('nama_produk', 'ASC')->findAll();

        // --- 4. Ambil list kategori unik untuk dropdown filter ---
        $kategori_list = $this->ProdukModel
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori', 'ASC')
            ->findAll();

        // --- 5. Generate kode pesanan otomatis ---
        $kodePesanan = $this->generateKodePesanan($db, 'KP', 4);

        // --- 6. Ambil item keranjang milik admin ini ---
        $tKeranjang = $db->prefixTable('tb_keranjang');
        $tProduk    = $db->prefixTable('tb_produk');
        $ownerKey   = 'admin-' . $adminId;

        $items = $db->table($tKeranjang . ' k')
            ->select('
            k.id_keranjang, k.owner_key, k.produk_id, k.jumlah, k.harga, k.subtotal,
            p.nama_produk, p.gambar, p.slug, p.status AS status_produk
        ')
            ->join($tProduk . ' p', 'p.id_produk = k.produk_id', 'left')
            ->where('k.owner_key', $ownerKey)
            ->orderBy('k.id_keranjang', 'ASC')
            ->get()
            ->getResultArray();

        // --- 7. Hitung total qty & total harga keranjang ---
        // --- 7. Hitung total qty & total harga keranjang ---
        $agg = $db->table($tKeranjang)
            ->select('COALESCE(SUM(jumlah),0) AS total_qty,
              COALESCE(SUM(subtotal),0) AS total')
            ->where('owner_key', $ownerKey)
            ->get()
            ->getRowArray() ?? [];

        $totalQty  = (int)   ($agg['total_qty'] ?? 0);
        $total     = (float) ($agg['total']     ?? 0.0);
        $cart_count = $totalQty; // <-- ini yang ditambahkan

        // --- 8. Kirim data ke view ---
        $data = [
            'title'         => 'Tambah Pemesanan | Waroeng Kami',
            'breadcrumb'    => 'Tambah Pemesanan',
            'nav_link'      => 'pemesanan',
            'admin'         => $admin,
            'mejaList'      => $mejaList,
            'd_produk'      => $produkList,
            'kategori_list' => array_column($kategori_list, 'kategori'),
            'kode_pesanan'  => $kodePesanan,
            'items'         => $items,
            'total_qty'     => $totalQty,
            'total'         => $total,
            'cart_count'    => $cart_count, // <-- kirim ke view
        ];

        return view('admin/tambah-pemesanan', $data);
    }
    public function delete_pelanggan($id)
    {
        // 🔒 Pastikan admin login
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $db = db_connect();
        $tPesanan      = $db->prefixTable('tb_pesanan');
        $tKeranjang    = $db->prefixTable('tb_keranjang');
        $tPesananItem  = $db->prefixTable('tb_pesanan_item');

        // 🔍 Cek apakah data pelanggan (pesanan) ada
        $pesanan = $db->table($tPesanan)
            ->where('id_pesanan', $id)
            ->get()
            ->getRowArray();

        if (!$pesanan) {
            return redirect()->to(site_url('admin/pelanggan'))
                ->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // 🧹 Hapus item pesanan (jika ada di tabel tb_pesanan_item)
        $db->table($tPesananItem)->where('pesanan_id', $id)->delete();

        // 🧹 Hapus keranjang berdasarkan kode pesanan
        $db->table($tKeranjang)->where('owner_key', $pesanan['kode_pesanan'])->delete();

        // 🗑️ Hapus data utama pelanggan dari tb_pesanan
        $db->table($tPesanan)->where('id_pesanan', $id)->delete();

        return redirect()->to(site_url('admin/pelanggan'))
            ->with('success', 'Data pelanggan dan pemesanan berhasil dihapus tanpa menghapus gambar produk.');
    }

    /**
     * Membuat kode pesanan otomatis berurutan
     * Contoh hasil: KP0001, KP0002, KP0003, dst
     */
    private function generateKodePesanan($db, string $prefix = 'KP', int $padLength = 4): string
    {
        $tPesanan = $db->prefixTable('tb_pesanan');

        $last = $db->table($tPesanan)
            ->select('kode_pesanan')
            ->orderBy('id_pesanan', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if ($last && preg_match('/\d+$/', $last['kode_pesanan'], $match)) {
            $nextNum = (int) $match[0] + 1;
        } else {
            $nextNum = 1;
        }

        $newCode = $prefix . str_pad($nextNum, $padLength, '0', STR_PAD_LEFT);
        return $newCode;
    }


    public function tambah_pemesanan()
    {
        $session = Services::session();
        $request = Services::request();

        // --- 1. Pastikan admin login ---
        if (!$session->has('admin_id')) {
            return redirect()->to(base_url('auth/login'))
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // --- 2. Ambil dan validasi input ---
        $produkIdRaw = $request->getPost('produk_id');
        $produkId = (int) ($produkIdRaw !== null ? $produkIdRaw : 0);

        $jumlahRaw = $request->getPost('jumlah');
        $jumlah = max(1, (int) ($jumlahRaw !== null ? $jumlahRaw : 1));

        if ($produkId <= 0) {
            return redirect()->back()->with('error', 'Produk tidak valid.');
        }

        // --- 3. Tentukan owner_key berdasarkan admin login ---
        $ownerKey = 'admin-' . $session->get('admin_id');

        // --- 4. Ambil data produk dari database ---
        $produkM = new ProdukModel();
        $produk  = $produkM->select('id_produk, harga, status')
            ->where('id_produk', $produkId)
            ->where('status', 'tersedia')
            ->first();

        if (!$produk) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan atau sedang tidak tersedia.');
        }

        $harga = (float) ($produk['harga'] ?? 0);
        if ($harga <= 0) {
            return redirect()->back()->with('error', 'Harga produk tidak valid.');
        }

        // --- 5. Proses tambah / update ke tabel keranjang ---
        $cartM   = new KeranjangModel();
        $db      = $cartM->db;
        $builder = $db->table('tb_keranjang');

        $db->transStart();

        try {
            // cek apakah produk sudah ada di keranjang admin ini
            $existing = $builder->where('owner_key', $ownerKey)
                ->where('produk_id', $produkId)
                ->get()
                ->getRowArray();

            if ($existing) {
                $newJumlah   = (int) $existing['jumlah'] + $jumlah;
                $newSubtotal = round($newJumlah * $harga, 2);

                $builder->where('owner_key', $ownerKey)
                    ->where('produk_id', $produkId)
                    ->update([
                        'jumlah'   => $newJumlah,
                        'harga'    => $harga,
                        'subtotal' => $newSubtotal,
                    ]);
            } else {
                $builder->insert([
                    'owner_key' => $ownerKey,
                    'produk_id' => $produkId,
                    'jumlah'    => $jumlah,
                    'harga'     => $harga,
                    'subtotal'  => round($jumlah * $harga, 2),
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal menyimpan ke keranjang.');
            }
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[KERANJANG ERROR] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan produk.');
        }

        // --- 6. Redirect ke halaman keranjang admin ---
        return redirect()->to(base_url('admin/pemesanan/tambah'))
            ->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function checkout()
    {
        $session = Services::session();
        $request = Services::request();
        $db      = db_connect();

        // --- 1. Pastikan admin login ---
        $adminId = $session->get('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi admin sudah habis. Silakan login lagi.');
        }

        // --- 2. Ambil input form ---
        $nama   = trim((string)$request->getPost('username'));
        $alamat = trim((string)$request->getPost('alamat'));
        $metodePembayaran = trim((string)$request->getPost('pembayaran'));
        $Status = trim((string)$request->getPost('status'));

        if ($nama === '' || $alamat === '') {
            return redirect()->back()->with('error', 'Lengkapi data pemesan terlebih dahulu.');
        }
        if (!in_array($metodePembayaran, ['cash', 'qris'])) {
            return redirect()->back()->with('error', 'Metode pembayaran tidak valid.');
        }
        if (!in_array($Status, ['baru', 'selesai', 'batal'])) {
            return redirect()->back()->with('error', 'Status pembayaran tidak valid.');
        }

        $makanDitempat = (int)($request->getPost('makan_ditempat') ?? 0);
        $mejaIdInput   = (int)($request->getPost('meja_id') ?? 0);

        $ownerKey = 'admin-' . $adminId;

        // --- 3. Ambil item keranjang milik admin ---
        $rows = $db->table('tb_keranjang k')
            ->select('k.id_keranjang, k.produk_id, k.jumlah, k.harga, k.subtotal, p.nama_produk')
            ->join('tb_produk p', 'p.id_produk = k.produk_id', 'left')
            ->where('k.owner_key', $ownerKey)
            ->orderBy('k.id_keranjang', 'ASC')
            ->get()->getResultArray();

        if (empty($rows)) {
            return redirect()->to(base_url('admin/pemesanan/tambah'))
                ->with('error', 'Keranjang kosong, tidak ada item untuk diproses.');
        }

        // --- 4. Hitung total & siapkan batch item ---
        $grandTotal = 0.0;
        $totalQty   = 0;
        $itemsBatch = [];

        foreach ($rows as $r) {
            $qty      = max(1, (int)$r['jumlah']);
            $harga    = (float)$r['harga'];
            $subtotal = (float)($r['subtotal'] ?: ($qty * $harga));

            $totalQty   += $qty;
            $grandTotal += $subtotal;

            $itemsBatch[] = [
                'produk_id'   => (int)$r['produk_id'],
                'nama_produk' => (string)$r['nama_produk'],
                'qty'         => $qty,
                'harga'       => $harga,
                'subtotal'    => $subtotal,
            ];
        }

        $nowJakarta = Time::now('Asia/Jakarta')->toDateTimeString();

        // --- 5. Payload utama pesanan ---
        $payload = [
            'owner_key'      => $ownerKey,
            'nama_pelanggan' => $nama,
            'alamat'         => $alamat,
            'makan_ditempat' => $makanDitempat,
            'meja_id'        => $mejaIdInput ?: null,
            'total'          => $grandTotal,
            'status'         => $Status,
            'pembayaran'     => $metodePembayaran,
            'created_at'     => $nowJakarta,
        ];

        try {
            // --- 6. Generate kode pesanan & insert header ---
            helper('ordercode');
            [$kode, $idPesanan] = claim_next_kode_from_pesanan($db, $payload, 'KP', 4);

            $db->transStart();

            // Insert detail item
            foreach ($itemsBatch as &$it) {
                $it['pesanan_id'] = $idPesanan;
                $it['created_at'] = $nowJakarta;
            }
            $db->table('tb_pesanan_item')->insertBatch($itemsBatch);

            // Kosongkan keranjang
            $db->table('tb_keranjang')->where('owner_key', $ownerKey)->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal disimpan.');
            }

            // --- 7. Redirect langsung ke halaman admin/pemesanan ---
            return redirect()->to(base_url('admin/pemesanan'))
                ->with('success', "Pesanan berhasil dibuat! Total: Rp " . number_format($grandTotal, 0, ',', '.'));
        } catch (\Throwable $e) {
            if ($db->transStatus() === false) {
                $db->transRollback();
            }

            log_message('error', 'Gagal membuat pesanan admin: {msg}', ['msg' => $e->getMessage()]);
            return redirect()->to(base_url('admin/pemesanan/tambah'))
                ->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    public function kurangi_item_keranjang($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->back()->with('error', 'ID keranjang tidak valid.');
        }

        $cartM = new \App\Models\KeranjangModel();

        // Ambil item keranjang berdasarkan ID
        $item = $cartM->where('id_keranjang', $id)->first();

        if (! $item) {
            return redirect()->back()->with('error', 'Item keranjang tidak ditemukan.');
        }

        // Jalankan dalam transaksi agar aman
        $db = $cartM->db;
        $db->transBegin();

        if ((int)$item['jumlah'] > 1) {
            // Kurangi jumlah 1 dan hitung subtotal baru
            $newJumlah   = (int)$item['jumlah'] - 1;
            $newSubtotal = $newJumlah * (float)$item['harga'];

            $cartM->update($id, [
                'jumlah'   => $newJumlah,
                'subtotal' => $newSubtotal,
            ]);
        } else {
            // Jika jumlah 1, hapus item tersebut
            $cartM->delete($id);
        }

        if (! $db->transStatus()) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal memperbarui keranjang.');
        }

        $db->transCommit();

        return redirect()->to(base_url('admin/pemesanan/tambah'))
            ->with('success', 'Item keranjang berhasil dikurangi.');
    }

    public function status()
    {
        $kode = $this->request->getPost('kode_pesanan');
        $status = strtolower($this->request->getPost('status'));

        if (!$kode || !$status) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $allowed = ['baru', 'selesai', 'batal'];
        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $model = new \App\Models\PesananModel();
        $pesanan = $model->where('kode_pesanan', $kode)->first();

        if (!$pesanan) {
            return redirect()->back()->with('error', 'Pesanan tidak ditemukan.');
        }

        $model->update($pesanan['id_pesanan'], ['status' => $status]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diubah menjadi ' . ucfirst($status) . '.');
    }

    public function cetak_struk($kode_pesanan)
    {
        $session = session();

        // Pastikan admin login
        $adminId = $session->get('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi admin sudah habis. Silakan login lagi.');
        }

        // Ambil data pesanan berdasarkan kode
        $pesanan = $this->PesananModel
            ->where('kode_pesanan', $kode_pesanan)
            ->first();

        if (!$pesanan) {
            return redirect()->back()->with('error', 'Data pesanan tidak ditemukan.');
        }

        // Ambil semua item terkait pesanan
        $items = $this->PesananItemModel
            ->where('id_pesanan_item', $kode_pesanan)
            ->findAll();

        // Kirim ke view struk
        return view('admin/struk', [
            'pesanan' => $pesanan,
            'items'   => $items
        ]);
    }


    // Laporan
    public function Laporan()
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->to(site_url('auth/login'))
                ->with('error', 'Sesi kamu sudah habis. Silakan login lagi.');
        }

        $admin = $this->AdminModel->find($adminId);
        if (!$admin) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Akun tidak ditemukan');
        }

        // Ambil parameter filter dari GET
        $keyword = $this->request->getGet('keyword');
        $selected_year = $this->request->getGet('tahun');

        // Ambil daftar tahun unik dari data pemesanan yang selesai
        $year_list_query = $this->PesananModel
            ->select('YEAR(created_at) AS tahun')
            ->where('status', 'selesai')
            ->distinct()
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();

        $year_list = array_column($year_list_query, 'tahun');

        // Query data pemesanan dengan filter
        $builder = $this->PesananModel
            ->where('status', 'selesai'); // hanya ambil yang selesai

        if ($keyword) {
            $builder->like('nama_pelanggan', $keyword);
        }

        if ($selected_year) {
            $builder->where('YEAR(created_at)', $selected_year);
        }

        $data_pemesanan = $builder->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Laporan | Waroeng Kami',
            'nav_link' => 'laporan',
            'breadcrumb' => 'Laporan',
            'data_pemesanan' => $data_pemesanan,
            'keyword' => $keyword,
            'year_list' => $year_list,
            'selected_year' => $selected_year,
            'admin' => $admin
        ];

        return view('admin/data-laporan', $data);
    }
}

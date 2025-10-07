<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_ujian;
use App\Models\M_siswa;
use App\Models\M_ujian_detail;

class Auth_siswa extends BaseController
{
    protected $ujian, $siswa, $detail;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->siswa = new M_siswa();
        $this->detail = new M_ujian_detail();
    }

    public function index()
    {
        $data['siswa'] = $this->siswa->where('status_login', "enable")->findAll();
        $where = "tbl_ujian_detail.status='final' OR tbl_ujian_detail.status='dikerjakan'";
        $data['ujian'] = $this->ujian->get_list_where($where);
        return view('ujian/V_auth_siswa', $data);
    }

    public function cek()
    {
        if ($this->request->isAJAX()) {
            $idSiswa = $this->request->getPost('id_siswa');
            $idUjian = $this->request->getPost('id_ujian');

            if (!$idSiswa || !$idUjian) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak lengkap'
                ]);
            }

            // cek validasi siswa dan ujian
            $siswa = $this->siswa->where(['id_siswa' => $idSiswa, 'status_login' => "enable"])->first();
            $ujian = $this->ujian->where('id_ujian', $idUjian)->first();

            if (!$siswa || !$ujian) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Siswa atau ujian tidak ditemukan'
                ]);
            }

            // set session
            session()->set([
                'id_siswa' => $idSiswa,
                'id_ujian' => $idUjian,
                'logged_in' => true,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semoga Berhasil!',
                'redirect_url' => base_url('/' . bin2hex('ujian-token') . '/' . bin2hex($idUjian) . '/' . bin2hex($idSiswa))
            ]);
        }

        // kalau bukan ajax, arahkan balik
        return redirect()->to('/ulangan');
    }

    function token($id_ujian, $id_siswa)
    {
        $data = [
            'id_ujian' => $id_ujian,
            'id_siswa' => $id_siswa
        ];
        // dd($data);
        return view('ujian/V_token', $data);
    }

    public function getToken($id)
    {
        $id_ujian = hex2bin($id);
        $ujian = $this->detail->select('token, expired_at')
            ->where('id_ujian', $id_ujian)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            return $this->response->setJSON([
                'success' => false,
                'token' => null,
                'id' => $id_ujian
            ]);
        }

        // cek expired
        if (!empty($ujian['expired_at']) && $ujian['expired_at'] < time()) {
            // hapus otomatis
            $this->detail->where('id_ujian', $id_ujian)
                ->update(['token' => null, 'expired_at' => null]);

            return $this->response->setJSON([
                'success' => false,
                'token' => null,
                'id' => $id_ujian
            ]);
        }

        return $this->response->setJSON([
            'success' => !empty($ujian['token']),
            'expired_at' => !empty($ujian['expired_at'])
                ? date('c', (int) $ujian['expired_at'])  // langsung cast ke int
                : null
        ]);

    }

    public function cekToken()
    {
        $id = hex2bin($this->request->getPost('id-ujian'));
        $id_siswa = hex2bin($this->request->getPost('id-siswa'));
        $tokenInput = trim($this->request->getPost('token'));

        $ujian = $this->detail->where('id_ujian', $id)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ujian tidak ditemukan',
                'id_ujian' => $id
            ]);
        }

        // Token kosong atau tidak sesuai
        if (empty($ujian['token']) || $ujian['token'] !== $tokenInput) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token salah atau tidak berlaku'
            ]);
        }

        // Token sudah expired
        if ($ujian['expired_at'] < time()) {
            // Hapus otomatis biar aman
            $this->detail->where('id_ujian', $id)
                ->update([
                    'token' => null,
                    'expired_at' => null
                ]);

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Token sudah kadaluarsa'
            ]);
        }

        // ✅ Token valid
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Token valid'
        ]);
    }


}
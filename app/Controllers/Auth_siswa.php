<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_ujian;
use App\Models\M_siswa;

class Auth_siswa extends BaseController
{
    protected $ujian, $siswa;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->siswa = new M_siswa();
    }

    public function index()
    {
        $data['siswa'] = $this->siswa->where('status_login', "enable")->findAll();
        $where = "tbl_ujian.status='final' OR tbl_ujian.status='dikerjakan'";
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
                'redirect_url' => base_url('/' . bin2hex('ujian-start') . '/' . bin2hex($idUjian) . '/' . bin2hex($idSiswa))
            ]);
        }

        // kalau bukan ajax, arahkan balik
        return redirect()->to('/ulangan');
    }


}
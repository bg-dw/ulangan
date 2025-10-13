<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_ujian;
use App\Models\M_siswa;
use App\Models\M_ujian_detail;
use App\Models\M_hasil;

class Auth_siswa extends BaseController
{
    protected $ujian, $siswa, $detail, $hasil;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->siswa = new M_siswa();
        $this->detail = new M_ujian_detail();
        $this->hasil = new M_hasil();
    }

    public function index()
    {
        session()->destroy();
        $where = "tbl_ujian_detail.status='dikerjakan'";
        $data['ujian'] = $this->ujian->get_list_where($where);
        return view('ujian/V_pilih_ujian', $data);
    }
    public function pilih_siswa()
    {
        $id_ujian = $this->request->getVar('id-ujian');
        $id_detail = $this->request->getVar('id-ujian-detail');
        $siswa = $this->siswa->get_siswa_enable();
        $mengerjakan = $this->hasil->get_mengerjakan($id_ujian);

        // Ambil semua id_siswa yang ingin dihapus
        $ids_to_remove = array_column($mengerjakan, 'id_siswa');

        // Filter array pertama, sisakan yang id_siswa-nya **tidak** ada di array kedua
        $siswa_filtered = array_filter($siswa, function ($s) use ($ids_to_remove) {
            return !in_array($s['id_siswa'], $ids_to_remove);
        });

        // Reset index array agar rapi
        $siswa_filtered = array_values($siswa_filtered);
        $data['siswa'] = $siswa_filtered;
        $data['id_ujian'] = $id_ujian;
        $data['id_detail'] = $id_detail;
        return view('ujian/V_auth_siswa', $data);
    }

    public function cek()
    {
        if ($this->request->isAJAX()) {
            $idSiswa = $this->request->getPost('id_siswa');
            $idUjian = $this->request->getPost('id_ujian');
            $idDetail = $this->request->getPost('id_detail');

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

            $get = $this->ujian->where('id_ujian', $idUjian)->first();
            // set session
            session()->set([
                'id_siswa' => $idSiswa,
                'id_soal' => $get['id_soal'],
                'id_ujian' => $idUjian,
                'id_detail' => $idDetail,
                'nama' => $siswa['nama_siswa'],
                'logged_in' => true,
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Semoga Berhasil!',
                'redirect_url' => base_url('/' . bin2hex('ujian-token'))
            ]);
        }

        // kalau bukan ajax, arahkan balik
        return redirect()->to('/ulangan');
    }

    function token()
    {
        return view('ujian/V_token');
    }

    public function getToken()
    {
        $id_ujian = session()->get('id_ujian');
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
            $sql = $this->detail->where('id_ujian', $id_ujian)
                ->update(['token' => null, 'expired_at' => null]);
            if ($sql) {
                return $this->response->setJSON([
                    'success' => false,
                    'token' => null,
                    'id' => $id_ujian
                ]);
            }
            return $this->response->setJSON([
                'success' => false,
                'token' => null,
                'id' => null
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
        $id = session()->get('id_ujian');
        $id_soal = session()->get('id_soal');
        $id_detail = session()->get('id_detail');
        $id_siswa = session()->get('id_siswa');
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
        try {
            // Cek apakah data hasil untuk siswa & soal ini sudah ada
            $cek = $this->hasil
                ->where([
                    'id_ujian_detail' => $id_detail,
                    'id_siswa' => $id_siswa,
                    'id_soal' => $id_soal
                ])->first();

            if ($cek) {
                // --- Jika data sudah ada
                $this->hasil->save([
                    'id_hasil' => $cek['id_hasil'],
                    'status' => 'dikerjakan',
                    'log' => "",
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Lanjut Mengerjakan!',
                ]);
            } else {
                // --- Jika data belum ada, buat baru ---
                $this->hasil->save([
                    'id_siswa' => $id_siswa,
                    'id_ujian_detail' => $id_detail,
                    'id_soal' => $id_soal,
                    'status' => 'dikerjakan',
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Token valid!',
                ]);
            }
        } catch (\Exception $e) {
            // Tangani error DB
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage(),
            ]);
        }

    }
}
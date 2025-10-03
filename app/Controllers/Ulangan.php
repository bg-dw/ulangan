<?php

namespace App\Controllers;
use App\Models\M_ujian;

class Ulangan extends BaseController
{
    protected $ujian;
    public function __construct()
    {
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $data['title'] = 'Status Ujian';
        return view('V_ulangan_daftar', $data);
    }
    public function reset()
    {
        $data['title'] = 'Reset Login';
        $data['ujian'] = $this->ujian->findAll();
        return view('V_ulangan_reset', $data);
    }
    public function status()
    {
        $data['title'] = 'Status Ujian';
        $where = "tbl_ujian_detail.status='final' OR tbl_ujian_detail.status='dikerjakan'";
        $data['ujian'] = $this->ujian->get_list_where($where);
        return view('V_ulangan_status', $data);
    }

    public function rilisToken()
    {
        $id = $this->request->getPost('id');
        $token = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        $expiredAt = time() + (5 * 60); // UNIX timestamp (detik)

        $this->ujian->update($id, ['token' => $token, 'expired_at' => $expiredAt]);

        return $this->response->setJSON([
            'success' => true,
            'token' => $token,
            'expired_at' => $expiredAt // ← langsung integer
        ]);
    }


    public function hapusToken()
    {
        $id = $this->request->getPost('id');
        $this->ujian->update($id, ['token' => null, 'expired_at' => null]);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Token berhasil dihapus'
        ]);
    }


    public function validasiToken($id, $inputToken)
    {
        $ujian = $this->db->table('ujian')->where('id_ujian', $id)->get()->getRowArray();

        if (!$ujian)
            return false;
        if ($ujian['token'] !== $inputToken)
            return false;
        if (strtotime($ujian['expired_at']) < time())
            return false;

        return true;
    }
    public function cekToken()
    {
        $id = $this->request->getPost('id');
        $tokenInput = $this->request->getPost('token');

        $ujian = $this->db->table('ujian')
            ->where('id_ujian', $id)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Ujian tidak ditemukan'
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
        if (strtotime($ujian['expired_at']) < time()) {
            // Hapus otomatis biar aman
            $this->db->table('ujian')
                ->where('id_ujian', $id)
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
    public function getToken($id)
    {
        $ujian = $this->db->table('ujian')
            ->select('token, expired_at')
            ->where('id_ujian', $id)
            ->get()
            ->getRowArray();

        if (!$ujian) {
            return $this->response->setJSON([
                'success' => false,
                'token' => null
            ]);
        }

        // cek expired
        if (!empty($ujian['expired_at']) && strtotime($ujian['expired_at']) < time()) {
            // hapus otomatis
            $this->db->table('ujian')
                ->where('id_ujian', $id)
                ->update(['token' => null, 'expired_at' => null]);

            return $this->response->setJSON([
                'success' => false,
                'token' => null
            ]);
        }

        return $this->response->setJSON([
            'success' => !empty($ujian['token']),
            'token' => $ujian['token'],
            'expired_at' => $ujian['expired_at']
        ]);
    }

}

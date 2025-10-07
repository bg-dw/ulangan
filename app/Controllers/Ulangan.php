<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_ujian_detail;

class Ulangan extends BaseController
{
    protected $ujian, $detail;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->detail = new M_ujian_detail();
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

        $this->detail->update($id, ['token' => $token, 'expired_at' => $expiredAt]);

        return $this->response->setJSON([
            'success' => true,
            'token' => $token,
            'expired_at' => $expiredAt // ← langsung integer
        ]);
    }


    public function hapusToken()
    {
        $id = $this->request->getPost('id');
        $this->detail->update($id, ['token' => null, 'expired_at' => null]);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Token berhasil dihapus'
        ]);
    }
}

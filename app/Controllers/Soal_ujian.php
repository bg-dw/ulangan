<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_ujian_detail;
use App\Models\M_hasil;

class Soal_ujian extends BaseController
{
    protected $ujian, $detail, $hasil;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->detail = new M_ujian_detail();
        $this->hasil = new M_hasil();
    }

    public function index()
    {
        $userId = session()->get('id');
        $data['title'] = 'Daftar Soal';
        $data['ujian'] = $this->detail->get_list();//daftar soal yang akan dikerjakan
        $data['soal'] = $this->ujian->get_list();//data soal ujian
        return view('ujian/V_ujian', $data);
    }

    //tambah daftar soal
    public function ac_add()
    {
        $id_ujian = $this->request->getVar('id-ujian');
        $where = [
            'id_ujian' => $id_ujian
        ];
        $data = [
            'id_ujian' => $id_ujian,
            'status' => "final"
        ];
        $cek = $this->detail->where($where)->first();
        if (!$cek):
            if ($id_ujian):
                $send = $this->detail->save($data);
                if ($send) {
                    session()->setFlashdata('success', ' Data berhasil disimpan.');
                } else {
                    session()->setFlashdata('warning', ' Data gagal ditambahkan.');
                }
            endif;
        else:
            session()->setFlashdata('warning', ' Data yang sama sudah ada!');
        endif;
        return redirect()->route(bin2hex('soal-ujian'));
    }

    //update daftar ujian
    public function ac_update()
    {
        $id_detail = $this->request->getVar('id');
        $id_ujian = $this->request->getVar('id-ujian');
        $where = "id_ujian_detail !=" . $id_detail . " AND id_ujian=" . $id_ujian;
        $cek = $this->detail->where($where)->first();
        if (!$cek):
            if ($id_detail):
                $data = [
                    'id_ujian' => $id_ujian
                ];
                $send = $this->detail->save($data);
                if ($send) {
                    session()->setFlashdata('success', ' Data berhasil disimpan.');
                } else {
                    session()->setFlashdata('warning', ' Data gagal ditambahkan.');
                }
            endif;
        else:
            session()->setFlashdata('warning', ' Data yang sama sudah ada!');
        endif;
        return redirect()->route(bin2hex('soal-ujian'));
    }

    //delete daftar ujian
    public function ac_delete()
    {

        $id = $this->request->getVar('id');
        $cek = $this->hasil->where('id_ujian_detail', $id)->first();
        if ($cek):
            session()->setFlashdata('error', ' Record digunakan oleh Hasil Ujian!');
        else:
            $send = $this->detail->where('id_ujian_detail', $id)->delete();
            if ($send):
                session()->setFlashdata('success', ' Data berhasil dihapus.');
            else:
                session()->setFlashdata('warning', ' Data gagal dihapus.');
            endif;
        endif;
        return redirect()->route(bin2hex('soal-ujian'));
    }


    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $data = [
            'id_ujian_detail' => $id,
            'status' => $status
        ];
        $this->detail->save($data);

        return $this->response->setJSON(['success' => true]);
    }

}

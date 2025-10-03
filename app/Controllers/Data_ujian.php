<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_ujian_detail;
use App\Models\M_soal;

class Data_ujian extends BaseController
{
    protected $ujian, $soal, $detail;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->detail = new M_ujian_detail();
        $this->soal = new M_soal();
    }

    public function index()
    {
        $userId = session()->get('id');
        $data['title'] = 'Data Ujian';
        $data['soal'] = $this->soal->soal_ready($userId);
        $data['ujian'] = $this->ujian->get_list();
        return view('V_data_ujian', $data);
    }

    //tambah ujian
    public function ac_add()
    {
        $id_soal = $this->request->getVar('id-soal');
        $tgl = $this->request->getVar('tgl');
        $data = [
            'id_soal' => $id_soal,
            'tgl' => $tgl
        ];
        $cek = $this->ujian->where($data)->first();
        if (!$cek):
            if ($id_soal):
                $send = $this->ujian->save($data);
                if ($send) {
                    session()->setFlashdata('success', ' Data berhasil disimpan.');
                } else {
                    session()->setFlashdata('warning', ' Data gagal ditambahkan.');
                }
            endif;
        else:
            session()->setFlashdata('warning', ' Data yang sama sudah ada!');
        endif;
        return redirect()->route(bin2hex('data-ujian'));
    }

    //update ujian
    public function ac_update()
    {
        $id_ujian = $this->request->getVar('id');
        $id_soal = $this->request->getVar('id-soal');
        $tgl = $this->request->getVar('tgl');
        $where = "id_ujian !=" . $id_ujian . " AND id_soal=" . $id_soal . " AND tgl='" . $tgl . "'";
        $cek = $this->ujian->where($where)->first();
        if (!$cek):
            if ($id_ujian):
                $data = [
                    'id_ujian' => $id_ujian,
                    'id_soal' => $id_soal,
                    'tgl' => $tgl
                ];
                $send = $this->ujian->save($data);
                if ($send) {
                    session()->setFlashdata('success', ' Data berhasil disimpan.');
                } else {
                    session()->setFlashdata('warning', ' Data gagal ditambahkan.');
                }
            endif;
        else:
            session()->setFlashdata('warning', ' Data yang sama sudah ada!');
        endif;
        return redirect()->route(bin2hex('data-ujian'));
    }

    //delete siswa
    public function ac_delete()
    {

        $id = $this->request->getVar('id');
        $cek = $this->detail->where('id_ujian', $id)->first();
        if ($cek):
            session()->setFlashdata('error', ' Record digunakan oleh Data Soal!');
        else:
            $send = $this->ujian->where('id_ujian', $id)->delete();
            if ($send):
                session()->setFlashdata('success', ' Data berhasil dihapus.');
            else:
                session()->setFlashdata('warning', ' Data gagal dihapus.');
            endif;
        endif;
        return redirect()->route(bin2hex('data-ujian'));
    }

}

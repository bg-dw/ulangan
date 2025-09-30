<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_mapel;
use App\Models\M_judul;

class Ujian extends BaseController
{
    protected $ujian, $mapel, $judul;
    public function __construct()
    {
        $this->is_session_available();
        $this->ujian = new M_ujian();
        $this->mapel = new M_mapel();
        $this->judul = new M_judul();
    }

    public function index()
    {
        $data['title'] = 'Ulangan';
        $data['mapel'] = $this->mapel->findAll();
        $data['judul'] = $this->judul->findAll();
        $data['ujian'] = $this->ujian->get_list();
        return view('V_ujian', $data);
    }

    //tambah ujian
    public function ac_add()
    {
        $id_judul = $this->request->getVar('id-judul');
        $id_mapel = $this->request->getVar('id-mapel');
        $tgl = $this->request->getVar('tgl');
        $where = [
            'id_judul' => $id_judul,
            'id_mapel' => $id_mapel,
            'tgl' => $tgl
        ];
        $cek = $this->ujian->where($where)->first();
        if (!$cek):
            if ($id_judul):
                $data = [
                    'id_judul' => $id_judul,
                    'id_mapel' => $id_mapel,
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

    //update ujian
    public function ac_update()
    {
        $id_ujian = $this->request->getVar('id');
        $id_judul = $this->request->getVar('id-judul');
        $id_mapel = $this->request->getVar('id-mapel');
        $tgl = $this->request->getVar('tgl');
        $where = [
            'id_judul' => $id_judul,
            'id_mapel' => $id_mapel,
            'tgl' => $tgl
        ];
        $cek = $this->ujian->where($where)->first();
        if (!$cek):
            if ($id_ujian):
                $data = [
                    'id_ujian' => $id_ujian,
                    'id_judul' => $id_judul,
                    'id_mapel' => $id_mapel,
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
        $send = $this->ujian->where('id_ujian', $this->request->getVar('id'))->delete();
        if ($send):
            session()->setFlashdata('success', ' Data berhasil dihapus.');
        else:
            session()->setFlashdata('warning', ' Data gagal dihapus.');
        endif;
        return redirect()->route(bin2hex('data-ujian'));
    }
}

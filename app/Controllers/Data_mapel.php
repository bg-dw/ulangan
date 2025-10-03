<?php

namespace App\Controllers;
use App\Models\M_mapel;
use App\Models\M_ujian;

class Data_mapel extends BaseController
{
    protected $mapel, $ujian;
    public function __construct()
    {
        $this->mapel = new M_mapel();
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $data['title'] = 'Data Mapel';
        $data['mapel'] = $this->mapel->findAll();
        return view('V_mapel', $data);
    }


    //tambah mapel
    public function ac_add()
    {
        $mapel = $this->request->getVar('mapel');
        if ($mapel):
            $data = [
                'mapel' => strtoupper($mapel)
            ];
            $send = $this->mapel->save($data);
            if ($send) {
                session()->setFlashdata('success', ' Data berhasil disimpan.');
            } else {
                session()->setFlashdata('warning', ' Data gagal ditambahkan.');
            }
        endif;
        return redirect()->route(bin2hex('data-mapel'));
    }

    //update mapel
    public function ac_update()
    {
        $data = [
            'id_mapel' => $this->request->getVar('id'),
            'mapel' => strtoupper($this->request->getVar('mapel'))
        ];

        $send = $this->mapel->save($data);
        if ($send) {
            session()->setFlashdata('success', ' Data berhasil disimpan.');
        } else {
            session()->setFlashdata('warning', ' Perubahan Data gagal!');
        }
        return redirect()->route(bin2hex('data-mapel'));
    }

    //delete mapel
    public function ac_delete()
    {
        $id = $this->request->getVar('id');
        $cek = $this->ujian->where('id_mapel', $id)->first();
        if ($cek):
            session()->setFlashdata('error', ' Record digunakan oleh Data Ujian!');
        else:
            $send = $this->mapel->where('id_mapel', $id)->delete();
            if ($send):
                session()->setFlashdata('success', ' Data berhasil dihapus.');
            else:
                session()->setFlashdata('warning', ' Data gagal dihapus.');
            endif;
        endif;
        return redirect()->route(bin2hex('data-mapel'));
    }
}

<?php

namespace App\Controllers;
use App\Models\M_judul;
use App\Models\M_ujian;

class Data_judul extends BaseController
{
    protected $judul, $ujian;
    public function __construct()
    {
        $this->judul = new M_judul();
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $data['title'] = 'Data Judul';
        $data['judul'] = $this->judul->findAll();
        return view('V_judul', $data);
    }

    //tambah judul
    public function ac_add()
    {
        $data = [
            'judul' => strtoupper($this->request->getVar('judul'))
        ];
        $cek = $this->judul->where($data)->first();
        if (!$cek) {
            $send = $this->judul->save($data);
            if ($send) {
                session()->setFlashdata('success', ' Data berhasil disimpan.');
            } else {
                session()->setFlashdata('warning', ' Data gagal ditambahkan.');
            }
        } else {
            session()->setFlashdata('warning', ' Data yang sama sudah ada!.');
        }
        return redirect()->route(bin2hex('data-judul'));
        // dd($send);
    }

    //update judul
    public function ac_update()
    {
        $data = [
            'id_judul' => $this->request->getVar('id'),
            'judul' => strtoupper($this->request->getVar('judul'))
        ];

        $cek = $this->judul->where($data)->first();
        if (!$cek) {
            $send = $this->judul->save($data);
            if ($send) {
                session()->setFlashdata('success', ' Data berhasil disimpan.');
            } else {
                session()->setFlashdata('warning', ' Data gagal ditambahkan.');
            }
        } else {
            session()->setFlashdata('warning', ' Data yang sama sudah ada!.');
        }
        return redirect()->route(bin2hex('data-judul'));
    }

    //delete judul
    public function ac_delete()
    {
        $id = $this->request->getVar('id');
        $cek = $this->ujian->where('id_judul', $id)->first();
        if ($cek):
            session()->setFlashdata('error', ' Record digunakan oleh Data Ujian!');
        else:
            $send = $this->judul->where('id_judul', $id)->delete();
            if ($send):
                session()->setFlashdata('success', ' Data berhasil dihapus.');
            else:
                session()->setFlashdata('warning', ' Data gagal dihapus.');
            endif;
        endif;
        return redirect()->route(bin2hex('data-judul'));
    }
}

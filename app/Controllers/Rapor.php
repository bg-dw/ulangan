<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_siswa;
use App\Models\M_nilai;
use App\Models\M_ujian_detail;

class Rapor extends BaseController
{
    protected $siswa, $nilai, $detail;
    public function __construct()
    {
        $this->siswa = new M_siswa();
        $this->nilai = new M_nilai();
        $this->detail = new M_ujian_detail();
    }

    public function index()
    {
        return view('rapor/V_auth');
    }
    public function auth()
    {
        $nama_inp = $this->request->getVar("nama");
        $tgl_inp = $this->request->getVar("tgl");
        $nama = strtoupper($nama_inp);
        $tgl = date('Y-m-d', strtotime($tgl_inp));
        $cek = $this->siswa->get_siswa($nama, $tgl);
        if ($cek):
            return $this->response->setJSON([
                'status' => true,
                'message' => 'Data Ditemukan!',
                'id_siswa' => bin2hex($cek['id_siswa']),
                'nama' => $cek['nama_siswa']
            ]);
        else:
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Data Tidak Ditemukan!'
            ]);
        endif;
    }

    public function tampil($id, $nama)
    {
        $data['title'] = 'Nilai Rapor';
        $id_siswa = hex2bin($id);

        $data['id_siswa'] = $id;
        $data['nama'] = $nama;
        // Ambil semua ujian yang statusnya "selesai"
        $data['ujian'] = $this->detail->get_list_selesai();
        if ($data['ujian']):
            $id_detail = $data['ujian'][0]['id_ujian_detail'];
            $data['mapel'] = $data['ujian'][0]['mapel'];
            $data['ujian_terpilih'] = $id_detail;
            //data ujian siswa
            $data['nilai'] = $this->nilai->get_list_where_siswa($id_siswa, $id_detail);
        endif;
        return view('rapor/V_nilai_rapor', $data);
    }

    public function tampil_pilihan($id, $nama, $id_detail)
    {
        $data['title'] = 'Nilai Rapor';
        $id_siswa = hex2bin($id);

        $data['nama'] = $nama;
        // Ambil semua ujian yang statusnya "selesai"
        $data['ujian'] = $this->detail->get_list_selesai();
        $data['mapel_terpilih'] = $this->detail->get_list_selesai_by($id_detail);
        if ($data['ujian']):
            $data['ujian_terpilih'] = $id_detail;
            $data['mapel'] = $data['mapel_terpilih']['mapel'];
            //data ujian siswa
            $data['nilai'] = $this->nilai->get_list_where_siswa($id_siswa, $id_detail);
        endif;
        return view('rapor/V_nilai_rapor', $data);
    }

}
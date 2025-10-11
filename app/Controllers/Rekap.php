<?php

namespace App\Controllers;
use App\Models\M_ujian;

class Rekap extends BaseController
{
    protected $ujian;
    public function __construct()
    {
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $data['title'] = 'Rekap Data';
        $where = "tbl_ujian_detail.status='selesai'";
        $data['ujian'] = $this->ujian->get_list_where($where);//mendapatkan data ujian
        $data['total_soal'] = null;
        if ($data['ujian']):
            $soal = $this->ujian->get_data_soal_by($data['ujian'][0]['id_ujian']);
            $json = $soal['data'];
            // Decode JSON jadi array PHP
            $tot = json_decode($json, true);

            // Ambil key 'jumlah_soal'
            $jumlah_soal = $tot['jumlah_soal']; // hasilnya: ["10", "5", "5"]

            // Ambil key 'bobot'
            $bobot = $tot['bobot'];

            // Ubah ke integer dan jumlahkan
            $total_soal = array_sum(array_map('intval', $jumlah_soal));
            $nilai_max = array_sum(array_map(function ($x, $y) {
                return $x * $y;
            }, $jumlah_soal, $bobot));
            $data['kunci'] = $tot['kunci'];
            $data['bobot'] = $bobot;

            $data['total_soal'] = $total_soal;
            $data['max'] = $nilai_max;
            $data['jenis'] = $tot['jenis_soal'];
            // print_r($data['jenis']);
            // dd($jumlah_soal);
        endif;
        return view('V_rekap', $data);
    }
}

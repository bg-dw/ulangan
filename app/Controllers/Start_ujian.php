<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_ujian;
use App\Models\M_soal;

class Start_ujian extends BaseController
{
    protected $ujian, $soal;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->soal = new M_soal();
    }

    public function start($idUjian, $idSiswa)
    {
        $id_ujian = hex2bin($idUjian);
        $id_siswa = hex2bin($idSiswa);

        // ambil soal berdasarkan id ujian
        $soal = $this->soal->soal_ready($id_ujian);
        echo $id_ujian;
        dd($soal);
        $data = [
            'id_ujian' => $idUjian,
            'id_siswa' => $idSiswa,
            'soal' => $soal,
        ];

        return view('ujian/V_halaman_ujian', $data);
    }
}
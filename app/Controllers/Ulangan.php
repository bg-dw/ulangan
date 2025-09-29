<?php

namespace App\Controllers;
use App\Models\M_siswa;

class Ulangan extends BaseController
{
    protected $siswa;
    public function __construct()
    {
        $this->is_session_available();
        $this->siswa = new M_siswa();
    }

    public function index()
    {
        $data['title'] = 'Beranda';
        $data['siswa'] = $this->siswa->findAll();
        return view('V_ulangan_daftar', $data);
    }
}

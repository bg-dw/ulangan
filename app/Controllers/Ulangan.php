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
        $data['title'] = 'Ulangan';
        $data['siswa'] = $this->siswa->findAll();
        return view('V_ulangan_daftar', $data);
    }
    public function reset()
    {
        $data['title'] = 'Reset Login';
        $data['siswa'] = $this->siswa->findAll();
        return view('V_ulangan_reset', $data);
    }
    public function status()
    {
        $data['title'] = 'Status Login';
        $data['siswa'] = $this->siswa->findAll();
        return view('V_ulangan_status', $data);
    }
}

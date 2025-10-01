<?php

namespace App\Controllers;
use App\Models\M_ujian;

class Hasil extends BaseController
{
    protected $ujian;
    public function __construct()
    {
        $this->is_session_available();
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $data['title'] = 'Hasil';
        $data['ujian'] = $this->ujian->get_list_hasil();
        return view('V_hasil', $data);
    }
}

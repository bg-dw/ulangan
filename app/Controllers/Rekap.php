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
        $data['ujian'] = $this->ujian->get_list_hasil();
        return view('V_rekap', $data);
    }
}

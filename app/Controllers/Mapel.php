<?php

namespace App\Controllers;
use App\Models\M_mapel;

class Mapel extends BaseController
{
    protected $mapel;
    public function __construct()
    {
        $this->is_session_available();
        $this->mapel = new M_mapel();
    }

    public function index()
    {
        $data['title'] = 'Data Mapel';
        $data['mapel'] = $this->mapel->findAll();
        return view('V_mapel', $data);
    }
}

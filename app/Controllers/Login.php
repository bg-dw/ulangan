<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_guru;

class Login extends BaseController
{
    protected $guru;
    public function __construct()
    {
        $this->guru = new M_guru();
    }

    public function index()
    {
        return view('V_login');
    }
    public function auth()
    {
        $cek = $this->guru->where(['username' => md5($this->request->getVar('user')), 'password' => md5($this->request->getVar('pass'))])->first();
        if ($cek):
            $ses = [
                'id' => $cek['id_guru'],
                'nama' => $cek['nama_guru'],
                'user' => $cek['username'],
                'pass' => $cek['password'],
                'passed' => true,
            ];
            session()->set($ses);
            session()->setFlashdata('success', ' Selamat Datang!');
            return redirect()->route(bin2hex('home'));
        else:
            return redirect()->back()->with('warning', ' Username atau password salah.');
        endif;
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/' . bin2hex('login')));
    }

}
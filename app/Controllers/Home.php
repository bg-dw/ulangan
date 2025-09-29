<?php

namespace App\Controllers;
use App\Models\M_siswa;

class Home extends BaseController
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
		return view('V_home', $data);
	}

	//tambah siswa
	public function ac_add()
	{
		$data = [
			'jk' => strtoupper($this->request->getVar('jk')),
			'nama_siswa' => strtoupper($this->request->getVar('nama'))
		];
		$send = $this->siswa->save($data);
		if ($send) {
			session()->setFlashdata('success', ' Data berhasil disimpan.');
		} else {
			session()->setFlashdata('warning', ' Data gagal ditambahkan.');
		}
		return redirect()->route(bin2hex('home'));
		// dd($send);
	}

	//update siswa
	public function ac_update()
	{
		$data = [
			'id_siswa' => $this->request->getVar('id'),
			'nama_siswa' => strtoupper($this->request->getVar('nama')),
			'jk' => strtoupper($this->request->getVar('jk'))
		];

		$send = $this->siswa->save($data);
		if ($send) {
			session()->setFlashdata('success', ' Data berhasil disimpan.');
		} else {
			session()->setFlashdata('warning', ' Perubahan Data gagal!');
		}
		return redirect()->route(bin2hex('home'));
	}

	//delete siswa
	public function ac_delete()
	{
		$send = $this->siswa->where('id_siswa', $this->request->getVar('id'))->delete();
		if ($send):
			session()->setFlashdata('success', ' Data berhasil dihapus.');
		else:
			session()->setFlashdata('warning', ' Data gagal dihapus.');
		endif;
		return redirect()->route(bin2hex('home'));
	}
}

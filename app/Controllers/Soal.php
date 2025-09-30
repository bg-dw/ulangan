<?php

namespace App\Controllers;
use App\Models\M_soal;
use App\Models\M_ujian;

class Soal extends BaseController
{
    protected $soal, $ujian;

    public function __construct()
    {
        $this->is_session_available();
        $this->soal = new M_soal();
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $userId = session()->get('id'); // konsisten pakai id_user

        $soals = $this->soal->get_list($userId);
        return view('soal/V_soal', ['soals' => $soals]);
    }

    function add_soal()
    {
        $data['title'] = 'Buat Soal';
        $data['ujian'] = $this->ujian->get_list();
        return view('soal/V_soal_add', $data);
    }

    public function saveSoal()
    {
        $data = $this->request->getPost();

        $errors = [];

        if (empty($data['id-ujian'])) {
            $errors['id-ujian'] = 'ujian harus dipilih';
        }

        // cek setiap pertanyaan
        if (!empty($data['pertanyaan'])) {
            foreach ($data['pertanyaan'] as $rowIndex => $row) {
                foreach ($row as $i => $pertanyaan) {
                    if (trim($pertanyaan) === '') {
                        $errors["pertanyaan_{$rowIndex}_{$i}"] = "Pertanyaan ke-{$i} pada kelompok {$rowIndex} harus diisi";
                    }
                }
            }
        }

        // cek setiap kunci jawaban
        if (!empty($data['kunci'])) {
            foreach ($data['kunci'] as $rowIndex => $row) {
                foreach ($row as $i => $kunci) {
                    if (trim($kunci) === '') {
                        $errors["kunci_{$rowIndex}_{$i}"] = "Kunci jawaban soal {$i} pada kelompok {$rowIndex} harus diisi";
                    }
                }
            }
        }

        // cek setiap jawaban pilihan
        if (!empty($data['jawaban'])) {
            foreach ($data['jawaban'] as $rowIndex => $row) {
                foreach ($row as $i => $pilihan) {
                    foreach ($pilihan as $key => $val) {
                        if (trim($val) === '') {
                            $errors["jawaban_{$rowIndex}_{$i}_{$key}"] = "Jawaban {$key} untuk soal {$i} (kelompok {$rowIndex}) harus diisi";
                        }
                    }
                }
            }
        }

        if ($errors) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => $errors
            ]);
        }

        $id_ujian = $this->request->getPost('id-ujian');
        //simpan ke database
        $this->soal->save([
            'id_guru' => session()->get('id'),
            'id_ujian' => $id_ujian,
            'data' => json_encode($this->request->getPost()),
            'status' => 'final',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Kalau lolos, lanjut simpan
        return $this->response->setJSON([
            'status' => 'ok',
            'msg' => 'Data tersimpan'
        ]);
    }


    // --- Draft Section ---

    public function saveDraft()
    {
        $userId = session()->get('id');
        $idujian = $this->request->getPost('id-ujian');

        $this->soal->save([
            'id_guru' => $userId,
            'id_ujian' => $idujian,
            'data' => json_encode($this->request->getPost()),
            'status' => 'draft',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function editDraft($idDraft)
    {
        $draft = $this->soal->find($idDraft);
        if (!$draft) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Draft tidak ditemukan");
        }

        $draft['data'] = json_decode($draft['data'], true);
        $id_ujian = $draft['id_ujian'];
        $draft['ujian'] = $this->ujian->get_ujian($id_ujian);
        return view('soal/V_soal_edit', ['draft' => $draft]);
    }

    public function updateDraft()
    {
        $idDraft = $this->request->getPost('id-soal');

        $send = $this->soal->update($idDraft, [
            'data' => json_encode($this->request->getPost())
        ]);
        if ($send) {
            session()->setFlashdata('success', ' Data berhasil disimpan.');
        } else {
            session()->setFlashdata('warning', ' Perubahan Data gagal!');
        }
        return redirect()->route(bin2hex('data-soal'));
    }

    public function finalDraft()
    {
        $idDraft = $this->request->getPost('id-soal');

        $send = $this->soal->update($idDraft, [
            'data' => json_encode($this->request->getPost())
        ]);
        if ($send) {
            session()->setFlashdata('success', ' Data FINAL berhasil disimpan.');
        } else {
            session()->setFlashdata('warning', ' Gagal menyimpan data!');
        }
        return redirect()->route(bin2hex('data-soal'));
    }

    //delete draft
    public function ac_delete()
    {
        $send = $this->soal->where('id_soal', $this->request->getVar('id'))->delete();
        if ($send):
            session()->setFlashdata('success', ' Data berhasil dihapus.');
        else:
            session()->setFlashdata('warning', ' Data gagal dihapus.');
        endif;
        return redirect()->route(bin2hex('data-soal'));
    }
}

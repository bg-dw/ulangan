<?php

namespace App\Controllers;
use App\Models\M_soal;
use App\Models\M_mapel;

class Soal extends BaseController
{
    protected $soal, $mapel;

    public function __construct()
    {
        $this->is_session_available();
        $this->soal = new M_soal();
        $this->mapel = new M_mapel();
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
        $data['mapel'] = $this->mapel->findAll();
        return view('soal/V_soal_add', $data);
    }

    public function saveSoal()
    {
        $data = $this->request->getPost();

        $errors = [];

        if (empty($data['judul'])) {
            $errors['judul'] = 'Judul harus diisi';
        }
        if (empty($data['id-mapel'])) {
            $errors['id-mapel'] = 'Mapel harus dipilih';
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

        $judul = $this->request->getPost('judul');
        $idMapel = $this->request->getPost('id-mapel');
        //simpan ke database
        $this->soal->save([
            'id_guru' => session()->get('id'),
            'id_mapel' => $idMapel,
            'judul' => $judul,
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
        $judul = $this->request->getPost('judul');
        $idMapel = $this->request->getPost('id-mapel');

        if (!$judul) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Judul wajib diisi']);
        }

        $this->soal->save([
            'id_guru' => $userId,
            'id_mapel' => $idMapel,
            'judul' => $judul,
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
        $id_mapel = $draft['data']['id-mapel'];
        $mapel = $this->mapel->where('id_mapel', $id_mapel)->first();
        $draft['mapel'] = $mapel['mapel'];
        return view('soal/draft_edit', ['draft' => $draft]);
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

    //delete draft
    public function ac_delete()
    {
        $send = $this->soal->where('id_draft', $this->request->getVar('id'))->delete();
        if ($send):
            session()->setFlashdata('success', ' Data berhasil dihapus.');
        else:
            session()->setFlashdata('warning', ' Data gagal dihapus.');
        endif;
        return redirect()->route(bin2hex('data-draft'));
    }
}

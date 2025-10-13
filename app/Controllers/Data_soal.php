<?php

namespace App\Controllers;
use App\Models\M_soal;
use App\Models\M_judul;
use App\Models\M_mapel;
use App\Models\M_ujian;

class Data_soal extends BaseController
{
    protected $soal, $judul, $mapel, $ujian;

    public function __construct()
    {
        $this->soal = new M_soal();
        $this->judul = new M_judul();
        $this->mapel = new M_mapel();
        $this->ujian = new M_ujian();
    }

    public function index()
    {
        $userId = session()->get('id'); // konsisten pakai id user

        $soals = $this->soal->get_list($userId);
        return view('soal/V_soal', ['soals' => $soals]);
    }

    function add_soal()
    {
        $data['title'] = 'Buat Soal';
        $data['judul'] = $this->judul->findAll();
        $data['mapel'] = $this->mapel->findAll();
        return view('soal/V_soal_add', $data);
    }
    public function uploadGambar()
    {
        $file = $this->request->getFile('upload'); // wajib 'upload' (CKFinderAdapter)
        if (!$file->isValid()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'File tidak valid.']);
        }

        // Validasi ekstensi & ukuran
        $ext = strtolower($file->getClientExtension());
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Tipe file tidak diizinkan.']);
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Ukuran file melebihi 5MB.']);
        }

        // Pastikan folder ada
        $uploadPath = FCPATH . 'public/assets/uploads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        // Simpan file
        $newName = $file->getRandomName();
        $file->move($uploadPath, $newName);

        $url = base_url('public/assets/uploads/' . $newName);

        return $this->response->setJSON(['url' => $url]);
    }
    public function hapusGambar()
    {
        $url = $this->request->getPost('url');
        if ($url) {
            $path = FCPATH . str_replace(base_url(), '', $url);
            if (is_file($path)) {
                unlink($path);
                return $this->response->setJSON(['success' => true]);
            }
        }
        return $this->response->setJSON(['success' => false]);
    }



    public function saveSoal()
    {
        $data = $this->request->getPost();

        $errors = [];

        if (empty($data['id-judul']) || empty($data['id-mapel'])) {
            $errors['id-judul'] = 'Judul harus dipilih';
            $errors['id-mapel'] = 'Mapel harus dipilih';
        }

        $cek = $this->soal->where(['id_judul' => $data['id-judul'], 'id_mapel' => $data['id-mapel']])->first();
        if ($cek) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Soal sudah pernah dibuat! '
            ]);
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

        //simpan ke database
        $this->soal->save([
            'id_guru' => session()->get('id'),
            'id_judul' => $data['id-judul'],
            'id_mapel' => $data['id-mapel'],
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
        $id_judul = $this->request->getPost('id-judul');
        $id_mapel = $this->request->getPost('id-mapel');
        $cek = $this->soal->where(['id_judul' => $id_judul, 'id_mapel' => $id_mapel])->first();
        if ($cek) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => 'Data Ujian sudah pernah dibuat! '
            ]);
        }
        $this->soal->save([
            'id_guru' => $userId,
            'id_judul' => $id_judul,
            'id_mapel' => $id_mapel,
            'data' => json_encode($this->request->getPost()),
            'status' => 'draft',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }
    public function editDraft($idDraft)
    {
        $judul = $this->judul->findAll();
        $mapel = $this->mapel->findAll();
        $draft = $this->soal->find($idDraft);
        if (!$draft) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Draft tidak ditemukan");
        }

        $draft['data'] = json_decode($draft['data'], true);
        return view('soal/V_soal_edit', ['draft' => $draft, 'judul' => $judul, 'mapel' => $mapel]);
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
            'data' => json_encode($this->request->getPost()),
            'status' => "final"
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
        $id = $this->request->getVar('id');
        $cek = $this->ujian->where('id_soal', $id)->first();
        if ($cek):
            session()->setFlashdata('error', ' Record digunakan oleh Data Ujian!');
        else:
            $send = $this->soal->where('id_soal', $id)->delete();
            if ($send):
                session()->setFlashdata('success', ' Data berhasil dihapus.');
            else:
                session()->setFlashdata('warning', ' Data gagal dihapus.');
            endif;
        endif;
        return redirect()->route(bin2hex('data-soal'));
    }
}

<?php

namespace App\Controllers;
use App\Models\M_soal;
use App\Models\M_mapel;
use App\Models\M_soal_draft;

class Soal extends BaseController
{
    protected $soal, $mapel, $draft;

    public function __construct()
    {
        $this->is_session_available();
        $this->soal = new M_soal();
        $this->mapel = new M_mapel();
        $this->draft = new M_soal_draft(); // sinkron dengan model
    }

    public function index()
    {
        $data['title'] = 'Beranda';
        $data['mapel'] = $this->mapel->findAll();
        $data['soal'] = $this->soal->findAll();
        return view('soal/V_soal', $data);
    }

    // --- Draft Section ---

    public function listDraft()
    {
        $userId = session()->get('id'); // konsisten pakai id_user

        $drafts = $this->draft
            ->select('id_draft, judul, status, updated_at, created_at')
            ->where('id_guru', $userId)
            ->orderBy('updated_at', 'DESC')
            ->findAll();

        return view('soal/V_soal_draft', ['drafts' => $drafts]);
    }

    public function createDraft()
    {
        return view('soal/create'); // form untuk buat judul draft baru
    }

    public function saveDraft()
    {
        $userId = session()->get('id');
        $judul = $this->request->getPost('judul');

        if (!$judul) {
            return $this->response->setJSON(['status' => 'error', 'msg' => 'Judul wajib diisi']);
        }

        $this->draft->save([
            'id_guru' => $userId,
            'judul' => $judul,
            'data' => json_encode($this->request->getPost()),
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['status' => 'ok']);
    }

    public function editDraft($idDraft)
    {
        $draft = $this->draft->find($idDraft);
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
        $idDraft = $this->request->getPost('id_draft');

        $send = $this->draft->update($idDraft, [
            'data' => json_encode($this->request->getPost()),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if ($send) {
            session()->setFlashdata('success', ' Data berhasil disimpan.');
        } else {
            session()->setFlashdata('warning', ' Perubahan Data gagal!');
        }
        return redirect()->route(bin2hex('data-draft'));
    }

    //delete draft
    public function ac_delete()
    {
        $send = $this->draft->where('id_draft', $this->request->getVar('id'))->delete();
        if ($send):
            session()->setFlashdata('success', ' Data berhasil dihapus.');
        else:
            session()->setFlashdata('warning', ' Data gagal dihapus.');
        endif;
        return redirect()->route(bin2hex('data-draft'));
    }
}

<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_nilai;

class Rekap extends BaseController
{
    protected $ujian, $nilai;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->nilai = new M_nilai();
    }

    public function index()
    {
        $data['title'] = 'Rekap Data';
        $where = "tbl_ujian_detail.status='selesai'";
        $data['ujian'] = $this->ujian->get_list_where($where);//mendapatkan data ujian
        $data['total_soal'] = null;
        if ($data['ujian']):
            $soal = $this->ujian->get_data_soal_by($data['ujian'][0]['id_ujian']);
            $json = $soal['data'];
            // Decode JSON jadi array PHP
            $tot = json_decode($json, true);

            // Ambil key 'jumlah_soal'
            $jumlah_soal = $tot['jumlah_soal']; // hasilnya: ["10", "5", "5"]

            // Ambil key 'bobot'
            $bobot = $tot['bobot'];

            // Ubah ke integer dan jumlahkan
            $total_soal = array_sum(array_map('intval', $jumlah_soal));
            $nilai_max = array_sum(array_map(function ($x, $y) {
                return $x * $y;
            }, $jumlah_soal, $bobot));

            // Ambil semua nilai yang sudah tersimpan dari tbl_nilai
            $nilaiData = $this->nilai->get_nilai_ujian_by($data['ujian'][0]['id_ujian']);
            $data['nilai_tersimpan'] = [];
            foreach ($nilaiData as $n) {
                $data['nilai_tersimpan'][$n['id_hasil']] = json_decode($n['nilai'], true);
            }

            $data['kunci'] = $tot['kunci'];
            $data['bobot'] = $bobot;

            $data['total_soal'] = $total_soal;
            $data['max'] = $nilai_max;
            $data['jenis'] = $tot['jenis_soal'];
            // print_r($data['jenis']);
            // dd($jumlah_soal);
        endif;
        return view('V_rekap', $data);
    }

    public function init_nilai()
    {
        if ($this->request->isAJAX()) {
            $json = $this->request->getJSON(true);
            $dataSiswa = $json['data'] ?? [];

            foreach ($dataSiswa as $s) {
                $id_hasil = $s['id_hasil'] ?? null; // ✅ aman jika tidak ada
                $nilai = json_encode($s['nilai'] ?? []);

                if (!$id_hasil)
                    continue; // ✅ lewati jika tidak ada id_hasil

                $cek = $this->nilai->where('id_hasil', $id_hasil)->get()->getRowArray();

                if (!$cek) {
                    $this->nilai->insert([
                        'id_hasil' => $id_hasil,
                        'nilai' => $nilai
                    ]);
                }
            }


            return $this->response->setJSON(['status' => 'ok']);
        }

        return $this->response->setJSON(['status' => 'error']);
    }


    public function update_nilai()
    {
        if ($this->request->isAJAX()) {
            $json = $this->request->getJSON(true);
            $id_hasil = $json['id_hasil'] ?? null;
            $nilaiBaru = $json['nilai'] ?? null;
            $total_skor = $json['total_skor'] ?? 0;
            $persentase = $json['persentase'] ?? 0;

            if (!$id_hasil || !$nilaiBaru) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
            }

            $data = [
                'nilai' => json_encode($nilaiBaru),
                'total_skor' => $total_skor,
                'persentase' => $persentase
            ];

            $this->nilai
                ->where('id_hasil', $id_hasil)
                ->set($data)
                ->update();

            return $this->response->setJSON([
                'status' => 'ok',
                'message' => 'Nilai berhasil diperbarui',
                'data' => $data
            ]);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
    }


    // public function get_nilai()
    // {
    //     if ($this->request->isAJAX()) {
    //         $json = $this->request->getJSON(true);
    //         $idDetail = $json['id_detail'] ?? 0;

    //         $result = $this->nilai->get_nilai($idDetail);

    //         return $this->response->setJSON(['status' => 'ok', 'data' => $result]);
    //     }

    //     return $this->response->setJSON(['status' => 'error']);
    // }


}

<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_nilai;
use App\Models\M_hasil;

class Rekap extends BaseController
{
    protected $ujian, $nilai, $hasil;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->nilai = new M_nilai();
        $this->hasil = new M_hasil();
    }

    public function index()
    {
        $data['title'] = 'Rekap Data';

        // Ambil semua ujian yang statusnya "selesai"
        $where = "tbl_ujian_detail.status='selesai'";
        $data['daftar'] = $this->ujian->get_list_where($where);
        if ($data['daftar']) {
            // Gunakan ujian pertama sebagai default
            $ujianDefault = $data['daftar'][0];
            $id_detail = $ujianDefault['id_ujian_detail'];
            $id_ujian = $ujianDefault['id_ujian'];

            // Ambil detail soal & kunci
            $soal = $this->ujian->get_data_soal_by($id_ujian);
            $json = $soal['data'];
            $tot = json_decode($json, true);

            // Hitung total soal dan nilai maksimal
            $jumlah_soal = $tot['jumlah_soal'];
            $bobot = $tot['bobot'];
            $total_soal = array_sum(array_map('intval', $jumlah_soal));

            $nilai_max = array_sum(array_map(function ($x, $y) {
                return $x * $y;
            }, $jumlah_soal, $bobot));

            // Ambil nilai tersimpan di tabel nilai
            $nilaiData = $this->nilai->get_nilai_ujian_by($id_ujian);
            $nilai_tersimpan = [];
            foreach ($nilaiData as $n) {
                $nilai_tersimpan[$n['id_hasil']] = json_decode($n['nilai'], true);
            }

            // Siapkan semua data untuk dikirim ke view
            $data['id_ujian_detail'] = $id_detail;
            $data['id_terpilih'] = $id_detail;
            $data['kunci'] = $tot['kunci'];
            $data['bobot'] = $bobot;
            $data['total_soal'] = $total_soal;
            $data['max'] = $nilai_max;
            $data['jenis'] = $tot['jenis_soal'];
            $data['nilai_tersimpan'] = $nilai_tersimpan;
            $data['judul'] = $ujianDefault['judul'];
            $data['mapel'] = $ujianDefault['mapel'];
            $data['tgl'] = $ujianDefault['tgl'];
        } else {
            $data['ujian_terpilih'] = null;
            $data['total_soal'] = null;
        }

        return view('V_rekap', $data);
    }

    public function get_data_by($id_detail)
    {
        $data['title'] = 'Rekap Data';
        // Ambil semua ujian yang statusnya "selesai"
        $where = "tbl_ujian_detail.status='selesai'";
        $data['daftar'] = $this->ujian->get_list_where($where);
        $terpilih = $this->ujian->get_list_where_by($id_detail);
        if ($terpilih) {
            $ujianDefault = $terpilih;
            $id_ujian = $ujianDefault['id_ujian'];

            // Ambil detail soal & kunci
            $soal = $this->ujian->get_data_soal_by($id_ujian);
            $json = $soal['data'];
            $tot = json_decode($json, true);

            // Hitung total soal dan nilai maksimal
            $jumlah_soal = $tot['jumlah_soal'];
            $bobot = $tot['bobot'];
            $total_soal = array_sum(array_map('intval', $jumlah_soal));

            $nilai_max = array_sum(array_map(function ($x, $y) {
                return $x * $y;
            }, $jumlah_soal, $bobot));

            // Ambil nilai tersimpan di tabel nilai
            $nilaiData = $this->nilai->get_nilai_ujian_by($id_ujian);
            $nilai_tersimpan = [];
            foreach ($nilaiData as $n) {
                $nilai_tersimpan[$n['id_hasil']] = json_decode($n['nilai'], true);
            }

            // Siapkan semua data untuk dikirim ke view
            $data['id_ujian_detail'] = $id_detail;
            $data['id_terpilih'] = $id_detail;
            $data['kunci'] = $tot['kunci'];
            $data['bobot'] = $bobot;
            $data['total_soal'] = $total_soal;
            $data['max'] = $nilai_max;
            $data['jenis'] = $tot['jenis_soal'];
            $data['nilai_tersimpan'] = $nilai_tersimpan;
            $data['judul'] = $ujianDefault['judul'];
            $data['mapel'] = $ujianDefault['mapel'];
            $data['tgl'] = $ujianDefault['tgl'];
        } else {
            $data['ujian_terpilih'] = null;
            $data['total_soal'] = null;
        }

        return view('V_rekap', $data);
    }

    public function init_nilai()
    {
        if ($this->request->isAJAX()) {
            $json = $this->request->getJSON(true);
            $dataSiswa = $json['data'] ?? [];

            foreach ($dataSiswa as $s) {
                $id_hasil = $s['id_hasil'] ?? null;
                $nilai = json_encode($s['nilai'] ?? []);

                if (!$id_hasil)
                    continue;

                $cek = $this->nilai->where('id_hasil', $id_hasil)->get()->getRowArray();

                if (!$cek) {
                    // belum ada -> insert
                    $this->nilai->insert([
                        'id_hasil' => $id_hasil,
                        'nilai' => $nilai,
                        'total_skor' => $s['total_skor'] ?? 0,
                        'persentase' => $s['persentase'] ?? 0
                    ]);
                } else {
                    // sudah ada -> cek apakah nilai lama semua nol/ kosong
                    $existingNilai = json_decode($cek['nilai'] ?? '[]', true);

                    $allZeroOrEmpty = true;
                    if (is_array($existingNilai)) {
                        foreach ($existingNilai as $no => $obj) {
                            $v = $obj['nilai'] ?? 0;
                            if (floatval($v) !== 0.0) {
                                $allZeroOrEmpty = false;
                                break;
                            }
                        }
                    }

                    // update only if old values are all zero (so we don't overwrite real scores)
                    if ($allZeroOrEmpty) {
                        $this->nilai
                            ->where('id_hasil', $id_hasil)
                            ->set(['nilai' => $nilai])
                            ->update();
                    }
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
}

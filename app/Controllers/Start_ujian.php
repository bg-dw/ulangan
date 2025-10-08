<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\M_ujian;
use App\Models\M_soal;
use App\Models\M_hasil;

class Start_ujian extends BaseController
{
    protected $ujian, $soal, $hasil;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->soal = new M_soal();
        $this->hasil = new M_hasil();
        if (!session()->get('logged_in')) {
            redirect()->to(base_url());
        }
    }

    public function start()
    {
        $id_ujian = session()->get('id_ujian');
        $id_siswa = session()->get('id_siswa');

        // Ambil soal berdasarkan id ujian
        $soalData = $this->soal->soal_dikerjakan($id_ujian);
        $soalFinal = [];

        if ($soalData):

            // Ambil jawaban siswa untuk ujian ini
            $hasilSiswa = $this->hasil
                ->where('id_siswa', $id_siswa)
                ->where('id_ujian_detail', $soalData[0]['id_ujian_detail'] ?? '')
                ->findAll();

            // Build jawaban siswa & status soal
            $jawabanSiswaDb = [];
            $statusSoal = [];

            foreach ($hasilSiswa as $h) {
                $jawabanDecoded = json_decode($h['jawaban'], true);
                $isi = $jawabanDecoded['isi'] ?? '';
                $ragu = (int) ($jawabanDecoded['ragu'] ?? 0);

                $id_soal = $h['id_soal'];

                $jawabanSiswaDb[$id_soal] = $jawabanDecoded;

                if ($ragu) {
                    $statusSoal[$id_soal] = 'ragu';
                } elseif (!empty($isi)) {
                    $statusSoal[$id_soal] = 'jawab';
                } else {
                    $statusSoal[$id_soal] = 'none';
                }
            }

            // Build soalFinal
            $no_soal = 1;
            foreach ($soalData as $s) {
                $decoded = is_string($s['data']) ? json_decode($s['data'], true) : $s['data'];
                if (!$decoded)
                    continue;

                unset($decoded['kunci']); // hapus kunci jawaban

                $jenisList = $decoded['jenis_soal'] ?? [];
                $pertanyaanList = $decoded['pertanyaan'] ?? [];

                foreach ($jenisList as $jIndex => $jenis) {
                    $pertanyaanJenis = $pertanyaanList[$jIndex] ?? [];

                    foreach ($pertanyaanJenis as $i => $p) {
                        $opsiGabung = [];
                        if ($jenis === 'pilihan_ganda') {
                            $opsiHuruf = $decoded['opsi'][0][$i] ?? [];
                            $opsiText = $decoded['opsi'][1][$i] ?? [];
                            foreach ($opsiHuruf as $idx => $huruf) {
                                $opsiGabung[$huruf] = $opsiText[$idx] ?? '';
                            }
                        }
                        // Ambil jawaban siswa jika ada
                        $jawabanSiswa = $jawabanSiswaDb[0][$no_soal]['isi'] ?? null;
                        $ragu = (int) ($jawabanSiswaDb[$no_soal]['ragu'] ?? 0);

                        $soalFinal[] = [
                            'id_soal' => $no_soal,
                            'jenis_soal' => $jenis,
                            'pertanyaan' => $p,
                            'opsi' => $opsiGabung,
                            'jawaban_siswa' => $jawabanSiswa,
                            'ragu' => $ragu,
                        ];

                        $no_soal++;
                    }
                }
            }
            // Kirim data ke view
            $data = [
                'id_detail' => $soalData[0]['id_ujian_detail'],
                'id_siswa' => $id_siswa,
                'soal' => $soalFinal,
                'statusSoal' => $statusSoal,
                'jawaban_siswa' => $jawabanSiswaDb
            ];

        endif;
        return view('ujian/V_halaman_ujian', $data);
    }

    //simpan jawaban
    public function simpan_jawaban()
    {
        $id_siswa = $this->request->getPost('id_siswa');
        $id_detail = $this->request->getPost('id_detail');
        $id_soal = $this->request->getPost('id_soal');
        $jawaban = $this->request->getPost('jawaban');
        $jenis_soal = $this->request->getPost('jenis_soal');
        $ragu = (int) $this->request->getPost('ragu');

        if (empty($id_siswa) || empty($id_detail) || empty($id_soal)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap',
            ]);
        }

        // Ambil data jawaban existing
        $existing = $this->hasil
            ->where('id_siswa', $id_siswa)
            ->where('id_ujian_detail', $id_detail)
            ->first();

        $allJawaban = [];

        if ($existing && !empty($existing['jawaban'])) {
            $allJawaban = json_decode($existing['jawaban'], true);
        }

        // Update jawaban soal saat ini
        $allJawaban[$id_soal] = [
            'isi' => $jawaban,
            'jenis' => $jenis_soal,
            'ragu' => $ragu
        ];
        // Sort index ascending
        ksort($allJawaban, SORT_NUMERIC);
        $data = [
            'id_siswa' => $id_siswa,
            'id_ujian_detail' => $id_detail,
            'jawaban' => json_encode($allJawaban)
        ];
        if ($existing) {
            $this->hasil->update($existing['id_hasil'], $data);
        } else {
            $this->hasil->save($data);
        }

        // Hitung progress (berapa persen soal sudah dijawab)
        $totalSoal = $this->hasil
            ->where('id_ujian_detail', $id_detail)
            ->where('id_siswa', $id_siswa)
            ->countAllResults(false);

        $totalJawaban = $this->hasil
            ->where('id_ujian_detail', $id_detail)
            ->where('id_siswa', $id_siswa)
            ->where("JSON_LENGTH(jawaban) > 0")
            ->countAllResults(false);

        $progress = $totalSoal > 0 ? round(($totalJawaban / $totalSoal) * 100) : 0;

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Jawaban disimpan',
            'progress' => $progress,
        ]);
    }
    function selesai()
    {
    }

}
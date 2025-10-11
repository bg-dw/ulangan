<?php

namespace App\Controllers;
use App\Models\M_ujian;
use App\Models\M_ujian_detail;
use App\Models\M_hasil;
use App\Models\M_soal;

class Ulangan extends BaseController
{
    protected $ujian, $detail, $hasil, $soal;
    public function __construct()
    {
        $this->ujian = new M_ujian();
        $this->detail = new M_ujian_detail();
        $this->hasil = new M_hasil();
        $this->soal = new M_soal();
    }

    public function index()
    {
        $data['title'] = 'Daftar Login';
        $where = "tbl_ujian_detail.status='final' OR tbl_ujian_detail.status='dikerjakan'";
        $data['ujian'] = $this->ujian->get_list_where($where);//mendapatkan data ujian
        $data['total_soal'] = null;
        if ($data['ujian']):
            $soal = $this->ujian->get_data_soal_by($data['ujian'][0]['id_ujian']);
            $json = $soal['data'];
            // Decode JSON jadi array PHP
            $tot = json_decode($json, true);

            // Ambil key 'jumlah_soal'
            $jumlah_soal = $tot['jumlah_soal']; // hasilnya: ["10", "5", "5"]

            // Ubah ke integer dan jumlahkan
            $total_soal = array_sum(array_map('intval', $jumlah_soal));

            $data['total_soal'] = $total_soal;
        endif;
        return view('V_ulangan_daftar', $data);
    }

    public function get_last_update()
    {
        $updated_at = $this->hasil->get_last_update();
        return json_encode($updated_at);
    }
    public function get_ujian()
    {
        $id_detail = $this->request->getPost('id_detail');
        $jawaban = $this->hasil->get_hasil_all_siswa_by($id_detail);
        return $this->response->setJSON($jawaban);
    }
    public function reset()
    {
        $data['title'] = 'Reset Login';
        $data['ujian'] = $this->ujian->findAll();
        return view('V_ulangan_reset', $data);
    }
    public function status()
    {
        $data['title'] = 'Status Ujian';
        $where = "tbl_ujian_detail.status='final' OR tbl_ujian_detail.status='dikerjakan'";
        $data['ujian'] = $this->ujian->get_list_where($where);
        if ($data['ujian']):
            $id = $data['ujian'][0]['id_ujian_detail']; // timestamp unix dari database
            $timestampLama = $data['ujian'][0]['expired_at']; // timestamp unix dari database

            // Timestamp sekarang
            $timestampSekarang = time(); // time() mengembalikan Unix timestamp saat ini

            // Hitung selisih dalam detik
            $selisihDetik = $timestampSekarang - $timestampLama;
            // 5 menit = 5 * 60 detik
            if ($selisihDetik > 5 * 60) {
                $this->detail->update($id, ['token' => null, 'expired_at' => null]);
            }
        endif;
        return view('V_ulangan_status', $data);
    }

    public function rilisToken()
    {
        $id = $this->request->getPost('id');
        $token = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
        $expiredAt = time() + (5 * 60); // UNIX timestamp (detik)

        $this->detail->update($id, ['token' => $token, 'expired_at' => $expiredAt]);

        return $this->response->setJSON([
            'success' => true,
            'token' => $token,
            'expired_at' => $expiredAt // ← langsung integer
        ]);
    }


    public function hapusToken()
    {
        $id = $this->request->getPost('id');
        $this->detail->update($id, ['token' => null, 'expired_at' => null]);
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Token berhasil dihapus'
        ]);
    }
}

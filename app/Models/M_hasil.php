<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_hasil extends Model
{
    protected $table = 'tbl_hasil';
    protected $primaryKey = 'id_hasil';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_hasil',
        'id_siswa',
        'id_ujian_detail',
        'id_soal',
        'jawaban',
        'status',
        'log',
        'created_at',
        'updated_at'
    ];

    function get_mengerjakan($id)
    {
        $this->select('tbl_hasil.id_siswa');
        $this->join('tbl_ujian_detail', 'tbl_ujian_detail.id_ujian_detail = tbl_hasil.id_ujian_detail');
        $this->join('tbl_ujian', 'tbl_ujian_detail.id_ujian = tbl_ujian.id_ujian');
        $this->where('tbl_ujian.id_ujian', $id);
        $this->where('tbl_hasil.status', "dikerjakan");
        $this->groupBy('tbl_hasil.id_siswa');
        return $this->findAll();
    }

    function get_hasil_siswa()
    {
        $this->select('tbl_siswa.nama_siswa,tbl_ujian.tgl,tbl_judul.id_judul,tbl_judul.judul,tbl_mapel.id_mapel,tbl_mapel.mapel,tbl_hasil.id_hasil,tbl_hasil.status,tbl_hasil.log');
        $this->join('tbl_siswa', 'tbl_hasil.id_siswa = tbl_siswa.id_siswa');
        $this->join('tbl_ujian_detail', 'tbl_hasil.id_ujian_detail = tbl_ujian_detail.id_ujian_detail');
        $this->join('tbl_soal', 'tbl_hasil.id_soal = tbl_soal.id_soal');
        $this->join('tbl_ujian', 'tbl_ujian.id_soal = tbl_soal.id_soal');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->orderBy('tbl_siswa.nama_siswa', 'ASC');
        return $this->findAll();
    }
    function get_hasil_all_siswa_by($id_detail)
    {
        $this->select('tbl_siswa.nama_siswa,tbl_hasil.jawaban,tbl_hasil.status,tbl_hasil.log');
        $this->join('tbl_siswa', 'tbl_hasil.id_siswa = tbl_siswa.id_siswa');
        $this->join('tbl_ujian_detail', 'tbl_hasil.id_ujian_detail = tbl_ujian_detail.id_ujian_detail');
        $this->join('tbl_soal', 'tbl_hasil.id_soal = tbl_soal.id_soal');
        $this->join('tbl_ujian', 'tbl_ujian.id_soal = tbl_soal.id_soal');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->where('tbl_ujian_detail.id_ujian_detail', $id_detail);
        $this->orderBy('tbl_siswa.nama_siswa', 'ASC');
        return $this->findAll();
    }

    function get_last_update()
    {
        $this->select('tbl_hasil.updated_at');
        $this->orderBy('tbl_hasil.updated_at', 'DESC');
        return $this->first();
    }
}
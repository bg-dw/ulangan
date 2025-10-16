<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_ujian_detail extends Model
{
    protected $table = 'tbl_ujian_detail';
    protected $primaryKey = 'id_ujian_detail';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_ujian_detail',
        'id_ujian',
        'status',
        'token',
        'expired_at',
        'created_at',
        'updated_at'
    ];

    function get_list()
    {
        $this->select('tbl_ujian_detail.id_ujian_detail,tbl_ujian_detail.status,tbl_ujian.id_ujian,tbl_ujian.tgl,tbl_judul.judul,tbl_mapel.mapel');
        $this->join('tbl_ujian', 'tbl_ujian.id_ujian = tbl_ujian_detail.id_ujian');
        $this->join('tbl_soal', 'tbl_soal.id_soal = tbl_ujian.id_soal');
        $this->join('tbl_mapel', 'tbl_mapel.id_mapel = tbl_soal.id_mapel');
        $this->join('tbl_judul', 'tbl_judul.id_judul = tbl_soal.id_judul');
        $this->orderBy('tbl_ujian_detail.updated_at', 'DESC');
        return $this->findAll();
    }
    function get_list_selesai()
    {
        $this->select('tbl_ujian_detail.id_ujian_detail,tbl_ujian_detail.status,tbl_ujian.id_ujian,tbl_ujian.tgl,tbl_judul.judul,tbl_mapel.mapel');
        $this->join('tbl_ujian', 'tbl_ujian.id_ujian = tbl_ujian_detail.id_ujian');
        $this->join('tbl_soal', 'tbl_soal.id_soal = tbl_ujian.id_soal');
        $this->join('tbl_mapel', 'tbl_mapel.id_mapel = tbl_soal.id_mapel');
        $this->join('tbl_judul', 'tbl_judul.id_judul = tbl_soal.id_judul');
        $this->where('tbl_ujian_detail.status', 'selesai');
        $this->orderBy('tbl_ujian_detail.updated_at', 'DESC');
        return $this->findAll();
    }

    function get_list_selesai_by($id_detail)
    {
        $this->select('tbl_mapel.mapel');
        $this->join('tbl_ujian', 'tbl_ujian.id_ujian = tbl_ujian_detail.id_ujian');
        $this->join('tbl_soal', 'tbl_soal.id_soal = tbl_ujian.id_soal');
        $this->join('tbl_mapel', 'tbl_mapel.id_mapel = tbl_soal.id_mapel');
        $this->join('tbl_judul', 'tbl_judul.id_judul = tbl_soal.id_judul');
        $this->where('tbl_ujian_detail.id_ujian_detail', $id_detail);
        $this->where('tbl_ujian_detail.status', 'selesai');
        return $this->first();
    }
}
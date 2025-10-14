<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_ujian extends Model
{
    protected $table = 'tbl_ujian';
    protected $primaryKey = 'id_ujian';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_ujian',
        'id_soal',
        'tgl',
        'created_at',
        'updated_at'
    ];

    function get_list()
    {
        $this->select('tbl_ujian.id_ujian,tbl_soal.id_soal,tbl_soal.status,tbl_judul.judul,tbl_mapel.mapel,tbl_ujian.tgl');
        $this->join('tbl_soal', 'tbl_ujian.id_soal = tbl_soal.id_soal');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->orderBy('tbl_soal.updated_at', 'DESC');
        return $this->findAll();
    }

    function get_list_where($where)
    {
        $this->select('tbl_ujian.id_ujian,tbl_ujian.tgl,tbl_ujian_detail.id_ujian_detail,tbl_ujian_detail.token,tbl_ujian_detail.status,tbl_ujian_detail.expired_at,tbl_judul.id_judul,tbl_judul.judul,tbl_mapel.id_mapel,tbl_mapel.mapel');
        $this->join('tbl_soal', 'tbl_soal.id_soal = tbl_ujian.id_soal');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->join('tbl_ujian_detail', 'tbl_ujian.id_ujian = tbl_ujian_detail.id_ujian');
        $this->where($where);
        $this->orderBy('tbl_ujian.updated_at', 'DESC');
        return $this->findAll();
    }

    function get_list_where_by($id_detail)
    {
        $this->select('tbl_ujian.id_ujian,tbl_ujian.tgl,tbl_ujian_detail.id_ujian_detail,tbl_ujian_detail.token,tbl_ujian_detail.status,tbl_ujian_detail.expired_at,tbl_judul.id_judul,tbl_judul.judul,tbl_mapel.id_mapel,tbl_mapel.mapel');
        $this->join('tbl_soal', 'tbl_soal.id_soal = tbl_ujian.id_soal');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->join('tbl_ujian_detail', 'tbl_ujian.id_ujian = tbl_ujian_detail.id_ujian');
        $this->where('tbl_ujian_detail.id_ujian_detail', $id_detail);
        return $this->first();
    }

    function get_data_soal_by($id_ujian)
    {
        $this->select('tbl_soal.data');
        $this->join('tbl_soal', 'tbl_ujian.id_soal = tbl_soal.id_soal');
        $this->where('tbl_ujian.id_ujian', $id_ujian);
        return $this->first();
    }
    function get_data_jawaban_by($id_ujian)
    {
        $this->select('tbl_soal.data');
        $this->join('tbl_soal', 'tbl_ujian.id_soal = tbl_soal.id_soal');
        $this->where('tbl_ujian.id_ujian', $id_ujian);
        return $this->first();
    }
}
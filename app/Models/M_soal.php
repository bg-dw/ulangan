<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_soal extends Model
{
    protected $table = 'tbl_soal';
    protected $primaryKey = 'id_soal';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_soal',
        'id_guru',
        'id_judul',
        'id_mapel',
        'data',
        'status',
        'created_at',
        'updated_at'
    ];

    function get_list($id)
    {
        $this->select('tbl_soal.id_soal,tbl_soal.status,tbl_soal.created_at,tbl_soal.updated_at,tbl_judul.judul,tbl_mapel.mapel');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->where(['tbl_soal.id_guru' => $id]);
        $this->orderBy('tbl_soal.updated_at', 'DESC');
        return $this->findAll();
    }

    function soal_ready($id)
    {
        $this->select('tbl_soal.id_soal,tbl_soal.status,tbl_soal.created_at,tbl_soal.updated_at,tbl_judul.judul,tbl_mapel.mapel');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->where(['tbl_soal.id_guru' => $id]);
        $this->where(['tbl_soal.status' => 'final']);
        $this->orderBy('tbl_soal.updated_at', 'DESC');
        return $this->findAll();
    }

    function soal_dikerjakan($id)
    {
        $this->select('tbl_soal.id_soal,tbl_soal.data,tbl_soal.status,tbl_soal.created_at,tbl_soal.updated_at,tbl_judul.judul,tbl_mapel.mapel,tbl_ujian_detail.id_ujian_detail');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_soal.id_judul = tbl_judul.id_judul');
        $this->join('tbl_ujian', 'tbl_ujian.id_soal = tbl_soal.id_soal');
        $this->join('tbl_ujian_detail', 'tbl_ujian_detail.id_ujian = tbl_ujian.id_ujian');
        $this->where(['tbl_ujian_detail.id_ujian' => $id]);
        $this->where(['tbl_ujian_detail.status' => 'dikerjakan']);
        $this->orderBy('tbl_soal.updated_at', 'DESC');
        return $this->findAll();
    }
}
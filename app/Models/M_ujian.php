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
        'id_mapel',
        'id_judul',
        'tgl',
        'status',
        'created_at',
        'updated_at'
    ];

    function get_list()
    {
        $this->select('tbl_ujian.id_ujian,tbl_ujian.tgl,tbl_ujian.status,tbl_judul.id_judul,tbl_judul.judul,tbl_mapel.id_mapel,tbl_mapel.mapel');
        $this->join('tbl_mapel', 'tbl_ujian.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_ujian.id_judul = tbl_judul.id_judul');
        $this->orderBy('tbl_ujian.updated_at', 'DESC');
        return $this->findAll();
    }

    function get_ujian($id)
    {
        $this->select('tbl_ujian.tgl,tbl_judul.judul,tbl_mapel.mapel');
        $this->join('tbl_mapel', 'tbl_ujian.id_mapel = tbl_mapel.id_mapel');
        $this->join('tbl_judul', 'tbl_ujian.id_judul = tbl_judul.id_judul');
        $this->where('tbl_ujian.id_ujian', $id);
        return $this->first();
    }
}
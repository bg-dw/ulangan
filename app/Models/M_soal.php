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
        'id_mapel',
        'judul',
        'data',
        'status',
        'created_at',
        'updated_at'
    ];

    function get_list($id)
    {
        $this->select('tbl_soal.id_soal,tbl_soal.judul,tbl_soal.status,tbl_soal.created_at,tbl_soal.updated_at,tbl_mapel.mapel');
        $this->join('tbl_mapel', 'tbl_soal.id_mapel = tbl_mapel.id_mapel');
        $this->where(['tbl_soal.id_guru' => $id]);
        $this->orderBy('tbl_soal.updated_at', 'DESC');
        return $this->findAll();
    }
}
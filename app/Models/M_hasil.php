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
        $this->groupBy('tbl_hasil.id_siswa');
        return $this->findAll();
    }
}
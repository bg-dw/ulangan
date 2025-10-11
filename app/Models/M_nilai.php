<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_nilai extends Model
{
    protected $table = 'tbl_nilai';
    protected $primaryKey = 'id_nilai';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_hasil',
        'nilai',
        'total_skor',
        'persentase',
        'created_at',
        'updated_at'
    ];
    function get_nilai_ujian_by($id_ujian)
    {
        $this->select('tbl_nilai.id_hasil,tbl_nilai.nilai,tbl_nilai.total_skor,tbl_nilai.persentase');
        $this->join('tbl_hasil', 'tbl_nilai.id_hasil = tbl_nilai.id_hasil');
        $this->join('tbl_ujian_detail', 'tbl_hasil.id_ujian_detail = tbl_ujian_detail.id_ujian_detail');
        $this->where('tbl_ujian_detail.id_ujian', $id_ujian);
        return $this->get()->getResultArray();
    }
}
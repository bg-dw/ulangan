<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_siswa extends Model
{
    protected $table = 'tbl_siswa';
    protected $primaryKey = 'id_siswa';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_siswa',
        'nama_siswa',
        'jk',
        'status_login'
    ];

    function get_siswa_enable()
    {
        $this->select('tbl_siswa.id_siswa,tbl_siswa.nama_siswa');
        $this->where('tbl_siswa.status_login', "enable");
        $this->orderBy('tbl_siswa.nama_siswa');
        return $this->findAll();
    }
}
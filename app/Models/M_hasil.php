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
        'id_ujain_detail',
        'jawaban',
        'status',
        'created_at',
        'updated_at'
    ];
}
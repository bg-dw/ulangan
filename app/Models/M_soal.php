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
        'id_mapel',
        'jenis_soal',
        'soal',
        'bobot'
    ];
}
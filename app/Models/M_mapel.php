<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_mapel extends Model
{
    protected $table = 'tbl_mapel';
    protected $primaryKey = 'id_mapel';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_mapel',
        'mapel'
    ];
}
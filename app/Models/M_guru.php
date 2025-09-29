<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_guru extends Model
{
    protected $table = 'tbl_guru';
    protected $primaryKey = 'id_guru';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_guru',
        'username',
        'password',
        'nama_guru'
    ];
}
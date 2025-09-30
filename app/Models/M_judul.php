<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_judul extends Model
{
    protected $table = 'tbl_judul';
    protected $primaryKey = 'id_judul';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_judul',
        'judul',
        'created_at',
        'updated_at'
    ];
}
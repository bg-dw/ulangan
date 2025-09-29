<?php
namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;

class M_soal_draft extends Model
{
    protected $table = 'tbl_draft_soal';
    protected $primaryKey = 'id_draft';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id_draft',
        'id_guru',
        'judul',
        'data',
        'status'
    ];
}
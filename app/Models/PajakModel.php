<?php

namespace App\Models;

use CodeIgniter\Model;

class PajakModel extends Model
{
    protected $table = 'pajak';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'bku_id',
        'jenis_pajak',
        'nilai',
        'kode_billing',
        'status_setor',
    ];

    protected $validationRules = [
        'bku_id' => 'required|integer',
        'jenis_pajak' => 'required|in_list[PPN,PPh]',
        'nilai' => 'required|decimal',
        'status_setor' => 'required|in_list[Belum,Sudah]',
    ];
}

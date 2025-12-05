<?php

namespace App\Models;

use CodeIgniter\Model;

class SppRincianModel extends Model
{
    protected $table = 'spp_rincian';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'spp_id',
        'apbdes_id',
        'nilai_pencairan',
    ];

    protected $validationRules = [
        'spp_id' => 'required|integer',
        'apbdes_id' => 'required|integer',
        'nilai_pencairan' => 'required|decimal',
    ];
}

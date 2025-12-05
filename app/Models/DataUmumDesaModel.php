<?php

namespace App\Models;

use CodeIgniter\Model;

class DataUmumDesaModel extends Model
{
    protected $table = 'data_umum_desa';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'kode_desa',
        'nama_desa',
        'nama_kepala_desa',
        'nama_bendahara',
        'npwp',
        'tahun_anggaran',
    ];

    protected $validationRules = [
        'kode_desa' => 'required',
        'nama_desa' => 'required',
        'nama_kepala_desa' => 'required',
        'nama_bendahara' => 'required',
        'tahun_anggaran' => 'required|integer',
    ];
}

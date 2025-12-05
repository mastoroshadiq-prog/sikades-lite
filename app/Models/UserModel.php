<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'username',
        'password_hash',
        'role',
        'kode_desa',
        'created_at',
    ];

    protected $validationRules = [
        'username' => 'required|is_unique[users.username,id,{id}]',
        'password_hash' => 'required',
        'role' => 'required|in_list[Administrator,Operator Desa,Kepala Desa]',
    ];

    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'is_unique' => 'Username sudah digunakan',
        ],
        'role' => [
            'required' => 'Role harus dipilih',
            'in_list' => 'Role tidak valid',
        ],
    ];
}

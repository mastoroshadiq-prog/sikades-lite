<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'Administrator',
                'kode_desa' => '3201012001', // Example village code
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'operator',
                'password_hash' => password_hash('operator123', PASSWORD_DEFAULT),
                'role' => 'Operator Desa',
                'kode_desa' => '3201012001',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'kades',
                'password_hash' => password_hash('kades123', PASSWORD_DEFAULT),
                'role' => 'Kepala Desa',
                'kode_desa' => '3201012001',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}

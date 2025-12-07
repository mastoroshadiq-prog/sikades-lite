<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AsetKategoriSeeder extends Seeder
{
    public function run()
    {
        // Kategori Aset sesuai Permendagri 1 Tahun 2016
        $data = [
            [
                'kode_golongan' => '01',
                'nama_golongan' => 'Tanah',
                'uraian'        => 'Tanah yang dimiliki/dikuasai oleh Pemerintah Desa',
                'masa_manfaat'  => null, // Tanah tidak disusutkan
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'kode_golongan' => '02',
                'nama_golongan' => 'Peralatan dan Mesin',
                'uraian'        => 'Alat-alat besar, alat angkutan, alat bengkel, alat pertanian, alat kantor, alat rumah tangga, peralatan komputer, dll',
                'masa_manfaat'  => 4, // 4 tahun
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'kode_golongan' => '03',
                'nama_golongan' => 'Gedung dan Bangunan',
                'uraian'        => 'Bangunan gedung, monumen, bangunan menara, tugu, dll',
                'masa_manfaat'  => 20, // 20 tahun
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'kode_golongan' => '04',
                'nama_golongan' => 'Jalan, Irigasi, dan Jaringan',
                'uraian'        => 'Jalan dan jembatan, bangunan air/irigasi, instalasi, jaringan',
                'masa_manfaat'  => 10, // 10 tahun
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'kode_golongan' => '05',
                'nama_golongan' => 'Aset Tetap Lainnya',
                'uraian'        => 'Buku perpustakaan, barang bercorak kebudayaan, hewan, tumbuhan, dll',
                'masa_manfaat'  => 4, // 4 tahun
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'kode_golongan' => '06',
                'nama_golongan' => 'Konstruksi Dalam Pengerjaan',
                'uraian'        => 'Aset tetap yang sedang dalam proses pembangunan/pengerjaan',
                'masa_manfaat'  => null, // Belum disusutkan
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Using insert with ignore to prevent duplicate errors
        $this->db->table('aset_kategori')->insertBatch($data);
    }
}

<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RefRekeningSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Level 1: Akun - PENDAPATAN
            ['id' => 1, 'kode_akun' => '4', 'nama_akun' => 'PENDAPATAN', 'level' => 1, 'parent_id' => null],
            
            // Level 2: Kelompok - Pendapatan
            ['id' => 2, 'kode_akun' => '4.1', 'nama_akun' => 'Pendapatan Asli Desa', 'level' => 2, 'parent_id' => 1],
            ['id' => 3, 'kode_akun' => '4.2', 'nama_akun' => 'Pendapatan Transfer', 'level' => 2, 'parent_id' => 1],
            ['id' => 4, 'kode_akun' => '4.3', 'nama_akun' => 'Pendapatan Lain-lain', 'level' => 2, 'parent_id' => 1],
            
            // Level 3: Jenis - Pendapatan Asli Desa
            ['id' => 5, 'kode_akun' => '4.1.1', 'nama_akun' => 'Hasil Usaha Desa', 'level' => 3, 'parent_id' => 2],
            ['id' => 6, 'kode_akun' => '4.1.2', 'nama_akun' => 'Hasil Aset Desa', 'level' => 3, 'parent_id' => 2],
            ['id' => 7, 'kode_akun' => '4.1.3', 'nama_akun' => 'Swadaya, Partisipasi dan Gotong Royong', 'level' => 3, 'parent_id' => 2],
            ['id' => 8, 'kode_akun' => '4.1.4', 'nama_akun' => 'Lain-lain Pendapatan Asli Desa', 'level' => 3, 'parent_id' => 2],
            
            // Level 3: Jenis - Pendapatan Transfer
            ['id' => 9, 'kode_akun' => '4.2.1', 'nama_akun' => 'Dana Desa', 'level' => 3, 'parent_id' => 3],
            ['id' => 10, 'kode_akun' => '4.2.2', 'nama_akun' => 'Bagian dari Hasil Pajak dan Retribusi Daerah Kabupaten/Kota', 'level' => 3, 'parent_id' => 3],
            ['id' => 11, 'kode_akun' => '4.2.3', 'nama_akun' => 'Alokasi Dana Desa', 'level' => 3, 'parent_id' => 3],
            ['id' => 12, 'kode_akun' => '4.2.4', 'nama_akun' => 'Bantuan Keuangan Provinsi', 'level' => 3, 'parent_id' => 3],
            ['id' => 13, 'kode_akun' => '4.2.5', 'nama_akun' => 'Bantuan Keuangan Kabupaten/Kota', 'level' => 3, 'parent_id' => 3],
            
            // Level 1: Akun - BELANJA
            ['id' => 14, 'kode_akun' => '5', 'nama_akun' => 'BELANJA', 'level' => 1, 'parent_id' => null],
            
            // Level 2: Kelompok - Belanja
            ['id' => 15, 'kode_akun' => '5.1', 'nama_akun' => 'Bidang Penyelenggaraan Pemerintahan Desa', 'level' => 2, 'parent_id' => 14],
            ['id' => 16, 'kode_akun' => '5.2', 'nama_akun' => 'Bidang Pelaksanaan Pembangunan Desa', 'level' => 2, 'parent_id' => 14],
            ['id' => 17, 'kode_akun' => '5.3', 'nama_akun' => 'Bidang Pembinaan Kemasyarakatan Desa', 'level' => 2, 'parent_id' => 14],
            ['id' => 18, 'kode_akun' => '5.4', 'nama_akun' => 'Bidang Pemberdayaan Masyarakat Desa', 'level' => 2, 'parent_id' => 14],
            ['id' => 19, 'kode_akun' => '5.5', 'nama_akun' => 'Bidang Penanggulangan Bencana, Darurat dan Mendesak Desa', 'level' => 2, 'parent_id' => 14],
            
            // Level 3: Jenis - Bidang Penyelenggaraan Pemerintahan Desa
            ['id' => 20, 'kode_akun' => '5.1.1', 'nama_akun' => 'Penghasilan Tetap dan Tunjangan', 'level' => 3, 'parent_id' => 15],
            ['id' => 21, 'kode_akun' => '5.1.2', 'nama_akun' => 'Operasional Perkantoran', 'level' => 3, 'parent_id' => 15],
            ['id' => 22, 'kode_akun' => '5.1.3', 'nama_akun' => 'Operasional BPD', 'level' => 3, 'parent_id' => 15],
            ['id' => 23, 'kode_akun' => '5.1.4', 'nama_akun' => 'Operasional RT/RW', 'level' => 3, 'parent_id' => 15],
            
            // Level 4: Objek - Penghasilan Tetap dan Tunjangan
            ['id' => 24, 'kode_akun' => '5.1.1.01', 'nama_akun' => 'Penghasilan Tetap Kepala Desa dan Perangkat', 'level' => 4, 'parent_id' => 20],
            ['id' => 25, 'kode_akun' => '5.1.1.02', 'nama_akun' => 'Tunjangan Kepala Desa dan Perangkat', 'level' => 4, 'parent_id' => 20],
            ['id' => 26, 'kode_akun' => '5.1.1.03', 'nama_akun' => 'Jaminan Sosial Kepala Desa dan Perangkat', 'level' => 4, 'parent_id' => 20],
            
            // Level 4: Objek - Operasional Perkantoran
            ['id' => 27, 'kode_akun' => '5.1.2.01', 'nama_akun' => 'Alat Tulis Kantor', 'level' => 4, 'parent_id' => 21],
            ['id' => 28, 'kode_akun' => '5.1.2.02', 'nama_akun' => 'Benda Pos', 'level' => 4, 'parent_id' => 21],
            ['id' => 29, 'kode_akun' => '5.1.2.03', 'nama_akun' => 'Pakaian Dinas dan Atribut', 'level' => 4, 'parent_id' => 21],
            ['id' => 30, 'kode_akun' => '5.1.2.04', 'nama_akun' => 'Alat dan Bahan Kebersihan', 'level' => 4, 'parent_id' => 21],
            ['id' => 31, 'kode_akun' => '5.1.2.05', 'nama_akun' => 'Perjalanan Dinas', 'level' => 4, 'parent_id' => 21],

            // Level 3: Jenis - Bidang Pelaksanaan Pembangunan Desa
            ['id' => 32, 'kode_akun' => '5.2.1', 'nama_akun' => 'Kegiatan Pembangunan', 'level' => 3, 'parent_id' => 16],
            ['id' => 33, 'kode_akun' => '5.2.1.01', 'nama_akun' => 'Pemeliharaan Jalan Desa', 'level' => 4, 'parent_id' => 32],
            ['id' => 34, 'kode_akun' => '5.2.1.02', 'nama_akun' => 'Pembangunan/Rehabilitasi/Peningkatan Jalan Desa', 'level' => 4, 'parent_id' => 32],
            ['id' => 35, 'kode_akun' => '5.2.1.03', 'nama_akun' => 'Pembangunan/Rehabilitasi/Peningkatan Jembatan Desa', 'level' => 4, 'parent_id' => 32],
            
            // Level 1: Akun - PEMBIAYAAN
            ['id' => 36, 'kode_akun' => '6', 'nama_akun' => 'PEMBIAYAAN', 'level' => 1, 'parent_id' => null],
            
            // Level 2: Kelompok - Penerimaan Pembiayaan
            ['id' => 37, 'kode_akun' => '6.1', 'nama_akun' => 'Penerimaan Pembiayaan', 'level' => 2, 'parent_id' => 36],
            ['id' => 38, 'kode_akun' => '6.1.1', 'nama_akun' => 'Sisa Lebih Perhitungan Anggaran (SiLPA) Tahun Sebelumnya', 'level' => 3, 'parent_id' => 37],
            ['id' => 39, 'kode_akun' => '6.1.2', 'nama_akun' => 'Pencairan Dana Cadangan', 'level' => 3, 'parent_id' => 37],
            ['id' => 40, 'kode_akun' => '6.1.3', 'nama_akun' => 'Hasil Penjualan Kekayaan Desa yang Dipisahkan', 'level' => 3, 'parent_id' => 37],
            
            // Level 2: Kelompok - Pengeluaran Pembiayaan
            ['id' => 41, 'kode_akun' => '6.2', 'nama_akun' => 'Pengeluaran Pembiayaan', 'level' => 2, 'parent_id' => 36],
            ['id' => 42, 'kode_akun' => '6.2.1', 'nama_akun' => 'Pembentukan Dana Cadangan', 'level' => 3, 'parent_id' => 41],
            ['id' => 43, 'kode_akun' => '6.2.2', 'nama_akun' => 'Penyertaan Modal Desa', 'level' => 3, 'parent_id' => 41],
        ];

        $this->db->table('ref_rekening')->insertBatch($data);
    }
}

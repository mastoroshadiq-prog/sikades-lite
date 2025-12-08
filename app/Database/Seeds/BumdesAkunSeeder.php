<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BumdesAkunSeeder extends Seeder
{
    public function run()
    {
        $akun = [
            // ASET (1xx)
            ['kode_akun' => '100', 'nama_akun' => 'ASET', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 100],
            ['kode_akun' => '110', 'nama_akun' => 'Aset Lancar', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 110],
            ['kode_akun' => '111', 'nama_akun' => 'Kas', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 111],
            ['kode_akun' => '112', 'nama_akun' => 'Kas di Bank', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 112],
            ['kode_akun' => '113', 'nama_akun' => 'Piutang Usaha', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 113],
            ['kode_akun' => '114', 'nama_akun' => 'Persediaan Barang', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 114],
            ['kode_akun' => '115', 'nama_akun' => 'Perlengkapan', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 115],
            ['kode_akun' => '120', 'nama_akun' => 'Aset Tetap', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 120],
            ['kode_akun' => '121', 'nama_akun' => 'Peralatan', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 121],
            ['kode_akun' => '122', 'nama_akun' => 'Akumulasi Penyusutan Peralatan', 'tipe' => 'ASET', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 122],
            ['kode_akun' => '123', 'nama_akun' => 'Kendaraan', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 123],
            ['kode_akun' => '124', 'nama_akun' => 'Akumulasi Penyusutan Kendaraan', 'tipe' => 'ASET', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 124],
            ['kode_akun' => '125', 'nama_akun' => 'Bangunan', 'tipe' => 'ASET', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 125],
            ['kode_akun' => '126', 'nama_akun' => 'Akumulasi Penyusutan Bangunan', 'tipe' => 'ASET', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 126],
            
            // KEWAJIBAN (2xx)
            ['kode_akun' => '200', 'nama_akun' => 'KEWAJIBAN', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 1, 'urutan' => 200],
            ['kode_akun' => '210', 'nama_akun' => 'Kewajiban Jangka Pendek', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 1, 'urutan' => 210],
            ['kode_akun' => '211', 'nama_akun' => 'Hutang Usaha', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 211],
            ['kode_akun' => '212', 'nama_akun' => 'Hutang Gaji', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 212],
            ['kode_akun' => '213', 'nama_akun' => 'Hutang Pajak', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 213],
            ['kode_akun' => '220', 'nama_akun' => 'Kewajiban Jangka Panjang', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 1, 'urutan' => 220],
            ['kode_akun' => '221', 'nama_akun' => 'Hutang Bank', 'tipe' => 'KEWAJIBAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 221],
            
            // EKUITAS (3xx)
            ['kode_akun' => '300', 'nama_akun' => 'EKUITAS', 'tipe' => 'EKUITAS', 'saldo_normal' => 'KREDIT', 'is_header' => 1, 'urutan' => 300],
            ['kode_akun' => '311', 'nama_akun' => 'Modal Penyertaan Desa', 'tipe' => 'EKUITAS', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 311],
            ['kode_akun' => '312', 'nama_akun' => 'Modal Masyarakat', 'tipe' => 'EKUITAS', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 312],
            ['kode_akun' => '313', 'nama_akun' => 'Modal Lainnya', 'tipe' => 'EKUITAS', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 313],
            ['kode_akun' => '320', 'nama_akun' => 'Laba Ditahan', 'tipe' => 'EKUITAS', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 320],
            ['kode_akun' => '330', 'nama_akun' => 'Laba Tahun Berjalan', 'tipe' => 'EKUITAS', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 330],
            
            // PENDAPATAN (4xx)
            ['kode_akun' => '400', 'nama_akun' => 'PENDAPATAN', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'KREDIT', 'is_header' => 1, 'urutan' => 400],
            ['kode_akun' => '411', 'nama_akun' => 'Pendapatan Penjualan', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 411],
            ['kode_akun' => '412', 'nama_akun' => 'Pendapatan Jasa', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 412],
            ['kode_akun' => '413', 'nama_akun' => 'Pendapatan Sewa', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 413],
            ['kode_akun' => '414', 'nama_akun' => 'Pendapatan Bunga', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 414],
            ['kode_akun' => '419', 'nama_akun' => 'Pendapatan Lain-lain', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'KREDIT', 'is_header' => 0, 'urutan' => 419],
            ['kode_akun' => '421', 'nama_akun' => 'Diskon Penjualan', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 421],
            ['kode_akun' => '422', 'nama_akun' => 'Retur Penjualan', 'tipe' => 'PENDAPATAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 422],
            
            // BEBAN (5xx)
            ['kode_akun' => '500', 'nama_akun' => 'BEBAN', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 500],
            ['kode_akun' => '510', 'nama_akun' => 'Beban Pokok Penjualan', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 510],
            ['kode_akun' => '511', 'nama_akun' => 'Harga Pokok Penjualan (HPP)', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 511],
            ['kode_akun' => '520', 'nama_akun' => 'Beban Operasional', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 520],
            ['kode_akun' => '521', 'nama_akun' => 'Beban Gaji Pegawai', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 521],
            ['kode_akun' => '522', 'nama_akun' => 'Beban Sewa', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 522],
            ['kode_akun' => '523', 'nama_akun' => 'Beban Listrik dan Air', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 523],
            ['kode_akun' => '524', 'nama_akun' => 'Beban Telepon dan Internet', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 524],
            ['kode_akun' => '525', 'nama_akun' => 'Beban Perlengkapan', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 525],
            ['kode_akun' => '526', 'nama_akun' => 'Beban Transportasi', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 526],
            ['kode_akun' => '527', 'nama_akun' => 'Beban Penyusutan', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 527],
            ['kode_akun' => '528', 'nama_akun' => 'Beban Pemeliharaan', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 528],
            ['kode_akun' => '529', 'nama_akun' => 'Beban Administrasi', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 529],
            ['kode_akun' => '530', 'nama_akun' => 'Beban Lain-lain', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 1, 'urutan' => 530],
            ['kode_akun' => '531', 'nama_akun' => 'Beban Bunga', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 531],
            ['kode_akun' => '532', 'nama_akun' => 'Beban Pajak', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 532],
            ['kode_akun' => '539', 'nama_akun' => 'Beban Tak Terduga', 'tipe' => 'BEBAN', 'saldo_normal' => 'DEBET', 'is_header' => 0, 'urutan' => 539],
        ];

        foreach ($akun as $a) {
            $this->db->table('bumdes_akun')->insert($a);
        }

        echo "BUMDes Chart of Accounts (SAK EMKM) seeded successfully!\n";
    }
}

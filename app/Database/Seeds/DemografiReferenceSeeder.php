<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemografiReferenceSeeder extends Seeder
{
    public function run()
    {
        // ========================================
        // Referensi Pendidikan (sesuai Dukcapil)
        // ========================================
        $pendidikan = [
            ['nama' => 'Tidak/Belum Sekolah', 'urutan' => 1],
            ['nama' => 'Belum Tamat SD/Sederajat', 'urutan' => 2],
            ['nama' => 'Tamat SD/Sederajat', 'urutan' => 3],
            ['nama' => 'SLTP/Sederajat', 'urutan' => 4],
            ['nama' => 'SLTA/Sederajat', 'urutan' => 5],
            ['nama' => 'Diploma I/II', 'urutan' => 6],
            ['nama' => 'Akademi/Diploma III/Sarjana Muda', 'urutan' => 7],
            ['nama' => 'Diploma IV/Strata I', 'urutan' => 8],
            ['nama' => 'Strata II', 'urutan' => 9],
            ['nama' => 'Strata III', 'urutan' => 10],
        ];

        $this->db->table('ref_pendidikan')->insertBatch($pendidikan);

        // ========================================
        // Referensi Pekerjaan (standar Dukcapil)
        // ========================================
        $pekerjaan = [
            ['nama' => 'Belum/Tidak Bekerja'],
            ['nama' => 'Mengurus Rumah Tangga'],
            ['nama' => 'Pelajar/Mahasiswa'],
            ['nama' => 'Pensiunan'],
            ['nama' => 'Pegawai Negeri Sipil (PNS)'],
            ['nama' => 'Tentara Nasional Indonesia (TNI)'],
            ['nama' => 'Kepolisian RI (POLRI)'],
            ['nama' => 'Perdagangan'],
            ['nama' => 'Petani/Pekebun'],
            ['nama' => 'Peternak'],
            ['nama' => 'Nelayan/Perikanan'],
            ['nama' => 'Industri'],
            ['nama' => 'Konstruksi'],
            ['nama' => 'Transportasi'],
            ['nama' => 'Karyawan Swasta'],
            ['nama' => 'Karyawan BUMN'],
            ['nama' => 'Karyawan BUMD'],
            ['nama' => 'Karyawan Honorer'],
            ['nama' => 'Buruh Harian Lepas'],
            ['nama' => 'Buruh Tani/Perkebunan'],
            ['nama' => 'Buruh Nelayan/Perikanan'],
            ['nama' => 'Buruh Peternakan'],
            ['nama' => 'Pembantu Rumah Tangga'],
            ['nama' => 'Tukang Cukur'],
            ['nama' => 'Tukang Listrik'],
            ['nama' => 'Tukang Batu'],
            ['nama' => 'Tukang Kayu'],
            ['nama' => 'Tukang Sol Sepatu'],
            ['nama' => 'Tukang Las/Pandai Besi'],
            ['nama' => 'Tukang Jahit'],
            ['nama' => 'Tukang Gigi'],
            ['nama' => 'Penata Rias'],
            ['nama' => 'Penata Busana'],
            ['nama' => 'Penata Rambut'],
            ['nama' => 'Mekanik'],
            ['nama' => 'Seniman'],
            ['nama' => 'Tabib'],
            ['nama' => 'Paraji'],
            ['nama' => 'Perancang Busana'],
            ['nama' => 'Penerjemah'],
            ['nama' => 'Imam Masjid'],
            ['nama' => 'Pendeta'],
            ['nama' => 'Pastor'],
            ['nama' => 'Wartawan'],
            ['nama' => 'Ustaz/Mubaligh'],
            ['nama' => 'Juru Masak'],
            ['nama' => 'Promotor Acara'],
            ['nama' => 'Anggota DPR-RI'],
            ['nama' => 'Anggota DPD'],
            ['nama' => 'Anggota DPRD Provinsi'],
            ['nama' => 'Anggota DPRD Kabupaten/Kota'],
            ['nama' => 'Presiden'],
            ['nama' => 'Wakil Presiden'],
            ['nama' => 'Dosen'],
            ['nama' => 'Guru'],
            ['nama' => 'Pilot'],
            ['nama' => 'Pengacara'],
            ['nama' => 'Notaris'],
            ['nama' => 'Arsitek'],
            ['nama' => 'Akuntan'],
            ['nama' => 'Konsultan'],
            ['nama' => 'Dokter'],
            ['nama' => 'Bidan'],
            ['nama' => 'Perawat'],
            ['nama' => 'Apoteker'],
            ['nama' => 'Psikiater/Psikolog'],
            ['nama' => 'Penyiar Televisi'],
            ['nama' => 'Penyiar Radio'],
            ['nama' => 'Pelaut'],
            ['nama' => 'Peneliti'],
            ['nama' => 'Sopir'],
            ['nama' => 'Pialang'],
            ['nama' => 'Paranormal'],
            ['nama' => 'Pedagang'],
            ['nama' => 'Perangkat Desa'],
            ['nama' => 'Kepala Desa'],
            ['nama' => 'Biarawati'],
            ['nama' => 'Wiraswasta'],
            ['nama' => 'Lainnya'],
        ];

        $this->db->table('ref_pekerjaan')->insertBatch($pekerjaan);
    }
}

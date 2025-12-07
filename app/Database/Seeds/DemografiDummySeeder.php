<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class DemografiDummySeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('id_ID');
        $kodeDesa = '3201010001';
        
        $agamaList = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];
        $pendidikanList = ['Tidak/Belum Sekolah', 'Tamat SD/Sederajat', 'SLTP/Sederajat', 'SLTA/Sederajat', 'Diploma I/II', 'Diploma IV/Strata I'];
        $pekerjaanList = ['Petani/Pekebun', 'Pedagang', 'Wiraswasta', 'Karyawan Swasta', 'Buruh Harian Lepas', 'Mengurus Rumah Tangga', 'Pelajar/Mahasiswa', 'Belum/Tidak Bekerja', 'PNS', 'Guru'];
        $statusKawinList = ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'];
        $golDarahList = ['A', 'B', 'AB', 'O'];
        $dusunList = ['Dusun I', 'Dusun II', 'Dusun III', 'Dusun IV'];
        
        echo "Creating dummy families and residents...\n";
        
        // Create 25 families
        for ($i = 1; $i <= 25; $i++) {
            $dusun = $dusunList[array_rand($dusunList)];
            $rt = str_pad(rand(1, 5), 3, '0', STR_PAD_LEFT);
            $rw = str_pad(rand(1, 3), 3, '0', STR_PAD_LEFT);
            
            // Generate KK number (16 digits)
            $noKk = '3201' . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
            $kepalaKeluarga = $faker->name('male');
            
            // Insert Keluarga
            $this->db->table('pop_keluarga')->insert([
                'kode_desa' => $kodeDesa,
                'no_kk' => $noKk,
                'kepala_keluarga' => $kepalaKeluarga,
                'alamat' => $faker->streetAddress,
                'rt' => $rt,
                'rw' => $rw,
                'dusun' => $dusun,
                'kode_pos' => '12345',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $keluargaId = $this->db->insertID();
            
            // Create family members (2-6 per family)
            $memberCount = rand(2, 6);
            
            for ($j = 0; $j < $memberCount; $j++) {
                if ($j == 0) {
                    // Kepala Keluarga
                    $nama = $kepalaKeluarga;
                    $jk = 'L';
                    $hubungan = 'Kepala Keluarga';
                    $tglLahir = $faker->dateTimeBetween('-60 years', '-30 years')->format('Y-m-d');
                    $statusKawin = 'Kawin';
                } elseif ($j == 1) {
                    // Istri
                    $nama = $faker->name('female');
                    $jk = 'P';
                    $hubungan = 'Istri';
                    $tglLahir = $faker->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d');
                    $statusKawin = 'Kawin';
                } else {
                    // Anak
                    $jk = rand(0, 1) ? 'L' : 'P';
                    $nama = $faker->name($jk == 'L' ? 'male' : 'female');
                    $hubungan = 'Anak';
                    $tglLahir = $faker->dateTimeBetween('-25 years', '-1 years')->format('Y-m-d');
                    $age = (new \DateTime($tglLahir))->diff(new \DateTime())->y;
                    $statusKawin = $age >= 17 ? $statusKawinList[array_rand(['Belum Kawin', 'Kawin'])] : 'Belum Kawin';
                }
                
                // Generate NIK (16 digits)
                $nik = '3201' . str_pad(rand(100000000000, 999999999999), 12, '0', STR_PAD_LEFT);
                
                $isMiskin = rand(0, 10) > 7 ? 1 : 0; // 30% chance
                
                $this->db->table('pop_penduduk')->insert([
                    'keluarga_id' => $keluargaId,
                    'nik' => $nik,
                    'nama_lengkap' => $nama,
                    'tempat_lahir' => $faker->city,
                    'tanggal_lahir' => $tglLahir,
                    'jenis_kelamin' => $jk,
                    'agama' => $agamaList[array_rand($agamaList)],
                    'pendidikan_terakhir' => $pendidikanList[array_rand($pendidikanList)],
                    'pekerjaan' => $pekerjaanList[array_rand($pekerjaanList)],
                    'status_perkawinan' => $statusKawin,
                    'status_hubungan' => $hubungan,
                    'golongan_darah' => $golDarahList[array_rand($golDarahList)],
                    'nama_ayah' => $faker->name('male'),
                    'nama_ibu' => $faker->name('female'),
                    'kewarganegaraan' => 'WNI',
                    'status_dasar' => 'HIDUP',
                    'is_miskin' => $isMiskin,
                    'is_disabilitas' => rand(0, 20) > 19 ? 1 : 0, // 5% chance
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                
                $pendudukId = $this->db->insertID();
                
                // Add some mutations for recent births
                if ($j >= 2 && rand(0, 5) > 4) {
                    $this->db->table('pop_mutasi')->insert([
                        'penduduk_id' => $pendudukId,
                        'jenis_mutasi' => 'KELAHIRAN',
                        'tanggal_peristiwa' => $tglLahir,
                        'keterangan' => 'Kelahiran tercatat',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        
        // Add some death and migration records
        $recentPenduduk = $this->db->table('pop_penduduk')
            ->where('status_hubungan !=', 'Kepala Keluarga')
            ->limit(5)
            ->get()
            ->getResultArray();
            
        foreach (array_slice($recentPenduduk, 0, 2) as $p) {
            // Record death
            $this->db->table('pop_penduduk')->update(['status_dasar' => 'MATI'], ['id' => $p['id']]);
            $this->db->table('pop_mutasi')->insert([
                'penduduk_id' => $p['id'],
                'jenis_mutasi' => 'KEMATIAN',
                'tanggal_peristiwa' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'keterangan' => 'Meninggal dunia',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        foreach (array_slice($recentPenduduk, 2, 2) as $p) {
            // Record migration
            $this->db->table('pop_penduduk')->update(['status_dasar' => 'PINDAH'], ['id' => $p['id']]);
            $this->db->table('pop_mutasi')->insert([
                'penduduk_id' => $p['id'],
                'jenis_mutasi' => 'PINDAH_KELUAR',
                'tanggal_peristiwa' => $faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
                'keterangan' => 'Pindah ke ' . $faker->city,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        echo "Demografi dummy data created successfully!\n";
        echo "- 25 Kartu Keluarga\n";
        echo "- ~100 Penduduk\n";
        echo "- Mutasi records\n";
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKesehatanTables extends Migration
{
    public function up()
    {
        // =============================================
        // TABEL: kes_posyandu (Master Data Posyandu)
        // =============================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_desa' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_posyandu' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'alamat_dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'rt' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'rw' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'ketua_posyandu' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
            ],
            'lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_desa');
        $this->forge->createTable('kes_posyandu', true);

        // =============================================
        // TABEL: kes_kader (Kader Posyandu)
        // =============================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'posyandu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'penduduk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK to pop_penduduk',
            ],
            'nama_kader' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jabatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Ketua/Sekretaris/Anggota',
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['AKTIF', 'TIDAK_AKTIF'],
                'default'    => 'AKTIF',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('posyandu_id');
        $this->forge->addKey('penduduk_id');
        $this->forge->createTable('kes_kader', true);

        // =============================================
        // TABEL: kes_pemeriksaan (Pemeriksaan Balita)
        // =============================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'posyandu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'penduduk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'FK to pop_penduduk (balita)',
            ],
            'tanggal_periksa' => [
                'type' => 'DATE',
            ],
            'usia_bulan' => [
                'type'       => 'INT',
                'constraint' => 3,
                'comment'    => 'Calculated from tgl_lahir at checkup',
            ],
            'berat_badan' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'comment'    => 'in kg',
            ],
            'tinggi_badan' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'comment'    => 'in cm',
            ],
            'lingkar_kepala' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'in cm',
            ],
            'lingkar_lengan' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'LILA in cm',
            ],
            'vitamin_a' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'imunisasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'JSON atau comma-separated list',
            ],
            'asi_eksklusif' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'status_gizi' => [
                'type'       => 'ENUM',
                'constraint' => ['BURUK', 'KURANG', 'BAIK', 'LEBIH', 'OBESITAS'],
                'default'    => 'BAIK',
            ],
            'z_score_bb_u' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => true,
                'comment'    => 'Weight-for-Age Z-Score',
            ],
            'z_score_tb_u' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => true,
                'comment'    => 'Height-for-Age Z-Score (for stunting)',
            ],
            'z_score_bb_tb' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'null'       => true,
                'comment'    => 'Weight-for-Height Z-Score',
            ],
            'indikasi_stunting' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'comment' => 'TRUE if z_score_tb_u < -2',
            ],
            'indikasi_gizi_buruk' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('posyandu_id');
        $this->forge->addKey('penduduk_id');
        $this->forge->addKey('tanggal_periksa');
        $this->forge->addKey('indikasi_stunting');
        $this->forge->createTable('kes_pemeriksaan', true);

        // =============================================
        // TABEL: kes_ibu_hamil (Tracking Ibu Hamil)
        // =============================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'posyandu_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'penduduk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'FK to pop_penduduk (ibu)',
            ],
            'tanggal_hpht' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Hari Pertama Haid Terakhir',
            ],
            'taksiran_persalinan' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'HPL - Hari Perkiraan Lahir',
            ],
            'usia_kandungan' => [
                'type'       => 'INT',
                'constraint' => 2,
                'null'       => true,
                'comment'    => 'dalam minggu',
            ],
            'kehamilan_ke' => [
                'type'       => 'INT',
                'constraint' => 2,
                'default'    => 1,
            ],
            'tinggi_badan_ibu' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'berat_badan_sebelum' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'BB sebelum hamil',
            ],
            'golongan_darah' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
            ],
            'resiko_tinggi' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'faktor_resiko' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Deskripsi faktor resiko',
            ],
            'pemeriksaan_k1' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'pemeriksaan_k2' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'pemeriksaan_k3' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'pemeriksaan_k4' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['HAMIL', 'MELAHIRKAN', 'KEGUGURAN', 'BATAL'],
                'default'    => 'HAMIL',
            ],
            'tanggal_persalinan' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('posyandu_id');
        $this->forge->addKey('penduduk_id');
        $this->forge->addKey('status');
        $this->forge->addKey('resiko_tinggi');
        $this->forge->createTable('kes_ibu_hamil', true);

        // =============================================
        // TABEL: kes_standar_who (Standar Pertumbuhan WHO)
        // =============================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'usia_bulan' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'indikator' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'BB_U, TB_U, BB_TB',
            ],
            'median' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'sd_min3' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'comment'    => '-3 SD (severely stunted/underweight)',
            ],
            'sd_min2' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'comment'    => '-2 SD (stunted/underweight)',
            ],
            'sd_min1' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'sd_plus1' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'sd_plus2' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
            'sd_plus3' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['jenis_kelamin', 'usia_bulan', 'indikator']);
        $this->forge->createTable('kes_standar_who', true);
    }

    public function down()
    {
        $this->forge->dropTable('kes_standar_who', true);
        $this->forge->dropTable('kes_ibu_hamil', true);
        $this->forge->dropTable('kes_pemeriksaan', true);
        $this->forge->dropTable('kes_kader', true);
        $this->forge->dropTable('kes_posyandu', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerencanaanTables extends Migration
{
    public function up()
    {
        // ========================================
        // 1. RPJM DESA (6 Tahun)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'tahun_awal' => [
                'type' => 'YEAR',
            ],
            'tahun_akhir' => [
                'type' => 'YEAR',
            ],
            'visi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'misi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'tujuan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sasaran' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Aktif', 'Selesai'],
                'default' => 'Draft',
            ],
            'nomor_perdes' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tanggal_perdes' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
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
        $this->forge->addKey('kode_desa');
        $this->forge->addKey('status');
        $this->forge->createTable('rpjmdesa');

        // ========================================
        // 2. BIDANG (Kategori Pembangunan)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_bidang' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'nama_bidang' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_bidang');
        $this->forge->createTable('ref_bidang');

        // ========================================
        // 3. RKP DESA (Tahunan)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'rpjmdesa_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'kode_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'tahun' => [
                'type' => 'YEAR',
            ],
            'tema' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'prioritas' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Musdes', 'Ditetapkan', 'Berjalan', 'Selesai'],
                'default' => 'Draft',
            ],
            'nomor_perdes' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tanggal_perdes' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'total_pagu' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
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
        $this->forge->addKey('rpjmdesa_id');
        $this->forge->addKey(['kode_desa', 'tahun']);
        $this->forge->addKey('status');
        $this->forge->createTable('rkpdesa');

        // ========================================
        // 4. KEGIATAN (Detail Program/Kegiatan)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'rkpdesa_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'kode_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'bidang_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
            ],
            'kode_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_kegiatan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'lokasi' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'volume' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'satuan' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'sasaran_manfaat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'waktu_pelaksanaan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'pagu_anggaran' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'sumber_dana' => [
                'type' => 'ENUM',
                'constraint' => ['DDS', 'ADD', 'PAD', 'Bantuan Keuangan', 'Swadaya', 'Lainnya'],
                'default' => 'DDS',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Usulan', 'Prioritas', 'Disetujui', 'Ditolak', 'Berjalan', 'Selesai'],
                'default' => 'Usulan',
            ],
            'prioritas' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ref_rekening_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
            ],
            'apbdes_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
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
        $this->forge->addKey('rkpdesa_id');
        $this->forge->addKey('kode_desa');
        $this->forge->addKey('bidang_id');
        $this->forge->addKey('status');
        $this->forge->createTable('kegiatan');

        // ========================================
        // 5. Seed ref_bidang dengan data awal
        // ========================================
        $bidangData = [
            ['kode_bidang' => '01', 'nama_bidang' => 'Penyelenggaraan Pemerintahan Desa', 'urutan' => 1],
            ['kode_bidang' => '02', 'nama_bidang' => 'Pelaksanaan Pembangunan Desa', 'urutan' => 2],
            ['kode_bidang' => '03', 'nama_bidang' => 'Pembinaan Kemasyarakatan Desa', 'urutan' => 3],
            ['kode_bidang' => '04', 'nama_bidang' => 'Pemberdayaan Masyarakat Desa', 'urutan' => 4],
            ['kode_bidang' => '05', 'nama_bidang' => 'Penanggulangan Bencana, Darurat dan Mendesak', 'urutan' => 5],
        ];

        $this->db->table('ref_bidang')->insertBatch($bidangData);
    }

    public function down()
    {
        $this->forge->dropTable('kegiatan', true);
        $this->forge->dropTable('rkpdesa', true);
        $this->forge->dropTable('ref_bidang', true);
        $this->forge->dropTable('rpjmdesa', true);
    }
}

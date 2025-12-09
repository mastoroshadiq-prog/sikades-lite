<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration for e-Pembangunan Infrastructure Monitoring Module
 * Creates proyek_fisik and proyek_log tables for tracking
 * physical project progress against financial realization
 */
class CreatePembangunanTables extends Migration
{
    public function up()
    {
        // ===================================
        // Proyek Fisik (Physical Projects)
        // ===================================
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
            'apbdes_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK to apbdes for budget linkage',
            ],
            'kode_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Reference to activity code',
            ],
            'nama_proyek' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'lokasi_detail' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'volume_target' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'M/M2/M3/Unit/Paket/Buah',
            ],
            'anggaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '18,2',
                'default'    => 0,
                'comment'    => 'Total budget allocation',
            ],
            'tgl_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tgl_selesai_target' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tgl_selesai_aktual' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'pelaksana_kegiatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'TPK Name',
            ],
            'kontraktor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
            'foto_0' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Photo at 0% (before)',
            ],
            'foto_50' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Photo at 50%',
            ],
            'foto_100' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Photo at 100% (complete)',
            ],
            'persentase_fisik' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
                'comment'    => 'Latest physical progress 0-100',
            ],
            'persentase_keuangan' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
                'comment'    => 'Financial realization percentage',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['RENCANA', 'PROSES', 'SELESAI', 'MANGKRAK'],
                'default'    => 'RENCANA',
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
        $this->forge->addKey('kode_desa');
        $this->forge->addKey('apbdes_id');
        $this->forge->addKey('status');
        $this->forge->createTable('proyek_fisik', true);

        // ===================================
        // Proyek Log (Progress History)
        // ===================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'proyek_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal_laporan' => [
                'type' => 'DATE',
            ],
            'persentase_fisik' => [
                'type'       => 'INT',
                'constraint' => 3,
                'comment'    => '0-100',
            ],
            'volume_terealisasi' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
            ],
            'kendala' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'solusi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'pelapor' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Reporter name (TPK member)',
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
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('proyek_id');
        $this->forge->addKey('tanggal_laporan');
        $this->forge->createTable('proyek_log', true);

        // ===================================
        // TPK Members (Tim Pelaksana Kegiatan)
        // ===================================
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
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jabatan' => [
                'type'       => 'ENUM',
                'constraint' => ['KETUA', 'SEKRETARIS', 'ANGGOTA'],
                'default'    => 'ANGGOTA',
            ],
            'no_telp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'penduduk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addKey('kode_desa');
        $this->forge->createTable('tpk_anggota', true);
    }

    public function down()
    {
        $this->forge->dropTable('proyek_log', true);
        $this->forge->dropTable('proyek_fisik', true);
        $this->forge->dropTable('tpk_anggota', true);
    }
}

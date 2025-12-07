<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAsetTables extends Migration
{
    public function up()
    {
        // ===========================================
        // Tabel: aset_kategori
        // Golongan/Kategori Aset sesuai Permendagri
        // ===========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_golongan' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'comment'    => 'Kode golongan aset (01, 02, dst)',
            ],
            'nama_golongan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nama golongan (Tanah, Peralatan, dll)',
            ],
            'uraian' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'masa_manfaat' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'Masa manfaat dalam tahun untuk penyusutan',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addUniqueKey('kode_golongan');
        $this->forge->createTable('aset_kategori');

        // ===========================================
        // Tabel: aset_inventaris
        // Data inventaris aset desa
        // ===========================================
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
            'kode_register' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Nomor register aset (auto-generated)',
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'kategori_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'merk_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'ukuran' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Ukuran/luas (m2 untuk tanah, unit untuk barang)',
            ],
            'bahan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tahun_perolehan' => [
                'type'       => 'YEAR',
                'constraint' => 4,
            ],
            'harga_perolehan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],
            'nilai_sisa' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
                'comment'    => 'Nilai setelah penyusutan',
            ],
            'kondisi' => [
                'type'       => 'ENUM',
                'constraint' => ['Baik', 'Rusak Ringan', 'Rusak Berat'],
                'default'    => 'Baik',
            ],
            'status_penggunaan' => [
                'type'       => 'ENUM',
                'constraint' => ['Digunakan', 'Tidak Digunakan', 'Dipinjamkan', 'Dihapuskan'],
                'default'    => 'Digunakan',
            ],
            'lokasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Lokasi penempatan aset',
            ],
            'pengguna' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nama pengguna/penanggung jawab',
            ],
            'sumber_dana' => [
                'type'       => 'ENUM',
                'constraint' => ['APBDes', 'Hibah', 'Bantuan Pemerintah', 'Swadaya', 'Lainnya'],
                'default'    => 'APBDes',
            ],
            // Integration with BKU
            'bku_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Link ke transaksi BKU (Belanja Modal)',
            ],
            // GIS Coordinates
            'lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
                'comment'    => 'Latitude untuk WebGIS',
            ],
            'lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
                'comment'    => 'Longitude untuk WebGIS',
            ],
            // Photo
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path ke file foto aset',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Audit trail
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
        $this->forge->addUniqueKey('kode_register');
        $this->forge->addKey('kode_desa');
        $this->forge->addKey('kategori_id');
        $this->forge->addKey('bku_id');
        $this->forge->addKey('tahun_perolehan');
        $this->forge->addKey('kondisi');
        $this->forge->addForeignKey('kategori_id', 'aset_kategori', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('aset_inventaris');
    }

    public function down()
    {
        $this->forge->dropTable('aset_inventaris', true);
        $this->forge->dropTable('aset_kategori', true);
    }
}

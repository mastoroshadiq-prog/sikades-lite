<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDemografiTables extends Migration
{
    public function up()
    {
        // ========================================
        // Table: pop_keluarga (Kartu Keluarga)
        // ========================================
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
            'no_kk' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'comment'    => 'Nomor Kartu Keluarga 16 digit',
            ],
            'kepala_keluarga' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'Nama Kepala Keluarga',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
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
            'dusun' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kode_pos' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
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
        $this->forge->addUniqueKey('no_kk');
        $this->forge->addKey('kode_desa');
        $this->forge->addKey(['rt', 'rw', 'dusun']);
        $this->forge->createTable('pop_keluarga', true);

        // ========================================
        // Table: pop_penduduk (Data Penduduk)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'keluarga_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'comment'    => 'Nomor Induk Kependudukan 16 digit',
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'tempat_lahir' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'default'    => 'L',
                'comment'    => 'L=Laki-laki, P=Perempuan',
            ],
            'agama' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'pendidikan_terakhir' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'pekerjaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status_perkawinan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'comment'    => 'Belum Kawin/Kawin/Cerai Hidup/Cerai Mati',
            ],
            'status_hubungan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Kepala Keluarga/Istri/Anak/Famili Lain/Lainnya',
            ],
            'golongan_darah' => [
                'type'       => 'CHAR',
                'constraint' => 3,
                'null'       => true,
                'comment'    => 'A/B/AB/O',
            ],
            'nama_ayah' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_ibu' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'kewarganegaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'WNI',
            ],
            'no_paspor' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'no_kitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'status_dasar' => [
                'type'       => 'ENUM',
                'constraint' => ['HIDUP', 'MATI', 'PINDAH', 'HILANG'],
                'default'    => 'HIDUP',
            ],
            'is_miskin' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'comment' => 'Flag DTKS/Penerima Bantuan',
            ],
            'is_disabilitas' => [
                'type'    => 'BOOLEAN',
                'default' => false,
            ],
            'jenis_disabilitas' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addUniqueKey('nik');
        $this->forge->addKey('keluarga_id');
        $this->forge->addKey('nama_lengkap');
        $this->forge->addKey('jenis_kelamin');
        $this->forge->addKey('status_dasar');
        $this->forge->addKey('is_miskin');
        $this->forge->addForeignKey('keluarga_id', 'pop_keluarga', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('pop_penduduk', true);

        // ========================================
        // Table: pop_mutasi (Peristiwa Kependudukan)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'penduduk_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jenis_mutasi' => [
                'type'       => 'ENUM',
                'constraint' => ['KELAHIRAN', 'KEMATIAN', 'PINDAH_MASUK', 'PINDAH_KELUAR', 'PERUBAHAN_DATA'],
            ],
            'tanggal_peristiwa' => [
                'type' => 'DATE',
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'dokumen_pendukung' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path file akta/surat keterangan',
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
        $this->forge->addKey('penduduk_id');
        $this->forge->addKey('jenis_mutasi');
        $this->forge->addKey('tanggal_peristiwa');
        $this->forge->addForeignKey('penduduk_id', 'pop_penduduk', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('pop_mutasi', true);

        // ========================================
        // Table: ref_pendidikan (Reference Data)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_pendidikan', true);

        // ========================================
        // Table: ref_pekerjaan (Reference Data)
        // ========================================
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ref_pekerjaan', true);
    }

    public function down()
    {
        $this->forge->dropTable('pop_mutasi', true);
        $this->forge->dropTable('pop_penduduk', true);
        $this->forge->dropTable('pop_keluarga', true);
        $this->forge->dropTable('ref_pendidikan', true);
        $this->forge->dropTable('ref_pekerjaan', true);
    }
}

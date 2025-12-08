<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBumdesTables extends Migration
{
    public function up()
    {
        // Tabel Unit Usaha BUMDes
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_unit' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'jenis_usaha' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'penanggung_jawab' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'modal_awal' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'tanggal_mulai' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['AKTIF', 'TIDAK_AKTIF'],
                'default' => 'AKTIF',
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'no_telp' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
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
        $this->forge->createTable('bumdes_unit');

        // Chart of Account (COA) BUMDes - SAK EMKM
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_akun' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'nama_akun' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'tipe' => [
                'type' => 'ENUM',
                'constraint' => ['ASET', 'KEWAJIBAN', 'EKUITAS', 'PENDAPATAN', 'BEBAN'],
            ],
            'saldo_normal' => [
                'type' => 'ENUM',
                'constraint' => ['DEBET', 'KREDIT'],
            ],
            'parent_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'is_header' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'urutan' => [
                'type' => 'INT',
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_akun');
        $this->forge->createTable('bumdes_akun');

        // Jurnal Header
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'no_bukti' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'bku_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'comment' => 'Link to village BKU for capital investment',
            ],
            'created_by' => [
                'type' => 'INT',
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
        $this->forge->addKey('unit_id');
        $this->forge->addKey('tanggal');
        $this->forge->createTable('bumdes_jurnal');

        // Jurnal Detail (Double Entry)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'jurnal_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'akun_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'debet' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'kredit' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'keterangan' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('jurnal_id');
        $this->forge->addKey('akun_id');
        $this->forge->createTable('bumdes_jurnal_detail');
    }

    public function down()
    {
        $this->forge->dropTable('bumdes_jurnal_detail', true);
        $this->forge->dropTable('bumdes_jurnal', true);
        $this->forge->dropTable('bumdes_akun', true);
        $this->forge->dropTable('bumdes_unit', true);
    }
}

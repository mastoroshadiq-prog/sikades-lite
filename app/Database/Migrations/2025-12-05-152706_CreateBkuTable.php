<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBkuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'nomor_bukti' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'uraian' => [
                'type' => 'TEXT',
            ],
            'jenis_transaksi' => [
                'type' => 'ENUM',
                'constraint' => ['Pendapatan', 'Belanja', 'Mutasi'],
            ],
            'debet' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'comment' => 'Uang Masuk',
            ],
            'kredit' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'comment' => 'Uang Keluar',
            ],
            'saldo_kumulatif' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => true,
                'comment' => 'Optional, calculated on view',
            ],
            'spp_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Nullable, if from SPP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_desa');
        $this->forge->addKey('tanggal');
        $this->forge->addKey('spp_id');
        $this->forge->addForeignKey('spp_id', 'spp', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('bku');
    }

    public function down()
    {
        $this->forge->dropTable('bku');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApbdesTable extends Migration
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
            'tahun' => [
                ' type' => 'YEAR',
                'constraint' => 4,
            ],
            'ref_rekening_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'uraian' => [
                'type' => 'TEXT',
            ],
            'anggaran' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'sumber_dana' => [
                'type' => 'ENUM',
                'constraint' => ['DDS', 'ADD', 'PAD', 'Bankeu'],
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_desa');
        $this->forge->addKey('ref_rekening_id');
        $this->forge->addForeignKey('ref_rekening_id', 'ref_rekening', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('apbdes');
    }

    public function down()
    {
        $this->forge->dropTable('apbdes');
    }
}

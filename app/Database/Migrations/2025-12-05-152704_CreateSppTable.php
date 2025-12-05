<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSppTable extends Migration
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
            'no_spp' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'kode_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'keterangan' => [
                'type' => 'TEXT',
            ],
            'jumlah_total' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Draft', 'Verified', 'Approved'],
                'default' => 'Draft',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('no_spp');
        $this->forge->addKey('kode_desa');
        $this->forge->createTable('spp');
    }

    public function down()
    {
        $this->forge->dropTable('spp');
    }
}

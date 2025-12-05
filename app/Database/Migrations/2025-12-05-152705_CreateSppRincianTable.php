<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSppRincianTable extends Migration
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
            'spp_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'apbdes_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'nilai_pencairan' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('spp_id');
        $this->forge->addKey('apbdes_id');
        $this->forge->addForeignKey('spp_id', 'spp', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('apbdes_id', 'apbdes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('spp_rincian');
    }

    public function down()
    {
        $this->forge->dropTable('spp_rincian');
    }
}

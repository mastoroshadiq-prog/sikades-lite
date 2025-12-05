<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePajakTable extends Migration
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
            'bku_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'jenis_pajak' => [
                'type' => 'ENUM',
                'constraint' => ['PPN', 'PPh'],
            ],
            'nilai' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
            ],
            'kode_billing' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'status_setor' => [
                'type' => 'ENUM',
                'constraint' => ['Belum', 'Sudah'],
                'default' => 'Belum',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('bku_id');
        $this->forge->addForeignKey('bku_id', 'bku', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pajak');
    }

    public function down()
    {
        $this->forge->dropTable('pajak');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRefRekeningTable extends Migration
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
            'kode_akun' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'nama_akun' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'level' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'comment' => '1=Akun, 2=Kelompok, 3=Jenis, 4=Objek',
            ],
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_akun');
        $this->forge->addKey('parent_id');
        $this->forge->createTable('ref_rekening');
    }

    public function down()
    {
        $this->forge->dropTable('ref_rekening');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataUmumDesaTable extends Migration
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
                'unique' => true,
            ],
            'nama_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_kepala_desa' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'nama_bendahara' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'npwp' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'tahun_anggaran' => [
                'type' => 'YEAR',
                'constraint' => 4,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kode_desa');
        $this->forge->createTable('data_umum_desa');
    }

    public function down()
    {
        $this->forge->dropTable('data_umum_desa');
    }
}

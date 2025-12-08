<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGisWilayahTable extends Migration
{
    public function up()
    {
        // ========================================
        // Table: gis_wilayah (Batas Wilayah Desa)
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
            'nama_wilayah' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nama dusun/RT/RW',
            ],
            'tipe' => [
                'type'       => 'ENUM',
                'constraint' => ['DESA', 'DUSUN', 'RW', 'RT'],
                'default'    => 'DUSUN',
            ],
            'parent_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'FK ke wilayah parent (DUSUN -> DESA, RT -> RW)',
            ],
            'center_lat' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,8',
                'null'       => true,
                'comment'    => 'Latitude titik pusat wilayah',
            ],
            'center_lng' => [
                'type'       => 'DECIMAL',
                'constraint' => '11,8',
                'null'       => true,
                'comment'    => 'Longitude titik pusat wilayah',
            ],
            'geojson' => [
                'type'    => 'LONGTEXT',
                'null'    => true,
                'comment' => 'Polygon boundary dalam format GeoJSON',
            ],
            'luas_area' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'comment'    => 'Luas area dalam meter persegi',
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'comment'    => 'Warna hex untuk display (#RRGGBB)',
            ],
            'keterangan' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('tipe');
        $this->forge->addKey(['nama_wilayah', 'tipe']);
        $this->forge->createTable('gis_wilayah', true);
    }

    public function down()
    {
        $this->forge->dropTable('gis_wilayah', true);
    }
}

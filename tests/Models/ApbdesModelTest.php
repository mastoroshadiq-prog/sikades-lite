<?php

namespace Tests\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ApbdesModel;

/**
 * APBDes Model Tests
 */
class ApbdesModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $namespace = '\App';
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ApbdesModel();
    }

    /**
     * Test insert anggaran
     */
    public function testInsertAnggaran()
    {
        $data = [
            'kode_desa' => '3201010001',
            'tahun' => 2025,
            'ref_rekening_id' => 1,
            'uraian' => 'Test Anggaran',
            'anggaran' => 1000000,
            'sumber_dana' => 'DDS',
        ];

        $insertId = $this->model->insert($data);
        
        $this->assertIsNumeric($insertId);
        $this->assertGreaterThan(0, $insertId);
    }

    /**
     * Test find anggaran by id
     */
    public function testFindAnggaran()
    {
        // Insert first
        $data = [
            'kode_desa' => '3201010001',
            'tahun' => 2025,
            'ref_rekening_id' => 1,
            'uraian' => 'Test Find',
            'anggaran' => 500000,
            'sumber_dana' => 'ADD',
        ];

        $insertId = $this->model->insert($data);
        
        // Find
        $result = $this->model->find($insertId);
        
        $this->assertIsArray($result);
        $this->assertEquals('Test Find', $result['uraian']);
        $this->assertEquals(500000, $result['anggaran']);
    }

    /**
     * Test update anggaran
     */
    public function testUpdateAnggaran()
    {
        // Insert first
        $data = [
            'kode_desa' => '3201010001',
            'tahun' => 2025,
            'ref_rekening_id' => 1,
            'uraian' => 'Before Update',
            'anggaran' => 200000,
            'sumber_dana' => 'PAD',
        ];

        $insertId = $this->model->insert($data);
        
        // Update
        $this->model->update($insertId, [
            'uraian' => 'After Update',
            'anggaran' => 300000,
        ]);
        
        // Verify
        $result = $this->model->find($insertId);
        
        $this->assertEquals('After Update', $result['uraian']);
        $this->assertEquals(300000, $result['anggaran']);
    }

    /**
     * Test delete anggaran
     */
    public function testDeleteAnggaran()
    {
        // Insert first
        $data = [
            'kode_desa' => '3201010001',
            'tahun' => 2025,
            'ref_rekening_id' => 1,
            'uraian' => 'To Delete',
            'anggaran' => 100000,
            'sumber_dana' => 'Bankeu',
        ];

        $insertId = $this->model->insert($data);
        
        // Delete
        $this->model->delete($insertId);
        
        // Verify
        $result = $this->model->find($insertId);
        
        $this->assertNull($result);
    }

    /**
     * Test get anggaran with rekening
     */
    public function testGetAnggaranWithRekening()
    {
        $result = $this->model->getAnggaranWithRekening('3201010001', 2025);
        
        $this->assertIsArray($result);
    }
}

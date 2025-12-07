<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * APBDes Controller Tests
 */
class ApbdesTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $namespace = '\App';
    protected $sessionData = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup session with admin user
        $this->sessionData = [
            'user_id' => 1,
            'username' => 'admin',
            'role' => 'Administrator',
            'kode_desa' => '3201010001',
            'isLoggedIn' => true,
        ];
        
        session()->set($this->sessionData);
    }

    /**
     * Test APBDes page loads with authentication
     */
    public function testApbdesPageLoadsWithAuth()
    {
        $result = $this->withSession($this->sessionData)->get('/apbdes');
        
        $result->assertOK();
        $result->assertSee('APBDes');
    }

    /**
     * Test APBDes create page loads
     */
    public function testApbdesCreatePageLoads()
    {
        $result = $this->withSession($this->sessionData)->get('/apbdes/create');
        
        $result->assertOK();
        $result->assertSee('Tambah Anggaran');
    }

    /**
     * Test APBDes report page loads
     */
    public function testApbdesReportPageLoads()
    {
        $result = $this->withSession($this->sessionData)->get('/apbdes/report');
        
        $result->assertOK();
        $result->assertSee('Laporan APBDes');
    }
}

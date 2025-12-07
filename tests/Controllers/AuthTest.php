<?php

namespace Tests\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Auth Controller Tests
 */
class AuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $refresh = true;
    protected $namespace = '\App';

    /**
     * Test login page loads
     */
    public function testLoginPageLoads()
    {
        $result = $this->get('/login');
        
        $result->assertOK();
        $result->assertSee('Login');
    }

    /**
     * Test login with invalid credentials
     */
    public function testLoginWithInvalidCredentials()
    {
        $result = $this->post('/login', [
            'username' => 'invaliduser',
            'password' => 'invalidpass',
        ]);
        
        $result->assertRedirect();
    }

    /**
     * Test dashboard requires authentication
     */
    public function testDashboardRequiresAuth()
    {
        $result = $this->get('/dashboard');
        
        // Should redirect to login
        $result->assertRedirectTo('/login');
    }

    /**
     * Test logout
     */
    public function testLogout()
    {
        $result = $this->get('/logout');
        
        $result->assertRedirect();
    }
}

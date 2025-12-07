<?php

namespace Tests\Libraries;

use CodeIgniter\Test\CIUnitTestCase;
use App\Libraries\PdfExport;

/**
 * PDF Export Library Tests
 */
class PdfExportTest extends CIUnitTestCase
{
    protected $pdfExport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pdfExport = new PdfExport();
    }

    /**
     * Test terbilang function
     */
    public function testTerbilang()
    {
        // Use reflection to access private method
        $reflection = new \ReflectionClass($this->pdfExport);
        $method = $reflection->getMethod('terbilang');
        $method->setAccessible(true);

        // Test basic numbers
        $this->assertEquals('satu', trim($method->invoke($this->pdfExport, 1)));
        $this->assertEquals('sepuluh', trim($method->invoke($this->pdfExport, 10)));
        $this->assertEquals('sebelas', trim($method->invoke($this->pdfExport, 11)));
        $this->assertEquals('seratus', trim($method->invoke($this->pdfExport, 100)));
        $this->assertEquals('seribu', trim($method->invoke($this->pdfExport, 1000)));
    }

    /**
     * Test terbilang with larger numbers
     */
    public function testTerbilangLargeNumbers()
    {
        $reflection = new \ReflectionClass($this->pdfExport);
        $method = $reflection->getMethod('terbilang');
        $method->setAccessible(true);

        // Test larger numbers
        $result = $method->invoke($this->pdfExport, 1500000);
        $this->assertStringContainsString('juta', $result);
        
        $result = $method->invoke($this->pdfExport, 25000);
        $this->assertStringContainsString('ribu', $result);
    }

    /**
     * Test PDF export class exists
     */
    public function testPdfExportClassExists()
    {
        $this->assertInstanceOf(PdfExport::class, $this->pdfExport);
    }
}

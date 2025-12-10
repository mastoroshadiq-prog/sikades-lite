<?php
// Full page test with CSRF support

$baseUrl = 'http://localhost:8080';
$cookieFile = tempnam(sys_get_temp_dir(), 'test_');

// Login first
echo "=== SIKADES LITE - Full Page Test ===\n\n";
echo "Logging in...\n";

$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$response = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
preg_match('/name="csrf_test_name"\s+value="([^"]+)"/', $response, $m);
$csrfToken = $m[1] ?? '';

$ch = curl_init($baseUrl . '/login');  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "username=admin&password=admin123&csrf_test_name=$csrfToken");
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);

echo "Login OK!\n\n";

// Pages to test
$pages = [
    '/dashboard' => 'Dashboard',
    '/apbdes' => 'APBDes',
    '/spp' => 'SPP',
    '/bku' => 'BKU',
    '/perencanaan/rpjm' => 'RPJM',
    '/perencanaan/rkp' => 'RKP',
    '/aset' => 'Aset Desa',
    '/demografi' => 'Demografi',
    '/pembangunan/proyek' => 'Proyek Pembangunan',
    '/report' => 'Laporan',
];

$passed = 0;
$failed = 0;
$errors = [];

foreach ($pages as $path => $name) {
    echo "Testing $name ($path)... ";
    
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($code != 200) {
        echo "FAILED (HTTP $code)\n";
        $failed++;
        $errors[] = "$name: HTTP $code";
        continue;
    }
    
    // Check for SQL errors
    if (preg_match('/function\s+(\w+)\([^)]*\)\s+does not exist/i', $response, $m)) {
        echo "FAILED - Missing SQL function: " . $m[1] . "()\n";
        $failed++;
        $errors[] = "$name: Missing SQL function " . $m[1] . "()";
        continue;
    }
    
    // Check for PHP errors
    if (strpos($response, 'Fatal error') !== false || strpos($response, 'Parse error') !== false) {
        echo "FAILED - PHP Error\n";
        $failed++;
        $errors[] = "$name: PHP Error";
        continue;
    }
    
    // Check for database errors
    if (strpos($response, 'Unable to connect') !== false) {
        echo "FAILED - Database connection error\n";
        $failed++;
        $errors[] = "$name: Database Error";
        continue;
    }
    
    echo "OK\n";
    $passed++;
}

@unlink($cookieFile);

echo "\n=== SUMMARY ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $e) {
        echo "  - $e\n";
    }
}

echo "\n";
if ($failed == 0) {
    echo "ALL TESTS PASSED!\n";
} else {
    echo "SOME TESTS FAILED - Fix the errors above\n";
}

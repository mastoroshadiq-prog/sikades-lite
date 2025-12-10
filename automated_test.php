<?php
/**
 * Automated Test Script for Sikades Lite
 * Tests database connection, login, and page accessibility
 */

echo "==========================================================\n";
echo "  SIKADES LITE - Automated Test Suite\n";
echo "==========================================================\n\n";

// Configuration
$baseUrl = 'http://localhost:8080';
$username = 'admin';
$password = 'admin123';

$tests = [
    'passed' => 0,
    'failed' => 0,
    'errors' => []
];

// Test 1: Check if server is running
echo "[TEST 1] Checking if server is running...\n";
$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 400) {
    echo "  ✓ PASSED - Server is running (HTTP $httpCode)\n\n";
    $tests['passed']++;
} else {
    echo "  ✗ FAILED - Server not reachable (HTTP $httpCode)\n";
    echo "  Make sure to run: php spark serve --port=8080\n\n";
    $tests['failed']++;
    $tests['errors'][] = "Server not running";
}

// Test 2: Check login page
echo "[TEST 2] Checking login page...\n";
$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 && strpos($response, 'login') !== false) {
    echo "  ✓ PASSED - Login page accessible\n\n";
    $tests['passed']++;
} else {
    echo "  ✗ FAILED - Login page issue (HTTP $httpCode)\n\n";
    $tests['failed']++;
    $tests['errors'][] = "Login page not accessible";
}

// Test 3: Test login with credentials
echo "[TEST 3] Testing login with admin credentials...\n";
$cookieFile = tempnam(sys_get_temp_dir(), 'sikades_cookie_');

// First get CSRF token if any
$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$response = curl_exec($ch);
curl_close($ch);

// Now try to login
$ch = curl_init($baseUrl . '/login/attempt');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'username' => $username,
    'password' => $password
]));
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

if (strpos($finalUrl, 'dashboard') !== false || strpos($response, 'Dashboard') !== false) {
    echo "  ✓ PASSED - Login successful! Redirected to dashboard\n\n";
    $tests['passed']++;
    $loginSuccess = true;
} elseif (strpos($response, 'salah') !== false || strpos($response, 'error') !== false) {
    echo "  ✗ FAILED - Login failed: Username or password incorrect\n";
    echo "  Final URL: $finalUrl\n\n";
    $tests['failed']++;
    $tests['errors'][] = "Login failed - credentials rejected";
    $loginSuccess = false;
} else {
    echo "  ? UNKNOWN - Could not determine login status\n";
    echo "  Final URL: $finalUrl\n";
    echo "  HTTP Code: $httpCode\n\n";
    $tests['failed']++;
    $loginSuccess = false;
}

// Test 4: Check dashboard (if login success)
if ($loginSuccess) {
    echo "[TEST 4] Checking dashboard page...\n";
    $ch = curl_init($baseUrl . '/dashboard');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 200) {
        // Check for common errors
        if (strpos($response, 'YEAR(') !== false || strpos($response, 'function year') !== false) {
            echo "  ✗ FAILED - SQL Error: YEAR() function not compatible\n\n";
            $tests['failed']++;
            $tests['errors'][] = "Dashboard: YEAR() SQL incompatibility";
        } elseif (strpos($response, 'Fatal error') !== false || strpos($response, 'Exception') !== false) {
            echo "  ✗ FAILED - PHP Error on dashboard\n";
            // Extract error message
            if (preg_match('/Fatal error.*?in.*?on line \d+/s', $response, $matches)) {
                echo "  Error: " . substr($matches[0], 0, 200) . "\n\n";
            }
            $tests['failed']++;
            $tests['errors'][] = "Dashboard: PHP Error";
        } else {
            echo "  ✓ PASSED - Dashboard loaded successfully\n\n";
            $tests['passed']++;
        }
    } else {
        echo "  ✗ FAILED - Dashboard not accessible (HTTP $httpCode)\n\n";
        $tests['failed']++;
    }

    // Test 5-10: Check other pages
    $pagesToTest = [
        '/penatausahaan/apbdes' => 'APBDes',
        '/penatausahaan/spp' => 'SPP',
        '/penatausahaan/bku' => 'BKU',
        '/perencanaan/rpjm' => 'RPJM',
        '/perencanaan/rkp' => 'RKP',
        '/aset' => 'Aset',
    ];

    $testNum = 5;
    foreach ($pagesToTest as $path => $name) {
        echo "[TEST $testNum] Checking $name page ($path)...\n";
        
        $ch = curl_init($baseUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            if (strpos($response, 'YEAR(') !== false || strpos($response, 'does not exist') !== false) {
                echo "  ✗ FAILED - SQL Error: YEAR() function not compatible\n\n";
                $tests['failed']++;
                $tests['errors'][] = "$name: YEAR() SQL incompatibility";
            } elseif (strpos($response, 'Fatal error') !== false) {
                echo "  ✗ FAILED - PHP Error\n\n";
                $tests['failed']++;
                $tests['errors'][] = "$name: PHP Error";
            } else {
                echo "  ✓ PASSED - $name loaded successfully\n\n";
                $tests['passed']++;
            }
        } else {
            echo "  ✗ FAILED - Page not accessible (HTTP $httpCode)\n\n";
            $tests['failed']++;
            $tests['errors'][] = "$name: HTTP $httpCode";
        }
        
        $testNum++;
    }
}

// Cleanup
@unlink($cookieFile);

// Summary
echo "==========================================================\n";
echo "  TEST SUMMARY\n";
echo "==========================================================\n";
echo "  Passed: " . $tests['passed'] . "\n";
echo "  Failed: " . $tests['failed'] . "\n";

if (!empty($tests['errors'])) {
    echo "\n  Errors:\n";
    foreach ($tests['errors'] as $error) {
        echo "  - $error\n";
    }
}

echo "\n==========================================================\n";

if ($tests['failed'] == 0) {
    echo "  ✓ ALL TESTS PASSED!\n";
    exit(0);
} else {
    echo "  ✗ SOME TESTS FAILED\n";
    exit(1);
}

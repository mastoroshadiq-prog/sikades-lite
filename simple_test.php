<?php
// Simple test with CSRF token support

$baseUrl = 'http://localhost:8080';
$cookieFile = tempnam(sys_get_temp_dir(), 'test_');

echo "1. Testing server...\n";
$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
echo "   Server: HTTP $code\n\n";

echo "2. Getting login page and CSRF token...\n";
$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$response = curl_exec($ch);
curl_close($ch);

// Extract CSRF token
$csrfToken = '';
$csrfName = 'csrf_test_name';
if (preg_match('/name="csrf_test_name"\s+value="([^"]+)"/', $response, $m)) {
    $csrfToken = $m[1];
    echo "   CSRF Token found: " . substr($csrfToken, 0, 20) . "...\n\n";
} elseif (preg_match('/name="([^"]*csrf[^"]*)"\s+value="([^"]+)"/i', $response, $m)) {
    $csrfName = $m[1];
    $csrfToken = $m[2];
    echo "   CSRF Token found ($csrfName): " . substr($csrfToken, 0, 20) . "...\n\n";
} else {
    echo "   No CSRF token found (CSRF might be disabled)\n\n";
}

echo "3. Attempting login...\n";
$postData = "username=admin&password=admin123";
if ($csrfToken) {
    $postData .= "&$csrfName=$csrfToken";
}

$ch = curl_init($baseUrl . '/login');  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   Final URL: $finalUrl\n";
echo "   HTTP Code: $code\n";

if (strpos($finalUrl, 'dashboard') !== false) {
    echo "   LOGIN SUCCESS!\n\n";
    $loggedIn = true;
} else {
    echo "   LOGIN RESULT: ";
    if (strpos($response, 'salah') !== false) {
        echo "Username/password salah\n\n";
    } elseif (strpos($response, 'Selamat') !== false || strpos($response, 'Dashboard') !== false) {
        echo "SUCCESS (on dashboard)\n\n";
        $loggedIn = true;
    } else {
        echo "Unknown status\n\n";
    }
    $loggedIn = false;
}

echo "4. Checking dashboard...\n";
$ch = curl_init($baseUrl . '/dashboard');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "   HTTP Code: $code\n";
echo "   Final URL: $finalUrl\n";

// Check for errors in response body
if (strpos($response, 'does not exist') !== false) {
    echo "   ERROR: SQL function not found\n";
    preg_match('/function ([a-z_]+)\([^)]*\) does not exist/i', $response, $m);
    if ($m) echo "   Missing function: " . $m[1] . "()\n";
    
    // Show more context
    if (preg_match('/Query failed:([^<]+)/i', $response, $m)) {
        echo "   Query: " . trim(substr($m[1], 0, 200)) . "\n";
    }
} elseif (strpos($response, 'Fatal error') !== false) {
    echo "   ERROR: PHP Fatal error\n";
    preg_match('/Fatal error:([^<]+)/i', $response, $m);
    if ($m) echo "   " . trim(substr($m[1], 0, 200)) . "\n";
} elseif (strpos($finalUrl, 'login') !== false) {
    echo "   Redirected to login (not authenticated)\n";
} else {
    echo "   Dashboard loaded OK!\n";
}

@unlink($cookieFile);
echo "\nDone.\n";

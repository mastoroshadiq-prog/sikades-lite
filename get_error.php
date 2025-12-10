<?php
// Get full error response

$baseUrl = 'http://localhost:8080';
$cookieFile = tempnam(sys_get_temp_dir(), 'test_');

// Login
$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$response = curl_exec($ch);
curl_close($ch);

preg_match('/name="csrf_test_name"\s+value="([^"]+)"/', $response, $m);
$csrf = $m[1] ?? '';

$ch = curl_init($baseUrl . '/login');  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, "username=admin&password=admin123&csrf_test_name=$csrf");
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_exec($ch);
curl_close($ch);

// Test /pembangunan/proyek page
$ch = curl_init($baseUrl . '/pembangunan/proyek');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);

// Find ERROR part in response
if (preg_match('/ERROR:\s*([^<]{0,500})/i', $response, $m)) {
    echo "SQL Error:\n";
    echo $m[0] . "\n\n";
}

if (preg_match('/Query failed:\s*ERROR([^<]{0,500})/i', $response, $m)) {
    echo "Query Error:\n";
    echo $m[0] . "\n";
}

// Look for other error patterns
if (preg_match('/"message":\s*"([^"]+)"/i', $response, $m)) {
    echo "Message: " . $m[1] . "\n";
}

@unlink($cookieFile);

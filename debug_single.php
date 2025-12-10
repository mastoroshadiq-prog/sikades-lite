<?php
// Get full error response from one failing page

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

// Test /aset page
$ch = curl_init($baseUrl . '/aset');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
curl_close($ch);

// Strip HTML and show text
$text = strip_tags($response);
$text = preg_replace('/\s+/', ' ', $text);
$text = trim($text);

// Find error part
if (preg_match('/pg_query\(\): Query failed: ERROR([^"]{0,500})/i', $response, $m)) {
    echo "SQL Error:\n";
    echo $m[0] . "\n";
} elseif (preg_match('/(Exception|Error|CRITICAL)(.{0,800})/i', $text, $m)) {
    echo "Error found:\n";
    echo $m[0] . "\n";
} else {
    echo "Response (first 2000 chars):\n";
    echo substr($text, 0, 2000);
}

@unlink($cookieFile);

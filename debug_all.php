<?php
// Detailed debug for each failing page

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

$failingPages = [
    '/aset' => 'Aset',
];

foreach ($failingPages as $path => $name) {
    echo "=== $name ===\n";
    
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Check for missing table
    if (preg_match('/relation "([^"]+)" does not exist/', $response, $m)) {
        echo "Missing table: " . $m[1] . "\n\n";
        continue;
    }
    
    // Check for missing column
    if (preg_match('/column "([^"]+)" does not exist/', $response, $m)) {
        echo "Missing column: " . $m[1] . "\n\n";
        continue;
    }
    
    // Check for undefined variable/property
    if (preg_match('/Undefined (variable|property|index|array key)[^<]{0,100}/i', $response, $m)) {
        echo "PHP Error: " . $m[0] . "\n\n";
        continue;
    }
    
    // Check for SQL function error
    if (preg_match('/function ([a-z_]+)\([^)]*\) does not exist/i', $response, $m)) {
        echo "Missing SQL function: " . $m[1] . "\n\n";
        continue;
    }
    
    // Generic error
    if (preg_match('/ERROR:\s*([^\\\\<]{0,200})/i', $response, $m)) {
        echo "Error: " . trim($m[1]) . "\n\n";
        continue;
    }
    
    echo "Unknown error - check server logs\n\n";
}

@unlink($cookieFile);

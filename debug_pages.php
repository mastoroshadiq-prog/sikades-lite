<?php
// Test specific failing pages with error details

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

echo "Testing failing pages...\n\n";

$failingPages = [
    '/perencanaan/rkp' => 'RKP',
    '/aset' => 'Aset',
    '/demografi' => 'Demografi',
    '/pembangunan/proyek' => 'Proyek',
];

foreach ($failingPages as $path => $name) {
    echo "=== $name ($path) ===\n";
    
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP $code\n";
    
    // Extract error
    if (preg_match('/\<h1[^>]*\>([^<]+)\<\/h1\>/i', $response, $m)) {
        echo "Title: " . trim($m[1]) . "\n";
    }
    
    if (preg_match('/does not exist/', $response)) {
        preg_match('/function\s+(\w+)\([^)]*\)/i', $response, $m);
        echo "Missing function: " . ($m[1] ?? 'unknown') . "\n";
    }
    
    if (preg_match('/Undefined|Fatal error|Exception/i', $response, $m)) {
        echo "Error type: " . $m[0] . "\n";
        
        // Get more context
        if (preg_match('/(Undefined[^<]{0,200})/i', $response, $detail)) {
            echo "Detail: " . trim($detail[1]) . "\n";
        }
        if (preg_match('/(Fatal error[^<]{0,200})/i', $response, $detail)) {
            echo "Detail: " . trim($detail[1]) . "\n";
        }
    }
    
    if (preg_match('/relation "([^"]+)" does not exist/', $response, $m)) {
        echo "Missing table: " . $m[1] . "\n";
    }
    
    echo "\n";
}

@unlink($cookieFile);

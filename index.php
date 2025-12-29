<?php

/**
 * SIKADES LITE - CI 4.6 FINAL BOOTSTRAP FIX
 * Menangani urutan konstanta dan namespace agar kompatibel dengan CI 4.5+
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

// 1. DEFINE ENVIRONMENT SECEPAT MUNGKIN
if (! defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('CI_ENVIRONMENT') ?: 'production');
}

// Support untuk file dengan namespace Config (seperti Events.php)
namespace Config {
    if (! defined('Config\ENVIRONMENT')) {
        define('Config\ENVIRONMENT', \ENVIRONMENT);
    }
}

// Kembali ke namespace global untuk proses selanjutnya
namespace {
    // 2. Inisialisasi Path Dasar
    define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
    define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
    chdir(__DIR__);

    // 3. Load Paths
    require ROOTPATH . 'app/Config/Paths.php';
    $paths = new Config\Paths();

    define('SYSTEMPATH', realpath($paths->systemDirectory) . DIRECTORY_SEPARATOR);
    define('APPPATH', realpath($paths->appDirectory) . DIRECTORY_SEPARATOR);

    // 4. Load Autoloader
    require ROOTPATH . 'vendor/autoload.php';

    // 5. Load Common Functions (Penting untuk fungsi env(), config(), dll)
    if (file_exists(SYSTEMPATH . 'Common.php')) {
        require_once SYSTEMPATH . 'Common.php';
    }

    // 6. Load Constants Sistem & Aplikasi
    require SYSTEMPATH . 'Config/Constants.php';
    require APPPATH . 'Config/Constants.php';

    // 7. Load Common Aplikasi
    if (file_exists(APPPATH . 'Common.php')) {
        require_once APPPATH . 'Common.php';
    }

    // 8. Load DotEnv jika tersedia
    if (file_exists(ROOTPATH . '.env')) {
        require_once SYSTEMPATH . 'Config/DotEnv.php';
        (new \CodeIgniter\Config\DotEnv(ROOTPATH))->load();
    }

    // 9. Jalankan Aplikasi
    try {
        $app = \Config\Services::codeigniter();
        $app->initialize();
        $app->setContext('web');
        $app->run();
    } catch (\Throwable $e) {
        header('Content-Type: text/plain', true, 500);
        echo "SIKADES LITE CRITICAL ERROR:\n";
        echo "Message: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
        echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
        exit;
    }
}

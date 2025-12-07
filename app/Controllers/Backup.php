<?php

namespace App\Controllers;

class Backup extends BaseController
{
    /**
     * Backup page
     */
    public function index()
    {
        if (!$this->hasRole(['Administrator'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        // Get list of existing backups
        $backupDir = WRITEPATH . 'backups';
        $backups = [];
        
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*.sql');
            foreach ($files as $file) {
                $backups[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'date' => date('Y-m-d H:i:s', filemtime($file)),
                ];
            }
            // Sort by date descending
            usort($backups, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        }

        $data = array_merge($this->data, [
            'title' => 'Backup & Restore Database',
            'backups' => $backups,
        ]);

        return view('backup/index', $data);
    }

    /**
     * Create backup
     */
    public function create()
    {
        if (!$this->hasRole(['Administrator'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $backupDir = WRITEPATH . 'backups';
        
        // Create backup directory if not exists
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        // Get database config
        $db = \Config\Database::connect();
        $dbName = $db->getDatabase();
        
        // Get all tables
        $tables = $db->listTables();
        
        $sql = "-- Siskeudes Lite Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- Database: {$dbName}\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
        
        foreach ($tables as $table) {
            // Get create table statement
            $query = $db->query("SHOW CREATE TABLE `{$table}`");
            $row = $query->getRow();
            
            $sql .= "-- Table: {$table}\n";
            $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
            $sql .= $row->{'Create Table'} . ";\n\n";
            
            // Get table data
            $data = $db->table($table)->get()->getResultArray();
            
            if (!empty($data)) {
                foreach ($data as $record) {
                    $values = array_map(function($val) use ($db) {
                        if ($val === null) return 'NULL';
                        return "'" . $db->escapeString($val) . "'";
                    }, $record);
                    
                    $sql .= "INSERT INTO `{$table}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        // Write to file
        if (file_put_contents($filepath, $sql)) {
            // Log activity
            \App\Models\ActivityLogModel::log('create', 'backup', "Backup database: {$filename}");
            return redirect()->to('/backup')->with('success', "Backup berhasil dibuat: {$filename}");
        }
        
        return redirect()->to('/backup')->with('error', 'Gagal membuat backup');
    }

    /**
     * Download backup
     */
    public function download($filename)
    {
        if (!$this->hasRole(['Administrator'])) {
            return redirect()->to('/backup')->with('error', 'Akses ditolak.');
        }

        $filepath = WRITEPATH . 'backups/' . $filename;
        
        if (!file_exists($filepath)) {
            return redirect()->to('/backup')->with('error', 'File tidak ditemukan');
        }

        return $this->response->download($filepath, null)
            ->setFileName($filename);
    }

    /**
     * Delete backup
     */
    public function delete($filename)
    {
        if (!$this->hasRole(['Administrator'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $filepath = WRITEPATH . 'backups/' . $filename;
        
        if (!file_exists($filepath)) {
            return $this->respondError('File tidak ditemukan', 404);
        }

        if (unlink($filepath)) {
            \App\Models\ActivityLogModel::log('delete', 'backup', "Hapus backup: {$filename}");
            return $this->respondSuccess(null, 'Backup berhasil dihapus');
        }

        return $this->respondError('Gagal menghapus backup');
    }

    /**
     * Restore from backup
     */
    public function restore()
    {
        if (!$this->hasRole(['Administrator'])) {
            return redirect()->to('/backup')->with('error', 'Akses ditolak.');
        }

        $filename = $this->request->getPost('filename');
        $filepath = WRITEPATH . 'backups/' . $filename;
        
        if (!file_exists($filepath)) {
            return redirect()->to('/backup')->with('error', 'File backup tidak ditemukan');
        }

        $sql = file_get_contents($filepath);
        
        if (empty($sql)) {
            return redirect()->to('/backup')->with('error', 'File backup kosong');
        }

        $db = \Config\Database::connect();
        
        try {
            // Split by semicolon and execute each statement
            $statements = array_filter(explode(";\n", $sql));
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && strpos($statement, '--') !== 0) {
                    $db->query($statement);
                }
            }
            
            \App\Models\ActivityLogModel::log('restore', 'backup', "Restore database dari: {$filename}");
            return redirect()->to('/backup')->with('success', 'Database berhasil di-restore dari backup');
        } catch (\Exception $e) {
            return redirect()->to('/backup')->with('error', 'Gagal restore: ' . $e->getMessage());
        }
    }
}

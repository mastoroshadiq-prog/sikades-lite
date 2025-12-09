<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ProyekLogModel - Progress History for Infrastructure Projects
 */
class ProyekLogModel extends Model
{
    protected $table            = 'proyek_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'proyek_id',
        'tanggal_laporan',
        'persentase_fisik',
        'volume_terealisasi',
        'kendala',
        'solusi',
        'foto',
        'pelapor',
        'created_by',
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    /**
     * Get logs for a project
     */
    public function getByProyek(int $proyekId): array
    {
        return $this->where('proyek_id', $proyekId)
            ->orderBy('tanggal_laporan', 'DESC')
            ->findAll();
    }

    /**
     * Get latest log for a project
     */
    public function getLatest(int $proyekId): ?array
    {
        return $this->where('proyek_id', $proyekId)
            ->orderBy('tanggal_laporan', 'DESC')
            ->first();
    }

    /**
     * Add progress log and update project percentage
     */
    public function addProgress(array $data): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Insert log
        $this->insert($data);
        
        // Update project's latest percentage
        $proyekModel = new ProyekModel();
        $proyekModel->update($data['proyek_id'], [
            'persentase_fisik' => $data['persentase_fisik'],
        ]);
        
        // Update status based on percentage
        if ($data['persentase_fisik'] >= 100) {
            $proyekModel->update($data['proyek_id'], [
                'status' => 'SELESAI',
                'tgl_selesai_aktual' => $data['tanggal_laporan'],
            ]);
        } elseif ($data['persentase_fisik'] > 0) {
            $proyek = $proyekModel->find($data['proyek_id']);
            if ($proyek && $proyek['status'] === 'RENCANA') {
                $proyekModel->update($data['proyek_id'], [
                    'status' => 'PROSES',
                ]);
            }
        }
        
        // Update photo milestones
        $persentase = $data['persentase_fisik'];
        if (!empty($data['foto'])) {
            if ($persentase <= 10) {
                $proyekModel->update($data['proyek_id'], ['foto_0' => $data['foto']]);
            } elseif ($persentase >= 45 && $persentase <= 55) {
                $proyekModel->update($data['proyek_id'], ['foto_50' => $data['foto']]);
            } elseif ($persentase >= 90) {
                $proyekModel->update($data['proyek_id'], ['foto_100' => $data['foto']]);
            }
        }
        
        $db->transComplete();
        
        return $db->transStatus();
    }

    /**
     * Get progress timeline for chart
     */
    public function getProgressTimeline(int $proyekId): array
    {
        return $this->select('tanggal_laporan, persentase_fisik, volume_terealisasi')
            ->where('proyek_id', $proyekId)
            ->orderBy('tanggal_laporan', 'ASC')
            ->findAll();
    }
}

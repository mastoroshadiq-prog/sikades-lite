<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'user_id',
        'kode_desa',
        'action',
        'module',
        'description',
        'data_before',
        'data_after',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Log an activity
     */
    public static function log(
        string $action,
        string $module,
        string $description = null,
        mixed $dataBefore = null,
        mixed $dataAfter = null
    ): bool {
        $session = session();
        $request = service('request');
        
        $model = new self();
        
        return $model->insert([
            'user_id' => $session->get('user_id'),
            'kode_desa' => $session->get('kode_desa'),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'data_before' => $dataBefore ? json_encode($dataBefore) : null,
            'data_after' => $dataAfter ? json_encode($dataAfter) : null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
        ]);
    }

    /**
     * Get activities with user info
     */
    public function getWithUser(int $limit = 50, int $offset = 0)
    {
        return $this->select('activity_logs.*, users.nama_lengkap as user_name, users.username')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->orderBy('activity_logs.created_at', 'DESC')
            ->findAll($limit, $offset);
    }

    /**
     * Get activities by module
     */
    public function getByModule(string $module, int $limit = 50)
    {
        return $this->select('activity_logs.*, users.nama_lengkap as user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->where('module', $module)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Get activities by user
     */
    public function getByUser(int $userId, int $limit = 50)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Get today's activities
     */
    public function getToday()
    {
        return $this->select('activity_logs.*, users.nama_lengkap as user_name')
            ->join('users', 'users.id = activity_logs.user_id', 'left')
            ->where('DATE(activity_logs.created_at)', date('Y-m-d'))
            ->orderBy('activity_logs.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get activity summary (count by action)
     */
    public function getSummary(string $startDate = null, string $endDate = null)
    {
        $builder = $this->builder();
        $builder->select('action, module, COUNT(*) as count');
        
        if ($startDate) {
            $builder->where('DATE(created_at) >=', $startDate);
        }
        if ($endDate) {
            $builder->where('DATE(created_at) <=', $endDate);
        }
        
        return $builder->groupBy(['action', 'module'])
            ->orderBy('count', 'DESC')
            ->get()
            ->getResultArray();
    }
}

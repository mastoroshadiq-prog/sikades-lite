<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;

class ActivityLog extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new ActivityLogModel();
    }

    /**
     * Display activity logs
     */
    public function index()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 50;
        $filter = $this->request->getGet('filter') ?? 'all';
        $module = $this->request->getGet('module') ?? '';
        $date = $this->request->getGet('date') ?? '';

        $builder = $this->logModel->builder();
        $builder->select('activity_logs.*, users.username as user_name')
                ->join('users', 'users.id = activity_logs.user_id', 'left');

        // Apply filters
        if ($module) {
            $builder->where('activity_logs.module', $module);
        }
        if ($date) {
            $builder->where('DATE(activity_logs.created_at)', $date);
        }
        if ($filter === 'today') {
            $builder->where('DATE(activity_logs.created_at)', date('Y-m-d'));
        }

        $total = $builder->countAllResults(false);
        $logs = $builder->orderBy('activity_logs.created_at', 'DESC')
                        ->limit($perPage, ($page - 1) * $perPage)
                        ->get()
                        ->getResultArray();

        // Get unique modules for filter
        $modules = $this->logModel->builder()
                                  ->select('module')
                                  ->distinct()
                                  ->get()
                                  ->getResultArray();

        $data = array_merge($this->data, [
            'title' => 'Activity Log',
            'logs' => $logs,
            'modules' => array_column($modules, 'module'),
            'total' => $total,
            'perPage' => $perPage,
            'currentPage' => $page,
            'totalPages' => ceil($total / $perPage),
            'filter' => $filter,
            'selectedModule' => $module,
            'selectedDate' => $date
        ]);

        return view('activity_log/index', $data);
    }

    /**
     * Get today's summary
     */
    public function summary()
    {
        $summary = $this->logModel->getSummary(date('Y-m-d'), date('Y-m-d'));
        $todayLogs = $this->logModel->getToday();

        return $this->response->setJSON([
            'summary' => $summary,
            'total_today' => count($todayLogs),
            'logs' => array_slice($todayLogs, 0, 10)
        ]);
    }

    /**
     * Clear old logs (admin only)
     */
    public function clearOld()
    {
        if (session()->get('role') !== 'Administrator') {
            return redirect()->to('/activity-log')->with('error', 'Akses ditolak');
        }

        $days = $this->request->getPost('days') ?? 90;
        $cutoffDate = date('Y-m-d', strtotime("-{$days} days"));

        $deleted = $this->logModel->where('DATE(created_at) <', $cutoffDate)->delete();

        return redirect()->to('/activity-log')
                        ->with('message', "Log yang lebih dari {$days} hari telah dihapus");
    }
}

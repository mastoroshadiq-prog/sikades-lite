<?php

namespace App\Models;

use CodeIgniter\Model;

class BumdesJurnalModel extends Model
{
    protected $table            = 'bumdes_jurnal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id',
        'no_bukti',
        'tanggal',
        'deskripsi',
        'total',
        'bku_id',
        'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate nomor bukti
     */
    public function generateNoBukti(int $unitId): string
    {
        $tahun = date('Y');
        $bulan = date('m');
        
        $last = $this->where('unit_id', $unitId)
            ->like('no_bukti', "JU/{$tahun}{$bulan}/", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $parts = explode('/', $last['no_bukti']);
            $num = (int)($parts[2] ?? 0) + 1;
        } else {
            $num = 1;
        }
        
        return "JU/{$tahun}{$bulan}/" . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get jurnal with details
     */
    public function getWithDetails(int $id): ?array
    {
        $jurnal = $this->find($id);
        if (!$jurnal) return null;
        
        $db = \Config\Database::connect();
        $jurnal['details'] = $db->query("
            SELECT jd.*, a.kode_akun, a.nama_akun
            FROM bumdes_jurnal_detail jd
            JOIN bumdes_akun a ON jd.akun_id = a.id
            WHERE jd.jurnal_id = ?
            ORDER BY jd.debet DESC, jd.id
        ", [$id])->getResultArray();
        
        return $jurnal;
    }

    /**
     * Get journal list for a unit with summary
     */
    public function getByUnit(int $unitId, array $filters = []): array
    {
        $builder = $this->select('bumdes_jurnal.*, u.nama_unit')
            ->join('bumdes_unit u', 'u.id = bumdes_jurnal.unit_id')
            ->where('bumdes_jurnal.unit_id', $unitId);
        
        if (!empty($filters['bulan'])) {
            $builder->where('EXTRACT(MONTH FROM tanggal)::int', $filters['bulan']);
        }
        
        if (!empty($filters['tahun'])) {
            $builder->where('EXTRACT(YEAR FROM tanggal)::int', $filters['tahun']);
        } else {
            $builder->where('EXTRACT(YEAR FROM tanggal)::int', date('Y'));
        }
        
        return $builder->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll();
    }

    /**
     * Create jurnal with details (double-entry)
     */
    public function createWithDetails(array $jurnalData, array $details): int
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        
        try {
            // Calculate total
            $total = 0;
            foreach ($details as $d) {
                $total += max($d['debet'], $d['kredit']);
            }
            $jurnalData['total'] = $total;
            
            // Insert jurnal
            $this->insert($jurnalData);
            $jurnalId = $this->getInsertID();
            
            // Insert details
            foreach ($details as $detail) {
                $db->table('bumdes_jurnal_detail')->insert([
                    'jurnal_id'  => $jurnalId,
                    'akun_id'    => $detail['akun_id'],
                    'debet'      => $detail['debet'] ?? 0,
                    'kredit'     => $detail['kredit'] ?? 0,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }
            
            $db->transCommit();
            return $jurnalId;
            
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Get Trial Balance (Neraca Saldo)
     */
    public function getTrialBalance(int $unitId, ?string $startDate = null, ?string $endDate = null): array
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                a.id,
                a.kode_akun,
                a.nama_akun,
                a.tipe,
                a.saldo_normal,
                a.is_header,
                COALESCE(SUM(jd.debet), 0) as total_debet,
                COALESCE(SUM(jd.kredit), 0) as total_kredit
            FROM bumdes_akun a
            LEFT JOIN bumdes_jurnal_detail jd ON a.id = jd.akun_id
            LEFT JOIN bumdes_jurnal j ON jd.jurnal_id = j.id AND j.unit_id = ?
        ";
        
        $params = [$unitId];
        
        if ($startDate && $endDate) {
            $query .= " AND j.tanggal BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }
        
        $query .= "
            WHERE a.is_header = 0
            GROUP BY a.id
            ORDER BY a.urutan
        ";
        
        $results = $db->query($query, $params)->getResultArray();
        
        // Calculate saldo
        foreach ($results as &$row) {
            if ($row['saldo_normal'] === 'DEBET') {
                $row['saldo'] = $row['total_debet'] - $row['total_kredit'];
            } else {
                $row['saldo'] = $row['total_kredit'] - $row['total_debet'];
            }
        }
        
        return $results;
    }

    /**
     * Get Profit & Loss (Laba Rugi)
     */
    public function getProfitLoss(int $unitId, int $tahun): array
    {
        $db = \Config\Database::connect();
        
        // Pendapatan
        $pendapatan = $db->query("
            SELECT 
                a.kode_akun,
                a.nama_akun,
                COALESCE(SUM(jd.kredit) - SUM(jd.debet), 0) as jumlah
            FROM bumdes_akun a
            LEFT JOIN bumdes_jurnal_detail jd ON a.id = jd.akun_id
            LEFT JOIN bumdes_jurnal j ON jd.jurnal_id = j.id AND j.unit_id = ? AND EXTRACT(YEAR FROM j.tanggal)::int = ?
            WHERE a.tipe = 'PENDAPATAN' AND a.is_header = 0
            GROUP BY a.id
            ORDER BY a.urutan
        ", [$unitId, $tahun])->getResultArray();
        
        // Beban
        $beban = $db->query("
            SELECT 
                a.kode_akun,
                a.nama_akun,
                COALESCE(SUM(jd.debet) - SUM(jd.kredit), 0) as jumlah
            FROM bumdes_akun a
            LEFT JOIN bumdes_jurnal_detail jd ON a.id = jd.akun_id
            LEFT JOIN bumdes_jurnal j ON jd.jurnal_id = j.id AND j.unit_id = ? AND EXTRACT(YEAR FROM j.tanggal)::int = ?
            WHERE a.tipe = 'BEBAN' AND a.is_header = 0
            GROUP BY a.id
            ORDER BY a.urutan
        ", [$unitId, $tahun])->getResultArray();
        
        $totalPendapatan = array_sum(array_column($pendapatan, 'jumlah'));
        $totalBeban = array_sum(array_column($beban, 'jumlah'));
        
        return [
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'total_pendapatan' => $totalPendapatan,
            'total_beban' => $totalBeban,
            'laba_rugi' => $totalPendapatan - $totalBeban,
        ];
    }

    /**
     * Get Balance Sheet (Neraca)
     */
    public function getBalanceSheet(int $unitId, string $tanggal): array
    {
        $db = \Config\Database::connect();
        
        $getByType = function($tipe) use ($db, $unitId, $tanggal) {
            return $db->query("
                SELECT 
                    a.kode_akun,
                    a.nama_akun,
                    a.saldo_normal,
                    CASE 
                        WHEN a.saldo_normal = 'DEBET' THEN COALESCE(SUM(jd.debet) - SUM(jd.kredit), 0)
                        ELSE COALESCE(SUM(jd.kredit) - SUM(jd.debet), 0)
                    END as saldo
                FROM bumdes_akun a
                LEFT JOIN bumdes_jurnal_detail jd ON a.id = jd.akun_id
                LEFT JOIN bumdes_jurnal j ON jd.jurnal_id = j.id AND j.unit_id = ? AND j.tanggal <= ?
                WHERE a.tipe = ? AND a.is_header = 0
                GROUP BY a.id
                HAVING saldo != 0
                ORDER BY a.urutan
            ", [$unitId, $tanggal, $tipe])->getResultArray();
        };
        
        $aset = $getByType('ASET');
        $kewajiban = $getByType('KEWAJIBAN');
        $ekuitas = $getByType('EKUITAS');
        
        return [
            'aset' => $aset,
            'kewajiban' => $kewajiban,
            'ekuitas' => $ekuitas,
            'total_aset' => array_sum(array_column($aset, 'saldo')),
            'total_kewajiban' => array_sum(array_column($kewajiban, 'saldo')),
            'total_ekuitas' => array_sum(array_column($ekuitas, 'saldo')),
        ];
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class KegiatanModel extends Model
{
    protected $table = 'kegiatan';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'rkpdesa_id',
        'kode_desa',
        'bidang_id',
        'kode_kegiatan',
        'nama_kegiatan',
        'lokasi',
        'volume',
        'satuan',
        'sasaran_manfaat',
        'waktu_pelaksanaan',
        'pagu_anggaran',
        'sumber_dana',
        'status',
        'prioritas',
        'keterangan',
        'ref_rekening_id',
        'apbdes_id',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'kode_desa' => 'required',
        'nama_kegiatan' => 'required',
    ];

    /**
     * Get kegiatan with bidang info
     */
    public function getWithBidang(int $rkpdesaId)
    {
        return $this->select('kegiatan.*, ref_bidang.kode_bidang, ref_bidang.nama_bidang')
                    ->join('ref_bidang', 'ref_bidang.id = kegiatan.bidang_id', 'left')
                    ->where('rkpdesa_id', $rkpdesaId)
                    ->orderBy('ref_bidang.urutan', 'ASC')
                    ->orderBy('kegiatan.prioritas', 'ASC')
                    ->findAll();
    }

    /**
     * Get kegiatan grouped by bidang
     */
    public function getGroupedByBidang(int $rkpdesaId)
    {
        $kegiatan = $this->getWithBidang($rkpdesaId);
        $grouped = [];
        
        foreach ($kegiatan as $item) {
            $bidangKey = $item['bidang_id'] ?? 0;
            if (!isset($grouped[$bidangKey])) {
                $grouped[$bidangKey] = [
                    'kode_bidang' => $item['kode_bidang'] ?? '-',
                    'nama_bidang' => $item['nama_bidang'] ?? 'Tanpa Bidang',
                    'kegiatan' => [],
                    'total_pagu' => 0,
                ];
            }
            $grouped[$bidangKey]['kegiatan'][] = $item;
            $grouped[$bidangKey]['total_pagu'] += $item['pagu_anggaran'];
        }
        
        return $grouped;
    }

    /**
     * Get summary per sumber dana
     */
    public function getSummaryBySumberDana(int $rkpdesaId)
    {
        return $this->select('sumber_dana, SUM(pagu_anggaran) as total, COUNT(*) as jumlah')
                    ->where('rkpdesa_id', $rkpdesaId)
                    ->groupBy('sumber_dana')
                    ->findAll();
    }

    /**
     * Get summary per status
     */
    public function getSummaryByStatus(int $rkpdesaId)
    {
        return $this->select('status, SUM(pagu_anggaran) as total, COUNT(*) as jumlah')
                    ->where('rkpdesa_id', $rkpdesaId)
                    ->groupBy('status')
                    ->findAll();
    }

    /**
     * Link kegiatan ke APBDes
     */
    public function linkToApbdes(int $kegiatanId, int $apbdesId, int $rekeningId = null)
    {
        return $this->update($kegiatanId, [
            'apbdes_id' => $apbdesId,
            'ref_rekening_id' => $rekeningId,
            'status' => 'Disetujui'
        ]);
    }

    /**
     * Get kegiatan yang belum masuk APBDes
     */
    public function getBelumApbdes(string $kodeDesa, int $tahun)
    {
        return $this->select('kegiatan.*, rkpdesa.tahun')
                    ->join('rkpdesa', 'rkpdesa.id = kegiatan.rkpdesa_id')
                    ->where('kegiatan.kode_desa', $kodeDesa)
                    ->where('rkpdesa.tahun', $tahun)
                    ->where('kegiatan.apbdes_id IS NULL')
                    ->where('kegiatan.status', 'Prioritas')
                    ->findAll();
    }
}

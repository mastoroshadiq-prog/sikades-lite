<?php

namespace App\Controllers;

use App\Models\BkuModel;
use App\Models\SppModel;
use App\Models\ActivityLogModel;

class Upload extends BaseController
{
    protected $uploadPath;
    protected $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    protected $maxSize = 5242880; // 5MB

    public function __construct()
    {
        $this->uploadPath = WRITEPATH . 'uploads/bukti/';
        
        // Create directory if not exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Upload bukti for BKU
     */
    public function bku($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $bkuModel = new BkuModel();
        $bku = $bkuModel->find($id);

        if (!$bku || $bku['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        $file = $this->request->getFile('bukti_file');

        if (!$file || !$file->isValid()) {
            return $this->respondError('File tidak valid');
        }

        // Validate file type
        if (!in_array($file->getMimeType(), $this->allowedTypes)) {
            return $this->respondError('Tipe file tidak diizinkan. Gunakan: JPG, PNG, GIF, atau PDF');
        }

        // Validate file size
        if ($file->getSize() > $this->maxSize) {
            return $this->respondError('Ukuran file melebihi batas (max 5MB)');
        }

        // Delete old file if exists
        if (!empty($bku['bukti_file']) && file_exists($this->uploadPath . $bku['bukti_file'])) {
            unlink($this->uploadPath . $bku['bukti_file']);
        }

        // Generate new filename
        $newName = 'bku_' . $id . '_' . time() . '.' . $file->getExtension();

        // Move file
        if ($file->move($this->uploadPath, $newName)) {
            $bkuModel->update($id, ['bukti_file' => $newName]);
            ActivityLogModel::log('upload', 'bku', "Upload bukti transaksi BKU #{$id}");
            
            return $this->respondSuccess([
                'filename' => $newName,
                'url' => base_url('/upload/view/bku/' . $newName)
            ], 'Bukti berhasil diupload');
        }

        return $this->respondError('Gagal mengupload file');
    }

    /**
     * Upload bukti for SPP
     */
    public function spp($id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $sppModel = new SppModel();
        $spp = $sppModel->find($id);

        if (!$spp || $spp['kode_desa'] != $this->session->get('kode_desa')) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        $file = $this->request->getFile('bukti_file');

        if (!$file || !$file->isValid()) {
            return $this->respondError('File tidak valid');
        }

        // Validate file type
        if (!in_array($file->getMimeType(), $this->allowedTypes)) {
            return $this->respondError('Tipe file tidak diizinkan. Gunakan: JPG, PNG, GIF, atau PDF');
        }

        // Validate file size
        if ($file->getSize() > $this->maxSize) {
            return $this->respondError('Ukuran file melebihi batas (max 5MB)');
        }

        // Delete old file if exists
        if (!empty($spp['bukti_file']) && file_exists($this->uploadPath . $spp['bukti_file'])) {
            unlink($this->uploadPath . $spp['bukti_file']);
        }

        // Generate new filename
        $newName = 'spp_' . $id . '_' . time() . '.' . $file->getExtension();

        // Move file
        if ($file->move($this->uploadPath, $newName)) {
            $sppModel->update($id, ['bukti_file' => $newName]);
            ActivityLogModel::log('upload', 'spp', "Upload bukti SPP #{$id}");
            
            return $this->respondSuccess([
                'filename' => $newName,
                'url' => base_url('/upload/view/spp/' . $newName)
            ], 'Bukti berhasil diupload');
        }

        return $this->respondError('Gagal mengupload file');
    }

    /**
     * View uploaded file
     */
    public function view($type, $filename)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa', 'Kepala Desa'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak');
        }

        $filepath = $this->uploadPath . $filename;

        if (!file_exists($filepath)) {
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan');
        }

        // Get MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($filepath));
    }

    /**
     * Delete uploaded file
     */
    public function delete($type, $id)
    {
        if (!$this->hasRole(['Administrator', 'Operator Desa'])) {
            return $this->respondError('Akses ditolak', 403);
        }

        $kodeDesa = $this->session->get('kode_desa');
        
        if ($type === 'bku') {
            $model = new BkuModel();
        } else {
            $model = new SppModel();
        }

        $record = $model->find($id);

        if (!$record || $record['kode_desa'] != $kodeDesa) {
            return $this->respondError('Data tidak ditemukan', 404);
        }

        // Delete file
        if (!empty($record['bukti_file']) && file_exists($this->uploadPath . $record['bukti_file'])) {
            unlink($this->uploadPath . $record['bukti_file']);
        }

        // Update record
        $model->update($id, ['bukti_file' => null]);

        ActivityLogModel::log('delete', $type, "Hapus bukti {$type} #{$id}");

        return $this->respondSuccess(null, 'Bukti berhasil dihapus');
    }
}

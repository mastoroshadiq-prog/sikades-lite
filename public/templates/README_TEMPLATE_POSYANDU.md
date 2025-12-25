# 📋 Template Import/Export e-Posyandu

## 📁 Lokasi Template
Semua template CSV tersedia di folder: `public/templates/`

## 📊 1. Template Pemeriksaan Balita
**File:** `import_pemeriksaan_balita.csv`

### Kolom-kolom:
| Kolom | Wajib | Format | Keterangan |
|-------|-------|--------|------------|
| NIK | Ya | 16 digit | NIK balita yang sudah terdaftar |
| NAMA_LENGKAP | Ya | Teks | Nama balita |
| TANGGAL_LAHIR | Ya | YYYY-MM-DD | Tanggal lahir balita |
| JENIS_KELAMIN | Ya | L/P | L = Laki-laki, P = Perempuan |
| TANGGAL_PERIKSA | Ya | YYYY-MM-DD | Tanggal pemeriksaan |
| BERAT_BADAN | Ya | Desimal | Dalam kilogram (kg), contoh: 12.5 |
| TINGGI_BADAN | Ya | Desimal | Dalam centimeter (cm), contoh: 85.2 |
| LINGKAR_KEPALA | Tidak | Desimal | Dalam cm, kosongkan jika tidak diukur |
| LINGKAR_LENGAN | Tidak | Desimal | Dalam cm (LILA) |
| VITAMIN_A | Tidak | YA/TIDAK | Apakah mendapat vitamin A |
| ASI_EKSKLUSIF | Tidak | YA/TIDAK | Untuk bayi < 6 bulan |
| IMUNISASI | Tidak | Teks | Contoh: "BCG, Polio, DPT" |
| KETERANGAN | Tidak | Teks | Catatan tambahan |

### Contoh Data:
```csv
NIK,NAMA_LENGKAP,TANGGAL_LAHIR,JENIS_KELAMIN,TANGGAL_PERIKSA,BERAT_BADAN,TINGGI_BADAN,LINGKAR_KEPALA,LINGKAR_LENGAN,VITAMIN_A,ASI_EKSKLUSIF,IMUNISASI,KETERANGAN
3201010001010001,Budi Santoso,2022-06-15,L,2024-12-15,12.5,85.2,46.5,15.0,YA,TIDAK,Campak DPT Polio,Pertumbuhan normal
```

### Catatan Penting:
- Sistem akan otomatis menghitung **Z-Score** dan **Status Stunting**
- Usia dalam bulan dihitung otomatis dari tanggal lahir
- Status gizi ditentukan otomatis berdasarkan standar WHO

---

## 👶 2. Template Ibu Hamil
**File:** `import_ibu_hamil.csv`

### Kolom-kolom:
| Kolom | Wajib | Format | Keterangan |
|-------|-------|--------|------------|
| NIK | Ya | 16 digit | NIK ibu hamil yang sudah terdaftar |
| NAMA_LENGKAP | Ya | Teks | Nama ibu hamil |
| TANGGAL_HPHT | Ya | YYYY-MM-DD | Hari Pertama Haid Terakhir |
| KEHAMILAN_KE | Ya | Angka | Kehamilan ke berapa (1, 2, 3, dst) |
| TINGGI_BADAN | Tidak | Desimal | Tinggi badan ibu dalam cm |
| BERAT_BADAN_SEBELUM | Tidak | Desimal | BB sebelum hamil dalam kg |
| GOLONGAN_DARAH | Tidak | A/B/AB/O | Golongan darah |
| RESIKO_TINGGI | Tidak | YA/TIDAK | Apakah kehamilan resiko tinggi |
| FAKTOR_RESIKO | Tidak | Teks | Pisahkan dengan koma jika lebih dari 1 |
| K1 | Tidak | YYYY-MM-DD | Tanggal pemeriksaan K1 |
| K2 | Tidak | YYYY-MM-DD | Tanggal pemeriksaan K2 |
| K3 | Tidak | YYYY-MM-DD | Tanggal pemeriksaan K3 |
| K4 | Tidak | YYYY-MM-DD | Tanggal pemeriksaan K4 |
| KETERANGAN | Tidak | Teks | Catatan tambahan |

### Faktor Resiko yang Tersedia:
- Usia < 20 tahun
- Usia > 35 tahun
- Tinggi badan < 145 cm
- Riwayat keguguran
- Riwayat operasi caesar
- Anemia (Hb < 11)
- Tekanan darah tinggi
- Diabetes gestasional
- Kehamilan kembar
- Jarak kehamilan < 2 tahun
- Kehamilan > 4
- Kelainan letak janin
- Pre-eklampsia
- Lain-lain

### Contoh Data:
```csv
NIK,NAMA_LENGKAP,TANGGAL_HPHT,KEHAMILAN_KE,TINGGI_BADAN,BERAT_BADAN_SEBELUM,GOLONGAN_DARAH,RESIKO_TINGGI,FAKTOR_RESIKO,K1,K2,K3,K4,KETERANGAN
3201010001020001,Dewi Kusuma,2024-09-15,2,155,52,O,TIDAK,,2024-10-10,2024-12-15,,,Kondisi baik
3201010001020003,Rina Marlina,2024-08-10,1,142,45,AB,YA,"Tinggi badan < 145 cm, Usia < 20 tahun",2024-09-05,2024-11-10,2025-01-15,,RISTI
```

### Catatan Penting:
- **HPL (Taksiran Persalinan)** dihitung otomatis: HPHT + 7 hari - 3 bulan + 1 tahun
- **Usia Kandungan** dihitung otomatis dari HPHT
- Jika RESIKO_TINGGI = YA, wajib diisi FAKTOR_RESIKO

---

## 📖 Cara Menggunakan Template

### 1. Download Template
- Template ada di `public/templates/`
- Buka dengan Excel, Google Sheets, atau LibreOffice Calc

### 2. Isi Data
- **Jangan ubah nama kolom** (baris header)
- Isi data mulai baris ke-2
- Gunakan format tanggal: **YYYY-MM-DD** (contoh: 2024-12-25)
- Untuk kolom YA/TIDAK, gunakan **YA** atau **TIDAK** (huruf besar)
- Untuk kolom kosong, biarkan kosong (jangan isi dengan "-" atau "null")

### 3. Simpan sebagai CSV
- File → Save As → **CSV (Comma delimited)**
- Encoding: **UTF-8**

### 4. Import ke Sistem
*(Fitur import akan ditambahkan di controller)*

---

## 🔄 Export Data

### Format Export CSV
Data yang di-export akan menggunakan format yang sama dengan template import, sehingga bisa langsung di-edit dan di-import kembali.

### Kolom Tambahan pada Export:
- **STATUS_GIZI** - Status gizi balita (BURUK/KURANG/BAIK/LEBIH/OBESITAS)
- **Z_SCORE_TB_U** - Z-Score tinggi badan untuk usia
- **Z_SCORE_BB_U** - Z-Score berat badan untuk usia
- **INDIKASI_STUNTING** - YA/TIDAK
- **USIA_BULAN** - Usia dalam bulan saat pemeriksaan
- **STATUS** - Status kehamilan (HAMIL/MELAHIRKAN/KEGUGURAN/BATAL)
- **HPL** - Taksiran persalinan (calculated)

---

## ⚠️ Validasi dan Error

### Error yang Mungkin Terjadi:

1. **NIK tidak ditemukan**
   - Pastikan NIK sudah terdaftar di data penduduk

2. **Format tanggal salah**
   - Gunakan format YYYY-MM-DD

3. **Jenis kelamin tidak valid**
   - Hanya L atau P (huruf besar)

4. **Data wajib kosong**
   - Periksa kolom yang wajib diisi

5. **Usia tidak sesuai**
   - Balita: < 5 tahun
   - Ibu hamil: Wanita usia subur (15-49 tahun)

---

## 📞 Bantuan

Jika ada kesulitan dalam penggunaan template, hubungi admin sistem atau lihat log error saat import untuk detail masalah.

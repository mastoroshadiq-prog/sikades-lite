# Sikades Lite - Supabase Setup Guide

## Langkah-langkah Setup dengan Supabase

### 1. Buat Project di Supabase

1. Buka [supabase.com](https://supabase.com) dan login/register
2. Klik **"New Project"**
3. Isi detail project:
   - **Name**: sikades-lite
   - **Database Password**: (catat password ini!)
   - **Region**: Singapore atau terdekat
4. Tunggu project selesai dibuat (~2 menit)

### 2. Jalankan Migration SQL

1. Di Supabase Dashboard, buka **SQL Editor**
2. Buat query baru
3. Copy paste isi file `database/supabase/01-schema.sql`
4. Klik **Run**
5. Tunggu semua tabel selesai dibuat

### 3. Insert Dummy Data

1. Di SQL Editor, buat query baru
2. Copy paste isi file `database/supabase/02-dummy-data.sql`  
3. Klik **Run**
4. Data dummy akan terisi

### 4. Dapatkan API Keys (PENTING!)

1. Di Supabase Dashboard, klik icon **⚙️ Settings** (gear) di sidebar
2. Klik **API** di submenu

Anda akan melihat:

```
┌─────────────────────────────────────────────────────────────┐
│ Project URL                                                 │
│ https://abcdefghij.supabase.co                             │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│ Project API keys                                            │
├─────────────────────────────────────────────────────────────┤
│ anon public                                                 │
│ eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJz...      │
│ [Copy button]                                               │
├─────────────────────────────────────────────────────────────┤
│ service_role  ⚠️ SECRET                                     │
│ eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJz...      │
│ [Reveal] [Copy button]                                      │
└─────────────────────────────────────────────────────────────┘
```

**Catat 3 nilai ini:**
- **Project URL**: `https://xxxxx.supabase.co`
- **anon key**: Key dengan label "anon public"
- **service_role key**: Klik "Reveal" untuk melihat, lalu copy

### 5. Konfigurasi .env

1. Copy file `.env.supabase` ke `.env`:
   ```bash
   cp .env.supabase .env
   ```

2. Edit `.env` dengan API keys dari Supabase:
   ```env
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost:8080/'

   # Supabase API Configuration
   supabase.url = 'https://YOUR_PROJECT_REF.supabase.co'
   supabase.anon_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6...(your anon key)'
   supabase.service_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6...(your service_role key)'
   ```

   > ⚠️ **PENTING:** Jangan pernah commit file `.env` ke git!
   > File ini berisi service_key yang memberi akses penuh ke database.

### 6. Install Dependencies & Run

```bash
# Install PHP dependencies
composer install

# Jalankan development server
php spark serve --port=8080
```

### 7. Akses Aplikasi

- URL: http://localhost:8080
- Login: `admin` / `admin123`

---

## Struktur Database

| Tabel | Keterangan |
|-------|------------|
| users | Data pengguna |
| data_umum_desa | Master data desa |
| ref_rekening | Referensi kode rekening |
| rpjmdesa | Rencana Pembangunan Jangka Menengah |
| rkpdesa | Rencana Kerja Pemerintah Desa |
| apbdes | Anggaran Pendapatan & Belanja Desa |
| spp | Surat Permintaan Pembayaran |
| bku | Buku Kas Umum |
| pajak | Pengelolaan Pajak |
| aset_desa | Inventaris Aset Desa |
| penduduk | Data Kependudukan |
| keluarga | Data Kartu Keluarga |
| gis_wilayah | Data GIS Wilayah |
| posyandu | Data Posyandu |
| balita | Data Balita |
| bumdes | BUM Desa |
| proyek_pembangunan | Proyek Pembangunan |
| progress_proyek | Progress Proyek |
| activity_logs | Log Aktivitas |

---

## Troubleshooting

### Error: SQLSTATE connection refused
- Pastikan password Supabase sudah benar
- Cek apakah IP Anda diblock di Supabase (Project Settings > Database > Network)

### Error: SSL required
- Tambahkan `database.default.sslmode = 'require'` di .env

### Data hilang setelah restart
- Supabase menyimpan data secara persistent
- Cek di Table Editor apakah data ada

---

## Migrasi dari MySQL ke PostgreSQL

Jika sebelumnya menggunakan MySQL, perhatikan:

1. **ENUM** → Menggunakan CHECK constraint
2. **AUTO_INCREMENT** → Menggunakan SERIAL
3. **YEAR** → Menggunakan INTEGER
4. **TINYINT(1)** → Menggunakan BOOLEAN
5. **ON DUPLICATE KEY UPDATE** → Menggunakan ON CONFLICT

---

## Backup Database

Di Supabase Dashboard:
1. **Settings** > **Database**
2. Scroll ke **Backups**
3. Download backup kapan saja

Atau via SQL:
```sql
-- Export via pgdump
pg_dump -h db.xxx.supabase.co -U postgres -d postgres > backup.sql
```

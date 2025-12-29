# 📋 DEPLOYMENT READINESS CHECKLIST - SIKADES LITE
**Tanggal Analisis:** 29 Desember 2025  
**Target Platform:** Google Cloud Run  
**Status:** 🟡 **HAMPIR SIAP** (Perlu beberapa penyesuaian)

---

## ✅ KESIAPAN TEKNIS

### ✅ 1. Kode Aplikasi
- [x] **CodeIgniter 4.6.3** - Framework stabil dan production-ready
- [x] **PHP 8.2** - Versi modern dengan performa optimal
- [x] **No TODO/FIXME** - Tidak ada kode yang belum selesai
- [x] **Error Handling** - Sudah ada error handling yang proper
- [x] **Validation** - Form validation sudah diimplementasi
- [x] **Security** - CSRF protection, XSS filtering, password hashing

**Status:** ✅ **READY** - Kode aplikasi production-ready!

---

### ✅ 2. Docker & Containerization
- [x] **Dockerfile.cloudrun** - Sudah ada dan sudah dikonfigurasi
- [x] **PHP Extensions** - Semua extension yang diperlukan (pdo_pgsql, gd, zip, dll)
- [x] **Apache Config** - Document root, mod_rewrite, .htaccess support
- [x] **Port Configuration** - Dynamic PORT untuk Cloud Run
- [x] **Writable Directories** - Permissions sudah diatur
- [x] **Composer Install** - `--no-dev --optimize-autoloader`

**Status:** ✅ **READY** - Docker configuration optimal!

---

### ✅ 3. Database
- [x] **PostgreSQL Support** - Sudah menggunakan PDO PostgreSQL
- [x] **Supabase Compatible** - Connection pooler ready
- [x] **Schema Complete** - Semua tabel sudah didefinisikan
- [x] **Migrations** - Schema migrations tersedia
- [x] **Session Storage** - Database session handler configured

**Status:** ✅ **READY** - Database configuration siap!

---

### ✅ 4. Deployment Documentation
- [x] **DEPLOY_CLOUDRUN.md** - Panduan lengkap deployment
- [x] **.env.cloudrun.template** - Template environment variables
- [x] **Step-by-step Guide** - Instruksi yang jelas dan detail
- [x] **Troubleshooting** - Common issues dan solusinya

**Status:** ✅ **READY** - Dokumentasi lengkap!

---

## 🟡 YANG PERLU DISIAPKAN SEBELUM DEPLOY

### 🔧 1. Environment Setup (30 menit)

#### A. Google Cloud Setup
```bash
# Install Google Cloud CLI jika belum:
# https://cloud.google.com/sdk/docs/install

# Login dan setup project
gcloud auth login
gcloud config set project YOUR_PROJECT_ID
gcloud services enable run.googleapis.com
gcloud services enable cloudbuild.googleapis.com
gcloud services enable secretmanager.googleapis.com
```

#### B. Supabase Database Setup
1. **Buat project Supabase** (jika belum):
   - Visit: https://supabase.com
   - Create project
   - Catat: hostname, database, username, password

2. **Run Database Migrations**:
   ```bash
   # Connect ke Supabase dan run schema
   # Bisa menggunakan Supabase SQL Editor atau pgAdmin
   ```

3. **Allow Cloud Run IP**:
   - Supabase → Settings → Database → Connection Pooling
   - Allow connections from GCP (biasanya auto-allow)

#### C. Secrets Management
```bash
# Store sensitive data di Secret Manager
echo -n "YOUR_SUPABASE_PASSWORD" | gcloud secrets create db-password --data-file=-
echo -n "$(openssl rand -base64 32)" | gcloud secrets create app-key --data-file=-
```

**Status:** ⏳ **TODO** - Perlu dilakukan setup

---

### 🔧 2. Configuration Files (15 menit)

#### A. Update .env untuk Production
File sudah ada (`.env.cloudrun.template`), tapi perlu update values:

```env
# Update these values:
app.baseURL=https://YOUR-SERVICE-xxxxx.a.run.app  # Akan dapat setelah deploy
database.default.hostname=aws-0-ap-southeast-1.pooler.supabase.com
database.default.username=postgres.YOUR_PROJECT_REF  # Dari Supabase
database.default.password=YOUR_SUPABASE_PASSWORD     # Dari Supabase
```

#### B. Verify Docker Build Locally (Optional)
```bash
# Test build locally dulu
docker build -f Dockerfile.cloudrun -t sikades-lite:test .
docker run -p 8080:8080 sikades-lite:test
```

**Status:** ⏳ **TODO** - Perlu update credentials

---

### 🔧 3. Data Migration (1-2 jam)

#### A. Database Schema
```sql
-- Run semua SQL files di Supabase:
-- 1. database/migrations/*.sql
-- 2. database/dummy-data/*.sql (optional, untuk testing)
```

#### B. Initial Data
- [ ] **Master Users** - Admin, Operator, Kepala Desa
- [ ] **Data Desa** - Informasi desa
- [ ] **Rekening (Chart of Accounts)** - 43 entries
- [ ] **Dummy Transactions** (optional untuk testing)

**Status:** ⏳ **TODO** - Perlu run migrations

---

## 🎯 DEPLOYMENT STEPS

### Step 1: Build & Deploy (5-10 menit)
```bash
# Deploy ke Cloud Run
gcloud run deploy sikades-lite \
  --source . \
  --platform managed \
  --region asia-southeast1 \
  --allow-unauthenticated \
  --set-env-vars="CI_ENVIRONMENT=production" \
  --set-env-vars="database.default.hostname=YOUR_SUPABASE_HOST" \
  --set-env-vars="database.default.database=postgres" \
  --set-env-vars="database.default.username=YOUR_USERNAME" \
  --set-env-vars="database.default.DBDriver=Postgre" \
  --set-env-vars="database.default.port=6543" \
  --set-secrets="database.default.password=db-password:latest" \
  --memory=512Mi \
  --cpu=1 \
  --min-instances=0 \
  --max-instances=10
```

**Output yang didapat:**
- Service URL: `https://sikades-lite-xxxxx-uc.a.run.app`
- Build logs
- Deployment status

### Step 2: Update Base URL (2 menit)
```bash
# Update dengan URL yang didapat dari step 1
gcloud run services update sikades-lite \
  --set-env-vars="app.baseURL=https://sikades-lite-xxxxx.a.run.app"
```

### Step 3: Verification (5 menit)
```bash
# Test endpoints
curl https://sikades-lite-xxxxx.a.run.app
curl https://sikades-lite-xxxxx.a.run.app/login

# Login dan test features
```

---

## 🔍 PRE-DEPLOYMENT CHECKLIST

### Critical Checks:
- [ ] ✅ **Kode sudah di-commit** ke Git repository
- [ ] ⏳ **Google Cloud Project** sudah dibuat
- [ ] ⏳ **Supabase Database** sudah setup
- [ ] ⏳ **Database migrations** sudah dijalankan
- [ ] ⏳ **Secrets** sudah disimpan di Secret Manager
- [ ] ⏳ **Local Docker build** sudah di-test (optional)
- [ ] ⏳ **Backup data** lokal (jika ada data penting)

### Environment Variables:
- [ ] ⏳ `CI_ENVIRONMENT=production`
- [ ] ⏳ `app.baseURL` (akan di-update setelah deploy)
- [ ] ⏳ `database.default.*` configuration
- [ ] ⏳ Session handler configuration
- [ ] ⏳ CSRF settings

### Security Checks:
- [x] ✅ **CSRF Protection** enabled
- [x] ✅ **XSS Filtering** enabled
- [x] ✅ **Password Hashing** (bcrypt)
- [x] ✅ **HTTPS Only** (Cloud Run default)
- [x] ✅ **Session Security** configured
- [ ] ⏳ **Database credentials** di Secret Manager (bukan hardcoded)

---

## 💰 ESTIMASI BIAYA

### Google Cloud Run (Pay-per-use):

| Component | Free Tier | Estimasi Usage | Biaya/Bulan |
|-----------|-----------|----------------|-------------|
| CPU | 180,000 vCPU-seconds/bulan | ~50,000 vCPU-seconds | **GRATIS** |
| Memory | 360,000 GiB-seconds/bulan | ~100,000 GiB-seconds | **GRATIS** |
| Requests | 2 juta requests/bulan | ~50,000 requests | **GRATIS** |
| **TOTAL** | | | **$0 - $2/bulan** |

### Supabase Database:

| Tier | Storage | Transfer | Biaya/Bulan |
|------|---------|----------|-------------|
| Free | 500 MB | 2 GB | **GRATIS** |
| Pro | 8 GB | 50 GB | **$25/bulan** |

**Rekomendasi untuk Start:**
- ✅ Cloud Run: **FREE TIER** cukup untuk low-traffic village app
- ✅ Supabase: **FREE TIER** cukup untuk 1-2 desa (upgrade jika perlu)

**Total Estimasi:** **GRATIS** sampai **$2/bulan** (kemungkinan besar GRATIS)

---

## 🚨 POTENTIAL ISSUES & SOLUTIONS

### 1. Connection Refused ke Database
**Cause:** IP Cloud Run tidak allowed di Supabase  
**Solution:**
- Gunakan connection pooler: `pooler.supabase.com:6543`
- Allow all IPs di Supabase (Settings → Database)

### 2. 502 Bad Gateway
**Cause:** Application error atau timeout  
**Solution:**
```bash
# Check logs
gcloud run logs read --service sikades-lite --limit 50

# Common fixes:
# - Increase memory (--memory=1Gi)
# - Check database connection
# - Verify environment variables
```

### 3. Session Tidak Persistent
**Cause:** Cloud Run adalah stateless  
**Solution:**
- ✅ Sudah configured: Session disimpan di database (ci_sessions table)
- Pastikan table `ci_sessions` ada di database

### 4. File Upload Issues
**Cause:** Writable directory permissions  
**Solution:**
- ✅ Sudah handled di Dockerfile
- File uploads disimpan di database atau Cloud Storage (recommended)

---

## 📊 MONITORING & MAINTENANCE

### After Deployment:

#### 1. Monitoring Dashboard
- **Cloud Run Metrics:** CPU, Memory, Request count, Latency
- **Cloud Logging:** Application logs, Error logs
- **Uptime Monitoring:** Setup alerts untuk downtime

#### 2. Regular Maintenance
- [ ] **Database Backup** - Setup automated backup di Supabase
- [ ] **Update Dependencies** - `composer update` (monthly)
- [ ] **Security Patches** - Monitor CVE alerts
- [ ] **Performance Review** - Check metrics (monthly)

#### 3. Cost Monitoring
```bash
# Check current usage
gcloud run services describe sikades-lite --region=asia-southeast1

# View billing
# GCP Console → Billing → Reports
```

---

## ✅ FINAL VERDICT

### STATUS: 🟡 **SIAP 90%** - Tinggal Setup External Dependencies

### Yang Sudah READY:
✅ **Kode Aplikasi** - 100% production-ready  
✅ **Docker Configuration** - Optimal untuk Cloud Run  
✅ **Database Schema** - Complete dengan migrations  
✅ **Documentation** - Lengkap dan detail  
✅ **Security** - Proper implementation  

### Yang Perlu Dilakukan (2-3 jam):
⏳ **Google Cloud Setup** (30 menit)  
⏳ **Supabase Database** (1 jam - create project + run migrations)  
⏳ **Secrets Configuration** (15 menit)  
⏳ **Deployment Execution** (15-30 menit)  
⏳ **Testing & Verification** (30 menit)  

---

## 🎯 NEXT ACTION ITEMS

### Immediate Next Steps:

1. **Setup Google Cloud** (30 min)
   ```bash
   gcloud auth login
   gcloud config set project YOUR_PROJECT_ID
   gcloud services enable run.googleapis.com cloudbuild.googleapis.com secretmanager.googleapis.com
   ```

2. **Setup Supabase** (60 min)
   - Create Supabase project
   - Run database migrations
   - Copy connection credentials

3. **Configure Secrets** (15 min)
   ```bash
   echo -n "PASSWORD" | gcloud secrets create db-password --data-file=-
   ```

4. **Deploy** (15-30 min)
   ```bash
   # Run deployment command (lihat Step 1 di atas)
   ```

5. **Test & Verify** (30 min)
   - Test login
   - Test all modules
   - Check performance

---

## 🎊 KESIMPULAN

**APLIKASI SIKADES-LITE SUDAH 90% SIAP UNTUK PUBLISH!**

### Yang Perlu Kamu Lakukan:
1. ✅ **Buat Google Cloud Project** (gratis untuk start)
2. ✅ **Setup Supabase Database** (free tier available)
3. ✅ **Ikuti deployment steps** di `DEPLOY_CLOUDRUN.md`
4. ✅ **Total waktu:** 2-3 jam untuk first deployment

### Setelah Deploy:
- ✅ Aplikasi akan running 24/7
- ✅ Auto-scaling berdasarkan traffic
- ✅ HTTPS by default
- ✅ Monitoring built-in
- ✅ **Kemungkinan besar GRATIS** (dengan free tier)

---

**Rekomendasi:** 
🚀 **READY TO DEPLOY!** - Lakukan setup external dependencies (GCP + Supabase), lalu deploy sesuai guide di `DEPLOY_CLOUDRUN.md`.

**Estimasi Waktu Total:** 2-3 jam dari sekarang sampai live di production!

---

**Dibuat:** 29 Desember 2025  
**Status:** 🟡 **90% READY** - Tinggal setup infrastructure  
**Next:** Follow deployment guide di `docs/DEPLOY_CLOUDRUN.md`

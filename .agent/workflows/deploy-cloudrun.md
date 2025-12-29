---
description: Deploy Sikades Lite to Google Cloud Run
---

# Deploy Sikades Lite ke Google Cloud Run

Workflow ini akan memandu Anda melakukan deployment aplikasi Sikades Lite ke Google Cloud Run.

## Prerequisites

- Google Cloud account dengan billing enabled
- Supabase account dan project
- Google Cloud CLI (gcloud) terinstall
- Git repository sudah ready

## Phase 1: Google Cloud Setup (30 menit)

### 1.1 Install Google Cloud CLI (jika belum)
Windows:
```powershell
# Download installer dari:
# https://cloud.google.com/sdk/docs/install

# Atau gunakan Chocolatey:
choco install gcloudsdk
```

### 1.2 Login dan Initialize
```powershell
# Login ke Google Cloud
gcloud auth login

# List projects atau buat baru
gcloud projects list

# Buat project baru (jika belum ada)
gcloud projects create sikades-lite-prod --name="Sikades Lite Production"

# Set active project
gcloud config set project sikades-lite-prod

# Set default region
gcloud config set run/region asia-southeast1
```

### 1.3 Enable Required APIs
```powershell
# Enable Cloud Run
gcloud services enable run.googleapis.com

# Enable Cloud Build
gcloud services enable cloudbuild.googleapis.com

# Enable Secret Manager
gcloud services enable secretmanager.googleapis.com

# Enable Container Registry
gcloud services enable containerregistry.googleapis.com
```

### 1.4 Setup Billing (PENTING!)
```powershell
# List billing accounts
gcloud billing accounts list

# Link billing account to project
gcloud billing projects link sikades-lite-prod --billing-account=BILLING_ACCOUNT_ID
```

## Phase 2: Supabase Database Setup (60 menit)

### 2.1 Create Supabase Project
1. Visit https://supabase.com
2. Click "New Project"
3. Isi details:
   - Name: `sikades-lite-prod`
   - Database Password: (buat password yang kuat, SIMPAN!)
   - Region: `Southeast Asia (Singapore)` atau terdekat
4. Wait ~2 minutes untuk provisioning

### 2.2 Get Connection Details
1. Supabase Dashboard → Settings → Database
2. Catat informasi berikut:
   - **Host:** `aws-0-ap-southeast-1.pooler.supabase.com`
   - **Database:** `postgres`
   - **Port:** `6543`
   - **User:** `postgres.[PROJECT_REF]` (contoh: `postgres.abcdefghijk`)
   - **Password:** (yang Anda buat tadi)

### 2.3 Run Database Migrations
1. Buka Supabase SQL Editor (Dashboard → SQL Editor)
2. Copy-paste dan jalankan SQL files secara berurutan:

**File 1: Create Tables**
```sql
-- Copy semua dari: database/migrations/2024-12-05-000000_create_initial_tables.php
-- Atau dari: docker/mysql/01-init.sql (convert ke PostgreSQL jika perlu)
```

**File 2: Insert Master Data**
```sql
-- Insert default users
INSERT INTO users (username, password, role, nama, email) VALUES
('admin', '$2y$10$...', 'admin', 'Administrator', 'admin@sikades.local'),
('operator', '$2y$10$...', 'operator', 'Operator Desa', 'operator@sikades.local'),
('kades', '$2y$10$...', 'kades', 'Kepala Desa', 'kades@sikades.local');

-- Insert chart of accounts
-- Copy dari: database/dummy-data/02-dummy-rekening.sql
```

### 2.4 Verify Database
```sql
-- Check tables
SELECT table_name FROM information_schema.tables 
WHERE table_schema = 'public';

-- Check users
SELECT id, username, role, nama FROM users;

-- Expected output:
-- 3 users (admin, operator, kades)
-- ~10 tables
```

## Phase 3: Configure Secrets (15 menit)

### 3.1 Create Database Password Secret
```powershell
# Create secret untuk database password
$DB_PASSWORD = "YOUR_SUPABASE_PASSWORD"
echo -n $DB_PASSWORD | gcloud secrets create db-password --data-file=-

# Verify
gcloud secrets versions access latest --secret="db-password"
```

### 3.2 Create App Encryption Key
```powershell
# Generate random key untuk CodeIgniter encryption
$APP_KEY = -join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | ForEach-Object {[char]$_})
echo -n $APP_KEY | gcloud secrets create app-encryption-key --data-file=-

# Verify
gcloud secrets versions access latest --secret="app-encryption-key"
```

### 3.3 Grant Cloud Run Access to Secrets
```powershell
# Get Cloud Run service account
$PROJECT_NUMBER = gcloud projects describe sikades-lite-prod --format="value(projectNumber)"
$SERVICE_ACCOUNT = "${PROJECT_NUMBER}-compute@developer.gserviceaccount.com"

# Grant access to db-password
gcloud secrets add-iam-policy-binding db-password `
  --member="serviceAccount:${SERVICE_ACCOUNT}" `
  --role="roles/secretmanager.secretAccessor"

# Grant access to app-encryption-key
gcloud secrets add-iam-policy-binding app-encryption-key `
  --member="serviceAccount:${SERVICE_ACCOUNT}" `
  --role="roles/secretmanager.secretAccessor"
```

## Phase 4: Prepare Deployment (15 menit)

### 4.1 Update Environment Configuration
Buat file `.env.production` dengan values production:

```env
# Created from template
CI_ENVIRONMENT=production

# Will be updated after first deployment
app.baseURL=https://sikades-lite-[RANDOM]-uc.a.run.app

# Supabase Database
database.default.hostname=aws-0-ap-southeast-1.pooler.supabase.com
database.default.database=postgres
database.default.username=postgres.YOUR_PROJECT_REF
database.default.DBDriver=Postgre
database.default.port=6543

# Session
app.sessionDriver=CodeIgniter\Session\Handlers\DatabaseHandler
app.sessionSavePath=ci_sessions

# Security
app.CSRFProtection=true
```

### 4.2 Test Docker Build Locally (Optional)
```powershell
# Build image
docker build -f Dockerfile.cloudrun -t sikades-lite:local .

# Run locally
docker run -p 8080:8080 `
  -e CI_ENVIRONMENT=production `
  -e "database.default.password=YOUR_DB_PASSWORD" `
  sikades-lite:local

# Test di browser: http://localhost:8080
```

## Phase 5: Deploy to Cloud Run (15-30 menit)

### 5.1 First Deployment
```powershell
# Deploy (ini akan build dan deploy sekaligus)
gcloud run deploy sikades-lite `
  --source . `
  --platform managed `
  --region asia-southeast1 `
  --allow-unauthenticated `
  --set-env-vars="CI_ENVIRONMENT=production,database.default.hostname=aws-0-ap-southeast-1.pooler.supabase.com,database.default.database=postgres,database.default.username=postgres.YOUR_PROJECT_REF,database.default.DBDriver=Postgre,database.default.port=6543,app.sessionDriver=CodeIgniter\Session\Handlers\DatabaseHandler,app.sessionSavePath=ci_sessions" `
  --set-secrets="database.default.password=db-password:latest" `
  --memory=512Mi `
  --cpu=1 `
  --min-instances=0 `
  --max-instances=10 `
  --timeout=300

# CATAT SERVICE URL yang muncul!
# Contoh: https://sikades-lite-abc123-uc.a.run.app
```

### 5.2 Update Base URL
```powershell
# Ganti YOUR_SERVICE_URL dengan URL yang didapat dari step 5.1
$SERVICE_URL = "https://sikades-lite-abc123-uc.a.run.app"

gcloud run services update sikades-lite `
  --region asia-southeast1 `
  --update-env-vars="app.baseURL=${SERVICE_URL}"
```

### 5.3 Set Custom Domain (Optional)
```powershell
# Jika punya domain custom
gcloud beta run domain-mappings create `
  --service sikades-lite `
  --domain your-domain.com `
  --region asia-southeast1

# Follow DNS configuration instructions
```

## Phase 6: Verification (30 menit)

### 6.1 Check Deployment Status
```powershell
# Get service info
gcloud run services describe sikades-lite --region=asia-southeast1

# Check logs
gcloud run services logs read sikades-lite --region=asia-southeast1 --limit=50
```

### 6.2 Test Endpoints
```powershell
# Get service URL
$SERVICE_URL = gcloud run services describe sikades-lite --region=asia-southeast1 --format="value(status.url)"

# Test homepage
curl $SERVICE_URL

# Test login page
curl "${SERVICE_URL}/login"
```

### 6.3 Manual Testing Checklist
Visit `$SERVICE_URL` in browser and test:

- [ ] Homepage loads
- [ ] Login page accessible
- [ ] Can login as admin (admin/admin123)
- [ ] Dashboard displays correctly
- [ ] Can create APBDes entry
- [ ] Can create SPP
- [ ] Can create BKU entry
- [ ] All charts render properly
- [ ] No console errors

### 6.4 Performance Check
```powershell
# Load test (simple)
for ($i=0; $i -lt 10; $i++) {
    Measure-Command { curl -s $SERVICE_URL }
}

# Should complete in < 1 second
```

## Phase 7: Post-Deployment Setup (15 menit)

### 7.1 Setup Monitoring
```powershell
# Create uptime check
gcloud monitoring uptime create sikades-lite-uptime `
  --resource-type=uptime-url `
  --display-name="Sikades Lite Uptime" `
  --monitoring-url="${SERVICE_URL}/login"

# Create alert policy (optional)
```

### 7.2 Setup Automated Backups
Di Supabase Dashboard:
1. Settings → Database → Backups
2. Enable Point-in-Time Recovery (PITR) - jika pakai Pro tier
3. Or setup manual backup schedule

### 7.3 Document Production Credentials
Update `CREDENTIALS.md` dengan:
- Production URL
- Supabase connection details
- Google Cloud project info
- Any custom domain info

## Troubleshooting

### Error: "Permission denied"
```powershell
# Grant permissions
gcloud projects add-iam-policy-binding sikades-lite-prod `
  --member="user:YOUR_EMAIL" `
  --role="roles/run.admin"
```

### Error: "Connection refused" to database
1. Check Supabase connection pooler: port 6543, not 5432
2. Verify credentials in secrets
3. Check Supabase firewall settings

### Error: "502 Bad Gateway"
```powershell
# Check logs
gcloud run services logs read sikades-lite --limit=100

# Common fixes:
# 1. Increase memory: --memory=1Gi
# 2. Check database connection
# 3. Verify all environment variables
```

### Application errors
```powershell
# Stream logs in real-time
gcloud run services logs tail sikades-lite --region=asia-southeast1

# Look for PHP errors, database connection issues
```

## Rollback Procedure

### If deployment fails:
```powershell
# List revisions
gcloud run revisions list --service=sikades-lite --region=asia-southeast1

# Rollback to previous revision
gcloud run services update-traffic sikades-lite `
  --region=asia-southeast1 `
  --to-revisions=sikades-lite-00001-xxx=100
```

## Update/Redeploy

### For code updates:
```powershell
# Simply re-run deployment command
gcloud run deploy sikades-lite --source . --region asia-southeast1

# Cloud Run will create new revision automatically
```

## Success Criteria

✅ Deployment successful jika:
- [ ] Service URL accessible
- [ ] Login works
- [ ] All modules functional
- [ ] No critical errors in logs
- [ ] Response time < 2s
- [ ] Database connection stable

## Next Steps After Deployment

1. **Monitor** performance selama 24 jam pertama
2. **Update** documentation dengan production URLs
3. **Notify** users tentang production URL
4. **Setup** regular maintenance schedule
5. **Review** costs dalam 1 minggu

---

**Estimated Total Time:** 2-3 hours  
**Cost:** $0-$5/month (likely FREE with free tiers)  
**Support:** Check `DEPLOYMENT_READINESS_CHECKLIST.md` for details

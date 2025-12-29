# 🚀 DEPLOYMENT TO GOOGLE CLOUD RUN - QUICK GUIDE
# Using EXISTING Supabase Database

## ✅ CURRENT STATUS
- ✅ Supabase Database: READY & CONFIGURED
- ✅ Project: tyhwxgggqzwjkbvgibut.supabase.co
- ⏳ Google Cloud CLI: Installing...

---

## 📋 EXISTING SUPABASE CREDENTIALS

Your application is ALREADY using Supabase with these credentials:

```
Project URL: https://tyhwxgggqzwjkbvgibut.supabase.co
Database: postgres
Username: postgres
Password: sikades@Jaya
Port: 5432 (currently using direct connection)
```

⚠️ **IMPORTANT:** For Cloud Run production, we need to use **Connection Pooler** (port 6543)

---

## 🔧 STEP 1: Wait for Google Cloud CLI Installation

Current status: ⏳ Installing...

**Action:** Wait for installer to complete, then RESTART PowerShell

**Verify installation:**
```powershell
# After restart
gcloud --version

# Should show: Google Cloud SDK 4xx.x.x
```

---

## 🔐 STEP 2: Login to Google Cloud (5 min)

```powershell
# Login (will open browser)
gcloud auth login

# You'll be redirected to browser to select Google account
# Click "Allow"
```

---

## 🏗️ STEP 3: Create/Select Project (5 min)

### Option A: Create New Project
```powershell
# Create project
gcloud projects create sikades-lite-prod --name="Sikades Lite Production"

# Set as active
gcloud config set project sikades-lite-prod

# Set region
gcloud config set run/region asia-southeast1
```

### Option B: Use Existing Project
```powershell
# List your projects
gcloud projects list

# Select project
gcloud config set project YOUR_PROJECT_ID
gcloud config set run/region asia-southeast1
```

---

## 🔌 STEP 4: Enable Required APIs (3 min)

```powershell
# Enable Cloud Run
gcloud services enable run.googleapis.com

# Enable Cloud Build
gcloud services enable cloudbuild.googleapis.com

# Enable Secret Manager
gcloud services enable secretmanager.googleapis.com

# Wait ~1-2 minutes for APIs to be enabled
```

---

## 🔐 STEP 5: Setup Billing (REQUIRED - 2 min)

```powershell
# List billing accounts
gcloud billing accounts list

# Link billing to project (REQUIRED even for free tier)
gcloud billing projects link sikades-lite-prod --billing-account=YOUR_BILLING_ACCOUNT_ID
```

⚠️ **Note:** Free tier available - likely $0 cost for village app

---

## 🗝️ STEP 6: Store Database Password in Secret Manager (2 min)

```powershell
# Store Supabase password
$DB_PASSWORD = "sikades@Jaya"
echo -n $DB_PASSWORD | gcloud secrets create db-password --data-file=-

# Verify
gcloud secrets versions access latest --secret="db-password"

# Grant Cloud Run access
$PROJECT_NUMBER = gcloud projects describe sikades-lite-prod --format="value(projectNumber)"
$SERVICE_ACCOUNT = "${PROJECT_NUMBER}-compute@developer.gserviceaccount.com"

gcloud secrets add-iam-policy-binding db-password --member="serviceAccount:${SERVICE_ACCOUNT}" --role="roles/secretmanager.secretAccessor"
```

---

## 🚀 STEP 7: DEPLOY TO CLOUD RUN! (10-15 min)

### Important: Get Supabase Connection Pooler URL

1. Go to Supabase Dashboard: https://supabase.com/dashboard
2. Select project: `tyhwxgggqzwjkbvgibut`
3. Settings → Database
4. Look for **"Connection Pooling"** section
5. Copy the hostname (should be like: `aws-0-ap-southeast-1.pooler.supabase.com`)

### Deploy Command

```powershell
# Change to project directory
cd f:\sikades-lite

# Set variables (REPLACE with your values)
$DB_HOST = "aws-0-ap-southeast-1.pooler.supabase.com"  # From Supabase
$DB_USER = "postgres.tyhwxgggqzwjkbvgibut"  # postgres.[PROJECT_REF]

# DEPLOY!
gcloud run deploy sikades-lite `
  --source . `
  --platform managed `
  --region asia-southeast1 `
  --allow-unauthenticated `
  --set-env-vars="CI_ENVIRONMENT=production" `
  --set-env-vars="database.default.hostname=$DB_HOST" `
  --set-env-vars="database.default.database=postgres" `
  --set-env-vars="database.default.username=$DB_USER" `
  --set-env-vars="database.default.DBDriver=Postgre" `
  --set-env-vars="database.default.port=6543" `
  --set-secrets="database.default.password=db-password:latest" `
  --memory=512Mi `
  --cpu=1 `
  --timeout=300 `
  --min-instances=0 `
  --max-instances=10

# This will:
# 1. Build Docker image (5-8 minutes)
# 2. Push to Container Registry
# 3. Deploy to Cloud Run
# 4. Return service URL
```

**SAVE THE SERVICE URL!**
Example: `https://sikades-lite-abc123-uc.a.run.app`

---

## 🔗 STEP 8: Update Base URL (1 min)

```powershell
# Replace with YOUR service URL from Step 7
$SERVICE_URL = "https://sikades-lite-abc123-uc.a.run.app"

# Update configuration
gcloud run services update sikades-lite `
  --region asia-southeast1 `
  --update-env-vars="app.baseURL=$SERVICE_URL"
```

---

## ✅ STEP 9: Verify Deployment (5 min)

### Check Service Status
```powershell
# Get service details
gcloud run services describe sikades-lite --region=asia-southeast1

# Check recent logs
gcloud run services logs read sikades-lite --limit=20
```

### Test in Browser
1. Open service URL in browser
2. Should see Sikades-Lite homepage
3. Click "Login"
4. Login with: `admin` / `admin123`
5. Test features

### Monitor Logs
```powershell
# Stream real-time logs
gcloud run services logs tail sikades-lite --region=asia-southeast1
```

---

## 🎉 SUCCESS CRITERIA

✅ Deployment successful if:
- [ ] gcloud command completes without errors
- [ ] Service URL is accessible
- [ ] Homepage loads correctly
- [ ] Can login as admin
- [ ] Dashboard displays data
- [ ] No 502/500 errors
- [ ] Logs show successful database connection

---

## 🆘 TROUBLESHOOTING

### Error: "Connection refused to database"
```powershell
# Verify you're using connection pooler (port 6543)
# Check Supabase Settings → Database → Connection Pooling
# Make sure URL is: xxx.pooler.supabase.com (not direct connection)
```

### Error: "502 Bad Gateway"
```powershell
# Check logs
gcloud run services logs read sikades-lite --limit=50

# Common fix: Increase memory
gcloud run services update sikades-lite --memory=1Gi --region=asia-southeast1
```

### Database Connection Issues
```powershell
# Verify password in secret
gcloud secrets versions access latest --secret="db-password"

# Should output: sikades@Jaya
```

### Build Errors
```powershell
# Check Cloud Build logs
gcloud builds list --limit=5

# View specific build log
gcloud builds log BUILD_ID
```

---

## 📊 SUMMARY

**Current Setup:**
- ✅ Database: Supabase PostgreSQL (READY)
- ✅ Application: Sikades-Lite (READY)
- ⏳ Google Cloud CLI: Installing
- 🔜 Cloud Run: To be deployed

**Estimated Time Remaining:**
- Wait for gcloud install: ~5 min
- Google Cloud setup: ~10 min
- Deployment: ~15 min
- Testing: ~5 min
**Total: ~35 minutes**

**Cost Estimate:**
- Cloud Run: $0-$2/month (likely FREE)
- Supabase: Already using (FREE or existing plan)
**Total: $0-$2/month**

---

## 🔜 NEXT ACTION

**WAIT for Google Cloud CLI installation to complete**

Then:
1. RESTART PowerShell
2. Run: `gcloud --version`
3. Follow steps above from Step 2

---

**Status:** ⏳ Waiting for gcloud installation  
**Time:** ~10 minutes to deployment ready  
**You're almost there! 🚀**

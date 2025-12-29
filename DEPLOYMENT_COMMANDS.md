# 🚀 DEPLOYMENT COMMANDS - Ready to Execute
# Simplified deployment steps for Sikades-Lite

## 📌 CURRENT STATUS
- ✅ Google Cloud CLI: Installed (v550.0.0)
- ⏳ Authentication: In progress (login in browser)
- ✅ Supabase Database: Ready (tyhwxgggqzwjkbvgibut.supabase.co)

---

## 🔐 STEP 1: Authentication (DOING NOW)

Browser should open automatically.
1. Select your Google account
2. Click "Allow" to give gcloud access
3. Return here after success

---

## 🏗️ STEP 2: Create/Select Project (2 minutes)

### List existing projects:
```powershell
gcloud projects list
```

### Create NEW project:
```powershell
# Replace sikades-lite-prod with your preferred project ID
gcloud projects create sikades-lite-prod --name="Sikades Lite Production"

# Set as active project
gcloud config set project sikades-lite-prod
```

### OR use existing project:
```powershell
# Set existing project as active
gcloud config set project YOUR_EXISTING_PROJECT_ID
```

### Set default region:
```powershell
gcloud config set run/region asia-southeast1
```

---

## 🔌 STEP 3: Enable Required APIs (3 minutes)

```powershell
# Enable Cloud Run
gcloud services enable run.googleapis.com

# Enable Cloud Build
gcloud services enable cloudbuild.googleapis.com

# Enable Secret Manager
gcloud services enable secretmanager.googleapis.com

# Wait ~1-2 minutes for APIs to activate
```

---

## 💳 STEP 4: Link Billing Account (REQUIRED - 2 minutes)

```powershell
# List your billing accounts
gcloud billing accounts list

# Link billing to project (COPY BILLING_ACCOUNT_ID from above)
gcloud billing projects link sikades-lite-prod --billing-account=BILLING_ACCOUNT_ID

# Example:
# gcloud billing projects link sikades-lite-prod --billing-account=01234A-567B8C-9DEF01
```

⚠️ **Note:** Even with free tier, billing account must be linked. No charges for small usage.

---

## 🗝️ STEP 5: Store Database Password (2 minutes)

### Get Supabase Connection Pooler Info:
1. Go to: https://supabase.com/dashboard
2. Select project: tyhwxgggqzwjkbvgibut
3. Settings → Database → Connection Info
4. Under "Connection Pooling", copy:
   - **Host:** Should be like `aws-0-ap-southeast-1.pooler.supabase.com`
   - **User:** Should be like `postgres.tyhwxgggqzwjkbvgibut`

### Store password in Secret Manager:
```powershell
# Your Supabase password
$DB_PASSWORD = "sikades@Jaya"

# Create secret
echo -n $DB_PASSWORD | gcloud secrets create db-password --data-file=-

# Verify
gcloud secrets versions access latest --secret="db-password"
```

### Grant Cloud Run access to secret:
```powershell
# Get project number
$PROJECT_NUMBER = gcloud projects describe sikades-lite-prod --format="value(projectNumber)"

# Service account
$SERVICE_ACCOUNT = "${PROJECT_NUMBER}-compute@developer.gserviceaccount.com"

# Grant access
gcloud secrets add-iam-policy-binding db-password `
  --member="serviceAccount:${SERVICE_ACCOUNT}" `
  --role="roles/secretmanager.secretAccessor"
```

---

## 🚀 STEP 6: DEPLOY TO CLOUD RUN! (10-15 minutes)

### Set deployment variables:
```powershell
# Get these from Supabase Dashboard → Settings → Database → Connection Pooling
$DB_HOST = "aws-0-ap-southeast-1.pooler.supabase.com"
$DB_USER = "postgres.tyhwxgggqzwjkbvgibut"  # postgres.[YOUR_PROJECT_REF]

# Verify values
Write-Host "DB Host: $DB_HOST"
Write-Host "DB User: $DB_USER"
```

### Deploy command:
```powershell
# Make sure you're in project directory
cd f:\sikades-lite

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
  --set-env-vars="app.sessionDriver=CodeIgniter\Session\Handlers\DatabaseHandler" `
  --set-env-vars="app.sessionSavePath=ci_sessions" `
  --set-secrets="database.default.password=db-password:latest" `
  --memory=512Mi `
  --cpu=1 `
  --timeout=300 `
  --min-instances=0 `
  --max-instances=10

# This will take 10-15 minutes:
# 1. Upload source code
# 2. Build Docker image with Cloud Build
# 3. Deploy to Cloud Run
# 4. Return service URL
```

**⚠️ SAVE THE SERVICE URL from the output!**

Example output:
```
Service [sikades-lite] revision [sikades-lite-00001-abc] has been deployed 
and is serving 100 percent of traffic.
Service URL: https://sikades-lite-abc123-uc.a.run.app
```

---

## 🔗 STEP 7: Update Base URL (1 minute)

```powershell
# Replace with YOUR actual service URL from Step 6
$SERVICE_URL = "https://sikades-lite-abc123-uc.a.run.app"

# Update app configuration
gcloud run services update sikades-lite `
  --region asia-southeast1 `
  --update-env-vars="app.baseURL=$SERVICE_URL"
```

---

## ✅ STEP 8: Verify Deployment (5 minutes)

### Check service status:
```powershell
# Get service info
gcloud run services describe sikades-lite --region=asia-southeast1

# Get service URL
gcloud run services describe sikades-lite --region=asia-southeast1 --format="value(status.url)"
```

### Check logs:
```powershell
# Recent logs
gcloud run services logs read sikades-lite --region=asia-southeast1 --limit=20

# Stream real-time logs
gcloud run services logs tail sikades-lite --region=asia-southeast1
```

### Test in browser:
1. Open service URL in browser
2. Should see Sikades-Lite homepage
3. Test login: admin / admin123
4. Check dashboard loads
5. Test creating APBDes entry

---

## 🎉 SUCCESS CRITERIA

Deployment successful if:
- [x] gcloud deploy completes without errors
- [x] Service URL is accessible
- [x] Homepage loads correctly
- [x] Can login with admin/admin123
- [x] Dashboard displays data
- [x] Database connection working
- [x] No errors in logs

---

## 🆘 COMMON ISSUES

### Issue: "Billing account required"
```powershell
# Link billing account first
gcloud billing projects link sikades-lite-prod --billing-account=YOUR_BILLING_ID
```

### Issue: "API not enabled"
```powershell
# Enable required APIs
gcloud services enable run.googleapis.com cloudbuild.googleapis.com
```

### Issue: "Permission denied"
```powershell
# Grant yourself necessary roles
gcloud projects add-iam-policy-binding sikades-lite-prod `
  --member="user:YOUR_EMAIL@gmail.com" `
  --role="roles/run.admin"
```

### Issue: "502 Bad Gateway" after deployment
```powershell
# Check logs for errors
gcloud run services logs read sikades-lite --limit=50

# Common fix: Increase memory
gcloud run services update sikades-lite --memory=1Gi --region=asia-southeast1
```

---

## 📊 DEPLOYMENT TIMELINE

| Step | Task | Time |
|------|------|------|
| 1 | ✅ Authentication | 1 min |
| 2 | Create/select project | 2 min |
| 3 | Enable APIs | 3 min |
| 4 | Link billing | 2 min |
| 5 | Store secrets | 2 min |
| 6 | 🚀 **Deploy!** | 10-15 min |
| 7 | Update base URL | 1 min |
| 8 | Verify & test | 5 min |

**Total: ~30-35 minutes**

---

## 🔜 NEXT STEP

**WAIT for Step 1 (authentication) to complete in browser**

Then follow steps 2-8 in order.

---

**You're doing great! Almost there! 🚀**

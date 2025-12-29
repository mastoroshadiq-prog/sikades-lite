# 🚀 FINAL DEPLOYMENT COMMAND - READY TO EXECUTE!

## ✅ PRE-DEPLOYMENT CHECKLIST
- ✅ Google Cloud CLI: Installed & authenticated
- ✅ Project: smartai-479606
- ✅ Region: asia-southeast1
- ✅ APIs: Enabled (Cloud Run, Cloud Build, Secret Manager)
- ✅ Billing: Linked
- ✅ Secret: db-password created
- ✅ Permissions: Granted to Cloud Run service account

---

## ⚠️ IMPORTANT: Get Supabase Connection Pooler Info

Before deploying, you MUST get these from Supabase:

### Steps:
1. Go to: https://supabase.com/dashboard
2. Login
3. Select project: **tyhwxgggqzwjkbvgibut**
4. Go to: **Settings → Database**
5. Scroll to **"Connection Pooling"** section (NOT "Connection string")
6. Copy these values:

```
Host: _________________________ (should end with .pooler.supabase.com)
User: _________________________ (should be postgres.tyhwxgggqzwjkbvgibut)
Port: 6543 (NOT 5432!)
```

**Example:**
```
Host: aws-0-ap-southeast-1.pooler.supabase.com
User: postgres.tyhwxgggqzwjkbvgibut
Port: 6543
```

---

## 🚀 DEPLOYMENT COMMAND

### Step 1: Set Variables (REPLACE with your Supabase values)

```powershell
# From Supabase Connection Pooling section
$DB_HOST = "aws-0-ap-southeast-1.pooler.supabase.com"  # REPLACE!
$DB_USER = "postgres.tyhwxgggqzwjkbvgibut"  # REPLACE!

# Verify values
Write-Host "Database Host: $DB_HOST"
Write-Host "Database User: $DB_USER"
Write-Host "Database Port: 6543"
Write-Host "Database Name: postgres"
```

### Step 2: Deploy to Cloud Run

```powershell
# Make sure you're in project directory
cd f:\sikades-lite

# Deploy! (This will take 10-15 minutes)
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

# Wait for deployment to complete...
# Save the SERVICE URL from the output!
```

### Step 3: Update Base URL

```powershell
# REPLACE with YOUR service URL from Step 2
$SERVICE_URL = "https://sikades-lite-xxxxxxxx-uc.a.run.app"

# Update configuration
gcloud run services update sikades-lite `
  --region asia-southeast1 `
  --update-env-vars="app.baseURL=$SERVICE_URL"
```

---

## 📊 DEPLOYMENT PROGRESS

The deployment will show progress like this:

```
Building using Dockerfile and deploying container to Cloud Run service [sikades-lite]
✓ Creating Container Repository...
✓ Uploading sources... 
✓ Building Container... (this takes ~10 minutes)
✓ Pushing Container to Registry...
✓ Deploying Container to Cloud Run...
✓ Setting traffic...

Service [sikades-lite] revision [sikades-lite-00001-abc] has been deployed
Service URL: https://sikades-lite-xxxxxxxx-uc.a.run.app
```

---

## ✅ VERIFY DEPLOYMENT

### Check Service
```powershell
gcloud run services describe sikades-lite --region=asia-southeast1
```

### Check Logs
```powershell
gcloud run services logs read sikades-lite --region=asia-southeast1 --limit=20
```

### Test in Browser
1. Open the SERVICE URL
2. Should see Sikades-Lite homepage
3. Click "Login"
4. Login: admin / admin123
5. Test all features

---

## 🎉 SUCCESS!

If deployment is successful:
- ✅ SERVICE URL accessible
- ✅ Homepage loads
- ✅ Login works
- ✅ Dashboard displays data
- ✅ Can create/edit records

Your Sikades-Lite is now LIVE in production! 🚀

---

**Next:** Get Supabase connection pooler info, then run deployment command!

# Deploy Sikades Lite ke Google Cloud Run

## Prerequisites

1. **Google Cloud Account** dengan billing enabled
2. **Google Cloud CLI (gcloud)** terinstall
3. **Supabase Database** sudah dikonfigurasi

## Langkah-langkah Deploy

### 1. Setup Google Cloud

```bash
# Login ke Google Cloud
gcloud auth login

# Set project (ganti YOUR_PROJECT_ID)
gcloud config set project YOUR_PROJECT_ID

# Enable APIs
gcloud services enable run.googleapis.com
gcloud services enable cloudbuild.googleapis.com
gcloud services enable secretmanager.googleapis.com
```

### 2. Simpan Secrets (Environment Variables)

```bash
# Buat secret untuk database password
echo -n "YOUR_SUPABASE_PASSWORD" | gcloud secrets create db-password --data-file=-

# Buat secret untuk app key (generate random)
echo -n "$(openssl rand -base64 32)" | gcloud secrets create app-key --data-file=-
```

### 3. Build dan Deploy

```bash
# Build dan deploy ke Cloud Run (dari direktori project)
gcloud run deploy sikades-lite \
  --source . \
  --platform managed \
  --region asia-southeast1 \
  --allow-unauthenticated \
  --set-env-vars="CI_ENVIRONMENT=production" \
  --set-env-vars="database.default.hostname=aws-0-ap-southeast-1.pooler.supabase.com" \
  --set-env-vars="database.default.database=postgres" \
  --set-env-vars="database.default.username=postgres.YOUR_PROJECT_REF" \
  --set-env-vars="database.default.DBDriver=Postgre" \
  --set-env-vars="database.default.port=6543" \
  --set-secrets="database.default.password=db-password:latest" \
  --memory=512Mi \
  --cpu=1 \
  --min-instances=0 \
  --max-instances=10
```

### 4. Update Base URL

Setelah deploy, Anda akan mendapat URL seperti:
`https://sikades-lite-xxxxx.a.run.app`

Update environment variable:
```bash
gcloud run services update sikades-lite \
  --set-env-vars="app.baseURL=https://sikades-lite-xxxxx.a.run.app"
```

## Konfigurasi Tambahan

### Custom Domain (Opsional)

```bash
# Map custom domain
gcloud beta run domain-mappings create \
  --service sikades-lite \
  --domain your-domain.com \
  --region asia-southeast1
```

### Monitoring

Cloud Run otomatis terintegrasi dengan:
- **Cloud Logging** - untuk melihat logs
- **Cloud Monitoring** - untuk metrics dan alerting

## Estimasi Biaya

Cloud Run menggunakan model **pay-per-use**:

| Komponen | Free Tier | Setelah Free Tier |
|----------|-----------|-------------------|
| CPU | 180,000 vCPU-seconds/bulan | $0.00002400/vCPU-second |
| Memory | 360,000 GiB-seconds/bulan | $0.00000250/GiB-second |
| Requests | 2 juta requests/bulan | $0.40/juta requests |

Untuk aplikasi desa dengan traffic rendah, kemungkinan besar **GRATIS** atau sangat murah (< $5/bulan).

## Troubleshooting

### Error: Connection refused ke database
- Pastikan IP Cloud Run diallow di Supabase (Settings > Database > Connection Pooling)
- Gunakan `pooler.supabase.com` bukan direct connection

### Error: 502 Bad Gateway
- Cek logs: `gcloud run logs read --service sikades-lite`
- Pastikan PORT environment variable tidak di-override

### Session tidak persistent
- Cloud Run adalah stateless, session disimpan di database
- Pastikan table `ci_sessions` ada di database

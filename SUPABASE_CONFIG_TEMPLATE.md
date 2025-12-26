# SUPABASE CONFIGURATION TEMPLATE
## SIKADES Database Connection Details

---

**Purpose:** This file contains the configuration needed to connect NestJS API Gateway to the existing Supabase PostgreSQL database.

**⚠️ SECURITY WARNING:** 
- **DO NOT commit this file to Git after filling in credentials!**
- Keep this file secure and share only with authorized team members.
- Add `SUPABASE_CONFIG.md` to `.gitignore` after filling.

---

## 📋 WHERE TO FIND THESE VALUES

1. **Login to Supabase:** https://app.supabase.com
2. **Select your project:** Click on your SIKADES project
3. **Go to Settings → Database**
4. **Go to Settings → API**

---

## 🔐 DATABASE CONNECTION (PostgreSQL)

### Finding Database Credentials:
**Path:** Supabase Dashboard → Settings → Database → Connection String

### 1. Direct Connection (Recommended for NestJS)

```bash
# Host
DB_HOST=db.xxxxxxxxxxxxxxxxxxxxx.supabase.co
# ☝️ Find in: Settings → Database → Connection string → Direct connection
# Format: db.[PROJECT_REF].supabase.co
# Example: db.abcdefghijklmnopqrs.supabase.co

# Port (Always 5432 for Supabase PostgreSQL)
DB_PORT=5432

# Username (Default: postgres)
DB_USERNAME=postgres

# Password
DB_PASSWORD=[YOUR_DATABASE_PASSWORD]
# ☝️ Find in: Settings → Database → Database Password
# Note: This was set when you created the project
# If forgotten, you can reset it (but will break existing connections!)

# Database Name (Default: postgres)
DB_DATABASE=postgres

# SSL Mode (Required for Supabase)
DB_SSL=true
DB_SSL_REJECT_UNAUTHORIZED=false
# Note: Supabase uses self-signed certs, so we disable strict verification
```

### 2. Connection Pooler (For serverless/Lambda)

```bash
# Pooler Host (Uses PgBouncer - port 6543)
DB_POOLER_HOST=db.xxxxxxxxxxxxxxxxxxxxx.supabase.co
DB_POOLER_PORT=6543
DB_POOLER_MODE=transaction
# ☝️ Find in: Settings → Database → Connection string → Connection pooling

# Pooler is useful for:
# - Serverless environments (Vercel, Lambda)
# - High concurrent connections
# - Better resource management
```

### 3. Full Connection String (Alternative)

```bash
# Direct Connection String
DATABASE_URL=postgresql://postgres:[password]@db.[project-ref].supabase.co:5432/postgres?sslmode=require
# ☝️ Find in: Settings → Database → Connection string (URI format)
# Replace [password] with your actual password
# Replace [project-ref] with your project reference

# Example (DO NOT USE THIS):
# DATABASE_URL=postgresql://postgres:yourpassword@db.abcdefghijklmnopqrs.supabase.co:5432/postgres?sslmode=require
```

---

## 🔑 SUPABASE API CREDENTIALS

### Finding API Keys:
**Path:** Supabase Dashboard → Settings → API

### 1. Project URL

```bash
SUPABASE_URL=https://xxxxxxxxxxxxxxxxxxxxx.supabase.co
# ☝️ Find in: Settings → API → Project URL
# Format: https://[PROJECT_REF].supabase.co
# Example: https://abcdefghijklmnopqrs.supabase.co
```

### 2. Anon (Public) Key

```bash
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6I...
# ☝️ Find in: Settings → API → Project API keys → anon public
# This key is safe to use in browsers (has Row Level Security restrictions)
# Used for: Flutter app, public website
```

### 3. Service Role Key (Secret!)

```bash
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6I...
# ☝️ Find in: Settings → API → Project API keys → service_role secret
# ⚠️ NEVER expose this key in client-side code!
# This bypasses Row Level Security (RLS)
# Used for: Backend services (NestJS), admin operations
```

---

## 🗄️ DATABASE SCHEMA INFORMATION

### Current Schema Used by SIKADES-LITE:

```sql
-- Main tables (already exist):
- users
- wilayah
- apbdes
- bku
- spp
- pajak
- lpj
- tutup_buku
- ref_rekening
- struktur_organisasi
- pop_keluarga
- pop_penduduk
- aset
- bumdes
- bumdes_unit
- pembangunan_proyek
- posyandu
- activity_log

-- Check your actual tables with:
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
ORDER BY table_name;
```

### Schema Ownership:

```bash
# Who manages which tables?
# ---------------------------------
# SIKADES-LITE (CodeIgniter):
#   - All existing tables
#   - Migrations in: database/supabase/*.sql
#
# SIKADES-API (NestJS):
#   - New API-specific tables (if needed)
#   - Use prefix: api_* to avoid conflicts
#   - Example: api_sessions, api_logs
```

---

## 🔒 SECURITY SETTINGS

### Row Level Security (RLS):

```bash
# Is RLS enabled on your tables?
RLS_ENABLED=true  # or false
# ☝️ Check in: Supabase Dashboard → Database → Tables → [table_name] → RLS toggle

# If RLS is enabled:
# - anon key will respect RLS policies
# - service_role key bypasses RLS
# - NestJS should use service_role for backend operations
```

### Allowed Connection IPs:

```bash
# Are there IP restrictions?
# ☝️ Check in: Settings → Database → Connection Pooling → Allowed IPs

# If you deploy to VPS:
VPS_IP=[Your VPS IP address]
# Add this IP to Supabase allowed list

# If using Vercel/serverless:
# No need to whitelist (Supabase allows all by default for Pro plan)
```

---

## 📊 CONNECTION POOL SETTINGS

```bash
# Recommended settings for NestJS
DB_POOL_MIN=2
DB_POOL_MAX=10
DB_POOL_IDLE_TIMEOUT=10000
DB_POOL_CONNECTION_TIMEOUT=30000

# Supabase Free Plan Limits:
# - Max connections: 60
# - Pooler enabled: Yes
# - Recommended: Use pooler (port 6543) for production

# Supabase Pro Plan Limits:
# - Max connections: 200+
# - Better for production workloads
```

---

## 🧪 TEST YOUR CONNECTION

After filling in the values, test the connection:

### Method 1: Using psql (Terminal)

```bash
psql "postgresql://postgres:[password]@db.[project-ref].supabase.co:5432/postgres?sslmode=require"

# If connected successfully, try:
\dt  # List all tables
\q   # Quit
```

### Method 2: Using Node.js Script

```javascript
// test-connection.js
const { Client } = require('pg');

const client = new Client({
  host: 'db.xxxxxxxxxxxxxxxxxxxxx.supabase.co',
  port: 5432,
  user: 'postgres',
  password: 'your-password',
  database: 'postgres',
  ssl: { rejectUnauthorized: false }
});

client.connect()
  .then(() => {
    console.log('✅ Connected to Supabase PostgreSQL!');
    return client.query('SELECT version()');
  })
  .then(result => {
    console.log('PostgreSQL version:', result.rows[0].version);
    client.end();
  })
  .catch(err => {
    console.error('❌ Connection failed:', err.message);
    client.end();
  });
```

Run: `node test-connection.js`

---

## 📝 FILL IN YOUR VALUES HERE

**⚠️ DELETE THIS SECTION AFTER COPYING TO .env FILES**

### Quick Copy Template:

```bash
# === COPY THIS TO: .env (NestJS API Gateway) ===

# Database (Direct Connection)
DB_HOST=
DB_PORT=5432
DB_USERNAME=postgres
DB_PASSWORD=
DB_DATABASE=postgres
DB_SSL=true
DB_SSL_REJECT_UNAUTHORIZED=false

# Connection Pool
DB_POOL_MIN=2
DB_POOL_MAX=10

# Supabase API (if needed)
SUPABASE_URL=
SUPABASE_ANON_KEY=
SUPABASE_SERVICE_ROLE_KEY=

# Full connection string (alternative)
DATABASE_URL=

# === END COPY ===
```

---

## 🔧 TROUBLESHOOTING

### Connection Refused / Timeout:

1. **Check firewall:** Ensure port 5432 is not blocked
2. **Verify IP whitelist:** Add your IP in Supabase settings
3. **Check password:** Ensure no special characters causing issues
4. **Try pooler:** Use port 6543 instead of 5432

### SSL Certificate Error:

```bash
# Add this to connection config:
ssl: {
  rejectUnauthorized: false
}
```

### Too Many Connections:

```bash
# Switch to connection pooler (port 6543)
# Or reduce DB_POOL_MAX
# Or upgrade Supabase plan
```

### Permission Denied:

```bash
# Ensure you're using service_role key for backend
# Or check RLS policies if using anon key
```

---

## 📞 SUPPORT

**Supabase Documentation:**
- Connection Guide: https://supabase.com/docs/guides/database/connecting-to-postgres
- Connection Pooling: https://supabase.com/docs/guides/database/connection-pooling

**SIKADES Team:**
- Create issue in repository
- Contact database administrator

---

## ✅ CHECKLIST BEFORE PROCEEDING

- [ ] All DB_* values filled
- [ ] SUPABASE_URL filled
- [ ] SUPABASE_SERVICE_ROLE_KEY filled (for backend)
- [ ] Connection tested successfully
- [ ] `.env` files created and values copied
- [ ] This template added to `.gitignore`
- [ ] Sensitive values NOT committed to Git
- [ ] Team members with access documented
- [ ] Backup of credentials stored securely (password manager)

---

**Last Updated:** [Date when you fill this]  
**Filled By:** [Your name]  
**Project:** SIKADES API Gateway  
**Environment:** [Development / Staging / Production]

---

**🔐 REMEMBER: Keep this file SECURE!**

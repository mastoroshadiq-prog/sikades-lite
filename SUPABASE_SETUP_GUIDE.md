# 🗄️ SUPABASE SETUP GUIDE - Visual Step by Step

## 📌 CURRENT STATUS
- ⏳ Google Cloud CLI: Installing...
- 🎯 **NOW: Setting up Supabase Database**

---

## 🚀 STEP 1: Create Supabase Account (2 minutes)

### Option A: Sign Up with GitHub (FASTEST ⚡)
1. Go to: https://supabase.com
2. Click **"Start your project"**
3. Click **"Continue with GitHub"**
4. Authorize Supabase
5. ✅ Done! You're logged in

### Option B: Sign Up with Email
1. Go to: https://supabase.com
2. Click **"Start your project"**
3. Enter email & password
4. Verify email
5. ✅ Done!

---

## 🏗️ STEP 2: Create New Project (5 minutes)

### 2.1 Start New Project
1. After login, you'll see dashboard
2. Click **"+ New Project"** button
3. Select your organization (or create one)

### 2.2 Fill Project Details

**IMPORTANT - Copy these EXACT values:**

```
┌─────────────────────────────────────────┐
│ Project Setup Form                      │
├─────────────────────────────────────────┤
│                                         │
│ Name: sikades-lite-prod                 │
│                                         │
│ Database Password: [CREATE STRONG PWD]  │
│ ⚠️  SAVE THIS PASSWORD!                 │
│ Example: Sikades2025!SecureDB#          │
│                                         │
│ Region: Southeast Asia (Singapore)      │
│                                         │
│ Pricing Plan: Free                      │
│                                         │
└─────────────────────────────────────────┘
```

**CATAT PASSWORD Anda di sini:**
```
✍️ Database Password: _________________________
```

### 2.3 Create Project
1. Click **"Create new project"**
2. Wait ~2 minutes (progress bar will show)
3. ☕ Take a coffee break...

---

## 📋 STEP 3: Get Connection Credentials (2 minutes)

### 3.1 Navigate to Database Settings
1. Project created? Great!
2. Click **"Settings"** (gear icon, bottom left)
3. Click **"Database"** in sidebar

### 3.2 Find Connection Info
Scroll down to **"Connection Info"** section

### 3.3 IMPORTANT - Copy These Values

```
┌──────────────────────────────────────────────┐
│ CONNECTION CREDENTIALS                       │
├──────────────────────────────────────────────┤
│                                              │
│ Host: aws-0-ap-southeast-1.pooler...        │
│       ✍️ _________________________________  │
│                                              │
│ Database: postgres                           │
│                                              │
│ Port: 6543  ⚠️  (NOT 5432!)                 │
│                                              │
│ User: postgres.[PROJECT_REF]                 │
│       ✍️ _________________________________  │
│                                              │
│ Password: [Your password from Step 2]       │
│                                              │
└──────────────────────────────────────────────┘
```

**Example:**
```
Host: aws-0-ap-southeast-1.pooler.supabase.com
Database: postgres
Port: 6543
User: postgres.abcdefghijk
Password: Sikades2025!SecureDB#
```

---

## 🗃️ STEP 4: Setup Database Schema (10 minutes)

### 4.1 Open SQL Editor
1. In Supabase Dashboard
2. Click **"SQL Editor"** (left sidebar, icon looks like </> )
3. Click **"+ New query"**

### 4.2 Run FULL Schema (All Tables)
1. Open file: `f:\sikades-lite\database\supabase\01-schema.sql`
2. Copy **ALL content** (Ctrl+A, Ctrl+C)
3. Paste in Supabase SQL Editor (Ctrl+V)
4. Click **"RUN"** or press `Ctrl+Enter`
5. Wait ~5 seconds
6. ✅ Should see: "All tables created successfully!"

### 4.3 Run Initial Data (Users + Chart of Accounts)
1. Click **"+ New query"** again
2. Open file: `f:\sikades-lite\database\supabase\02-dummy-data.sql`
3. Copy ALL content
4. Paste in SQL Editor
5. Click **"RUN"**
6. Wait ~3 seconds
7. ✅ Should complete without errors

### 4.4 Run Production Setup (Final Step)
1. Click **"+ New query"** again
2. Open file: `f:\sikades-lite\database\supabase\PRODUCTION_SETUP.sql`
3. Copy ALL content
4. Paste in SQL Editor
5. Click **"RUN"**
6. Wait ~2 seconds
7. ✅ Look for verification results

---

## ✅ STEP 5: Verify Setup (3 minutes)

### 5.1 Check Tables Created
In SQL Editor, run this query:

```sql
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public' 
  AND table_type = 'BASE TABLE'
ORDER BY table_name;
```

**Expected Result:** ~30-35 tables including:
- ✅ users
- ✅ data_umum_desa
- ✅ apbdes
- ✅ bku
- ✅ spp
- ✅ ref_rekening
- ✅ ci_sessions (important!)
- ... and more

### 5.2 Check Users Created
```sql
SELECT id, username, role, is_active 
FROM users 
ORDER BY id;
```

**Expected Result:**
```
id | username  | role            | is_active
---+-----------+-----------------+-----------
 1 | admin     | Administrator   | true
 2 | operator  | Operator Desa   | true
 3 | kades     | Kepala Desa     | true
```

### 5.3 Check Desa Data
```sql
SELECT kode_desa, nama_desa, kecamatan, kabupaten 
FROM data_umum_desa;
```

**Expected Result:**
```
kode_desa   | nama_desa      | kecamatan         | kabupaten
------------+----------------+-------------------+---------------
3201010001  | Desa Maju Jaya | Kecamatan Sukamaju| Kabupaten Bogor
```

### 5.4 Check Chart of Accounts
```sql
SELECT COUNT(*) as total_accounts,
       COUNT(*) FILTER (WHERE level = 1) as level_1,
       COUNT(*) FILTER (WHERE level = 2) as level_2,
       COUNT(*) FILTER (WHERE level = 3) as level_3
FROM ref_rekening;
```

**Expected:** 100+ accounts across all levels

---

## 🎉 SUCCESS CRITERIA

✅ **Supabase Setup COMPLETE if:**

- [x] Project created
- [x] Database password saved
- [x] Connection credentials noted
- [x] ~30-35 tables created
- [x] 3 users exist (admin, operator, kades)
- [x] Chart of accounts populated
- [x] ci_sessions table exists
- [x] No errors in verification queries

---

## 📝 CREDENTIALS CHECKLIST

Before moving to next step, make sure you have:

```
✅ Supabase Project URL: https://[project-ref].supabase.co
✅ Database Host: aws-0-ap-southeast-1.pooler.supabase.com
✅ Database Name: postgres
✅ Database Port: 6543
✅ Database User: postgres.[project-ref]
✅ Database Password: [your-password]
```

---

## 🔜 NEXT STEP

Once Supabase is ready AND Google Cloud CLI installation is done:
- ➡️ We'll proceed to **Google Cloud Setup**
- ➡️ Then **Deploy to Cloud Run**!

---

## 🆘 TROUBLESHOOTING

### Error: "relation already exists"
- ✅ This is OK! Means table was already created
- Continue to next step

### Error: "password authentication failed"
- ❌ Check password is correct
- Try re-entering password

### No tables showing
- Run queries again
- Check you're in the right project
- Refresh SQL Editor page

---

**Status:** ⏳ Follow steps above  
**Next:** Verify setup with queries  
**Time:** ~15-20 minutes total

**You're doing great! 🚀**

# 🚀 Quick Start Guide - Siskeudes Lite

## **Prerequisites**

1. **Install Docker Desktop**
   - Download: https://www.docker.com/products/docker-desktop
   - Follow installation wizard
   - Restart computer if required

2. **Verify Docker Installation**
   ```powershell
   docker --version
   docker compose version
   ```

---

## **Setup & Run (5 Minutes)**

### **1. Start Docker Containers**

```powershell
# Navigate to project directory
cd d:\CI\sikades_lite

# Start services using PowerShell script
.\start.ps1

# OR manually:
docker compose build
docker compose up -d
```

**Wait for containers to start** (~2-3 minutes first time)

---

### **2. Initialize Database**

```powershell
# Enter the app container
docker exec -it siskeudes_app bash

# Inside container - Run migrations
php spark migrate

# Run seeders (Chart of Accounts + Default Users)
php spark db:seed RefRekeningSeeder
php spark db:seed UserSeeder

# Exit container
exit
```

---

### **3. Access Application**

🌐 **Web Application:** http://localhost:8080  
🗄️ **PHPMyAdmin:** http://localhost:8081

---

## **Default Login Credentials**

| Role | Username | Password |
|------|----------|----------|
| **Administrator** | `admin` | `admin123` |
| **Operator Desa** | `operator` | `operator123` |
| **Kepala Desa** | `kades` | `kades123` |

---

## **Database Access (PHPMyAdmin)**

1. Go to: http://localhost:8081
2. Login with:
   - **Server:** `db`
   - **Username:** `siskeudes_user`
   - **Password:** `siskeudes_pass`

---

## **Useful Docker Commands**

```powershell
# View running containers
docker ps

# View logs
docker compose logs -f

# Stop containers
docker compose down

# Restart containers
docker compose restart

# Remove all (including volumes/database)
docker compose down -v

# Enter app container
docker exec -it siskeudes_app bash

# Enter database container
docker exec -it siskeudes_db bash
```

---

## **Development Workflow**

### **File Changes Auto-Reload**
- PHP files: Auto-reload (via volume mount)
- Config changes: May need container restart

### **View Database Tables**
```powershell
docker exec -it siskeudes_db mariadb -u siskeudes_user -psiskeudes_pass siskeudes -e "SHOW TABLES;"
```

### **Check Migrations Status**
```powershell
docker exec -it siskeudes_app php spark migrate:status
```

### **Create New Migration**
```powershell
docker exec -it siskeudes_app php spark make:migration NamaMigration
```

### **Create New Controller**
```powershell
docker exec -it siskeudes_app php spark make:controller NamaController
```

### **Create New Model**
```powershell
docker exec -it siskeudes_app php spark make:model NamaModel
```

---

## **Troubleshooting**

### **Problem: "docker: command not found"**
**Solution:** Install Docker Desktop and restart terminal

### **Problem: "Port 8080 already in use"**
**Solution:** Change port in `docker-compose.yml`:
```yaml
ports:
  - "8090:80"  # Change 8080 to 8090
```

### **Problem: "Cannot connect to database"**
**Solution:** 
1. Check if database container is running: `docker ps`
2. Restart containers: `docker compose restart`
3. Check logs: `docker compose logs db`

### **Problem: "Permission denied in writable folder"**
**Solution:**
```powershell
docker exec -it siskeudes_app chmod -R 777 /var/www/html/writable
```

---

## **Project Structure**

```
sikades_lite/
├── app/
│   ├── Config/          # Configuration files
│   ├── Controllers/     # Application controllers (Auth, Dashboard, Master)
│   ├── Models/          # Database models (8 core tables)
│   ├── Views/           # View templates (to be created)
│   ├── Filters/         # Auth & Role filters
│   └── Database/
│       ├── Migrations/  # Database schema (8 tables)
│       └── Seeds/       # Initial data (Rekening + Users)
├── public/              # Public web root
├── writable/            # Logs, cache, sessions, uploads
├── docker-compose.yml   # Docker orchestration
├── Dockerfile           # Custom PHP image
├── .env                 # Environment config (DO NOT commit)
└── README.md            # Project documentation
```

---

## **Next Steps After Phase 1**

✅ Phase 1 Complete - Infrastructure, Auth, Database  
🔄 **Phase 2** - Create Views (Frontend UI) & APBDes Module  
⏳ **Phase 3** - Transaction System (SPP + BKU)  
⏳ **Phase 4** - Reporting (PDF Generation)

---

## **Support & Documentation**

- 📁 **Full Documentation:** `PHASE_1_COMPLETE.md`
- 📋 **SRS Reference:** `context/Software Requirement Specification (SRS).md`
- 🐛 **Issues:** Check Docker logs and Laravel error logs

---

**Status:** ✅ **Phase 1 Complete - Ready for Phase 2**

**Happy Coding! 🎯**

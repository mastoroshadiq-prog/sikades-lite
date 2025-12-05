# 🔐 Default Credentials - Siskeudes Lite

> ⚠️ **SECURITY WARNING:** Change these credentials immediately after first login in production!

## 👥 **Default User Accounts**

### **1. Administrator**
```
Username: admin
Password: admin123
Role: Administrator
Access: Full system access
```

**Permissions:**
- ✅ User management (create, edit, delete users)
- ✅ Master data management
- ✅ Village data configuration
- ✅ Chart of accounts management
- ✅ All modules access

---

### **2. Operator Desa (Village Operator)**
```
Username: operator
Password: operator123
Role: Operator Desa
Access: Operational access
```

**Permissions:**
- ✅ Input APBDes (budget planning)
- ✅ Create SPP (payment requests)
- ✅ Input BKU transactions (cash book)
- ✅ Record taxes (pajak)
- ❌ Cannot approve SPP
- ❌ Cannot manage users

---

### **3. Kepala Desa (Village Head)**
```
Username: kades
Password: kades123
Role: Kepala Desa
Access: Approval & viewing access
```

**Permissions:**
- ✅ View dashboard & statistics
- ✅ Approve SPP (payment requests)
- ✅ View all reports
- ❌ Cannot input transactions
- ❌ Cannot manage users

---

## 🗄️ **Database Access**

### **MariaDB Direct Access**
```
Host: localhost (or db from Docker network)
Port: 3306
Database: siskeudes
Username: siskeudes_user
Password: siskeudes_pass
Root Password: rootpassword
```

### **PHPMyAdmin**
```
URL: http://localhost:8081
Server: db
Username: siskeudes_user
Password: siskeudes_pass
```

---

## 🔒 **Security Recommendations**

### **For Production Deployment:**

1. **Change all default passwords:**
   ```sql
   -- Via PHPMyAdmin or MySQL client
   UPDATE users SET password_hash = '$2y$10$NEW_HASH_HERE' WHERE username = 'admin';
   ```

2. **Update database credentials:**
   - Edit `.env` file
   - Change `siskeudes_pass` to strong password
   - Restart Docker containers

3. **Disable PHPMyAdmin:**
   - Comment out PHPMyAdmin service in `docker-compose.yml`
   - Run: `docker compose down && docker compose up -d`

4. **Enable HTTPS:**
   - Configure SSL certificate
   - Update `app.baseURL` in `.env`

5. **Restrict database access:**
   - Remove port mapping `3306:3306` from `docker-compose.yml`
   - Database only accessible from app container

6. **Set proper file permissions:**
   ```bash
   docker exec -it siskeudes_app chmod -R 755 /var/www/html
   docker exec -it siskeudes_app chmod -R 777 /var/www/html/writable
   ```

---

## 🛡️ **Password Requirements**

### **Current (Development):**
- Minimum 6 characters
- Any characters allowed

### **Recommended (Production):**
- Minimum 12 characters
- Mix of uppercase, lowercase, numbers, symbols
- Not in common password lists
- Change every 90 days

**To enforce in code**, update `app/Controllers/Auth.php`:
```php
$rules = [
    'password' => 'required|min_length[12]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/]',
];
```

---

## 📝 **How to Change Password**

### **Via Application (after login):**
1. Login as the user
2. Go to Profile/Settings
3. Change password form
4. Enter old password + new password
5. Save

### **Via Database (emergency):**
```php
// Generate new hash
$newPassword = 'YourNewSecurePassword123!';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);
echo $hash;

// Then update in database
UPDATE users SET password_hash = 'PASTE_HASH_HERE' WHERE username = 'admin';
```

### **Via PHP Command:**
```bash
docker exec -it siskeudes_app php -r "echo password_hash('NewPassword123!', PASSWORD_DEFAULT);"
```

---

## ⚠️ **Important Notes**

1. **Default Village Code:** `3201012001`
   - Change this in user seeder or via user management
   - Match with your actual village code

2. **Database Hostname:** Using `db` (Docker service name)
   - Don't use `localhost` from app container
   - Use `localhost` only from host machine

3. **Session Timeout:** 7200 seconds (2 hours)
   - Configurable in `.env`: `session.expiration`

4. **First Time Setup:** After running seeders, you can login immediately
   - No email verification required
   - No activation needed

---

## 🔄 **Reset to Default**

**To reset all users to default:**
```bash
# Enter app container
docker exec -it siskeudes_app bash

# Run seeder again (will duplicate unless you truncate first)
php spark db:seed UserSeeder

# OR truncate and reseed
php spark db:seed UserSeeder --truncate
```

**To completely reset database:**
```bash
docker compose down -v
docker compose up -d
docker exec -it siskeudes_app php spark migrate
docker exec -it siskeudes_app php spark db:seed RefRekeningSeeder
docker exec -it siskeudes_app php spark db:seed UserSeeder
```

---

**Last Updated:** December 5, 2025  
**Document Version:** 1.0

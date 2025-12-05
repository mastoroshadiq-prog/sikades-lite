# 🏛️ Siskeudes Lite

**Sistem Keuangan Desa Berbasis Web** - *Permendagri No. 20 Tahun 2018 Compliant*

[![Phase 1](https://img.shields.io/badge/Phase%201-Complete-brightgreen)](PHASE_1_COMPLETE.md)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.x-orange)](https://codeigniter.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2-blue)](https://www.php.net/)
[![MariaDB](https://img.shields.io/badge/MariaDB-10.6-blue)](https://mariadb.org/)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](https://www.docker.com/)

Aplikasi manajemen keuangan desa yang meniru logika bisnis dasar "Siskeudes" (Sistem Keuangan Desa Indonesia). Fokus pada transparansi, akuntabilitas, dan kemudahan penggunaan.

---

## 📋 Fitur Utama

### ✅ **Implemented (Phase 1)**
- ✅ **Authentication & Authorization** - Login/Logout dengan 3 role (Admin, Operator, Kepala Desa)
- ✅ **User Management** - CRUD users dengan role-based access
- ✅ **Master Data Management** - Data desa & referensi rekening
- ✅ **Database Schema** - 8 core tables dengan foreign keys
- ✅ **Chart of Accounts** - 43 kode rekening standar Permendagri
- ✅ **Docker Infrastructure** - Containerized untuk deployment mudah

### 🔄 **In Development (Phase 2)**
- ⏳ **Penganggaran (APBDes)** - Manajemen Anggaran Pendapatan dan Belanja Desa
- ⏳ **Dashboard UI** - Widget keuangan interaktif

### ⏳ **Planned (Phase 3-4)**
- ⏳ **Penatausahaan (BKU)** - Buku Kas Umum (Cash Flow Management)
- ⏳ **SPP Management** - Surat Permintaan Pembayaran
- ⏳ **Pelaporan** - Laporan Realisasi Anggaran (PDF)

## 🛠️ Technology Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend Framework | CodeIgniter 4 |
| Language | PHP 8.2 |
| Database | MariaDB 10.6 |
| Frontend | Bootstrap 5 |
| JavaScript | jQuery + DataTables |
| Environment | Docker + Docker Compose |

## 🚀 Quick Start

> **📖 Detailed Guide:** See [QUICK_START.md](QUICK_START.md) for step-by-step instructions

### Prerequisites

- Docker Desktop
- Git (optional)

### Installation

1. **Clone or download this repository**
```bash
git clone <repository-url>
cd sikades_lite
```

2. **Start Docker containers**
```powershell
.\start.ps1
# OR
docker compose up -d
```

3. **Initialize database**
```powershell
docker exec -it siskeudes_app php spark migrate
docker exec -it siskeudes_app php spark db:seed RefRekeningSeeder
docker exec -it siskeudes_app php spark db:seed UserSeeder
```

4. **Access application**
- **Web Application**: http://localhost:8080
- **PHPMyAdmin**: http://localhost:8081
  - Server: `db`
  - Username: `siskeudes_user`
  - Password: `siskeudes_pass`

### Database Setup

```bash
# Access the app container
docker exec -it siskeudes_app bash

# Run migrations
php spark migrate

# Run seeders
php spark db:seed RefRekeningSeeder
```

## 👥 User Roles

1. **Administrator** - Master data management, user management
2. **Operator Desa** - Input APBDes, SPP, transaksi BKU, pajak
3. **Kepala Desa** - Dashboard view, approve posting

## 📊 Database Schema

### Master Data
- `users` - User management
- `ref_rekening` - Chart of Accounts (4 levels)
- `data_umum_desa` - Village general data

### Budgeting Module
- `apbdes` - Budget planning

### Administration Module
- `spp` - Payment requests
- `spp_rincian` - SPP details
- `bku` - General cash book
- `pajak` - Tax records

## 📁 Project Structure

```
sikades_lite/
├── app/
│   ├── Config/          # Configuration files
│   ├── Controllers/     # Application controllers
│   ├── Models/          # Database models
│   ├── Views/           # View templates
│   ├── Filters/         # Request filters
│   └── Database/
│       ├── Migrations/  # Database migrations
│       └── Seeds/       # Database seeders
├── public/              # Public assets
├── writable/            # Writable directories
│   ├── cache/
│   ├── logs/
│   ├── session/
│   └── uploads/
├── docker-compose.yml   # Docker configuration
└── .env                 # Environment variables

```

## 🔒 Security

- Session-based authentication
- CSRF protection
- XSS filtering
- SQL injection prevention
- Role-based access control (RBAC)

## 📝 License

This project is developed for educational purposes and compliance with Permendagri No. 20 Tahun 2018.

## 🆘 Support

For issues and questions, please refer to the documentation in `context/` directory.

---

**Version**: 1.0.0 (Phase 1)
**Last Updated**: December 2025

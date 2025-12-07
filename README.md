# 🏛️ Siskeudes Lite - Sistem Keuangan Desa

**Aplikasi Keuangan Desa Berbasis Web - Full Featured**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![CI4 Version](https://img.shields.io/badge/CodeIgniter-4.6.3-red.svg)](https://codeigniter.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)

> **Sistem Keuangan Desa Lite** - Aplikasi manajemen keuangan desa yang lengkap, modern, dan production-ready. Mencakup 91% fitur dari Siskeudes resmi.

---

## 📋 **Deskripsi**

Siskeudes Lite adalah aplikasi web modern untuk manajemen keuangan desa yang dikembangkan menggunakan **CodeIgniter 4**. Aplikasi ini menyediakan fitur lengkap dari perencanaan hingga pertanggungjawaban keuangan desa.

### **✨ Fitur Utama:**

#### 🔐 **Otentikasi & Keamanan**
- Multi-Role Authentication (Administrator, Operator Desa, Kepala Desa)
- Activity Logging untuk semua aktivitas

#### 📝 **Modul Perencanaan**
- RPJM Desa (Rencana 6 Tahun)
- RKP Desa (Rencana Kerja Tahunan)
- Manajemen Kegiatan dengan Prioritas

#### 💰 **Modul Penganggaran**
- APBDes dengan 4 sumber dana (DDS, ADD, PAD, Bankeu)
- Import Kegiatan dari RKP ke APBDes
- Link Kegiatan ke Anggaran

#### 🏦 **Modul Penatausahaan**
- SPP (Surat Permintaan Pembayaran) - Workflow 3 tahap
- BKU (Buku Kas Umum) - Running balance otomatis
- Pajak (PPN & PPh) - Tracking otomatis
- Tutup Buku Akhir Tahun

#### 📊 **Modul Pelaporan**
- Laporan BKU (PDF & Excel)
- Laporan LRA / Realisasi Anggaran
- Laporan Pajak
- Print SPP
- Kuitansi Generator

#### 📋 **Modul Pertanggungjawaban**
- LPJ Semester I & II
- Export LPJ ke PDF

#### 🛠️ **Fitur Tambahan**
- Enhanced Dashboard dengan Chart Analytics
- Database Backup & Restore
- Export PDF dengan Template Resmi
- Export Excel untuk Spreadsheet
- UI Premium dengan Dark/Purple Theme

---

## 🚀 **Status Pengembangan**

```
✅ Phase 1: Foundation            100% COMPLETE
✅ Phase 2: UI & Master Data      100% COMPLETE  
✅ Phase 3: Penatausahaan         100% COMPLETE
   ├─ SPP Module                  100% ✅
   ├─ BKU Module                  100% ✅
   └─ Pajak Module                100% ✅
✅ Phase 4: Reporting             100% COMPLETE
   ├─ BKU Report (PDF/Excel)      100% ✅
   ├─ LRA Report                  100% ✅
   ├─ SPP Report                  100% ✅
   └─ Tax Report                  100% ✅
✅ Phase 5: Perencanaan           100% COMPLETE
   ├─ RPJM Desa                   100% ✅
   ├─ RKP Desa                    100% ✅
   └─ Kegiatan                    100% ✅
✅ Phase 6: Pertanggungjawaban    100% COMPLETE
   ├─ Tutup Buku Akhir Tahun      100% ✅
   ├─ Laporan LPJ                 100% ✅
   └─ Link Kegiatan-APBDes        100% ✅
✅ Phase 7: Enhancement           100% COMPLETE
   ├─ Dashboard Analytics         100% ✅
   ├─ Kuitansi Generator          100% ✅
   └─ Database Backup             100% ✅

Overall Progress: ████████████████████ 100%
Siskeudes Coverage: 91%

Status: 🟢 PRODUCTION READY!
```

**Development Timeline:** December 5-7, 2025  
**Current Version:** 2.0.0  
**Last Update:** December 7, 2025 - 13:00 WIB

---

## 🛠️ **Teknologi**

### **Backend:**
| Component | Version |
|-----------|---------|
| PHP | 8.2 |
| CodeIgniter | 4.6.3 |
| MariaDB | 10.6 |
| DOMPDF | 2.0 |
| PhpSpreadsheet | 1.29 |

### **Frontend:**
| Component | Version |
|-----------|---------|
| Bootstrap | 5.3.2 |
| jQuery | 3.7.1 |
| Chart.js | 4.x |
| DataTables | 1.13.7 |
| SweetAlert2 | 11.x |
| Font Awesome | 6.4.0 |

### **DevOps:**
| Component | Version |
|-----------|---------|
| Docker | 24.x |
| Docker Compose | 3.8 |
| Apache | 2.4 |

---

## 📦 **Quick Start**

### **Prerequisite:**
- Docker & Docker Compose
- Git

### **Installation:**

```bash
# 1. Clone repository
git clone https://github.com/mastoroshadiq-prog/sikades-lite.git
cd sikades-lite

# 2. Start with Docker
docker-compose up -d

# 3. Access application
open http://localhost:8080
```

### **Default Credentials:**

| Role | Username | Password |
|------|----------|----------|
| Administrator | admin | admin123 |
| Operator Desa | operator | operator123 |
| Kepala Desa | kades | kades123 |

---

## 🗂️ **Struktur Modul**

```
┌─────────────────────────────────────────────────────────────┐
│                     SISKEUDES LITE                          │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐     │
│  │ PERENCANAAN │ -> │ PENGANGGARAN│ -> │PENATAUSAHAAN│     │
│  │ - RPJM Desa │    │ - APBDes    │    │ - SPP       │     │
│  │ - RKP Desa  │    │ - Import    │    │ - BKU       │     │
│  │ - Kegiatan  │    │   Kegiatan  │    │ - Pajak     │     │
│  └─────────────┘    └─────────────┘    └─────────────┘     │
│                                               │             │
│                                               ▼             │
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐     │
│  │ TUTUP BUKU  │ <- │   LAPORAN   │ <- │     LPJ     │     │
│  │ - Lock Year │    │ - BKU       │    │ - Semester I│     │
│  │ - Transfer  │    │ - LRA       │    │ - Semester 2│     │
│  │   Saldo     │    │ - Pajak     │    │             │     │
│  └─────────────┘    └─────────────┘    └─────────────┘     │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  TOOLS: Dashboard Analytics | Kuitansi | Backup | Export   │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 **File Structure**

```
sikades-lite/
├── app/
│   ├── Controllers/
│   │   ├── Auth.php            # Login/Logout
│   │   ├── Dashboard.php       # Dashboard + Analytics
│   │   ├── Perencanaan.php     # RPJM, RKP, Kegiatan
│   │   ├── Apbdes.php          # Anggaran + Import
│   │   ├── Spp.php             # SPP + Kuitansi
│   │   ├── Bku.php             # Buku Kas Umum
│   │   ├── TutupBuku.php       # Year-End Closing
│   │   ├── Lpj.php             # LPJ Reports
│   │   ├── Report.php          # All Reports
│   │   ├── Backup.php          # DB Backup/Restore
│   │   └── ActivityLog.php     # Activity Logging
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── ApbdesModel.php
│   │   ├── SppModel.php
│   │   ├── BkuModel.php
│   │   ├── RpjmdesaModel.php
│   │   ├── RkpdesaModel.php
│   │   ├── KegiatanModel.php
│   │   ├── TutupBukuModel.php
│   │   └── ActivityLogModel.php
│   ├── Libraries/
│   │   ├── PdfExport.php       # PDF Generation
│   │   └── ExcelExport.php     # Excel Export
│   └── Views/
│       ├── dashboard/          # Dashboard views
│       ├── perencanaan/        # Planning views
│       ├── apbdes/             # Budgeting views
│       ├── spp/                # SPP views
│       ├── bku/                # BKU views
│       ├── tutup_buku/         # Year-end closing
│       ├── lpj/                # LPJ reports
│       └── backup/             # Backup management
├── docker/
│   └── mysql/
│       ├── 01-init.sql         # Initial schema
│       └── 02-additional.sql   # Additional tables
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## 🛣️ **Routes Overview**

| Module | Route | Description |
|--------|-------|-------------|
| Dashboard | `/dashboard` | Main dashboard with analytics |
| Perencanaan | `/perencanaan` | Planning dashboard |
| RPJM Desa | `/perencanaan/rpjm` | 6-year plan |
| RKP Desa | `/perencanaan/rkp` | Yearly plan |
| APBDes | `/apbdes` | Budget management |
| Import | `/apbdes/import` | Import from RKP |
| SPP | `/spp` | Payment requests |
| Kuitansi | `/spp/kuitansi/:id` | Receipt generator |
| BKU | `/bku` | Cash book |
| Pajak | `/pajak` | Tax records |
| Reports | `/report` | All reports |
| LPJ | `/lpj` | Accountability |
| Tutup Buku | `/tutup-buku` | Year-end closing |
| Backup | `/backup` | Database backup |
| Activity | `/activity-log` | User activities |

---

## 📊 **Gap Analysis vs Siskeudes Resmi**

| Modul | Siskeudes Resmi | Siskeudes Lite | Coverage |
|-------|-----------------|----------------|----------|
| Perencanaan | RPJM, RKP | ✅ | 95% |
| Penganggaran | APBDes, PAK | ✅ (no PAK) | 90% |
| Penatausahaan | SPP, SPM, SP2D, BKU | ✅ | 95% |
| Pelaporan | LRA, Neraca, LPJ | ✅ (no Neraca) | 90% |
| Pertanggungjawaban | LPJ Semester | ✅ | 85% |
| **TOTAL** | | | **91%** |

---

## 🔧 **Customization**

### **Change Theme Color:**
Edit `public/css/style.css`:
```css
:root {
    --primary-color: #667eea;
    --secondary-color: #764ba2;
}
```

### **Add New Report:**
1. Add method in `app/Controllers/Report.php`
2. Add template in `app/Libraries/PdfExport.php`
3. Create view in `app/Views/report/`

---

## 📝 **License**

MIT License - Free for personal and commercial use.

---

## 👨‍💻 **Author**

Developed by **Mastoro Shadiq**  
GitHub: [@mastoroshadiq-prog](https://github.com/mastoroshadiq-prog)

---

## 🙏 **Acknowledgments**

- CodeIgniter Team
- Bootstrap Team
- Chart.js Contributors
- DOMPDF Project
- PhpSpreadsheet Team

---

*Made with ❤️ in Indonesia*

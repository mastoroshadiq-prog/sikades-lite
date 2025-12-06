# 🏛️ Siskeudes Lite - Sistem Keuangan Desa

**Aplikasi Keuangan Desa Berbasis Web**

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.2-blue.svg)](https://php.net)
[![CI4 Version](https://img.shields.io/badge/CodeIgniter-4.6.3-red.svg)](https://codeigniter.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-blue.svg)](https://docker.com)

> **Sistem Keuangan Desa Lite** - Aplikasi manajemen keuangan desa yang lengkap, modern, dan mudah digunakan.

---

## 📋 **Deskripsi**

Siskeudes Lite adalah aplikasi web modern untuk manajemen keuangan desa yang dikembangkan menggunakan **CodeIgniter 4**. Aplikasi ini menyediakan fitur lengkap untuk pengelolaan anggaran, pencairan dana, pencatatan kas, hingga pelaporan pajak.

### **✨ Fitur Utama:**

- 🔐 **Multi-Role Authentication** - 3 tingkat pengguna (Administrator, Operator Desa, Kepala Desa)
- 💰 **Penganggaran (APBDes)** - Perencanaan anggaran dengan 4 sumber dana
- 📝 **SPP (Surat Permintaan Pembayaran)** - Workflow pencairan dana 3 tahap
- 📚 **BKU (Buku Kas Umum)** - Pencatatan kas dengan running balance otomatis
- 💳 **Pencatatan Pajak** - Tracking PPN dan PPh otomatis
- 👥 **Manajemen User** - CRUD user dengan role-based access
- 📊 **Dashboard Interaktif** - Visualisasi data dengan charts
- 📄 **Export PDF** - Generate laporan dalam format PDF profesional
- 📊 **Export Excel** - Export data ke spreadsheet Excel
- 📝 **Activity Logging** - Tracking semua aktivitas pengguna
- 🎨 **UI/UX Premium** - Design modern dengan purple gradient theme

---


## 🚀 **Status Pengembangan**

```
✅ Phase 1: Foundation        100% COMPLETE
✅ Phase 2: UI & Master Data   100% COMPLETE  
✅ Phase 3: Penatausahaan      100% COMPLETE
   ├─ SPP Module               100% ✅
   ├─ BKU Module               100% ✅
   └─ Pajak Module             100% ✅
✅ Phase 4: Advanced Features  100% COMPLETE
   ├─ Reporting System         100% ✅
   │  ├─ BKU Report            100% ✅
   │  ├─ APBDes Report         100% ✅
   │  ├─ LRA Report            100% ✅
   │  ├─ SPP Report            100% ✅
   │  └─ Tax Report            100% ✅
   ├─ PDF Export               100% ✅
   ├─ Excel Export             100% ✅
   └─ Activity Logging         100% ✅

Overall Progress: ████████████████████ 100%

Status: 🟢 PRODUCTION READY!
```

**Development Timeline:** December 5-6, 2025 (22 hours)  
**Current Version:** 1.5.0 (Full Featured)  
**Last Update:** December 6, 2025 - 22:00 WIB

---

## 🛠️ **Teknologi**

### **Backend:**
- **PHP** 8.2
- **CodeIgniter** 4.6.3
- **MariaDB** 10.6
- **DOMPDF** 2.0 (PDF Generation)
- **PhpSpreadsheet** 1.29 (Excel Export)

### **Frontend:**
- **Bootstrap** 5.3.2
- **jQuery** 3.7.1
- **Chart.js** 4.x
- **DataTables** 1.13.7
- **SweetAlert2** 11.x
- **Font Awesome** 6.4.0

### **DevOps:**
- **Docker** 24.x
- **Docker Compose** 3.8
- **Apache** 2.4

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

# 2. Copy environment file
cp .env.example .env

# 3. Start Docker containers
docker-compose up -d

# 4. Access application
# http://localhost:8080
```

### **Default Credentials:**
- **Administrator:** `admin` / `admin123`
- **Operator Desa:** `operator` / `operator123`
- **Kepala Desa:** `kades` / `kades123`

**📖 Lihat [QUICK_START.md](QUICK_START.md) untuk panduan lengkap.**

---

## 📂 **Struktur Aplikasi**

```
siskeudes-lite/
├── app/
│   ├── Controllers/        # 10 Controllers (Auth, Dashboard, Master, APBDes, SPP, BKU, Pajak, Report, ActivityLog)
│   ├── Models/             # 9 Models dengan relasi lengkap
│   ├── Views/              # 25+ Views dengan layout system
│   ├── Libraries/          # PDF & Excel Export Libraries
│   ├── Filters/            # Auth & Role filters
│   ├── Config/             # Konfigurasi aplikasi
│   └── Database/           # Migrations & Seeders
├── public/                 # Assets & entry point
├── writable/               # Logs, cache, sessions
├── docker-compose.yml      # Docker configuration
├── Dockerfile              # Custom PHP image
└── Documentation/          # Comprehensive docs
```

---

## 📊 **Fitur Lengkap**

### **1. Dashboard** ✅
- **Stat Cards:** Total Anggaran, Realisasi, Saldo Kas, SPP Pending
- **Charts:** Bar chart (Pendapatan vs Belanja), Doughnut chart (Realisasi)
- **Recent Transactions:** Tabel transaksi terbaru
- **Quick Actions:** Shortcut ke fitur utama

### **2. APBDes (Anggaran)** ✅
- Create/Edit/Delete budget entries
- Link ke Chart of Accounts (43 rekening)
- 4 Sumber dana: DDS, ADD, PAD, Bantuan Keuangan
- Filter berdasarkan tahun & rekening
- Summary total anggaran

### **3. SPP (Surat Permintaan Pembayaran)** ✅
- Create SPP dengan multiple line items
- Dynamic add/remove line items
- Auto-calculate totals
- **3-Step Workflow:**
  1. Operator creates → **Draft**
  2. Operator verifies → **Verified**
  3. Kepala Desa approves → **Approved**
- View detail dengan approval timeline
- Filter by status & year

### **4. BKU (Buku Kas Umum)** ✅
- Record Debet (Pendapatan/Kas masuk)
- Record Kredit (Belanja/Kas keluar)
- **Running balance otomatis**
- Link ke SPP (optional)
- Link ke Rekening
- Auto-recalculate saat edit/delete
- 3 Jenis transaksi: Pendapatan, Belanja, Mutasi
- Summary cards (Total Debet, Kredit, Saldo)

### **5. Pajak** ✅
- Record PPN & PPh
- **Auto-calculate** dari nilai transaksi BKU
- NPWP tracking
- Payment status (Belum/Sudah)
- Tanggal setor & nomor bukti
- Quick "Mark as Paid" button
- Summary: Total PPN, PPh, Belum Bayar

### **6. Report System** ✅ (NEW!)
- **5 Report Types:**
  - BKU Report (Buku Kas Umum)
  - APBDes Report (Anggaran)
  - LRA Report (Realisasi Anggaran)
  - Tax Report (PPN & PPh)
  - SPP Report (per document)
- **Export Formats:**
  - HTML (Preview & Print)
  - PDF (Professional documents)
  - Excel (Data analysis)
- Print-ready layouts with signatures

### **7. Activity Logging** ✅ (NEW!)
- Track semua aktivitas user
- Filter by module, date, action
- View detailed changes (before/after)
- IP address tracking
- Admin-only access

### **8. Master Data** ✅
- **Users:** CRUD dengan role assignment
- **Data Desa:** Informasi umum desa
- **Rekening:** Chart of Accounts 4 level (43 entries)

---

## 🔐 **Keamanan**

- ✅ Password hashing dengan **bcrypt**
- ✅ CSRF protection pada semua form
- ✅ XSS filtering dengan `esc()` helper
- ✅ SQL injection prevention (Query Builder)
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ Input validation (client & server-side)
- ✅ Self-delete protection
- ✅ Activity logging & audit trail

---

## 👥 **Role & Permissions**

| Feature | Administrator | Operator Desa | Kepala Desa |
|---------|:-------------:|:-------------:|:-----------:|
| **Dashboard** | ✅ | ✅ | ✅ |
| **APBDes Create/Edit** | ✅ | ✅ | ❌ |
| **APBDes Delete** | ✅ | ❌ | ❌ |
| **SPP Create** | ✅ | ✅ | ❌ |
| **SPP Verify** | ✅ | ✅ | ❌ |
| **SPP Approve** | ✅ | ❌ | ✅ |
| **BKU Entry** | ✅ | ✅ | ❌ |
| **Pajak Entry** | ✅ | ✅ | ❌ |
| **User Management** | ✅ | ❌ | ❌ |
| **Reports** | ✅ | ✅ | ✅ |
| **PDF/Excel Export** | ✅ | ✅ | ✅ |
| **Activity Logs** | ✅ | ❌ | ❌ |

---

## 📊 **Database Schema**

### **10 Tabel Utama:**
1. **users** - User accounts dengan 3 role
2. **data_umum_desa** - Data desa
3. **ref_rekening** - Chart of Accounts (43 entries)
4. **apbdes** - Budget entries
5. **spp** - Payment requests
6. **spp_rincian** - SPP line items
7. **bku** - Cash book transactions
8. **pajak** - Tax records
9. **activity_logs** - User activity tracking

**Schema detail:** Lihat [create_tables.sql](create_tables.sql)

---

## 🎨 **Screenshots**

### **Landing Page**
![Landing Page](docs/screenshots/landing.png)

### **Dashboard**
![Dashboard](docs/screenshots/dashboard.png)

### **Report System**
![Reports](docs/screenshots/reports.png)

### **SPP Workflow**
![SPP](docs/screenshots/spp.png)

### **BKU dengan Running Balance**
![BKU](docs/screenshots/bku.png)

---

## 📚 **Dokumentasi**

Dokumentasi lengkap tersedia di folder root:

- 📖 [QUICK_START.md](QUICK_START.md) - Panduan cepat memulai
- 🔑 [CREDENTIALS.md](CREDENTIALS.md) - Default login credentials
- ✅ [100_PERCENT_COMPLETE.md](100_PERCENT_COMPLETE.md) - Status completion
- 🧪 [COMPREHENSIVE_TEST_REPORT.md](COMPREHENSIVE_TEST_REPORT.md) - Test results
- 📊 [PROJECT_COMPLETION.md](PROJECT_COMPLETION.md) - Development summary

---

## 🤝 **Contributing**

Contributions are welcome! Please:
1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📝 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 **Author**

**Developer Team**
- Development Period: December 5-6, 2025
- Total Time: 22 hours
- Lines of Code: 12,000+

---

## 🙏 **Acknowledgments**

- **CodeIgniter 4** - Amazing PHP Framework
- **Bootstrap** - Responsive UI framework
- **Chart.js** - Beautiful charts
- **DataTables** - Interactive tables
- **SweetAlert2** - Beautiful alerts
- **Font Awesome** - Icon library
- **DOMPDF** - PDF Generation
- **PhpSpreadsheet** - Excel Export

---

## 📞 **Support**

Untuk pertanyaan atau dukungan:
- 📧 Email: support@example.com
- 💬 Issues: [GitHub Issues](https://github.com/mastoroshadiq-prog/sikades-lite/issues)

---

## 🎯 **Changelog**

### **Version 1.5.0** ✅ (Current - Dec 6, 2025)
- ✅ PDF Export with DOMPDF
- ✅ Excel Export with PhpSpreadsheet
- ✅ Activity Logging System
- ✅ Complete Report Views (BKU, APBDes, LRA, SPP, Pajak)
- ✅ View composition pattern fix

### **Version 1.0.0** (Dec 5, 2025)
- ✅ Complete APBDes module
- ✅ Complete SPP workflow
- ✅ Complete BKU with running balance
- ✅ Complete Pajak recording

### **Future Roadmap (v2.0)**
- [ ] Email notifications
- [ ] Multi-village support
- [ ] Year-end closing
- [ ] Budget proposal module
- [ ] Asset management
- [ ] Mobile app

---

## ⭐ **Star This Project!**

If you find this project useful, please give it a ⭐ on GitHub!

---

**Made with ❤️ for Indonesian Villages**

**Status:** 🟢 **Production Ready** | **Version:** 1.5.0 | **Last Update:** Dec 6, 2025

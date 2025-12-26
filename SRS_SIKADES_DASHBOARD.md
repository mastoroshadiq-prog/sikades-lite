# SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
## SIKADES Dashboard - Multi-Level Mobile & Web Application

---

**Document Version:** 1.0  
**Date:** 26 Desember 2024  
**Project:** SIKADES Dashboard (Flutter Web + Mobile)  
**Status:** Draft - Ready for Development  

---

## 📋 TABLE OF CONTENTS

1. [Introduction](#1-introduction)
2. [Overall Description](#2-overall-description)
3. [System Features](#3-system-features)
4. [External Interface Requirements](#4-external-interface-requirements)
5. [System Requirements](#5-system-requirements)
6. [Non-Functional Requirements](#6-non-functional-requirements)
7. [Technical Architecture](#7-technical-architecture)
8. [Data Models](#8-data-models)
9. [User Stories](#9-user-stories)
10. [Acceptance Criteria](#10-acceptance-criteria)

---

## 1. INTRODUCTION

### 1.1 Purpose
Dokumen ini menjelaskan spesifikasi lengkap untuk aplikasi **SIKADES Dashboard** - platform monitoring dan analytics multi-level untuk pemerintahan desa di Indonesia, yang akan dibangun menggunakan **Flutter** untuk platform **Mobile (Android/iOS)** dan **Web**.

### 1.2 Scope
Aplikasi ini akan menyediakan dashboard monitoring real-time dengan 4 level hierarki:
- **Level 1**: Dashboard Desa (untuk 1 desa)
- **Level 2**: Dashboard Kecamatan (agregasi dari 10-20 desa)
- **Level 3**: Dashboard Kabupaten/Kota (agregasi dari 15-30 kecamatan)
- **Level 4**: Dashboard Provinsi (agregasi dari 25-35 kabupaten)

### 1.3 Definitions, Acronyms, Abbreviations

| Term | Definition |
|------|------------|
| SRS | Software Requirements Specification |
| SIKADES | Sistem Informasi Keuangan dan Aset Desa |
| ADD | Alokasi Dana Desa |
| APBDes | Anggaran Pendapatan dan Belanja Desa |
| BKU | Buku Kas Umum |
| LRA | Laporan Realisasi Anggaran |
| LPJ | Laporan Pertanggungjawaban |
| SPP | Surat Permintaan Pembayaran |
| OLTP | Online Transaction Processing |
| OLAP | Online Analytical Processing |
| REST | Representational State Transfer |
| JWT | JSON Web Token |
| RBAC | Role-Based Access Control |

### 1.4 References
- Panduan Dashboard Multi-Level (PANDUAN_DASHBOARD_MULTI_LEVEL.md)
- Roadmap Fitur (ROADMAP_FITUR.md)
- Permendagri No. 20 Tahun 2018 tentang Pengelolaan Keuangan Desa
- Flutter Documentation (https://docs.flutter.dev)

### 1.5 Overview
Dokumen ini terdiri dari:
- **Section 2**: Deskripsi umum produk
- **Section 3**: Fitur-fitur sistem detail
- **Section 4-6**: Requirements teknis dan non-teknis
- **Section 7-8**: Arsitektur dan data model
- **Section 9-10**: User stories dan acceptance criteria

---

## 2. OVERALL DESCRIPTION

### 2.1 Product Perspective

**SIKADES Dashboard** adalah bagian dari ekosistem SIKADES yang lebih besar:

```
┌─────────────────────────────────────────────┐
│         SIKADES ECOSYSTEM                   │
├─────────────────────────────────────────────┤
│                                             │
│  [SIKADES Backend - CodeIgniter]            │
│     - Data entry & management               │
│     - Transaction processing (OLTP)         │
│     - Reports generation                    │
│                                             │
│  [SIKADES API Gateway - NestJS] ← NEW       │
│     - REST API endpoints                    │
│     - Authentication & Authorization        │
│     - Data aggregation layer                │
│     - Rate limiting & caching               │
│                                             │
│  [SIKADES Dashboard - Flutter] ← THIS DOC   │
│     - Mobile App (Android + iOS)            │
│     - Web App (Admin portal)                │
│     - Real-time monitoring                  │
│     - Analytics & visualization             │
│                                             │
│  [Database - PostgreSQL]                    │
│     - OLTP: Transactional data              │
│     - OLAP: Analytics data (aggregated)     │
│                                             │
└─────────────────────────────────────────────┘
```

### 2.2 Product Functions

Dashboard akan menyediakan fungsi utama:

#### 2.2.1 Authentication & Authorization
- Login dengan username/password
- Login dengan biometrik (mobile)
- Multi-factor authentication (OTP)
- Role-based access control (RBAC)
- Session management
- Logout & security

#### 2.2.2 Dashboard Level Desa
- **Keuangan**: Realisasi anggaran, cash flow, SPP pending
- **Demografi**: Populasi, mutasi penduduk, piramida usia
- **Pembangunan**: Progress proyek, budget vs actual
- **Kesehatan**: Stunting, imunisasi, ibu hamil risti
- **Pelayanan**: Surat issued, satisfaction score

#### 2.2.3 Dashboard Level Kecamatan
- Agregasi dari 10-20 desa
- Ranking & comparison desa
- Alert & notification (desa bermasalah)
- Drill-down ke detail desa
- Export reports

#### 2.2.4 Dashboard Level Kabupaten
- Agregasi dari 15-30 kecamatan
- Performance matrix kecamatan
- Trend analysis multi-year
- Executive summary
- Policy recommendation engine

#### 2.2.5 Dashboard Level Provinsi
- Agregasi dari 25-35 kabupaten
- National benchmarking
- Inter-provincial comparison
- Strategic planning dashboard
- Data export untuk Kemendagri

#### 2.2.6 Common Features
- Real-time data sync
- Offline mode (mobile)
- Interactive charts & maps
- Custom date range filter
- Export to PDF/Excel
- Notifications & alerts
- Search & filter data

### 2.3 User Classes and Characteristics

| User Class | Level Access | Technical Skill | Frequency | Priority |
|------------|--------------|-----------------|-----------|----------|
| Kepala Desa | Desa | Low-Medium | Daily | High |
| Bendahara Desa | Desa | Medium | Daily | High |
| Sekretaris Desa | Desa | Medium | Daily | High |
| Camat | Kecamatan | Medium | Daily | High |
| Kabag BPKAD | Kabupaten | High | Weekly | Medium |
| Kepala Bappeda | Kabupaten | High | Weekly | Medium |
| Sekda Provinsi | Provinsi | High | Monthly | Medium |
| Masyarakat | Desa (Read-only) | Low | Occasional | Low |

### 2.4 Operating Environment

#### 2.4.1 Mobile App
- **Android**: Android 6.0 (API 23) atau lebih tinggi
- **iOS**: iOS 11.0 atau lebih tinggi
- **Screen Size**: 4.7" hingga 12.9" (phone to tablet)
- **Orientation**: Portrait & Landscape
- **Storage**: Minimal 100 MB free space
- **Network**: 2G/3G/4G/5G/WiFi (offline capable)

#### 2.4.2 Web App
- **Browsers**: 
  - Chrome 90+ (recommended)
  - Firefox 88+
  - Safari 14+
  - Edge 90+
- **Screen Resolution**: 1280x720 minimum, 1920x1080 optimal
- **Network**: Broadband internet (minimal 1 Mbps)

### 2.5 Design and Implementation Constraints

#### 2.5.1 Technology Constraints
- **Framework**: Flutter 3.16+
- **Language**: Dart 3.0+
- **State Management**: Riverpod 2.0+
- **API Communication**: REST API dengan JSON
- **Local Database**: Hive / Isar
- **Charts**: fl_chart / syncfusion_flutter_charts
- **Maps**: google_maps_flutter / mapbox_gl

#### 2.5.2 Regulatory Constraints
- Harus comply dengan Permendagri No. 20 Tahun 2018
- Data privacy (UU PDP Indonesia)
- Government data security standards
- Audit trail untuk semua transaksi

#### 2.5.3 Performance Constraints
- App startup time: < 3 detik
- API response time: < 500ms (p95)
- UI refresh rate: 60 FPS
- Maximum offline storage: 500 MB
- Sync conflict resolution: Last-write-wins

### 2.6 Assumptions and Dependencies

#### 2.6.1 Assumptions
- Users memiliki smartphone Android/iOS atau akses komputer
- Internet connectivity tersedia (minimal intermittent)
- Backend API sudah tersedia dan stabil
- Data sudah ter-standarisasi di backend

#### 2.6.2 Dependencies
- **Backend API**: SIKADES API Gateway (NestJS)
- **Authentication Service**: OAuth 2.0 / JWT
- **Map Services**: Google Maps API / Mapbox
- **Push Notification**: Firebase Cloud Messaging (FCM)
- **Analytics**: Firebase Analytics / Mixpanel
- **Crash Reporting**: Sentry / Firebase Crashlytics

---

## 3. SYSTEM FEATURES

### 3.1 Authentication & User Management

#### 3.1.1 Description
Sistem autentikasi yang aman dengan multi-factor authentication dan session management.

#### 3.1.2 Functional Requirements

**FR-AUTH-001**: Login dengan Credentials
- User dapat login menggunakan username dan password
- Password harus minimal 8 karakter
- Login gagal 5x = account locked selama 15 menit
- Support "Remember Me" option (mobile)
- Support biometric login (fingerprint/face ID) setelah first login

**FR-AUTH-002**: Multi-Factor Authentication
- OTP via SMS untuk login dari device baru
- OTP valid selama 5 menit
- Maksimal 3x request OTP per 10 menit

**FR-AUTH-003**: Role-Based Access Control
- User hanya bisa akses data sesuai role dan scope wilayah
- Role hierarchy: Desa < Kecamatan < Kabupaten < Provinsi
- Permission matrix untuk setiap action (read/write/approve)

**FR-AUTH-004**: Session Management
- Session timeout setelah 30 menit inactivity (web)
- Session valid 7 hari (mobile dengan Remember Me)
- Concurrent session limit: 3 devices
- Force logout dari semua device option

**FR-AUTH-005**: Password Management
- Change password option
- Forgot password (reset via email/SMS)
- Password history (tidak boleh sama dengan 5 password terakhir)
- Password expiry setiap 90 hari (optional)

#### 3.1.3 Priority
**High** - 95% of features depend on this

---

### 3.2 Dashboard Level Desa

#### 3.2.1 Description
Dashboard comprehensive untuk monitoring aktivitas satu desa dengan 5 domain: Keuangan, Demografi, Pembangunan, Kesehatan, dan Pelayanan.

#### 3.2.2 Functional Requirements

**FR-DESA-001**: Ringkasan Keuangan
- Display total anggaran, realisasi pendapatan, realisasi belanja, saldo kas
- Show persentase realisasi (against target)
- Color coding: Hijau (>80%), Kuning (50-80%), Merah (<50%)
- Line chart trend realisasi bulanan (12 bulan)
- Pie chart proporsi belanja per bidang

**FR-DESA-002**: BKU Real-Time
- Table last 10 transactions dengan kolom: Tanggal, Kode Rekening, Uraian, Debit, Kredit, Saldo
- Filter by: Date range, Jenis (pendapatan/belanja), Rekening
- Search by uraian
- Detail transaction on tap/click
- Export to Excel option

**FR-DESA-003**: Monitoring SPP
- Card showing: SPP pending count, total amount
- List SPP dengan status: Draft, Submitted, Approved, Rejected
- Status badge dengan warna
- Detail SPP dengan approval workflow
- Notification jika SPP pending > 3 hari

**FR-DESA-004**: Demografi Overview
- Stats card: Total penduduk, laki-laki, perempuan, jumlah KK
- Piramida penduduk (age pyramid chart)
- Bar chart distribusi per dusun
- Pie chart tingkat pendidikan
- Mutasi bulan ini: Lahir, mati, pindah, datang

**FR-DESA-005**: Proyek Pembangunan
- Card stats: Total proyek, selesai, berjalan, belum mulai, mangkrak
- Gantt chart timeline proyek
- Progress bar per proyek dengan foto
- Map showing project locations
- Alert jika proyek progress < 30% di 50% timeline

**FR-DESA-006**: Kesehatan Masyarakat
- Stats: Balita total, stunting, ibu hamil, ibu hamil risti
- Coverage imunisasi (%) with target line
- Trend stunting 12 bulan (line chart)
- Alert list: Balita baru stunting, ibu hamil risti
- Map: Heat map lokasi stunting

**FR-DESA-007**: Pelayanan Publik
- Stats: Total surat bulan ini, by kategori
- Average processing time
- Customer satisfaction score (1-5 stars)
- Recent surat table (10 terakhir)
- Chart trend surat per bulan

#### 3.2.3 Priority
**High** - Core feature for primary users

---

### 3.3 Dashboard Level Kecamatan

#### 3.3.1 Description
Dashboard agregasi untuk monitoring 10-20 desa dalam satu kecamatan dengan fitur comparison dan ranking.

#### 3.3.2 Functional Requirements

**FR-KEC-001**: Overview Kecamatan
- Total desa count
- Agregat anggaran seluruh desa
- Average realisasi (%)
- Performance distribution: Hijau (n desa), Kuning (n), Merah (n)

**FR-KEC-002**: Ranking Desa
- Table ranking dengan kolom: 
  - Rank, Nama Desa, Realisasi %, Status (emoji/badge)
- Multi-indicator ranking:
  - Realisasi keuangan (30%)
  - Kelengkapan laporan (25%)
  - Kecepatan input data (20%)
  - Pelayanan publik (15%)
  - Partisipasi warga (10%)
- Sortable by each indicator
- Drill-down ke dashboard desa on tap

**FR-KEC-003**: Comparison Matrix
- Clustered bar chart: Realisasi semua desa side-by-side
- Heat map table: Desa (rows) x Indicators (columns)
- Color gradient: Red → Yellow → Green
- Filter by date range

**FR-KEC-004**: Alert & Monitoring
- Alert list dengan priority:
  - 🔴 URGENT (realisasi <50%, tutup buku belum)
  - 🟡 WARNING (realisasi 50-70%)
  - 🟢 INFO (on track)
- Filter by priority
- Send reminder to Kepala Desa (push notification)

**FR-KEC-005**: Geographic View
- Choropleth map kecamatan
- Desa color-coded by performance
- Pin showing project locations
- Click desa → show summary popup
- Toggle layers: Realisasi, Stunting, Projects

**FR-KEC-006**: Trend Analysis
- Line chart: Agregat realisasi kecamatan vs target (12 bulan)
- Comparison with previous year (YoY)
- Forecast untuk 3 bulan ke depan (simple linear)
- Export chart as image

#### 3.3.3 Priority
**High** - Critical for supervision

---

### 3.4 Dashboard Level Kabupaten

#### 3.4.1 Description
Dashboard executive untuk monitoring seluruh kabupaten dengan analytics advanced dan policy recommendations.

#### 3.4.2 Functional Requirements

**FR-KAB-001**: Executive Summary
- Total kecamatan, total desa count
- Total alokasi ADD kabupaten
- Average realisasi kabupaten
- Ranking kabupaten (dalam provinsi)
- Performance trend 3 tahun (line chart)

**FR-KAB-002**: Kecamatan Comparison
- Table ranking kecamatan dengan multi-indicator
- Bubble chart: 3 dimensi (realisasi, jumlah desa, total budget)
- Radar chart: Multi-indicator per kecamatan
- Top 3 performers & Bottom 3

**FR-KAB-003**: Sektor Analysis
- Treemap: Proporsi budget per sektor (Infrastruktur, Ekonomi, Pendidikan, Kesehatan)
- Waterfall chart: Contribution masing-masing sektor ke total realisasi
- Pareto chart: 80/20 rule analysis

**FR-KAB-004**: Compliance Dashboard
- Kelengkapan laporan (%): LPJ Semester, Tutup Buku, SPJ APBDes
- Table desa yang belum laporan dengan reminder button
- Audit findings summary (BPK, Inspektorat)
- Clean opinion percentage

**FR-KAB-005**: Impact Measurement
- Infrastructure impact: Jalan diperbaiki (km), Jembatan dibangun (unit)
- Social welfare: Kemiskinan (%), Stunting (%), Pengangguran (%)
- Economic: UMKM tumbuh, Pendapatan Asli Desa
- Before-After comparison (start of year vs current)

**FR-KAB-006**: Data Export & Reports
- Export executive summary to PDF
- Export detailed data to Excel (all kecamatan)
- Schedule automated monthly report (email)
- Custom report builder (drag & drop fields)

#### 3.4.3 Priority
**Medium** - Important for strategic planning

---

### 3.5 Dashboard Level Provinsi

#### 3.5.1 Description
Dashboard strategic untuk overview seluruh provinsi dengan benchmarking nasional.

#### 3.5.2 Functional Requirements

**FR-PROV-001**: Provincial Overview
- Total kabupaten/kota count
- Total kecamatan dan desa count
- Total alokasi ADD provinsi
- Average realisasi provinsi
- Ranking nasional (among 34 provinsi)

**FR-PROV-002**: Kabupaten Benchmarking
- Table ranking kabupaten/kota
- Choropleth map provinsi (color by performance)
- Comparison with top 3 provinsi nasional
- Gap analysis to #1 rank

**FR-PROV-003**: National Comparison
- Bar chart: Provinsi ranking nasional (top 10)
- Multi-indicator comparison: Realisasi ADD, Governance Score, Inovasi Desa
- Your position highlighted
- Trend over 5 years

**FR-PROV-004**: Strategic Analytics
- Correlation analysis: Realisasi vs Kemiskinan, vs Pendidikan
- Predictive model: End-of-year forecast
- Risk assessment matrix (Impact vs Probability)
- Recommendation engine based on data

**FR-PROV-005**: Policy Dashboard
- Balanced scorecard: Financial, Customer, Process, Learning perspectives
- Strategy map: Cause-effect relationships
- OKR (Objectives & Key Results) tracking
- Milestone timeline for provincial initiatives

**FR-PROV-006**: Data Integration
- Export data to Kemendagri format
- SIPD (Sistem Informasi Pemerintahan Daerah) integration
- API for third-party analytics tools
- Real-time dashboard untuk Gubernur (public display)

#### 3.5.3 Priority
**Medium** - Strategic importance

---

### 3.6 Cross-Cutting Features

#### 3.6.1 Offline Mode (Mobile Only)

**FR-OFFLINE-001**: Data Sync
- Auto-sync saat online
- Manual sync button
- Last sync timestamp display
- Sync conflict resolution (server wins)
- Sync progress indicator

**FR-OFFLINE-002**: Local Cache
- Cache last 30 days data
- Prioritize: Dashboard stats, Charts data, Recent transactions
- Max storage: 500 MB
- Clear cache option

#### 3.6.2 Notifications

**FR-NOTIF-001**: Push Notifications (Mobile)
- Alert untuk deadline (tutup buku, laporan)
- SPP approval notification
- Proyek mangkrak alert
- New message dari atasan
- Configurable notification preferences

**FR-NOTIF-002**: In-App Notifications
- Badge count di navigation bar
- Notification center page
- Mark as read/unread
- Clear all option
- Notification history (30 hari)

#### 3.6.3 Search & Filter

**FR-SEARCH-001**: Global Search
- Search by: Desa name, Kecamatan, Project name, Transaction uraian
- Recent searches saved
- Search suggestions
- Filter results by type

**FR-FILTER-001**: Advanced Filters
- Date range picker
- Multi-select filters (Status, Kategori, dll)
- Save filter presets
- Reset filter button

#### 3.6.4 Export & Share

**FR-EXPORT-001**: Data Export
- Export chart sebagai image (PNG/JPEG)
- Export table as Excel/CSV
- Export dashboard as PDF report
- Email report option

**FR-SHARE-001**: Share Data
- Share chart via WhatsApp/Email
- Generate shareable link (dengan expiry)
- Copy data to clipboard
- Screenshot & annotate

---

## 4. EXTERNAL INTERFACE REQUIREMENTS

### 4.1 User Interfaces

#### 4.1.1 Design Principles
- **Material Design 3** (Android)
- **Cupertino Design** (iOS) 
- **Responsive Design** (Web)
- **Accessibility**: WCAG 2.1 Level AA compliant
- **Dark Mode**: Support system theme

#### 4.1.2 Screen Specifications

**Mobile Portrait (Phone):**
- Width: 360dp - 428dp
- Height: 640dp - 926dp
- Safe area: Account for notch/camera cutout
- Bottom navigation bar (5 tabs max)

**Mobile Landscape (Phone):**
- Side navigation drawer
- Dual-pane layout for tablet

**Tablet:**
- Master-detail layout
- Use of split view
- Floating action buttons

**Web (Desktop):**
- Minimum: 1280x720px
- Optimal: 1920x1080px
- Max width container: 1440px (centered)
- Sidebar navigation (persistent)

#### 4.1.3 Navigation Structure

```
Bottom Navigation (Mobile):
├── 🏠 Home (Dashboard utama sesuai level)
├── 📊 Analytics (Charts & graphs)
├── 📍 Map (Geographic view)
├── 🔔 Notifications
└── 👤 Profile (Settings & logout)

Sidebar (Web):
├── Dashboard
│   ├── Desa (if role = kepala_desa)
│   ├── Kecamatan (if role = camat)
│   ├── Kabupaten (if role = kabag)
│   └── Provinsi (if role = gubernur)
├── Keuangan
│   ├── Realisasi Anggaran
│   ├── BKU
│   └── SPP
├── Demografi
│   ├── Penduduk
│   └── Mutasi
├── Pembangunan
│   ├── Proyek
│   └── Progress
├── Kesehatan
│   ├── Stunting
│   └── Imunisasi
├── Laporan
│   ├── Templates
│   └── Export
└── Pengaturan
    ├── Profile
    ├── Notifications
    └── About
```

#### 4.1.4 UI Components Library

Use Flutter's standard widgets plus:
- `fl_chart` for charts
- `google_maps_flutter` for maps
- `flutter_spinkit` for loaders
- `shimmer` for skeleton loading
- `cached_network_image` for images

### 4.2 Hardware Interfaces

#### 4.2.1 Mobile Sensors
- **GPS**: For location-based features (project location, attendance)
- **Camera**: For photo upload (project progress, evidence)
- **Biometric**: Fingerprint/Face ID for authentication
- **Storage**: Access to internal storage for offline data

#### 4.2.2 Network
- **WiFi**: Preferred for data sync
- **Mobile Data**: 2G/3G/4G/5G support
- **Bluetooth**: (Future) For offline peer-to-peer sync

### 4.3 Software Interfaces

#### 4.3.1 Backend API
- **Protocol**: HTTPS REST API
- **Data Format**: JSON
- **Authentication**: Bearer Token (JWT)
- **Base URL**: https://api.sikades.id/v1
- **Rate Limit**: 1000 requests/hour per user
- **Timeout**: 30 seconds

#### 4.3.2 Third-Party Services

**Google Maps API:**
- Purpose: Display maps, geocoding
- Key required: Android & iOS separate
- Usage limit: Monitor quota

**Firebase Cloud Messaging (FCM):**
- Purpose: Push notifications
- Platforms: Android, iOS, Web
- Max payload: 4 KB

**Firebase Analytics:**
- Purpose: User behavior tracking
- Events: Screen views, button clicks, errors
- Privacy: Anonymous user ID

**Sentry:**
- Purpose: Error tracking & crash reporting
- DSN: Configure per environment
- Release tracking: Sync with version

### 4.4 Communication Interfaces

#### 4.4.1 HTTP API Communication
- **Method**: GET, POST, PUT, DELETE
- **Headers**:
  ```
  Authorization: Bearer {jwt_token}
  Content-Type: application/json
  Accept: application/json
  X-App-Version: {app_version}
  X-Device-ID: {device_id}
  ```
- **Status Codes**:
  - 200: Success
  - 201: Created
  - 400: Bad Request
  - 401: Unauthorized
  - 403: Forbidden
  - 404: Not Found
  - 429: Too Many Requests
  - 500: Server Error

#### 4.4.2 WebSocket (Optional for Real-Time)
- **Protocol**: WSS (WebSocket Secure)
- **Events**: `transaction.created`, `approval.updated`, `alert.triggered`
- **Reconnection**: Exponential backoff (1s, 2s, 4s, 8s, max 30s)

---

## 5. SYSTEM REQUIREMENTS

### 5.1 Functional Requirements Summary

| ID | Category | Requirement | Priority |
|----|----------|-------------|----------|
| FR-AUTH-001 | Authentication | Login with credentials | Must Have |
| FR-AUTH-002 | Authentication | Multi-factor auth | Should Have |
| FR-DESA-001 | Dashboard Desa | Keuangan summary | Must Have |
| FR-DESA-005 | Dashboard Desa | Proyek tracking | Must Have |
| FR-KEC-001 | Dashboard Kecamatan | Overview | Must Have |
| FR-KEC-002 | Dashboard Kecamatan | Ranking desa | Must Have |
| FR-KAB-001 | Dashboard Kabupaten | Executive summary | Should Have |
| FR-PROV-001 | Dashboard Provinsi | Provincial overview | Could Have |
| FR-OFFLINE-001 | Offline | Data sync | Must Have (Mobile) |
| FR-NOTIF-001 | Notification | Push notification | Should Have |

### 5.2 Data Requirements

#### 5.2.1 Data Storage
- **Local Storage (Mobile)**: Hive/Isar - Max 500 MB
- **Cache (Web)**: IndexedDB - Max 50 MB
- **Secure Storage**: flutter_secure_storage for tokens

#### 5.2.2 Data Sync
- **Frequency**: Every 5 minutes when active & online
- **Batch Size**: Max 1000 records per request
- **Priority**: Critical data (SPP, Approval) first

#### 5.2.3 Data Retention
- **Local**: Keep last 30 days
- **Cloud**: Unlimited (server-side)
- **Cache TTL**: 15 minutes for dynamic data, 24 hours for static

---

## 6. NON-FUNCTIONAL REQUIREMENTS

### 6.1 Performance Requirements

**PERF-001**: App Launch Time
- Cold start: < 3 seconds
- Warm start: < 1 second
- Measured on mid-range device (Snapdragon 660 equivalent)

**PERF-002**: API Response Time
- P50: < 200ms
- P95: < 500ms
- P99: < 1000ms
- Timeout: 30s

**PERF-003**: UI Responsiveness
- Touch response: < 100ms
- Frame rate: 60 FPS sustained
- Jank-free scrolling using `ListView.builder`

**PERF-004**: Data Loading
- Initial dashboard load: < 2 seconds
- Pagination load more: < 500ms
- Image loading: Progressive (low-res → high-res)

**PERF-005**: Memory Usage
- Idle: < 100 MB (mobile)
- Active usage: < 200 MB (mobile)
- Peak: < 300 MB (mobile)
- No memory leaks (profiled)

### 6.2 Security Requirements

**SEC-001**: Data Encryption
- Data in transit: TLS 1.3
- Data at rest: AES-256 for local storage
- Token storage: Secure storage (Keychain/Keystore)

**SEC-002**: Authentication
- Password hashing: bcrypt (backend)
- JWT expiry: 15 minutes (access), 7 days (refresh)
- Auto-logout on token expiry

**SEC-003**: Authorization
- Role-based access control (RBAC)
- Scope-based access (wilayah hierarchy)
- API permission check on every request

**SEC-004**: Input Validation
- Client-side validation (UX)
- Server-side validation (security)
- Sanitize all user input
- SQL injection prevention (backend)

**SEC-005**: Audit Trail
- Log all critical actions (create, update, delete, approve)
- Include: User, timestamp, action, resource, IP
- Tamper-proof logging (append-only)

### 6.3 Reliability Requirements

**REL-001**: Availability
- Mobile app: N/A (offline capable)
- Web app: 99.5% uptime (SLA)
- Backend API: 99.9% uptime

**REL-002**: Error Handling
- Graceful degradation on API errors
- User-friendly error messages
- Retry logic dengan exponential backoff
- Offline fallback untuk critical features

**REL-003**: Data Integrity
- Atomic transactions (all-or-nothing)
- Foreign key constraints
- Checksum validation untuk sync data
- Conflict resolution strategy documented

**REL-004**: Backup & Recovery
- Auto-backup local data setiap sync
- Manual export option
- Restore from backup feature

### 6.4 Scalability Requirements

**SCALE-001**: User Concurrency
- Support 10,000 concurrent users (web)
- Support 100,000 DAU (daily active users) mobile

**SCALE-002**: Data Volume
- Handle 10 million rows (transactions)
- 100,000 desa data
- Pagination for large datasets (500 rows page)

**SCALE-003**: Response Time Under Load
- Performance tidak degrade > 20% pada 80% peak load
- Load test dengan 5x expected traffic

### 6.5 Maintainability Requirements

**MAINT-001**: Code Quality
- Test coverage: > 80% (unit + integration)
- Linting: Pass `flutter analyze` with 0 errors
- Code review: Required untuk semua PR
- Documentation: Inline comments untuk complex logic

**MAINT-002**: Modularity
- Clean Architecture (Presentation, Domain, Data layers)
- Feature-based folder structure
- Dependency injection (get_it / riverpod)
- No circular dependencies

**MAINT-003**: Logging & Debugging
- Structured logging (JSON format)
- Log levels: Debug, Info, Warning, Error, Fatal
- Remote logging untuk production crashes
- Debug mode dengan mock data option

### 6.6 Usability Requirements

**USE-001**: Learning Curve
- First-time user dapat navigate dashboard dalam < 5 menit
- Onboarding tutorial (optional skip)
- Tooltips untuk fitur complex
- Help documentation in-app

**USE-002**: Accessibility
- Font size adjustable (small, medium, large)
- High contrast mode
- Screen reader compatible
- Touch targets minimal 48dp x 48dp

**USE-003**: Internationalization
- Support Bahasa Indonesia (primary)
- RTL layout ready (future Arabic support)
- Number format: Indonesian (1.000.000,00)
- Date format: DD/MM/YYYY

**USE-004**: Responsive Design
- Auto-adapt to screen size
- Orientation change handled
- Keyboard show/hide smooth
- No horizontal scroll

### 6.7 Portability Requirements

**PORT-001**: Cross-Platform
- Single codebase untuk Android, iOS, Web
- Platform-specific code < 5% (abstracted)
- Consistent UX across platforms (95% similarity)

**PORT-002**: Browser Compatibility
- Chrome: Full support
- Firefox: Full support
- Safari: Full support
- Edge: Full support
- IE11: Not supported (show upgrade message)

**PORT-003**: Device Compatibility
- Screen sizes: 4.7" to 12.9"
- Pixel densities: ldpi to xxxhdpi
- CPU architectures: ARM, ARM64, x86, x64

---

## 7. TECHNICAL ARCHITECTURE

### 7.1 Application Architecture

**Clean Architecture Pattern:**

```
┌──────────────────────────────────────────────────┐
│              PRESENTATION LAYER                  │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │   Pages    │  │  Widgets   │  │ Controllers│ │
│  │            │  │            │  │  (Riverpod)│ │
│  └────────────┘  └────────────┘  └────────────┘ │
└────────────────────┬─────────────────────────────┘
                     │ Depends on
┌────────────────────┴─────────────────────────────┐
│               DOMAIN LAYER                       │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │  Entities  │  │ Use Cases  │  │Repositories│ │
│  │            │  │            │  │(Interfaces)│ │
│  └────────────┘  └────────────┘  └────────────┘ │
└────────────────────┬─────────────────────────────┘
                     │ Implements
┌────────────────────┴─────────────────────────────┐
│                DATA LAYER                        │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐ │
│  │   Models   │  │ Repository │  │Data Sources│ │
│  │            │  │ Impls      │  │(API, Local)│ │
│  └────────────┘  └────────────┘  └────────────┘ │
└──────────────────────────────────────────────────┘
```

### 7.2 Folder Structure

```
lib/
├── main.dart
├── app.dart
├── core/
│   ├── constants/
│   │   ├── api_constants.dart
│   │   ├── app_constants.dart
│   │   └── assets_constants.dart
│   ├── errors/
│   │   ├── exceptions.dart
│   │   └── failures.dart
│   ├── network/
│   │   ├── api_client.dart
│   │   ├── dio_interceptor.dart
│   │   └── network_info.dart
│   ├── theme/
│   │   ├── app_theme.dart
│   │   ├── colors.dart
│   │   └── text_styles.dart
│   ├── utils/
│   │   ├── date_formatter.dart
│   │   ├── number_formatter.dart
│   │   └── validators.dart
│   └── injection_container.dart
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── datasources/
│   │   │   │   ├── auth_local_datasource.dart
│   │   │   │   └── auth_remote_datasource.dart
│   │   │   ├── models/
│   │   │   │   ├── user_model.dart
│   │   │   │   └── token_model.dart
│   │   │   └── repositories/
│   │   │       └── auth_repository_impl.dart
│   │   ├── domain/
│   │   │   ├── entities/
│   │   │   │   └── user.dart
│   │   │   ├── repositories/
│   │   │   │   └── auth_repository.dart
│   │   │   └── usecases/
│   │   │       ├── login.dart
│   │   │       ├── logout.dart
│   │   │       └── check_auth_status.dart
│   │   └── presentation/
│   │       ├── pages/
│   │       │   ├── login_page.dart
│   │       │   └── otp_page.dart
│   │       ├── widgets/
│   │       │   ├── login_form.dart
│   │       │   └── password_field.dart
│   │       └── providers/
│   │           └── auth_provider.dart
│   ├── dashboard_desa/
│   │   ├── data/
│   │   ├── domain/
│   │   └── presentation/
│   ├── dashboard_kecamatan/
│   ├── dashboard_kabupaten/
│   └── dashboard_provinsi/
└── shared/
    ├── widgets/
    │   ├── custom_button.dart
    │   ├── custom_text_field.dart
    │   ├── loading_indicator.dart
    │   └── error_widget.dart
    └── extensions/
        ├── context_extension.dart
        └── date_extension.dart
```

### 7.3 State Management

**Riverpod Architecture:**

```dart
// Provider definition
final dashboardProvider = FutureProvider.autoDispose
  .family<DashboardData, String>((ref, kodeDesa) async {
    final repository = ref.watch(dashboardRepositoryProvider);
    return await repository.getDashboard(kodeDesa);
});

// Consumer in UI
class DashboardPage extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final dashboardAsync = ref.watch(dashboardProvider(kodeDesa));
    
    return dashboardAsync.when(
      data: (data) => DashboardView(data: data),
      loading: () => LoadingIndicator(),
      error: (error, stack) => ErrorView(error: error),
    );
  }
}
```

### 7.4 Navigation

**Auto Route (Type-safe routing):**

```dart
@MaterialAutoRouter(
  replaceInRouteName: 'Page,Route',
  routes: <AutoRoute>[
    AutoRoute(page: SplashPage, initial: true),
    AutoRoute(page: LoginPage),
    AutoRoute(
      page: MainPage,
      children: [
        AutoRoute(page: DashboardPage),
        AutoRoute(page: AnalyticsPage),
        AutoRoute(page: MapPage),
        AutoRoute(page: NotificationPage),
        AutoRoute(page: ProfilePage),
      ],
    ),
  ],
)
class $AppRouter {}
```

### 7.5 Local Database

**Hive (NoSQL, Fast):**

```dart
// Model
@HiveType(typeId: 0)
class DashboardCache extends HiveObject {
  @HiveField(0)
  String kodeDesa;
  
  @HiveField(1)
  Map<String, dynamic> data;
  
  @HiveField(2)
  DateTime cachedAt;
}

// Usage
final box = await Hive.openBox<DashboardCache>('dashboard');
box.put(kodeDesa, DashboardCache(...));
final cached = box.get(kodeDesa);
```

---

## 8. DATA MODELS

### 8.1 Core Entities

#### 8.1.1 User

```dart
class User {
  final int id;
  final String username;
  final String email;
  final String role; // 'kepala_desa', 'camat', 'kabag', 'gubernur'
  final String level; // 'desa', 'kecamatan', 'kabupaten', 'provinsi'
  final String kodeDesa; // '3216012001'
  final String kodeKecamatan; // '321601'
  final String kodeKabupaten; // '3216'
  final String kodeProvinsi; // '32'
  final String? photoUrl;
  final DateTime createdAt;
  final DateTime lastLoginAt;
  
  const User({...});
  
  Map<String, dynamic> toJson() => {...};
  factory User.fromJson(Map<String, dynamic> json) => User(...);
}
```

#### 8.1.2 DashboardDesa

```dart
class DashboardDesa {
  final Stats keuangan;
  final Stats demografi;
  final Stats pembangunan;
  final Stats kesehatan;
  final Stats pelayanan;
  final List<Transaction> recentTransactions;
  final List<Project> activeProjects;
  final List<Alert> alerts;
  
  const DashboardDesa({...});
}

class Stats {
  final String title;
  final dynamic value;
  final String? unit;
  final double? percentage;
  final Trend trend; // 'up', 'down', 'stable'
  final String? color; // 'green', 'yellow', 'red'
  
  const Stats({...});
}
```

#### 8.1.3 DashboardKecamatan

```dart
class DashboardKecamatan {
  final String kodeKecamatan;
  final String namaKecamatan;
  final int jumlahDesa;
  final double totalAnggaran;
  final double avgRealisasi;
  final PerformanceDistribution distribution;
  final List<DesaRanking> ranking;
  final List<Alert> alerts;
  final Map<String, List<ChartData>> charts;
  
  const DashboardKecamatan({...});
}

class DesaRanking {
  final int rank;
  final String kodeDesa;
  final String namaDesa;
  final double score;
  final double realisasiPersen;
  final String status; // 'green', 'yellow', 'red'
  final Map<String, double> indicators;
  
  const DesaRanking({...});
}
```

### 8.2 API Response Models

```dart
class ApiResponse<T> {
  final bool success;
  final String? message;
  final T? data;
  final Map<String, dynamic>? errors;
  final Map<String, dynamic>? meta; // pagination, etc
  
  const ApiResponse({...});
  
  factory ApiResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Object? json) fromJsonT,
  ) => ApiResponse(...);
}
```

---

## 9. USER STORIES

### 9.1 As Kepala Desa

**US-001**: Monitoring Keuangan Real-Time
```
As a Kepala Desa
I want to see real-time keuangan dashboard
So that I can monitor realisasi anggaran setiap saat

Acceptance Criteria:
- Dashboard shows: Total anggaran, Realisasi pendapatan, Realisasi belanja, Saldo kas
- Data auto-refresh setiap 5 menit saat online
- Dapat lihat trend 12 bulan terakhir dalam line chart
- Color coding: Hijau (>80%), Kuning (50-80%), Merah (<50%)
```

**US-002**: Approval SPP
```
As a Kepala Desa
I want to approve/reject SPP from my mobile
So that I don't need to be in office untuk approval

Acceptance Criteria:
- Notifikasi push saat ada SPP baru submit
- Dapat lihat detail SPP (nomor, judul, amount, supporting docs)
- Button: Approve / Reject dengan catatan
- Confirmation dialog sebelum final action
- History approval tercatat with timestamp
```

### 9.2 As Camat

**US-003**: Ranking Desa
```
As a Camat
I want to see ranking all desa in my kecamatan
So that I can identify which desa needs assistance

Acceptance Criteria:
- Table ranking dengan multi-indicator
- Can sort by each indicator
- Tap desa row → navigate to desa detail
- Export ranking to Excel
- Filter by date range
```

**US-004**: Send Alert to Desa
```
As a Camat
I want to send reminder to desa yang realisasinya rendah
So that they improve their performance

Acceptance Criteria:
- Alert list dengan priority filter
- Select desa → "Send Reminder" button
- Template message dengan custom note option
- Reminder sent via push notification & email
- Log reminder history
```

### 9.3 As Bupati

**US-005**: Executive Summary
```
As a Bupati
I want to see executive summary of all kabupaten
So that I can present to DPRD in quick meeting

Acceptance Criteria:
- One-page summary dengan key metrics
- Visual charts (pie, bar, trend line)
- Export to PDF dengan logo daerah
- Can customize date range
- Print-friendly layout
```

---

## 10. ACCEPTANCE CRITERIA

### 10.1 General Criteria

**AC-GEN-001**: All screens must load within 3 seconds  
**AC-GEN-002**: App must work offline untuk last cached data  
**AC-GEN-003**: No crashes for common user flows (100 iterations test)  
**AC-GEN-004**: Data accuracy 100% (match with backend)  
**AC-GEN-005**: Responsive across all screen sizes (phone, tablet, desktop)  

### 10.2 Feature-Specific Criteria

**Dashboard Desa:**
- ✅ Show 5 main stats cards (Keuangan, Demografi, Pembangunan, Kesehatan, Pelayanan)
- ✅ Charts load within 2 seconds
- ✅ Pull-to-refresh works
- ✅ Tap card → navigate to detail page
- ✅ Export data button available

**Dashboard Kecamatan:**
- ✅ Agregasi data dari semua desa correct
- ✅ Ranking calculation accurate
- ✅ Map shows all desa locations
- ✅ Drill-down to desa works
- ✅ Alert system functional

**Authentication:**
- ✅ Login with valid credentials succeeds
- ✅ Login with invalid credentials shows error
- ✅ Biometric login works (if device supports)
- ✅ Session timeout after 30 mins inactivity
- ✅ Logout clears all local data

---

## APPENDIX A: GLOSSARY

| Term | Definition |
|------|------------|
| ADD | Alokasi Dana Desa - Budget dari pemerintah pusat untuk desa |
| APBDes | Anggaran Pendapatan dan Belanja Desa - Village annual budget |
| DAU | Daily Active Users |
| JWT | JSON Web Token - Token format untuk authentication |
| OKR | Objectives and Key Results - Goal-setting framework |
| OLAP | Online Analytical Processing - For analytics queries |
| OLTP | Online Transaction Processing - For transactional data |
| RBAC | Role-Based Access Control |
| SLA | Service Level Agreement |
| YoY | Year over Year - Comparison with previous year |

---

## APPENDIX B: REVISION HISTORY

| Version | Date | Author | Changes |
|---------|------|--------|---------|
| 0.1 | 2024-12-26 | AI Assistant | Initial draft |
| 0.2 | 2024-12-26 | AI Assistant | Added user stories |
| 1.0 | 2024-12-26 | AI Assistant | Complete SRS ready for development |

---

**End of Document**

---

**Next Steps:**
1. Review SRS dengan stakeholders
2. Create API Documentation (separate doc)
3. Setup Flutter project dengan folder structure
4. Implement authentication module (Sprint 1)
5. Implement Dashboard Desa (Sprint 2-3)

**Estimated Development Timeline:**
- Setup & Auth: 2 weeks
- Dashboard Desa: 3 weeks
- Dashboard Kecamatan: 2 weeks
- Dashboard Kabupaten: 2 weeks
- Dashboard Provinsi: 1 week
- Testing & Polish: 2 weeks
**Total: 12 weeks (3 months) for MVP**

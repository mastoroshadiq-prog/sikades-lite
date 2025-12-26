# PANDUAN DASHBOARD MULTI-LEVEL
## Sistem Dashboard Bertingkat: Desa → Kecamatan → Kabupaten → Provinsi

---

## 📋 DAFTAR ISI

1. [Konsep Multi-Level Dashboard](#konsep)
2. [Level 1: Dashboard Desa](#level-1-dashboard-desa)
3. [Level 2: Dashboard Kecamatan](#level-2-dashboard-kecamatan)
4. [Level 3: Dashboard Kabupaten/Kota](#level-3-dashboard-kabupatenkota)
5. [Level 4: Dashboard Provinsi](#level-4-dashboard-provinsi)
6. [Arsitektur Teknis](#arsitektur-teknis)
7. [Database Design](#database-design)
8. [API & Data Flow](#api--data-flow)
9. [Security & Access Control](#security--access-control)
10. [Implementation Roadmap](#implementation-roadmap)

---

## 🎯 KONSEP MULTI-LEVEL DASHBOARD

### Prinsip Dasar

**Hierarki Data:**
```
PROVINSI (1)
  ├── KABUPATEN/KOTA (35)
  │     ├── KECAMATAN (266)
  │     │     ├── DESA/KELURAHAN (5,136)
  │     │     │     ├── DUSUN
  │     │     │     │     ├── RW
  │     │     │     │     │     └── RT
```

**Data Flow:**
```
Desa (Input Detail) 
  → Agregasi Kecamatan
    → Agregasi Kabupaten
      → Agregasi Provinsi
```

**Prinsip Akses:**
- **Bottom-Up**: Data naik dari desa ke provinsi
- **Top-Down**: Monitoring turun dari provinsi ke desa
- **Role-Based**: Setiap level hanya akses data di scope-nya

---

## 📊 LEVEL 1: DASHBOARD DESA

### Target User
- Kepala Desa
- Sekretaris Desa
- Bendahara Desa
- Perangkat Desa
- Masyarakat (Public View)

### Key Metrics

#### A. KEUANGAN
```
┌─────────────────────────────────────┐
│ RINGKASAN KEUANGAN 2024             │
├─────────────────────────────────────┤
│ Total Anggaran    : Rp 2.500.000.000│
│ Realisasi Pendapatan: Rp 2.300.000.000 (92%)│
│ Realisasi Belanja : Rp 1.850.000.000 (74%)│
│ Saldo Kas         : Rp 450.000.000  │
│ SPP Pending       : 3 Dokumen       │
└─────────────────────────────────────┘
```

**Detail Metrics:**
- Realisasi per Bidang (4.1-4.9 untuk Pendapatan, 5.1-5.4 untuk Belanja)
- Trend bulanan (grafik line)
- Perbandingan tahun lalu (YoY %)
- Top 5 Belanja terbesar
- Cash flow projection

#### B. DEMOGRAFI
```
┌─────────────────────────────────────┐
│ KEPENDUDUKAN                        │
├─────────────────────────────────────┤
│ Total Penduduk    : 5,432 jiwa     │
│ Laki-laki         : 2,715 (50%)    │
│ Perempuan         : 2,717 (50%)    │
│ Jumlah KK         : 1,632          │
│ Mutasi Bulan Ini:                  │
│   - Lahir  : 5                     │
│   - Mati   : 2                     │
│   - Pindah : 3                     │
│   - Datang : 1                     │
└─────────────────────────────────────┘
```

**Detail Metrics:**
- Piramida penduduk (age pyramid)
- Distribusi per dusun
- Tingkat pendidikan
- Mata pencaharian
- Status perkawinan

#### C. PEMBANGUNAN
```
┌─────────────────────────────────────┐
│ PROYEK PEMBANGUNAN                  │
├─────────────────────────────────────┤
│ Total Proyek : 12                   │
│ Selesai      : 5  (42%)            │
│ Berjalan     : 6  (50%)            │
│ Belum Mulai  : 1  (8%)             │
│ Mangkrak     : 0  (0%)             │
│                                     │
│ Total Anggaran: Rp 850.000.000     │
│ Realisasi     : Rp 420.000.000 (49%)│
└─────────────────────────────────────┘
```

**Detail Metrics:**
- Progress per proyek (gantt chart)
- Monitoring foto progress
- Budget vs actual per proyek
- Timeline compliance

#### D. KESEHATAN
```
┌─────────────────────────────────────┐
│ KESEHATAN MASYARAKAT                │
├─────────────────────────────────────┤
│ Balita Total      : 342 anak        │
│ Balita Stunting   : 15 (4.4%)       │
│ Ibu Hamil         : 23 orang        │
│ Ibu Hamil Risti   : 3 (13%)         │
│ Coverage Imunisasi: 87%             │
│ Posyandu Aktif    : 3 unit          │
└─────────────────────────────────────┘
```

#### E. PELAYANAN
```
┌─────────────────────────────────────┐
│ PELAYANAN PUBLIK                    │
├─────────────────────────────────────┤
│ Surat Bulan Ini  : 47 surat         │
│ - Keterangan     : 28               │
│ - Pengantar      : 15               │
│ - Lainnya        : 4                │
│                                     │
│ Avg. Processing  : 12 menit         │
│ Satisfaction     : 4.7/5 ⭐         │
└─────────────────────────────────────┘
```

### Visualisasi

**Charts:**
1. **Line Chart**: Trend realisasi bulanan
2. **Bar Chart**: Realisasi per bidang
3. **Pie Chart**: Proporsi belanja
4. **Map**: Sebaran proyek pembangunan
5. **Heatmap**: Kepadatan penduduk per dusun

**Tables:**
- Recent transactions (BKU)
- Active projects
- Upcoming deadlines

### Actions
- ✏️ Input data transaksi
- 📊 Generate laporan
- 🔔 Set reminder
- 📤 Export data
- 👥 Manajemen user

---

## 📊 LEVEL 2: DASHBOARD KECAMATAN

### Target User
- Camat
- Sekretaris Kecamatan
- Staff Kecamatan
- Pendamping Desa

### Key Metrics

#### A. AGREGAT KEUANGAN
```
┌─────────────────────────────────────┐
│ KEUANGAN 15 DESA DI KEC. PARONGPONG │
├─────────────────────────────────────┤
│ Total ADD Kecamatan: Rp 37.5 M      │
│ Realisasi Rata-rata: 78%            │
│                                     │
│ Performance Desa:                   │
│ ⚠️  3 Desa < 50% (merah)            │
│ ⚡ 8 Desa 50-80% (kuning)           │
│ ✅ 4 Desa > 80% (hijau)             │
│                                     │
│ Desa Top Performer:                 │
│ 1. Desa Cihanjuang : 95%            │
│ 2. Desa Cigugur    : 92%            │
│ 3. Desa Karyawangi : 89%            │
│                                     │
│ Perlu Pendampingan:                 │
│ 1. Desa Sariwangi  : 35% ⚠️         │
│ 2. Desa Ciwaruga   : 42% ⚠️         │
└─────────────────────────────────────┘
```

**Detail Metrics:**
- Comparison matrix antar desa
- Heatmap realisasi per desa
- Trend bulanan agregat
- Variance analysis (standard deviation)

#### B. RANKING DESA
```
┌─────────────────────────────────────┐
│ RANKING KINERJA DESA                │
├─────────────────────────────────────┤
│ Berdasar Multi-Indicator:           │
│ 1. Realisasi Keuangan (30%)         │
│ 2. Kelengkapan Laporan (25%)        │
│ 3. Kecepatan Input Data (20%)       │
│ 4. Pelayanan Publik (15%)           │
│ 5. Partisipasi Warga (10%)          │
│                                     │
│ 🥇 Desa Cihanjuang   : 88.5 poin   │
│ 🥈 Desa Cigugur      : 85.2 poin   │
│ 🥉 Desa Karyawangi   : 82.7 poin   │
│ ...                                 │
│ 13. Desa Ciwaruga    : 52.3 poin   │
└─────────────────────────────────────┘
```

#### C. PETA KECAMATAN
- GIS dengan overlay data:
  - Warna per desa by realisasi (hijau-kuning-merah)
  - Pin proyek pembangunan
  - Heatmap stunting
  - Coverage posyandu

#### D. ALERT & MONITORING
```
┌─────────────────────────────────────┐
│ ALERT KECAMATAN                     │
├─────────────────────────────────────┤
│ 🔴 URGENT (3):                      │
│ • Desa Sariwangi belum tutup buku   │
│ • Desa Ciwaruga SPP pending 10 hari │
│ • Desa X proyek mangkrak            │
│                                     │
│ 🟡 WARNING (5):                     │
│ • 5 Desa realisasi < 60% di Q3      │
│                                     │
│ 🟢 INFO (8):                        │
│ • 8 Desa on track                   │
└─────────────────────────────────────┘
```

### Visualisasi

**Charts:**
1. **Clustered Bar**: Realisasi 15 desa
2. **Radar Chart**: Multi-indicator performance
3. **Choropleth Map**: Peta kecamatan dengan gradasi warna
4. **Bullet Chart**: Target vs actual per desa
5. **Waterfall Chart**: Contribution per desa ke total

### Actions
- 🔍 Drill-down ke data desa
- 📊 Export ranking report
- 📧 Send alert ke desa
- 📅 Schedule pendampingan
- 🎯 Set target per desa

---

## 📊 LEVEL 3: DASHBOARD KABUPATEN/KOTA

### Target User
- Bupati/Walikota
- Sekda
- Kepala BPKAD
- Kepala Bappeda
- Camat (Read-only)

### Key Metrics

#### A. OVERVIEW KABUPATEN
```
┌─────────────────────────────────────┐
│ KABUPATEN BANDUNG BARAT             │
│ 16 Kecamatan | 165 Desa             │
├─────────────────────────────────────┤
│ ALOKASI ADD 2024                    │
│ Total        : Rp 825 Miliar        │
│ Tersalurkan  : Rp 742 M (90%)       │
│ Realisasi    : Rp 578 M (70%)       │
│                                     │
│ STATUS DESA:                        │
│ ✅ 98 Desa (59%) : Realisasi > 70%  │
│ ⚡ 52 Desa (32%) : Realisasi 50-70% │
│ ⚠️  15 Desa (9%)  : Realisasi < 50% │
│                                     │
│ KELENGKAPAN LAPORAN:                │
│ • LPJ Semester 1 : 162/165 (98%)    │
│ • Tutup Buku 2023: 165/165 (100%)   │
│ • SPJ APBDes     : 158/165 (96%)    │
└─────────────────────────────────────┘
```

#### B. COMPARISON ANTAR KECAMATAN
```
┌─────────────────────────────────────┐
│ TOP 5 KECAMATAN (by Realisasi)      │
├─────────────────────────────────────┤
│ 1. Kec. Lembang      : 85.2%        │
│ 2. Kec. Parongpong   : 82.7%        │
│ 3. Kec. Cisarua      : 80.1%        │
│ 4. Kec. Cikalong Wetan: 78.5%       │
│ 5. Kec. Cipeundeuy   : 76.3%        │
│                                     │
│ BOTTOM 3 (Perlu Perhatian):         │
│ 14. Kec. Batujajar   : 58.2% ⚠️     │
│ 15. Kec. Gununghalu  : 55.7% ⚠️     │
│ 16. Kec. Saguling    : 52.1% ⚠️     │
└─────────────────────────────────────┘
```

#### C. SEKTOR FOKUS
```
┌─────────────────────────────────────┐
│ PEMBANGUNAN PER SEKTOR              │
├─────────────────────────────────────┤
│ Infrastruktur  : 412 proyek (45%)   │
│   Budget: Rp 285 M | Real: Rp 198 M │
│                                     │
│ Ekonomi        : 286 proyek (31%)   │
│   Budget: Rp 142 M | Real: Rp 95 M  │
│                                     │
│ Pendidikan     : 125 proyek (14%)   │
│   Budget: Rp 78 M  | Real: Rp 52 M  │
│                                     │
│ Kesehatan      : 89 proyek (10%)    │
│   Budget: Rp 45 M  | Real: Rp 31 M  │
└─────────────────────────────────────┘
```

#### D. DEMOGRAFI KABUPATEN
```
┌─────────────────────────────────────┐
│ KEPENDUDUKAN KAB. BANDUNG BARAT     │
├─────────────────────────────────────┤
│ Total Penduduk : 1.8 Juta jiwa      │
│ Kepadatan      : 1,408 jiwa/km²     │
│ Jumlah KK      : 485,320 KK         │
│                                     │
│ KESEHATAN:                          │
│ Prevalensi Stunting: 8.2% (Target <10%)│
│ Coverage Imunisasi : 82% (Target >90%) │
│ Ibu Hamil Risti    : 2,145 (12%)    │
│                                     │
│ TREND:                              │
│ Population Growth  : +1.8% YoY      │
│ Birth Rate         : 18.5/1000      │
│ Death Rate         : 6.2/1000       │
└─────────────────────────────────────┘
```

#### E. COMPLIANCE & GOVERNANCE
```
┌─────────────────────────────────────┐
│ GOOD GOVERNANCE SCORE               │
├─────────────────────────────────────┤
│ Transparansi       : 85/100 ⭐⭐⭐⭐  │
│ Akuntabilitas      : 88/100 ⭐⭐⭐⭐  │
│ Partisipasi        : 72/100 ⭐⭐⭐   │
│ Responsiveness     : 79/100 ⭐⭐⭐   │
│                                     │
│ AUDIT FINDINGS:                     │
│ • BPK: 3 temuan minor (2023)        │
│ • Inspektorat: 8 temuan (resolved)  │
│ • Clean Opinion: 142/165 desa (86%) │
└─────────────────────────────────────┘
```

### Visualisasi

**Charts:**
1. **TreeMap**: Proporsi budget per sektor
2. **Funnel Chart**: Pipeline realisasi (alokasi → tersalur → realisasi)
3. **Bubble Chart**: Kecamatan by 3 dimensi (realisasi, jumlah desa, budget)
4. **Gantt Chart**: Timeline pencairan ADD per kecamatan
5. **Sankey Diagram**: Flow dana dari provinsi → kabupaten → desa

**Maps:**
- Choropleth map kabupaten
- Cluster map proyek pembangunan
- Heat map stunting per kecamatan

### Actions
- 📋 Approval anggaran desa
- 📊 Generate executive summary
- 🎯 Set OKR per kecamatan
- 📢 Broadcast pengumuman
- 🔍 Audit trail monitoring

---

## 📊 LEVEL 4: DASHBOARD PROVINSI

### Target User
- Gubernur
- Sekda Provinsi
- Kepala Bappeda Provinsi
- Kepala BPKAD Provinsi
- Tim Monitoring Kemendagri

### Key Metrics

#### A. OVERVIEW PROVINSI
```
┌─────────────────────────────────────┐
│ PROVINSI JAWA BARAT                 │
│ 27 Kab/Kota | 626 Kec | 5,962 Desa  │
├─────────────────────────────────────┤
│ TOTAL ADD 2024                      │
│ Alokasi       : Rp 29.8 Triliun     │
│ Tersalurkan   : Rp 27.2 T (91%)     │
│ Realisasi     : Rp 20.5 T (69%)     │
│                                     │
│ PERFORMANCE KAB/KOTA:               │
│ ⭐⭐⭐⭐⭐ (>80%)  : 12 Kab/Kota (44%) │
│ ⭐⭐⭐⭐ (70-80%) : 10 Kab/Kota (37%) │
│ ⭐⭐⭐ (60-70%)   : 4 Kab/Kota (15%)  │
│ ⚠️  (<60%)      : 1 Kab/Kota (4%)   │
│                                     │
│ KELENGKAPAN LAPORAN PROVINSI:       │
│ • LPJ Semester 1  : 98.5%           │
│ • Tutup Buku 2023 : 99.8%           │
│ • SIPD Integration: 92.3%           │
└─────────────────────────────────────┘
```

#### B. TOP & BOTTOM PERFORMERS
```
┌─────────────────────────────────────┐
│ RANKING KABUPATEN/KOTA              │
├─────────────────────────────────────┤
│ TOP 5:                              │
│ 🥇 Kab. Bandung Barat    : 92.5%    │
│ 🥈 Kota Bandung          : 89.3%    │
│ 🥉 Kab. Sumedang         : 86.7%    │
│ 4. Kab. Cianjur          : 84.2%    │
│ 5. Kota Cimahi           : 82.8%    │
│                                     │
│ BOTTOM 3 (Assistance Needed):       │
│ 25. Kab. Indramayu       : 62.1% ⚠️ │
│ 26. Kab. Subang          : 58.7% ⚠️ │
│ 27. Kab. Majalengka      : 54.3% ⚠️ │
│                                     │
│ IMPROVEMENT:                        │
│ Most Improved: Kab. Garut (+15.2%)  │
└─────────────────────────────────────┘
```

#### C. ANALISIS MULTIDIMENSI
```
┌─────────────────────────────────────┐
│ PROVINCIAL ANALYTICS                │
├─────────────────────────────────────┤
│ CORRELATION ANALYSIS:               │
│ ✓ Realisasi vs Jumlah Desa: 0.82   │
│ ✓ Realisasi vs Kemiskinan: -0.67   │
│ ✓ Realisasi vs Pendidikan: 0.71    │
│                                     │
│ TREND ANALYSIS:                     │
│ • Realisasi 2024: ↑ 5.2% vs 2023    │
│ • Efisiensi: 92.3% (target 90%)     │
│ • Timeline compliance: 87%          │
│                                     │
│ FORECASTING:                        │
│ • Prediksi akhir tahun: 88.5%       │
│ • Estimasi sisa: Rp 3.4 T           │
│ • Risk factor: Low                  │
└─────────────────────────────────────┘
```

#### D. IMPACT & OUTCOME
```
┌─────────────────────────────────────┐
│ DEVELOPMENT IMPACT                  │
├─────────────────────────────────────┤
│ INFRASTRUCTURE:                     │
│ • Jalan: 2,845 km diperbaiki        │
│ • Jembatan: 342 unit dibangun       │
│ • Irigasi: 12,500 ha difungsikan    │
│                                     │
│ SOCIAL WELFARE:                     │
│ • Kemiskinan: 7.8% → 7.1% (↓0.7%)   │
│ • Stunting: 10.2% → 8.5% (↓1.7%)    │
│ • Pengangguran: 8.9% → 8.2% (↓0.7%) │
│                                     │
│ ECONOMIC:                           │
│ • UMKM Tumbuh: +12,500 unit         │
│ • Pendapatan Asli Desa: ↑18%        │
│ • BUMDes Profit: Rp 145 M total     │
└─────────────────────────────────────┘
```

#### E. BENCHMARKING NASIONAL
```
┌─────────────────────────────────────┐
│ NATIONAL COMPARISON                 │
├─────────────────────────────────────┤
│ Jawa Barat Position:                │
│                                     │
│ Realisasi ADD    : 🥈 Rank 2/34     │
│ Governance Score : 🥇 Rank 1/34     │
│ Inovasi Desa     : 🥈 Rank 2/34     │
│ Digital Maturity : 🥇 Rank 1/34     │
│                                     │
│ Comparison with Top 3:              │
│ 1. Jatim  : 91.2% (🥇)              │
│ 2. Jabar  : 89.5% (🥈) ← YOU        │
│ 3. Jateng : 87.8% (🥉)              │
│                                     │
│ Gap Analysis: -1.7% to #1           │
└─────────────────────────────────────┘
```

### Visualisasi

**Executive Dashboard:**
1. **Geographic Map**: Peta Jabar dengan 27 kab/kota, color by performance
2. **Time Series**: Trend 5 tahun realisasi ADD
3. **Scatter Plot Matrix**: Multi-variable correlation
4. **Pareto Chart**: 80/20 rule - top contributors
5. **Network Diagram**: Inter-kabupaten dependencies

**Strategic Dashboard:**
1. **Balanced Scorecard**: 4 perspektif (Financial, Customer, Process, Learning)
2. **Strategy Map**: Cause-effect relationship
3. **KPI Cockpit**: Real-time monitoring
4. **Risk Matrix**: Impact vs Probability

### Actions
- 📋 Policy recommendation
- 🎯 Set provincial OKR
- 💰 Budget reallocation
- 🏆 Award & recognition program
- 📊 Annual report generation
- 🔍 Deep-dive investigation
- 📢 Province-wide announcement

---

## 🏗️ ARSITEKTUR TEKNIS

### System Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                    │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐│
│  │ Desa App │  │Kec Portal│  │Kab Portal│  │Prov Portal││
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘│
└───────────────────────┬─────────────────────────────────┘
                        │ HTTPS/REST API
┌───────────────────────┴─────────────────────────────────┐
│                    API GATEWAY                           │
│  ┌─────────────────────────────────────────────────────┐│
│  │ Authentication │ Rate Limiting │ Load Balancing     ││
│  └─────────────────────────────────────────────────────┘│
└───────────────────────┬─────────────────────────────────┘
                        │
        ┌───────────────┴──────────────┐
        │                              │
┌───────┴────────┐            ┌────────┴────────┐
│ BUSINESS LAYER │            │ ANALYTICS LAYER │
│                │            │                 │
│ • Desa API     │            │ • ETL Process   │
│ • Kec API      │            │ • OLAP Server   │
│ • Kab API      │            │ • ML Models     │
│ • Prov API     │            │ • Cache (Redis) │
└───────┬────────┘            └────────┬────────┘
        │                              │
        └───────────────┬──────────────┘
                        │
┌───────────────────────┴─────────────────────────────────┐
│                    DATA LAYER                            │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     │
│  │  OLTP DB    │  │  OLAP DB    │  │ Data Lake   │     │
│  │ PostgreSQL  │  │ ClickHouse/│  │  (S3/Minio) │     │
│  │ (Transact.) │  │ TimescaleDB │  │             │     │
│  └─────────────┘  └─────────────┘  └─────────────┘     │
└─────────────────────────────────────────────────────────┘
```

### Technology Stack

**Frontend:**
- Framework: Vue.js 3 / React
- Charts: ApexCharts / ECharts
- Maps: Leaflet / Mapbox
- State Management: Pinia / Redux
- UI Framework: Vuetify / Ant Design

**Backend:**
- API: CodeIgniter 4 / Laravel
- Microservices: Go / Node.js (untuk analytics)
- Queue: Redis + BullMQ
- Cache: Redis
- Search: Elasticsearch (optional)

**Database:**
- OLTP: PostgreSQL 15+ (partitioning)
- OLAP: ClickHouse / TimescaleDB
- Data Warehouse: Snowflake / BigQuery (cloud)
- Object Storage: MinIO / S3

**DevOps:**
- Container: Docker + Kubernetes
- CI/CD: GitLab CI / GitHub Actions
- Monitoring: Prometheus + Grafana
- Logging: ELK Stack (Elasticsearch, Logstash, Kibana)

---

## 💾 DATABASE DESIGN

### Multi-Tenant Strategy

**Option 1: Shared Database, Shared Schema**
```sql
-- Setiap tabel ada kolom tenant_id
CREATE TABLE apbdes (
    id SERIAL PRIMARY KEY,
    kode_desa VARCHAR(20) NOT NULL, -- tenant identifier
    kode_provinsi VARCHAR(2),
    kode_kabupaten VARCHAR(4),
    kode_kecamatan VARCHAR(7),
    -- ... other columns
    
    INDEX idx_provinsi (kode_provinsi),
    INDEX idx_kabupaten (kode_kabupaten),
    INDEX idx_kecamatan (kode_kecamatan),
    INDEX idx_desa (kode_desa)
);
```

**Option 2: Database per Kabupaten (Recommended)**
```
sikades_kab_32_01  (Kab. Bogor)
sikades_kab_32_02  (Kab. Sukabumi)
sikades_kab_32_16  (Kab. Bandung Barat)
...

sikades_analytics  (Central analytics DB)
sikades_master     (Master reference data)
```

### Hierarchical Reference

```sql
-- Tabel wilayah reference
CREATE TABLE ref_wilayah (
    kode VARCHAR(13) PRIMARY KEY, -- Format: PPKKCCDDDDDD
    nama VARCHAR(255),
    level ENUM('provinsi','kabupaten','kecamatan','desa'),
    parent_kode VARCHAR(13),
    lat DECIMAL(10,8),
    lng DECIMAL(11,8),
    metadata JSON
);

-- Indexes for quick lookup
CREATE INDEX idx_level ON ref_wilayah(level);
CREATE INDEX idx_parent ON ref_wilayah(parent_kode);
CREATE INDEX idx_provinsi ON ref_wilayah(substring(kode, 1, 2));
CREATE INDEX idx_kabupaten ON ref_wilayah(substring(kode, 1, 4));

-- Sample data:
INSERT INTO ref_wilayah VALUES
('32', 'Jawa Barat', 'provinsi', NULL, -6.9175, 107.6191, '{}'),
('3216', 'Kab. Bandung Barat', 'kabupaten', '32', -6.8628, 107.4932, '{}'),
('321601', 'Kec. Lembang', 'kecamatan', '3216', -6.8113, 107.6172, '{}'),
('3216012001', 'Desa Lembang', 'desa', '321601', -6.8165, 107.6180, '{}');
```

### Aggregation Tables

```sql
-- Tabel agregasi harian (pre-computed)
CREATE TABLE agg_keuangan_desa (
    kode_desa VARCHAR(20),
    tanggal DATE,
    total_anggaran DECIMAL(15,2),
    realisasi_pendapatan DECIMAL(15,2),
    realisasi_belanja DECIMAL(15,2),
    saldo_kas DECIMAL(15,2),
    
    PRIMARY KEY (kode_desa, tanggal),
    INDEX idx_tanggal (tanggal)
);

-- Agregasi kecamatan (rollup dari desa)
CREATE TABLE agg_keuangan_kecamatan (
    kode_kecamatan VARCHAR(7),
    tanggal DATE,
    jumlah_desa INTEGER,
    total_anggaran DECIMAL(15,2),
    avg_realisasi_persen DECIMAL(5,2),
    desa_hijau INTEGER, -- realisasi > 70%
    desa_kuning INTEGER, -- 50-70%
    desa_merah INTEGER, -- < 50%
    
    PRIMARY KEY (kode_kecamatan, tanggal)
);

-- Agregasi kabupaten
CREATE TABLE agg_keuangan_kabupaten (
    kode_kabupaten VARCHAR(4),
    tanggal DATE,
    jumlah_kecamatan INTEGER,
    jumlah_desa INTEGER,
    total_anggaran DECIMAL(15,2),
    total_realisasi DECIMAL(15,2),
    avg_realisasi_persen DECIMAL(5,2),
    ranking_nasional INTEGER,
    
    PRIMARY KEY (kode_kabupaten, tanggal)
);

-- Agregasi provinsi
CREATE TABLE agg_keuangan_provinsi (
    kode_provinsi VARCHAR(2),
    tanggal DATE,
    jumlah_kabupaten INTEGER,
    jumlah_kecamatan INTEGER,
    jumlah_desa INTEGER,
    total_anggaran DECIMAL(18,2),
    total_realisasi DECIMAL(18,2),
    avg_realisasi_persen DECIMAL(5,2),
    ranking_nasional INTEGER,
    
    PRIMARY KEY (kode_provinsi, tanggal)
);
```

### ETL Process

```sql
-- Stored procedure untuk agregasi harian
CREATE OR REPLACE FUNCTION aggregate_daily()
RETURNS void AS $$
BEGIN
    -- 1. Agregasi level desa
    INSERT INTO agg_keuangan_desa
    SELECT 
        kode_desa,
        CURRENT_DATE,
        SUM(pagu) as total_anggaran,
        COALESCE(SUM(CASE WHEN jenis='pendapatan' THEN realisasi ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN jenis='belanja' THEN realisasi ELSE 0 END), 0),
        0 -- saldo akan dihitung terpisah
    FROM apbdes
    WHERE tahun = EXTRACT(YEAR FROM CURRENT_DATE)
    GROUP BY kode_desa
    ON CONFLICT (kode_desa, tanggal) 
    DO UPDATE SET
        total_anggaran = EXCLUDED.total_anggaran,
        realisasi_pendapatan = EXCLUDED.realisasi_pendapatan,
        realisasi_belanja = EXCLUDED.realisasi_belanja;
    
    -- 2. Agregasi level kecamatan (rollup dari desa)
    INSERT INTO agg_keuangan_kecamatan
    SELECT 
        LEFT(kode_desa, 7) as kode_kecamatan,
        tanggal,
        COUNT(*) as jumlah_desa,
        SUM(total_anggaran),
        AVG((realisasi_pendapatan + realisasi_belanja) / total_anggaran * 100),
        SUM(CASE WHEN (realisasi_pendapatan + realisasi_belanja) / total_anggaran > 0.7 THEN 1 ELSE 0 END),
        SUM(CASE WHEN (realisasi_pendapatan + realisasi_belanja) / total_anggaran BETWEEN 0.5 AND 0.7 THEN 1 ELSE 0 END),
        SUM(CASE WHEN (realisasi_pendapatan + realisasi_belanja) / total_anggaran < 0.5 THEN 1 ELSE 0 END)
    FROM agg_keuangan_desa
    WHERE tanggal = CURRENT_DATE
    GROUP BY kode_kecamatan, tanggal
    ON CONFLICT (kode_kecamatan, tanggal)
    DO UPDATE SET
        jumlah_desa = EXCLUDED.jumlah_desa,
        total_anggaran = EXCLUDED.total_anggaran,
        avg_realisasi_persen = EXCLUDED.avg_realisasi_persen,
        desa_hijau = EXCLUDED.desa_hijau,
        desa_kuning = EXCLUDED.desa_kuning,
        desa_merah = EXCLUDED.desa_merah;
    
    -- 3. Kabupaten (similar)
    -- 4. Provinsi (similar)
END;
$$ LANGUAGE plpgsql;

-- Cron job untuk run setiap malam
-- 0 2 * * * psql -d sikades -c "SELECT aggregate_daily();"
```

---

## 🔌 API & DATA FLOW

### REST API Endpoints

```
# LEVEL DESA
GET    /api/v1/desa/{kode_desa}/dashboard
POST   /api/v1/desa/{kode_desa}/apbdes
GET    /api/v1/desa/{kode_desa}/keuangan/summary

# LEVEL KECAMATAN
GET    /api/v1/kecamatan/{kode_kec}/dashboard
GET    /api/v1/kecamatan/{kode_kec}/desa/ranking
GET    /api/v1/kecamatan/{kode_kec}/alerts
GET    /api/v1/kecamatan/{kode_kec}/comparison

# LEVEL KABUPATEN
GET    /api/v1/kabupaten/{kode_kab}/dashboard
GET    /api/v1/kabupaten/{kode_kab}/kecamatan/ranking
GET    /api/v1/kabupaten/{kode_kab}/analytics
POST   /api/v1/kabupaten/{kode_kab}/approval

# LEVEL PROVINSI
GET    /api/v1/provinsi/{kode_prov}/dashboard
GET    /api/v1/provinsi/{kode_prov}/kabupaten/comparison
GET    /api/v1/provinsi/{kode_prov}/trends
GET    /api/v1/provinsi/{kode_prov}/forecasting
```

### GraphQL Alternative (Flexible Queries)

```graphql
query DashboardKecamatan($kode: String!) {
  kecamatan(kode: $kode) {
    nama
    desaList {
      kode
      nama
      keuangan {
        totalAnggaran
        realisasiPersen
        status
      }
      demografi {
        totalPenduduk
        jumlahKK
      }
    }
    agregat {
      totalAnggaran
      avgRealisasi
      ranking {
        topPerformers
        needAssistance
      }
    }
  }
}
```

### WebSocket for Real-Time

```javascript
// Client subscription
const ws = new WebSocket('wss://api.sikades.id/ws');

ws.on('connect', () => {
  ws.send({
    type: 'subscribe',
    channel: 'kecamatan:3216',
    events: ['transaction', 'alert', 'approval']
  });
});

ws.on('message', (data) => {
  if (data.type === 'transaction') {
    updateDashboard(data.payload);
  } else if (data.type === 'alert') {
    showNotification(data.payload);
  }
});
```

---

## 🔒 SECURITY & ACCESS CONTROL

### Role-Based Access Control (RBAC)

```sql
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE,
    level VARCHAR(20), -- 'desa','kecamatan','kabupaten','provinsi','nasional'
    scope VARCHAR(20), -- kode wilayah yang bisa diakses
    permissions JSON
);

-- Sample roles
INSERT INTO roles VALUES
(1, 'kepala_desa', 'desa', '3216012001', 
 '{"read":["*"],"write":["apbdes","bku","spp"],"approve":["spp"]}'),
 
(2, 'camat', 'kecamatan', '321601',
 '{"read":["*"],"write":["approval"],"approve":["apbdes"]}'),
 
(3, 'kabag_bpkad', 'kabupaten', '3216',
 '{"read":["*"],"write":["budget_allocation"],"approve":["apbdes","lpj"]}'),
 
(4, 'kepala_bappeda', 'provinsi', '32',
 '{"read":["*"],"write":["policy"],"approve":["*"]}');
```

### Permission Middleware

```php
class AccessControl {
    public function checkAccess($user, $resource, $action) {
        // 1. Get user role
        $role = $this->getRoleById($user->role_id);
        
        // 2. Check level hierarchy
        $resourceLevel = $this->getResourceLevel($resource);
        if (!$this->canAccessLevel($role->level, $resourceLevel)) {
            return false;
        }
        
        // 3. Check scope
        if (!$this->inScope($user->scope, $resource->wilayah_kode)) {
            return false;
        }
        
        // 4. Check permission
        $permissions = json_decode($role->permissions, true);
        return in_array($resource, $permissions[$action]) || 
               in_array('*', $permissions[$action]);
    }
    
    private function canAccessLevel($userLevel, $resourceLevel) {
        $hierarchy = ['desa' => 1, 'kecamatan' => 2, 'kabupaten' => 3, 'provinsi' => 4];
        return $hierarchy[$userLevel] >= $hierarchy[$resourceLevel];
    }
    
    private function inScope($userScope, $resourceWilayah) {
        // User di kecamatan 321601 bisa akses semua desa yang dimulai dengan 321601
        return strpos($resourceWilayah, $userScope) === 0;
    }
}
```

### Audit Log

```sql
CREATE TABLE audit_log (
    id SERIAL PRIMARY KEY,
    user_id INTEGER,
    action VARCHAR(50), -- 'create','read','update','delete','approve'
    resource VARCHAR(50), -- 'apbdes','bku','lpj', etc
    resource_id INTEGER,
    wilayah_kode VARCHAR(20),
    ip_address VARCHAR(45),
    user_agent TEXT,
    changes JSON, -- {field: {old: value, new: value}}
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index for forensics
CREATE INDEX idx_audit_user ON audit_log(user_id);
CREATE INDEX idx_audit_resource ON audit_log(resource, resource_id);
CREATE INDEX idx_audit_wilayah ON audit_log(wilayah_kode);
CREATE INDEX idx_audit_time ON audit_log(created_at);
```

---

## 🚀 IMPLEMENTATION ROADMAP

### Phase 1: Foundation (Month 1-3)
✅ Complete LEVEL 1 (Desa Dashboard)
- All modules operational
- Basic reports
- User training

### Phase 2: Aggregation (Month 4-6)
🔨 Build LEVEL 2 (Kecamatan Dashboard)
- Agregasi data dari desa
- Comparison & ranking
- Alert system
- Admin portal kecamatan

### Phase 3: Analytics (Month 7-9)
🔨 Build LEVEL 3 (Kabupaten Dashboard)
- Advanced analytics
- Predictive models
- Executive dashboard
- Policy recommendation engine

### Phase 4: Integration (Month 10-12)
🔨 Build LEVEL 4 (Provinsi Dashboard)
- Provincial overview
- Inter-kabupaten comparison
- National benchmarking
- Strategic planning tools

### Phase 5: Optimization (Month 13-15)
🚀 Enhancement & Scale
- Performance tuning
- Multi-region deployment
- Advanced AI features
- Mobile app

---

## 📊 SUCCESS METRICS

### Adoption Rate
- **Desa**: 90% active usage
- **Kecamatan**: 95% monitoring
- **Kabupaten**: 100% oversight
- **Provinsi**: Full visibility

### Performance
- **Response Time**: < 200ms (p95)
- **Uptime**: 99.9%
- **Concurrent Users**: 10,000+

### Impact
- **Time Saving**: 60% reduction in reporting time
- **Data Accuracy**: > 98%
- **Decision Speed**: 50% faster
- **Transparency**: 100% public access

---

**Last Updated:** 26 Desember 2024  
**Document Version:** 1.0  
**Author:** AI Assistant + User Collaboration

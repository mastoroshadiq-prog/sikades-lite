# **Software Requirement Specification (Addendum)**

## **Project: Siskeudes Lite \- Integrated Modules**

## **Modules: SIPADES (Asset), BUMDes (Business), WebGIS, Demografi (Population)**

Note to AI Agent:  
This document is an extension of siskeudes\_ci\_spec.md. It describes additional modules that share the same Database and Auth system but have distinct logic.  
Architecture: Modular Monolith.

### **Module A: SIPADES (Sistem Pengelolaan Aset Desa)**

**Goal:** Automatic asset registration triggered by Budget Execution (BKU) and Spatial Mapping.

#### **1\. Database Schema Extension**

* **aset\_kategori**:  
  * id, kode\_golongan (e.g., 01=Tanah, 02=Peralatan), uraian.  
* **aset\_inventaris**:  
  * id, kode\_desa, kode\_register (Auto-generated), nama\_barang, kode\_kategori\_id, tahun\_perolehan, harga\_perolehan (Decimal), kondisi (Baik/Rusak Ringan/Rusak Berat), sumber\_dana (APBDes/Hibah).  
  * **Integration Columns:**  
    * bku\_id (FK to bku table): Links asset to the financial transaction.  
    * lat (Decimal 10,8): Latitude for GIS.  
    * lng (Decimal 11,8): Longitude for GIS.  
    * foto (Varchar): Path to image file.

#### **2\. Integration Logic (The "Auto-Register" Feature)**

**Scenario:** When Operator inputs a transaction in **BKU** (Penatausahaan).

* **Logic:**  
  1. Check if the selected ref\_rekening (Account Code) starts with **5.3** (Belanja Modal).  
  2. If YES, after saving bku data, prompt the user (or show a Modal): *"Transaksi ini terdeteksi sebagai Belanja Modal. Apakah ingin dicatat ke Inventaris Aset?"*  
  3. If User confirms, redirect to AsetController::create with pre-filled data:  
     * nama\_barang taken from bku.uraian.  
     * harga\_perolehan taken from bku.debet (or Kredit expense).  
     * tahun\_perolehan taken from bku.tanggal.  
     * bku\_id linked automatically.

### **Module B: SA-BUMDes (Sistem Akuntansi BUMDes)**

Goal: Commercial accounting for village business units.  
Distinct Logic: Unlike Village Govt (Cash Basis/Single Entry for BKU), BUMDes uses Accrual Basis (Double Entry).

#### **1\. Database Schema Extension**

* **bumdes\_unit**:  
  * id, kode\_desa, nama\_unit (e.g., Unit Toko, Unit Wisata), penanggung\_jawab.  
* **bumdes\_akun** (Commercial COA \- SAK EMKM):  
  * Distinct from ref\_rekening.  
  * Examples: 111 (Kas), 112 (Piutang), 411 (Penjualan), 511 (HPP).  
* **bumdes\_jurnal**:  
  * id, unit\_id, tanggal, deskripsi, no\_bukti.  
* **bumdes\_jurnal\_detail**:  
  * id, jurnal\_id, bumdes\_akun\_id, debet (Decimal), kredit (Decimal).

#### **2\. Features**

* **General Ledger (Jurnal Umum):** Double entry input (Debet & Kredit must balance).  
* **Auto-Posting:** From Journal to Ledger to Trial Balance.  
* **Financial Statements:**  
  * Laba Rugi (Profit & Loss).  
  * Neraca (Balance Sheet).

#### **3\. Cross-Module Integration (Penyertaan Modal)**

**Logic:**

* When Village Govt transfers capital to BUMDes.  
* **Action:**  
  1. User inputs **BKU** (Siskeudes): Pengeluaran Pembiayaan (Kode 6.2.1 \- Penyertaan Modal Desa).  
  2. System Notification: *"Catat sebagai penerimaan modal di BUMDes?"*  
  3. **Action:** Create entry in bumdes\_jurnal:  
     * Debet: Kas BUMDes.  
     * Kredit: Modal Desa (Equity).

### **Module C: WebGIS (Visualisasi Spasial)**

**Goal:** Map visualization using LeafletJS.

#### **1\. Library Requirement**

* Frontend: LeafletJS (Open Source).  
* Map Tile: OpenStreetMap (Free).

#### **2\. Controller & View Logic (Gis.php)**

* **Endpoint:** Gis::getJsonData($kode\_desa)  
  * Fetch data from aset\_inventaris where lat and lng are not null.  
  * Return JSON.  
* **View:** Render Map \-\> Loop JSON \-\> Place Markers.  
* **Interactivity:** Clicking a marker shows a popup with Asset Details & Photo.

### **Module D: Demografi & Kependudukan (Population Management)**

**Goal:** Comprehensive database of residents to support social aid planning and development analysis.

#### **1\. Database Schema Extension**

* **pop\_keluarga** (Family Card / KK):  
  * id, kode\_desa, no\_kk (Unique), kepala\_keluarga (String), alamat, rt, rw, dusun.  
  * *Index:* no\_kk is crucial for searching.  
* **pop\_penduduk** (Residents):  
  * id, keluarga\_id (FK to pop\_keluarga), nik (Unique 16 digit), nama\_lengkap, tempat\_lahir, tanggal\_lahir, jenis\_kelamin (L/P), agama, pendidikan\_terakhir, pekerjaan, status\_perkawinan, status\_hubungan\_dalam\_keluarga (Kepala Keluarga/Istri/Anak/Famili Lain).  
  * golongan\_darah: (A/B/AB/O).  
  * nama\_ayah, nama\_ibu: (For genealogy/verification).  
  * status\_dasar: ENUM('HIDUP', 'MATI', 'PINDAH', 'HILANG').  
  * is\_miskin: BOOLEAN (Flag for social aid eligibility/DTKS).  
* **pop\_mutasi** (Vital Statistics Events):  
  * id, penduduk\_id, jenis\_mutasi (KELAHIRAN, KEMATIAN, PINDAH\_MASUK, PINDAH\_KELUAR), tanggal\_peristiwa, keterangan.

#### **2\. Features & Logic**

* **A. Family Grouping (Kartu Keluarga View)**  
  * **Logic:** Display residents grouped by no\_kk.  
  * **Validation:** One KK must have exactly one "Kepala Keluarga".  
* **B. Dynamic Statistics (Dashboard)**  
  * **Pyramid of Age:**  
    * Calculate age from tanggal\_lahir (do not store static Age in DB).  
    * Group by intervals (0-5, 6-10, etc.) and Gender.  
  * **Education Stats:** Count distinct pendidikan\_terakhir.  
  * **Job Stats:** Count distinct pekerjaan.  
* **C. Mutation Logic (Peristiwa)**  
  * **Death (Kematian):**  
    * User inputs "Laporan Kematian".  
    * System updates pop\_penduduk.status\_dasar to 'MATI'.  
    * System records entry in pop\_mutasi.  
    * *Integration:* Remove from "Active Voters" or "Social Aid Candidates" list automatically.  
  * **Birth (Kelahiran):**  
    * Create new entry in pop\_penduduk.  
    * Record in pop\_mutasi.

#### **3\. Cross-Module Integration (Siskeudes \- BLT Dana Desa)**

**Goal:** Ensure financial aid (BLT) goes to real, eligible people.

**Logic:**

1. **In Siskeudes Module (Penganggaran/Belanja):**  
   * When creating SPP (Payment Request) for "BLT Dana Desa".  
2. **Action:**  
   * Button: "Ambil Data Penerima BLT".  
   * Query: SELECT \* FROM pop\_penduduk WHERE is\_miskin \= 1 AND status\_dasar \= 'HIDUP'.  
   * **Verification:** Ensure 1 KK only receives 1 Aid (Prevent duplicate aid per family if rule applies).

### **Updated Development Roadmap**

**Phase 5: Asset Management (SIPADES)**

1. Create aset\_inventaris migration.  
2. Modify Penatausahaan Controller: Add "Trigger Hook" after saving BKU (Belanja Modal).

**Phase 6: Demographics (New Priority)**

1. Create pop\_keluarga and pop\_penduduk tables.  
2. Create Import feature (Excel/CSV) for initial population data (Critical for UX \- inputting 3000 people manually is impossible).  
3. Create "Penduduk Dashboard" (Stats & Charts).  
4. Implement Mutation Logic (Birth/Death/Move).

**Phase 7: BUMDes Module**

1. Create Commercial COA Seeder.  
2. Create Double Entry Journal.

**Phase 8: GIS Dashboard**

1. Integrate LeafletJS.  
2. Layer 1: Asset Locations.  
3. Layer 2: Population Density per RT/RW (Choropleth Map).
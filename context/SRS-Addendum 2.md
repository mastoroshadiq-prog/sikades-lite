# **Software Requirement Specification (Addendum)**

## **Project: Siskeudes Lite \- Integrated Modules**

## **Modules: SIPADES (Asset), BUMDes (Business), WebGIS, Demografi (Population), e-Posyandu (Health), e-Pembangunan (Infrastructure)**

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
  * Fetch data from proyek\_fisik (Module F) where status is 'ON PROGRESS'.  
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

### **Module E: e-Posyandu & Kesehatan Masyarakat (Health & Stunting)**

**Goal:** Monitor public health, track stunting history, and provide data for budget planning (PMT/Supplemental Feeding).

#### **1\. Database Schema Extension**

* **kes\_posyandu**:  
  * Master data of Posyandu locations in the village.  
  * id, kode\_desa, nama\_posyandu, alamat\_dusun.  
* **kes\_kader**:  
  * Health volunteers/workers.  
  * id, penduduk\_id (FK to pop\_penduduk), posyandu\_id.  
* **kes\_pemeriksaan** (Transactional \- The core health record):  
  * id, posyandu\_id, penduduk\_id (FK to pop\_penduduk), tanggal\_periksa.  
  * usia\_bulan: Calculated automatically from tgl\_lahir at the time of checkup.  
  * berat\_badan (Decimal, kg), tinggi\_badan (Decimal, cm), lingkar\_kepala (cm).  
  * vitamin\_a (Boolean), imunisasi (String/JSON).  
  * status\_gizi: ENUM('BURUK', 'KURANG', 'BAIK', 'LEBIH', 'OBESITAS').  
  * indikasi\_stunting: BOOLEAN.  
* **kes\_ibu\_hamil**:  
  * Tracking high-risk pregnancies (K1-K4).  
  * id, penduduk\_id (Mother), usia\_kandungan (week), taksiran\_persalinan (date), resiko\_tinggi (Boolean).

#### **2\. Features & Logic**

* **A. Stunting Detection Logic (Z-Score Simplified)**  
  * **Input:** User enters Height and Weight for a toddler (Balita).  
  * **Process:** System calculates Age (in months).  
  * **Logic:** Compare with WHO Growth Standards (Store simplified standard table in JSON or DB).  
  * **Output:** Auto-flag indikasi\_stunting \= TRUE if height-for-age is below standard (-2 SD).  
  * **Alert:** Show Red Badge "Potensi Stunting".  
* **B. Health Dashboard & Map (GIS Integration)**  
  * **Stats:** "Jumlah Balita Stunting", "Jumlah Ibu Hamil Risti (Resiko Tinggi)".  
  * **Map Integration:** Show markers on the map where kes\_pemeriksaan.indikasi\_stunting \== TRUE. This helps visualize clusters of malnutrition.

#### **3\. Cross-Module Integration (Siskeudes \- Budget Planning)**

**Goal:** Evidence-based Budgeting.

**Logic:**

1. **Trigger:** User opens **Penganggaran (APBDes)** \-\> Bidang Kesehatan.  
2. **Action:** System queries kes\_pemeriksaan.  
   * "Warning: Terdapat 15 Anak terindikasi Stunting. Anggaran 'Pemberian Makanan Tambahan' (PMT) yang disarankan minimal: Rp$$15 anak x Rp 15.000 x 90 hari$$  
     ."  
3. **Result:** Creates a stronger justification for the Village Head to approve the budget.

### **Module F: e-Pembangunan (Infrastructure Monitoring)**

**Goal:** Track physical progress (0%, 50%, 100%) against financial realization (SPP/Cair) to prevent corruption or project stalling.

#### **1\. Database Schema Extension**

* **proyek\_fisik**:  
  * The main project header linked to the budget.  
  * id, kode\_desa, apbdes\_id (FK to apbdes table), nama\_proyek (e.g., Pembangunan Talud RT 01), lokasi\_detail, volume\_target (e.g., 200 Meter), satuan (M/M2/Unit), tgl\_mulai, tgl\_selesai\_target, pelaksana\_kegiatan (TPK Name).  
  * lat, lng: Coordinates for WebGIS integration.  
  * status: ENUM('RENCANA', 'PROSES', 'SELESAI', 'MANGKRAK').  
* **proyek\_log** (Progress History):  
  * id, proyek\_id, tanggal\_laporan, persentase\_fisik (0-100), keterangan.  
  * foto\_0, foto\_50, foto\_100: Upload paths for evidence.

#### **2\. Features & Logic**

* **A. Realization Comparison Logic (Financial vs Physical)**  
  * **Input 1 (Financial):** Sum of all spp\_rincian linked to this apbdes\_id (How much money has been withdrawn).  
  * **Input 2 (Physical):** Latest persentase\_fisik from proyek\_log.  
  * **Logic (Deviation Alert):**  
    * Calculate: Financial % \= (Total Cair / Total Anggaran) \* 100.  
    * Calculate: Physical %.  
    * **Alert:** If Financial % \> Physical % by more than 20% (Example: Money 80% out, Physical only 20% done), show **RED FLAG ALERT** on Dashboard.  
* **B. Visual Transparency (Public View)**  
  * **Feature:** Integration with WebGIS.  
  * **Display:** Clicking a "Road Project" on the map shows the "Before (0%)" and "Current Progress" photos side-by-side.  
* **C. Tim Pelaksana Kegiatan (TPK) Input**  
  * **Role:** A simplified view for TPK/Field Workers to upload photos via mobile when they reach a milestone.

### **Updated Development Roadmap**

**Phase 5: Asset Management (SIPADES)**

1. Create aset\_inventaris migration.  
2. Modify Penatausahaan Controller: Add "Trigger Hook" after saving BKU (Belanja Modal).

**Phase 6: Demographics (Foundation)**

1. Create pop\_keluarga and pop\_penduduk tables.  
2. Create Import feature.  
3. Implement Mutation Logic.

**Phase 7: e-Posyandu (Health)**

1. Create kes\_pemeriksaan table linked to pop\_penduduk.  
2. Create Input Form for "Kader Posyandu".  
3. Implement "Stunting Calculator" logic.

**Phase 8: e-Pembangunan (Infrastructure)**

1. Create proyek\_fisik linked to apbdes.  
2. Create Dashboard Widget: "Financial vs Physical Realization Chart".  
3. Implement Logic: Deviation Alert (Red Flag if money flows out faster than construction).  
4. Integrate Project Markers into WebGIS.

**Phase 9: BUMDes Module & Full GIS**

1. Create Commercial COA Seeder.  
2. Finalize GIS Layers (Assets, Stunting, Infrastructure).
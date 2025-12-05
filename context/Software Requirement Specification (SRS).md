# **Software Requirement Specification (SRS)**

## **Project: Siskeudes Lite (Web-Based)**

## **Framework: CodeIgniter 4 (PHP)**

### **1\. Project Overview**

Membangun aplikasi manajemen keuangan desa berbasis web yang meniru logika bisnis dasar "Siskeudes" (Sistem Keuangan Desa Indonesia). Aplikasi ini berfokus pada **Penganggaran (APBDes)**, **Penatausahaan (Cash Flow/BKU)**, dan **Pelaporan**.

**Tujuan:** Menciptakan sistem pencatatan keuangan yang transparan, akuntabel, dan sesuai standar Permendagri No. 20 Tahun 2018 (Simplified).

### **2\. Technology Stack Requirements**

* **Backend Framework:** CodeIgniter 4 (Strict Typing, Spark).  
* **Language:** PHP 8.x.  
* **Database:** MariaDB (via Docker).  
* **Frontend:** Bootstrap 5 (atau AdminLTE Template).  
* **JavaScript:** jQuery (untuk AJAX operations) & Datatables.  
* **Authentication:** CI4 Auth (Session based / Filter).  
* **Environment:** Docker & Docker Compose.

### **3\. User Roles & Permissions**

1. **Administrator (Admin Kabupaten/Superuser):** Manajemen master data, user management.  
2. **Operator Desa (Kaur Keuangan/Bendahara):** Input APBDes, buat SPP, input Transaksi Kas (BKU), input Pajak.  
3. **Kepala Desa (Approver):** Melihat dashboard, menyetujui (Approve) posting anggaran dan SPP.

### **4\. Database Schema Design (Core Tables)**

*Instruksi untuk AI: Gunakan struktur berikut sebagai referensi migrasi database.*

#### **A. Master Data**

1. **users**: id, username, password\_hash, role, kode\_desa, created\_at.  
2. **ref\_rekening** (Chart of Accounts):  
   * Menyimpan struktur kode rekening (Misal: 4\. Pendapatan, 4.1. PAD, dst).  
   * Columns: id, kode\_akun (varchar), nama\_akun, level (1=Akun, 2=Kelompok, 3=Jenis, 4=Objek), parent\_id.  
3. **data\_umum\_desa**:  
   * Columns: id, kode\_desa, nama\_desa, nama\_kepala\_desa, nama\_bendahara, npwp, tahun\_anggaran.

#### **B. Modul Penganggaran (Budgeting)**

4. **apbdes** (Anggaran Pendapatan dan Belanja Desa):  
   * Columns: id, kode\_desa, tahun, ref\_rekening\_id, uraian, anggaran (decimal/double), sumber\_dana (DDS, ADD, PAD, Bankeu).  
   * *Constraint:* Satu kode rekening bisa memiliki multiple entry anggaran jika rinciannya beda.

#### **C. Modul Penatausahaan (Administration)**

5. **spp** (Surat Permintaan Pembayaran):  
   * Columns: id, no\_spp, tanggal, kode\_desa, keterangan, jumlah\_total, status (Draft, Verified, Approved).  
6. **spp\_rincian**:  
   * Detail belanja apa saja yang diminta dalam SPP.  
   * Columns: id, spp\_id, apbdes\_id (FK ke tabel anggaran), nilai\_pencairan.  
7. **bku** (Buku Kas Umum \- Transaksi Riil):  
   * Tabel utama pencatatan uang masuk/keluar.  
   * Columns: id, kode\_desa, tanggal, nomor\_bukti, uraian, jenis\_transaksi (Pendapatan/Belanja/Mutasi), debet (Masuk), kredit (Keluar), saldo\_kumulatif (Optional, better calculated on view), spp\_id (Nullable, jika berasal dari SPP).  
8. **pajak**:  
   * Pencatatan pungutan/potongan pajak.  
   * Columns: id, bku\_id, jenis\_pajak (PPN, PPh), nilai, kode\_billing, status\_setor (Belum/Sudah).

### **5\. Functional Modules & Logic (MVC Structure)**

#### **Module 1: Master Data (Configuration)**

* **Controller:** Master.php  
* **Logic:** CRUD untuk data desa dan import kode rekening (Standar Permendagri).  
* **AI Prompt Tip:** "Create a Seeder for ref\_rekening that populates standard Indonesian village account codes (e.g., 4\. Pendapatan, 5\. Belanja)."

#### **Module 2: Penganggaran (APBDes)**

* **Controller:** Apbdes.php  
* **Features:**  
  * Input Rencana Anggaran per Kode Rekening.  
  * Validasi: Input tidak boleh minus.  
  * **Logic:** Grouping data berdasarkan Level Rekening untuk tampilan tree-view (Pendapatan \-\> Asli Desa \-\> Hasil Usaha).  
  * **Output:** Cetak Lampiran 1 APBDes (Ringkasan Anggaran).

#### **Module 3: Penatausahaan (Core Transaction)**

* **Controller:** Penatausahaan.php  
* **Feature A: SPP (Surat Permintaan Pembayaran)**  
  * Operator membuat SPP.  
  * Sistem mengecek **Sisa Pagu Anggaran** (Anggaran awal dikurangi realisasi sebelumnya). Jika sisa kurang, tolak input.  
* **Feature B: Pencairan SPP (Masuk ke BKU)**  
  * Saat SPP di-approve Kades, sistem otomatis mencatat di Tabel bku sebagai Pengeluaran (Kredit).  
* **Feature C: Penerimaan Desa**  
  * Input uang masuk (Transfer Dana Desa, Pendapatan Asli) langsung ke bku (Debet).

#### **Module 4: Pelaporan (Reporting)**

* **Controller:** Laporan.php  
* **Report 1: BKU (Buku Kas Umum)**  
  * Logic: Iterasi data bku berdasarkan tanggal.  
  * Formula: Saldo Akhir \= Saldo Awal \+ Debet \- Kredit.  
* **Report 2: Laporan Realisasi Anggaran (LRA)**  
  * Logic: Join tabel apbdes dengan sum of bku berdasarkan kode rekening.  
  * Columns: Anggaran | Realisasi | Lebih/Kurang (%).

### **6\. Development Roadmap for AI Agent**

**Phase 1: Setup & Auth (with Docker)**

1. Create docker-compose.yml with PHP-Apache and MariaDB services.  
2. Setup CI4 project connected to Docker DB.  
3. Create Auth system (Login/Logout) & Filters.  
4. Create Master Data CRUD (Users, Data Desa).

**Phase 2: Budgeting System**

1. Create ref\_rekening table and seeder.  
2. Create APBDes CRUD input interface.  
3. Create Dashboard Widget: "Total Pendapatan vs Total Belanja".

**Phase 3: Transaction System**

1. Create SPP logic (Check budget availability).  
2. Create BKU (General Cash Book) input.  
3. Implement Tax recording logic attached to BKU transactions.

**Phase 4: Reporting**

1. Generate PDF report for BKU.  
2. Generate LRA (Budget Realization Report).

### **7\. UI/UX Guidelines**

* **Menu Structure:**  
  * Dashboard  
  * Data Entri  
    * Penganggaran  
    * Penatausahaan  
  * Laporan  
  * Pengaturan  
* **Form Style:** Gunakan Modal Bootstrap untuk input data cepat.  
* **Notifications:** Gunakan SweetAlert2 untuk konfirmasi simpan/hapus.

### **8\. Example Logic for AI (Pseudo-code)**

**Realisasi Anggaran Logic:**

// In ApbdesModel  
function getRealisasi($kode\_rekening, $kode\_desa) {  
    // Sum all transactions in BKU that match this account code  
    return $db-\>table('bku')  
             \-\>join('apbdes', 'bku.apbdes\_id \= apbdes.id')  
             \-\>join('ref\_rekening', 'apbdes.ref\_rekening\_id \= ref\_rekening.id')  
             \-\>where('ref\_rekening.kode\_akun', $kode\_rekening)  
             \-\>where('bku.kode\_desa', $kode\_desa)  
             \-\>selectSum('bku.kredit') // Assuming expense  
             \-\>get()-\>getRow()-\>kredit;  
}

### **9\. Infrastructure & Docker Setup (New Section)**

**Instruction for AI Agent:**

Please generate a docker-compose.yml file to orchestrate the environment.

* **Service app:**  
  * Image: php:8.2-apache (with required extensions: intl, mysqli, pdo\_mysql).  
  * Volume: Map current directory to /var/www/html.  
  * Ports: 8080:80.  
* **Service db:**  
  * Image: mariadb:10.6.  
  * Environment: MYSQL\_ROOT\_PASSWORD, MYSQL\_DATABASE=siskeudes, MYSQL\_USER, MYSQL\_PASSWORD.  
  * Volume: Persistent storage for /var/lib/mysql.  
  * Ports: 3306:3306 (for local inspection).

**Important for CodeIgniter .env:**

When configuring the database hostname in CI4 .env, ensure it points to the service name db (not localhost), e.g.:

database.default.hostname \= db  
database.default.database \= siskeudes  
database.default.username \= user  
database.default.password \= userpass  
database.default.DBDriver \= MySQLi  

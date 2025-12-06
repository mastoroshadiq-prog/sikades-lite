# 📊 PHASE 4 - REPORTING & ADVANCED FEATURES

**Start Date:** December 6, 2025 - 17:15 WIB  
**Target Completion:** 8-10 hours  
**Status:** 🟡 PLANNING

---

## 🎯 **OBJECTIVES**

Phase 4 focuses on two major areas:

### **Part A: Reporting System** (3-5 hours)
1. **PDF Reports** - Professional reports with logo & signature
2. **Excel Export** - Data export functionality
3. **Email Notifications** - Workflow notifications
4. **Activity Logging** - Audit trail system
5. **Advanced Reporting Dashboard** - Analytics & insights

### **Part B: System Enhancements** (5-7 hours)
1. **Multi-Village Support** - Support multiple villages
2. **Year-End Closing** - Fiscal year closing process
3. **Budget Proposal Module** - RKPDes integration
4. **Asset Management** - Village asset tracking
5. **Mobile-Responsive Improvements** - Better mobile UX

---

## 📊 **PART A: REPORTING SYSTEM**

### **Module 1: PDF Reports** ⏳

#### **Libraries to Use:**
- **TCPDF** or **Dompdf** for PDF generation
- Template-based rendering

#### **Reports to Implement:**
1. **BKU Report** (Buku Kas Umum)
   - Monthly/Yearly BKU
   - Running balance display
   - Signature fields (Bendahara, Kades)
   - Logo desa header

2. **APBDes Report** (Lampiran 1)
   - Budget summary by account
   - Pendapatan vs Belanja breakdown
   - 4 funding sources detail
   - Official format compliance

3. **LRA Report** (Laporan Realisasi Anggaran)
   - Budget vs Realization comparison
   - Percentage achievement
   - Variance analysis
   - Period: Monthly/Quarterly/Yearly

4. **SPP Report**
   - SPP detail with line items
   - Approval signatures
   - Print-ready format
   - Sequential numbering

5. **Tax Report** (Pajak)
   - PPN/PPh summary
   - Payment status
   - NPWP details
   - Billing code tracking

#### **Implementation Tasks:**
- [ ] Install TCPDF/Dompdf via Composer
- [ ] Create `app/Controllers/Report.php`
- [ ] Create `app/Libraries/PdfGenerator.php`
- [ ] Create PDF templates in `app/Views/pdf/`
- [ ] Add report routes
- [ ] Add "Export PDF" buttons to existing pages

---

### **Module 2: Excel Export** ⏳

#### **Library to Use:**
- **PhpSpreadsheet** - Modern Excel library

#### **Export Features:**
1. **APBDes Export**
   - Budget entries to Excel
   - Multiple sheets (by category)
   - Auto-sum formulas
   - Formatted cells

2. **BKU Export**
   - Transaction history
   - Running balance column
   - Monthly sheets
   - Charts (optional)

3. **SPP Export**
   - SPP list with status
   - Detail breakdown
   - Approval tracking

4. **Report Export**
   - LRA report to Excel
   - Tax report to Excel
   - Dashboard stats

#### **Implementation Tasks:**
- [ ] Install PhpSpreadsheet via Composer
- [ ] Create `app/Libraries/ExcelGenerator.php`
- [ ] Add export methods to controllers
- [ ] Add "Export Excel" buttons
- [ ] Format cells (currency, dates, bold headers)

---

### **Module 3: Email Notifications** ⏳

#### **Use Cases:**
1. **SPP Workflow Notifications**
   - Notify Kades when SPP needs approval
   - Notify Operator when SPP approved/rejected
   - Daily digest of pending SPP

2. **Budget Alerts**
   - Alert when budget threshold reached (80%, 100%)
   - Monthly budget summary email

3. **System Notifications**
   - New user created
   - Password reset
   - Data backup completed

#### **Implementation:**
- Use CI4's built-in Email library
- Queue system for bulk emails (optional)
- Email templates with HTML

#### **Implementation Tasks:**
- [ ] Configure Email settings in `.env`
- [ ] Create `app/Libraries/EmailService.php`
- [ ] Create email templates in `app/Views/email/`
- [ ] Add notification triggers in controllers
- [ ] Add email preference settings

---

### **Module 4: Activity Logging** ⏳

#### **What to Log:**
1. **User Actions**
   - Login/Logout
   - Create/Edit/Delete records
   - Approval actions
   - Export reports

2. **System Events**
   - Database changes (audit trail)
   - Failed login attempts
   - Configuration changes

#### **Database Schema:**
```sql
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    module VARCHAR(50),
    record_id INT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

#### **Implementation Tasks:**
- [ ] Create `activity_logs` table migration
- [ ] Create `ActivityLogModel.php`
- [ ] Create `app/Libraries/ActivityLogger.php`
- [ ] Add logging to all controllers
- [ ] Create Activity Log viewer page
- [ ] Add filters (user, date, action, module)

---

### **Module 5: Advanced Reporting Dashboard** ⏳

#### **New Analytics Views:**
1. **Financial Overview Dashboard**
   - Budget utilization chart (by category)
   - Monthly cash flow trend
   - Top 10 expenditures
   - Budget vs Realization gauge charts

2. **Performance Metrics**
   - SPP approval time (average)
   - Budget absorption rate
   - Pending vs Completed SPP ratio
   - Tax payment compliance

3. **Comparison Reports**
   - Year-over-year comparison
   - Budget category comparison
   - Funding source breakdown

#### **Implementation Tasks:**
- [ ] Create `app/Controllers/Analytics.php`
- [ ] Create `app/Views/analytics/dashboard.php`
- [ ] Add more Chart.js visualizations
- [ ] Create data aggregate functions in models
- [ ] Add date range filters
- [ ] Add export analytics to PDF/Excel

---

## 🚀 **PART B: SYSTEM ENHANCEMENTS**

### **Enhancement 1: Multi-Village Support** ⏳

#### **Current State:**
- System supports single village (kode_desa in session)

#### **Target State:**
- Support multiple villages in one instance
- Village selection dropdown
- Isolated data per village
- Admin can manage all villages

#### **Database Changes:**
```sql
-- Add village management table
CREATE TABLE villages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20) UNIQUE,
    nama_desa VARCHAR(100),
    nama_kecamatan VARCHAR(100),
    nama_kabupaten VARCHAR(100),
    provinsi VARCHAR(100),
    logo_path VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Modify users table to support multiple villages
ALTER TABLE users 
ADD COLUMN village_access TEXT NULL COMMENT 'JSON array of village IDs user can access';
```

#### **Implementation Tasks:**
- [ ] Create `villages` table
- [ ] Create `VillageModel.php`
- [ ] Add village selector in header
- [ ] Update all queries to filter by selected village
- [ ] Create village management CRUD
- [ ] Add village switching functionality
- [ ] Update session to store selected village

---

### **Enhancement 2: Year-End Closing** ⏳

#### **Purpose:**
- Lock previous year's data
- Transfer balances to new year
- Archive reports
- Reset transaction numbering

#### **Features:**
1. **Closing Process**
   - Verify all SPP approved
   - Calculate final balances
   - Generate year-end reports
   - Lock year for editing

2. **Opening Balance**
   - Transfer BKU closing balance to next year
   - Archive previous year

3. **Archiving**
   - Move old data to archive tables
   - Keep for historical reporting

#### **Database Schema:**
```sql
CREATE TABLE fiscal_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20),
    tahun INT,
    opening_balance DECIMAL(15,2),
    closing_balance DECIMAL(15,2),
    status ENUM('open', 'closed') DEFAULT 'open',
    closed_at TIMESTAMP NULL,
    closed_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **Implementation Tasks:**
- [ ] Create `fiscal_years` table
- [ ] Create `app/Controllers/FiscalYear.php`
- [ ] Add year-end closing wizard
- [ ] Add validation to prevent editing closed years
- [ ] Create opening balance entry form
- [ ] Generate year-end summary report

---

### **Enhancement 3: Budget Proposal Module (RKPDes)** ⏳

#### **Purpose:**
- Proposal planning before APBDes approved
- Musrenbang documentation
- Draft budget versions

#### **Features:**
1. **Proposal Creation**
   - Create draft budgets
   - Multiple proposal versions
   - Approval workflow
   - Convert proposal to APBDes

2. **Musrenbang Integration**
   - Record meeting participants
   - Upload meeting minutes (PDF/Image)
   - Proposal discussion history

#### **Database Schema:**
```sql
CREATE TABLE budget_proposals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20),
    tahun INT,
    nomor_proposal VARCHAR(50),
    tanggal_proposal DATE,
    keterangan TEXT,
    status ENUM('draft', 'submitted', 'approved', 'rejected'),
    created_by INT,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL
);

CREATE TABLE budget_proposal_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proposal_id INT,
    ref_rekening_id INT,
    uraian TEXT,
    usulan_anggaran DECIMAL(15,2),
    sumber_dana VARCHAR(50),
    FOREIGN KEY (proposal_id) REFERENCES budget_proposals(id)
);
```

#### **Implementation Tasks:**
- [ ] Create proposal tables
- [ ] Create `ProposalModel.php`
- [ ] Create `app/Controllers/Proposal.php`
- [ ] Create proposal CRUD views
- [ ] Add proposal to APBDes conversion
- [ ] Add attachment upload functionality

---

### **Enhancement 4: Asset Management** ⏳

#### **Purpose:**
- Track village assets (land, buildings, vehicles)
- Asset depreciation
- Asset maintenance history
- Asset reporting

#### **Features:**
1. **Asset Registry**
   - Asset details (name, type, value)
   - Purchase date, condition
   - Location, responsible person
   - Photos/documents

2. **Asset Categories**
   - Tanah (Land)
   - Bangunan (Buildings)
   - Kendaraan (Vehicles)
   - Peralatan (Equipment)
   - Lain-lain (Others)

3. **Depreciation Tracking**
   - Auto-calculate depreciation
   - Link to chart of accounts
   - Annual depreciation report

#### **Database Schema:**
```sql
CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_desa VARCHAR(20),
    kode_barang VARCHAR(50) UNIQUE,
    nama_barang VARCHAR(200),
    kategori VARCHAR(50),
    tanggal_perolehan DATE,
    nilai_perolehan DECIMAL(15,2),
    kondisi ENUM('baik', 'rusak_ringan', 'rusak_berat'),
    lokasi VARCHAR(200),
    penanggung_jawab VARCHAR(100),
    umur_ekonomis INT COMMENT 'in years',
    nilai_residu DECIMAL(15,2),
    foto_path VARCHAR(255),
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE asset_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT,
    tanggal_maintenance DATE,
    jenis_maintenance VARCHAR(100),
    biaya DECIMAL(15,2),
    keterangan TEXT,
    FOREIGN KEY (asset_id) REFERENCES assets(id)
);
```

#### **Implementation Tasks:**
- [ ] Create asset tables
- [ ] Create `AssetModel.php`
- [ ] Create `app/Controllers/Asset.php`
- [ ] Create asset CRUD views
- [ ] Add photo upload functionality
- [ ] Add depreciation calculator
- [ ] Create asset reports (inventory, depreciation)

---

### **Enhancement 5: Mobile-Responsive Improvements** ⏳

#### **Current State:**
- Basic Bootstrap 5 responsive
- Desktop-optimized

#### **Target State:**
- Optimized mobile experience
- Touch-friendly buttons
- Simplified mobile navigation
- Mobile-specific views

#### **Implementation Tasks:**
- [ ] Add mobile detection
- [ ] Create mobile-optimized sidebar (hamburger menu)
- [ ] Optimize tables for mobile (responsive DataTables)
- [ ] Add touch gestures support
- [ ] Create mobile dashboard view
- [ ] Test on various devices
- [ ] Add PWA support (optional)

---

## 📅 **IMPLEMENTATION SCHEDULE**

### **Week 1: Reporting (3-5 hours)**
**Session 1 (2 hours):** PDF Reports
- Install TCPDF
- Create BKU PDF report
- Create APBDes PDF report
- Create LRA PDF report

**Session 2 (1.5 hours):** Excel Export
- Install PhpSpreadsheet
- Add Excel export to APBDes
- Add Excel export to BKU
- Add Excel export to reports

**Session 3 (1 hour):** Email & Logging
- Configure email
- Add SPP approval notifications
- Create activity log system
- Create log viewer page

### **Week 2: Enhancements (5-7 hours)**
**Session 4 (2 hours):** Multi-Village Support
- Create villages table
- Add village selector
- Update queries with village filter
- Test isolation

**Session 5 (1.5 hours):** Year-End Closing
- Create fiscal years table
- Build closing wizard
- Add validation for closed years
- Generate year-end reports

**Session 6 (1.5 hours):** Budget Proposal
- Create proposal tables
- Build proposal CRUD
- Add conversion to APBDes
- Test workflow

**Session 7 (2 hours):** Asset Management
- Create asset tables
- Build asset CRUD
- Add photo upload
- Create asset reports

**Session 8 (1 hour):** Mobile Optimization
- Optimize navigation
- Test responsive tables
- Polish mobile UX

---

## ✅ **SUCCESS CRITERIA**

### **Reporting:**
- [ ] All 5 PDF reports generate correctly
- [ ] Excel export works for all modules
- [ ] Email notifications send successfully
- [ ] Activity log records all actions
- [ ] Analytics dashboard displays insights

### **Enhancements:**
- [ ] Multi-village fully functional
- [ ] Year-end closing process works
- [ ] Budget proposal workflow complete
- [ ] Asset management operational
- [ ] Mobile experience smooth

---

## 🎯 **PRIORITY ORDER**

**High Priority (Must Have):**
1. ✅ PDF Reports (BKU, APBDes, LRA)
2. ✅ Excel Export (Basic)
3. ✅ Activity Logging
4. ✅ Multi-Village Support

**Medium Priority (Should Have):**
5. Email Notifications
6. Year-End Closing
7. Advanced Analytics Dashboard

**Low Priority (Nice to Have):**
8. Budget Proposal Module
9. Asset Management
10. Mobile PWA

---

## 📝 **NOTES**

- Start with high-priority items
- Each enhancement should be tested before moving to next
- Keep backward compatibility
- Update documentation as we go
- Consider performance with large datasets

---

**Ready to start Phase 4!** 🚀

**Next Action:** Begin with PDF Reports implementation

---

**Document Created:** December 6, 2025 - 17:15 WIB  
**Target Start:** Immediately  
**Estimated Completion:** 8-10 hours total

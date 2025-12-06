# 📋 PHASE 3 - PENATAUSAHAAN MODULE

**Start Date:** December 6, 2025 - 13:00 WIB  
**Target Completion:** 4 hours  
**Status:** 🟡 IN PROGRESS

---

## 🎯 **Objectives**

Phase 3 focuses on transaction recording and financial management:
1. **SPP (Surat Permintaan Pembayaran)** - Payment Request Management
2. **BKU (Buku Kas Umum)** - General Cash Book
3. **Pajak** - Tax Recording linked to BKU

---

## 📊 **Features to Build**

### **Module 1: SPP Management** ⏳

#### **SPP List Page**
- [⏳] SPP list with DataTable
- [⏳] Filter by status (Draft, Verified, Approved)
- [⏳] Filter by date range
- [⏳] Filter by year
- [⏳] Summary cards (total SPP, amounts by status)
- [⏳] Color-coded status badges
- [⏳] Actions: View, Edit, Delete, Print

#### **SPP Create/Edit Form**
- [⏳] SPP header (nomor, tanggal, uraian)
- [⏳] Line items (detail rincian from APBDes)
- [⏳] Dynamic add/remove line items
- [⏳] Auto-calculate total
- [⏳] Link to APBDes budget items
- [⏳] Save as Draft functionality

#### **SPP Approval Workflow**
- [⏳] Operator creates SPP (status: Draft)
- [⏳] Operator verifies SPP (status: Verified)
- [⏳] Kepala Desa approves SPP (status: Approved)
- [⏳] Role-based buttons display
- [⏳] Approval tracking (who & when)

#### **SPP Detail View**
- [⏳] Display SPP header
- [⏳] Show line items table
- [⏳] Display approval status & history
- [⏳] Print button (PDF export)
- [⏳] Timeline of actions

---

### **Module 2: BKU (Buku Kas Umum)** ⏳

#### **BKU List Page**
- [⏳] BKU entries with DataTable
- [⏳] Filter by transaction type (Pendapatan, Belanja, Mutasi)
- [⏳] Filter by date range
- [⏳] Filter by month/year
- [⏳] Running balance display
- [⏳] Summary cards (total debet, kredit, saldo)
- [⏳] Link to SPP if applicable

#### **BKU Entry Form**
- [⏳] Transaction date picker
- [⏳] Receipt number (no_bukti)
- [⏳] Transaction description
- [⏳] Rekening selection (from ref_rekening)
- [⏳] Transaction type (Pendapatan/Belanja/Mutasi)
- [⏳] Debet/Kredit amount
- [⏳] Link to SPP (optional, for Belanja)
- [⏳] Auto-calculate running balance

#### **BKU Reports**
- [⏳] Monthly BKU report
- [⏳] BKU summary by rekening
- [⏳] Cash flow report
- [⏳] Export to Excel/PDF

---

### **Module 3: Tax Recording** ⏳

#### **Tax List Page**
- [⏳] Tax entries linked to BKU
- [⏳] Filter by tax type (PPN, PPh)
- [⏳] Filter by payment status
- [⏳] Summary cards (total tax, unpaid)

#### **Tax Entry Form**
- [⏳] Link to BKU transaction
- [⏳] Tax type selection (PPN/PPh)
- [⏳] Tax rate input
- [⏳] Auto-calculate tax amount
- [⏳] NPWP input
- [⏳] Taxpayer name
- [⏳] Payment status
- [⏳] Payment date & receipt number

---

## 🗂️ **Files to Create**

### **Controllers** (3 files)
- [ ] `app/Controllers/Spp.php`
- [ ] `app/Controllers/Bku.php`
- [ ] `app/Controllers/Pajak.php`

### **Views** (12+ files)
**SPP Module:**
- [ ] `app/Views/spp/index.php` - List
- [ ] `app/Views/spp/form.php` - Create/Edit
- [ ] `app/Views/spp/detail.php` - View detail
- [ ] `app/Views/spp/print.php` - Print template

**BKU Module:**
- [ ] `app/Views/bku/index.php` - List
- [ ] `app/Views/bku/form.php` - Create/Edit
- [ ] `app/Views/bku/report.php` - Monthly report

**Pajak Module:**
- [ ] `app/Views/pajak/index.php` - List
- [ ] `app/Views/pajak/form.php` - Create/Edit

---

## 📐 **Database Changes**

All tables already exist:
- ✅ `spp` table
- ✅ `spp_rincian` table
- ✅ `bku` table
- ✅ `pajak` table

**Additional Indexes (Optional):**
- [ ] Index on `bku.tanggal` for faster filtering
- [ ] Index on `spp.status` for status filtering
- [ ] Index on `pajak.status_pembayaran`

---

## 🎨 **UI/UX Features**

### **Design Elements:**
- Status badges with colors:
  - Draft: Gray
  - Verified: Blue
  - Approved: Green
- Transaction type badges:
  - Pendapatan: Green
  - Belanja: Red
  - Mutasi: Orange
- Timeline component for approval history
- Dynamic line item table with add/remove buttons
- Date range picker (daterangepicker.js)
- Currency auto-formatting
- Running balance visualization

### **Interactive Features:**
- Live calculation of totals
- Inline editing for quick updates
- Modal for quick view
- Confirmation dialogs for workflow actions
- Toast notifications for success/error
- Loading states during calculations

---

## 🔄 **Workflow Logic**

### **SPP Workflow:**
```
[Operator] → Create SPP (Draft)
           ↓
[Operator] → Verify SPP (Verified)
           ↓
[Kepala Desa] → Approve SPP (Approved)
                ↓
            [Generate BKU Entry]
```

### **BKU Workflow:**
```
[Transaction Entry] → Calculate Running Balance
                    ↓
              [Update Saldo]
                    ↓
        [Link to Tax if applicable]
```

---

## ✅ **Acceptance Criteria**

### **SPP Module:**
- [ ] Can create SPP with multiple line items
- [ ] Line items correctly link to APBDes
- [ ] Total auto-calculates from line items
- [ ] Workflow transitions work (Draft → Verified → Approved)
- [ ] Only appropriate roles can perform actions
- [ ] SPP detail view shows all information
- [ ] Can filter by status, date, year

### **BKU Module:**
- [ ] Can create debet/kredit entries
- [ ] Running balance calculates correctly
- [ ] Can filter by transaction type
- [ ] Can filter by date range
- [ ] Summary totals are accurate
- [ ] Entries link to SPP when applicable

### **Tax Module:**
- [ ] Tax entries link to BKU transactions
- [ ] Tax amount auto-calculates from rate
- [ ] Can track payment status
- [ ] Can record payment details

---

## 📅 **Implementation Schedule**

### **Session 1 (1.5 hours): SPP Module**
- [ ] Create Spp controller with CRUD methods
- [ ] Build SPP list view with filters
- [ ] Build SPP form with line items
- [ ] Implement approval workflow

### **Session 2 (1.5 hours): BKU Module**
- [ ] Create Bku controller with CRUD methods
- [ ] Build BKU list view with running balance
- [ ] Build BKU entry form
- [ ] Implement balance calculation

### **Session 3 (1 hour): Tax Module**
- [ ] Create Pajak controller
- [ ] Build Tax list view
- [ ] Build Tax form linked to BKU
- [ ] Test all integrations

### **Session 4 (30 min): Testing & Polish**
- [ ] Test complete workflows
- [ ] Fix any issues
- [ ] Add finishing touches
- [ ] Update documentation

---

## 🎯 **Success Metrics**

- [ ] All 3 modules functional
- [ ] Workflow logic working correctly
- [ ] Running balance calculations accurate
- [ ] Forms validate properly
- [ ] Reports display correctly
- [ ] Role-based access enforced
- [ ] UI/UX is consistent with Phase 2

---

## 📝 **Notes**

- **Priority 1:** Get basic CRUD working for all 3 modules
- **Priority 2:** Implement workflow and calculations
- **Priority 3:** Polish UI and add reports

**Start Time:** December 6, 2025 - 13:00 WIB  
**Current Step:** Creating SPP Controller

---

**Let's build Phase 3!** 🚀

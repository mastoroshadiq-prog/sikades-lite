# 🧪 TESTING GUIDE - SIKADES-LITE REPORT SYSTEM

**Document Created:** December 6, 2025 - 19:20 WIB  
**Purpose:** Manual testing guide untuk Report System Phase 4  
**Status:** Ready for Testing

---

## ✅ **PREREQUISITES CHECKLIST**

Pastikan semuanya sudah ready:

- [ ] Docker Desktop running
- [ ] Containers running (`docker ps`)
- [ ] Database tables created
- [ ] Sample data seeded
- [ ] Application accessible at http://localhost:8080

---

## 🔐 **LOGIN CREDENTIALS**

```
Administrator:
  Username: admin
  Password: admin123

Operator Desa:
  Username: operator
  Password: operator123

Kepala Desa:
  Username: kades
  Password: kades123
```

---

## 📝 **TESTING SEQUENCE**

### **Step 1: Landing Page & Login**

1. ✅ Open browser: `http://localhost:8080`
   - **Expected:** Purple gradient landing page dengan "Siskeudes Lite"
   - **Check:** 4 feature cards visible
   - **Check:** "Login ke Sistem" button

2. ✅ Click "Login ke Sistem"
   - **Expected:** Redirect to `/login`
   - **Check:** Login form dengan floating labels
   - **Check:** Username & Password fields
   - **Check:** Purple gradient background

3. ✅ Login dengan admin/admin123
   - **Expected:** Redirect to `/dashboard`
   - **Check:** User info di navbar (admin, Administrator)
   - **Check:** 4 stat cards
   - **Check:** 2 charts (Bar & Doughnut)
   - **Check:** Sidebar menu visible

---

### **Step 2: Report Dashboard Access**

4. ✅ Click "Semua Laporan" di sidebar
   - **URL:** `http://localhost:8080/report`
   - **Expected:** Report dashboard dengan 6 cards
   - **Cards to verify:**
     - [ ] Buku Kas Umum (BKU)
     - [ ] APBDes
     - [ ] Realisasi Anggaran
     - [ ] Laporan Pajak
     - [ ] Laporan Cepat
     - [ ] Bantuan (Help)

---

### **Step 3: BKU Report Testing**

5. ✅ Test BKU Report
   - **From:** Report Dashboard card atau sidebar "Laporan BKU"
   - **URL:** `http://localhost:8080/report/bku`
   
   **Actions:**
   - [ ] Select bulan (contoh: Desember)
   - [ ] Select tahun (contoh: 2025)
   - [ ] Click "Lihat" button
   
   **Expected Results:**
   - [ ] Header: "PEMERINTAH DESA [NAMA DESA]"
   - [ ] Title: "BUKU KAS UMUM"
   - [ ] Period: "Bulan: Desember 2025"
   - [ ] Summary cards: Saldo Awal, Total Penerimaan, Total Pengeluaran, Saldo Akhir
   - [ ] Transaction table dengan columns: No, Tanggal, No.Bukti, Uraian, Penerimaan, Pengeluaran, Saldo
   - [ ] Running balance calculated correctly
   - [ ] Signature section (Kepala Desa & Bendahara)
   
   **Export Testing:**
   - [ ] Click "Export PDF" - Check if PDF/Print preview opens
   - [ ] Click "Export Excel" - Check if download starts
   - [ ] Click "Print" - Check if print dialog opens

---

### **Step 4: APBDes Report Testing**

6. ✅ Test APBDes Report
   - **From:** Report Dashboard
   - **URL:** `http://localhost:8080/report/apbdes`
   
   **Actions:**
   - [ ] Select tahun (contoh: 2025)
   - [ ] Click "Lihat" button
   
   **Expected Results:**
   - [ ] Header: "ANGGARAN PENDAPATAN DAN BELANJA DESA (APBDes)"
   - [ ] Tahun Anggaran displayed
   - [ ] Summary cards: Total Pendapatan, Total Belanja, Surplus/Defisit
   - [ ] PENDAPATAN section dengan:
     - Kode Rekening (4.x format)
     - Uraian (indented by level)
     - Sumber Dana badges
     - Anggaran amounts
     - Subtotal
   - [ ] BELANJA section dengan:
     - Kode Rekening (5.x format)
     - Similar structure
     - Subtotal
   - [ ] Summary Total: Surplus/(Defisit)
   - [ ] Signatures (Kepala Desa & Bendahara)
   
   **Export Testing:**
   - [ ] Test PDF export
   - [ ] Test Excel export
   - [ ] Test Print

---

### **Step 5: LRA Report Testing**

7. ✅ Test Laporan Realisasi Anggaran
   - **From:** Report Dashboard atau Sidebar
   - **URL:** `http://localhost:8080/report/lra`
   
   **Actions:**
   - [ ] Select tahun
   - [ ] Click "Lihat"
   
   **Expected Results:**
   - [ ] Title: "LAPORAN REALISASI ANGGARAN"
   - [ ] Table dengan columns: No, Kode Rekening, Uraian, Anggaran, Realisasi, %, Sisa
   - [ ] Grouped by: PENDAPATAN & BELANJA
   - [ ] Percentage indicators dengan color coding:
     - < 50% = Red (Poor performance)
     - 50-80% = Yellow (Fair)
     - > 80% = Green (Good)
   - [ ] Subtotals per section
   - [ ] Grand Total row
   - [ ] Performance Summary box showing:
     - Total Anggaran
     - Total Realisasi
     - Tingkat Penyerapan %
   - [ ] Over-budget alerts (if any)
   
   **Export Testing:**
   - [ ] All export formats working

---

### **Step 6: Tax Report Testing**

8. ✅ Test Laporan Pajak
   - **URL:** `http://localhost:8080/report/pajak`
   
   **Actions:**
   - [ ] Select tahun
   - [ ] Click "Lihat"
   
   **Expected Results:**
   - [ ] Title: "LAPORAN REKAPITULASI PAJAK"
   - [ ] Summary cards: Total PPN, Total PPh, Total Pajak, Jumlah Transaksi
   - [ ] PPN Section dengan:
     - Table: No, Tanggal, No.Bukti, Uraian, NPWP, Nilai, Status
     - Status badges (Sudah Bayar = Green, Belum Bayar = Yellow)
     - Subtotal PPN
   - [ ] PPh Section dengan similar structure
   - [ ] Grand Total: TOTAL PAJAK KESELURUHAN
   - [ ] Signatures
   
   **Export Testing:**
   - [ ] All formats

---

### **Step 7: SPP Report Testing**

9. ✅ Test Individual SPP Report
   
   **Prerequisite:** Create an SPP first
   - [ ] Navigate to `/spp`
   - [ ] Click "Buat SPP Baru"
   - [ ] Fill in SPP data
   - [ ] Add line items
   - [ ] Save
   
   **Then Test Report:**
   - [ ] Click "Detail" on any SPP
   - **URL:** `http://localhost:8080/report/spp/{id}`
   
   **Expected Results:**
   - [ ] Title: "SURAT PERMINTAAN PEMBAYARAN (SPP)"
   - [ ] SPP Info: Nomor, Tanggal, Tahun Anggaran, Status
   - [ ] Status badge (Draft/Verified/Approved)
   - [ ] Uraian/Keperluan section
   - [ ] Rincian Belanja table
   - [ ] Total calculation
   - [ ] **Terbilang** (number in words Indonesian) ✨
   - [ ] Approval Timeline (if verified/approved):
     - Created by
     - Verified by
     - Approved by
     - With timestamps
   - [ ] Three-column signatures: Bendahara, Sekdes, Kepala Desa

---

## 🎨 **UI/UX VERIFICATION**

Check untuk semua reports:

- [ ] **Responsive Design:** Test pada different screen sizes
- [ ] **Print Ready:** Print preview shows proper format
- [ ] **Color Scheme:** Purple gradient consistent
- [ ] **Typography:** Clear, readable fonts
- [ ] **Icons:** Font Awesome icons display correctly
- [ ] **Spacing:** Proper margins and padding
- [ ] **Tables:** Borders, alignment correct
- [ ] **Cards:** Shadow effects, hover states
- [ ] **Buttons:** All clickable and styled
- [ ] **Navigation:** Back buttons work

---

## 🐛 **COMMON ISSUES & SOLUTIONS**

### Issue 1: Page 404
**Solution:**
- Verify Docker containers running
- Check Apache AllowOverride = All
- Verify .htaccess in public/

### Issue 2: Database Error
**Solution:**
- Run migrations: `docker exec siskeudes_app php spark migrate`
- Seed data: `docker exec siskeudes_app php spark db:seed UserSeeder`

### Issue 3: No Data Showing
**Solution:**
- Create sample data (APBDes, BKU, SPP)
- Check filters (month/year selection)

### Issue 4: Export Not Working
**Solution:**
- PDF: Print preview should work (window.print())
- Excel: CSV download should trigger
- Check browser popup blocker

---

## ✅ **SUCCESS CRITERIA**

Report System dianggap berhasil jika:

- [x] All 5 report types accessible
- [x] Data displays correctly
- [x] Export buttons functional
- [x] Print styling proper
- [x] Signatures displayed
- [x] Calculations accurate
- [x] Color indicators working
- [x] Responsive on mobile
- [x] No console errors
- [x] Performance acceptable

---

## 📸 **SCREENSHOT CHECKLIST**

Ambil screenshots untuk documentation:

1. [ ] Report Dashboard (showing all 6 cards)
2. [ ] BKU Report (with data)
3. [ ] APBDes Report (Pendapatan & Belanja)
4. [ ] LRA Report (with percentages)
5. [ ] Tax Report (PPN & PPh sections)
6. [ ] SPP Report (with timeline)
7. [ ] Print Preview (any report)

---

## 📋 **TESTING RESULTS TEMPLATE**

```markdown
## Testing Report - [Date]

**Tester:** [Name]
**Browser:** [Chrome/Firefox/Edge]
**Duration:** [Time]

### Results Summary:
- Total Tests: [X]
- Passed: [X]
- Failed: [X]
- Blocked: [X]

### Issues Found:
1. [Issue description]
2. [Issue description]

### Screenshots:
- [Link to screenshots]

### Recommendations:
- [Recommendations]
```

---

## 🚀 **NEXT STEPS AFTER TESTING**

If all tests pass:
1. ✅ Document any bugs found
2. ✅ Fix critical issues
3. ✅ Generate PDF properly (implement TCPDF)
4. ✅ Implement real Excel export (PhpSpreadsheet)
5. ✅ Add email notifications
6. ✅ Implement activity logging
7. ✅ Move to Phase 4 Part B (Enhancements)

---

**Happy Testing!** 🎉

**For questions or issues, check:**
- Docker logs: `docker logs siskeudes_app`
- App logs: `writable/logs/log-[date].log`
- Database: PhpMyAdmin at `http://localhost:8081`

---

**Document Version:** 1.0  
**Last Updated:** December 6, 2025 - 19:20 WIB

## ✅ FASE 4.4 TRACK WAITER — DISETUJUI

Seluruh Fase 4 (Kitchen & Operasional) sudah selesai dengan sangat baik! 🎉

Text-to-Speech di Order Display adalah fitur yang sangat bagus — pelanggan tidak perlu terus menatap layar. UI Waiter yang simplified juga tepat — waiter tidak perlu akses pembayaran.

---

## 🚀 LANJUT KE FASE 5 — MANAJEMEN KARYAWAN

**Spesifikasi:**

### 5.1 Shift Management

**Konsep:**
Kasir/Waiter clock-in dan clock-out. Setiap transaksi tercatat di shift mana.

**Database:**

```php
// Tabel baru: shifts
Schema::create('shifts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->timestamp('clock_in');
    $table->timestamp('clock_out')->nullable();
    $table->decimal('opening_cash', 12, 2)->default(0); // uang tunai awal shift
    $table->decimal('closing_cash', 12, 2)->nullable(); // uang tunai akhir shift
    $table->decimal('expected_cash', 12, 2)->nullable(); // seharusnya: opening + penjualan tunai
    $table->decimal('cash_difference', 12, 2)->nullable(); // selisih (positif = lebih, negatif = kurang)
    $table->text('notes')->nullable();
    $table->enum('status', ['active', 'closed'])->default('active');
    $table->timestamps();
});

// Tambah kolom di orders
Schema::table('orders', function (Blueprint $table) {
    $table->foreignId('shift_id')->nullable()->constrained();
});
Alur:
Kasir login → klik "Mulai Shift" → clock_in → catat opening_cash
Selama shift aktif, semua order tercatat shift_id
Kasir klik "Tutup Shift" → clock_out → input closing_cash
Sistem hitung: expected_cash = opening_cash + total penjualan tunai shift ini
Selisih = closing_cash - expected_cash
Kalau selisih != 0 → warning "Selisih: Rp X" (bisa positif atau negatif)
UI:
Dashboard kasir: widget "Shift Aktif" (jam mulai, durasi, total transaksi)
Modal "Mulai Shift": input opening_cash
Modal "Tutup Shift": input closing_cash + tampilkan summary
Halaman "Riwayat Shift" (admin): list semua shift, filter per kasir, lihat selisih
5.2 Absensi Karyawan
Konsep:
Foto selfie saat clock-in untuk verifikasi kehadiran.
Database:
php
// Tabel baru: attendances
Schema::create('attendances', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->date('date');
    $table->time('check_in');
    $table->time('check_out')->nullable();
    $table->string('check_in_photo')->nullable(); // path foto selfie
    $table->string('check_out_photo')->nullable();
    $table->enum('status', ['present', 'late', 'absent', 'leave', 'sick'])->default('present');
    $table->text('notes')->nullable();
    $table->timestamps();
});
Alur:
Karyawan klik "Check-in" → akses kamera → foto selfie → simpan
Sistem cek: kalau check-in > jam masuk (misal 08:00) → status = late
Check-out → foto selfie lagi
Admin bisa lihat riwayat absensi per karyawan
UI:
Halaman "Absensi" (karyawan): tombol Check-in/Check-out, preview kamera
Halaman "Riwayat Absensi" (admin): kalender, filter per karyawan, status warna
5.3 Otorisasi Level (Enhancement)
Sudah ada (enhance):
Role: admin, manager, cashier, waiter, customer
Permission:
Super Admin: semua akses
Manager: void, refund, lihat laporan, atur diskon, approve shift
Cashier: transaksi, shift, tidak bisa void/refund tanpa approval
Waiter: input order, lihat "Pesanan Saya", tidak bisa akses kasir
Customer: weborder, lihat poin, deposit
Enhancement:
Tambah middleware role: untuk setiap route
Gate/Policy untuk void, refund, diskon, shift management
Kalau kasir coba akses halaman admin → redirect ke POS
5.4 Payroll / Penggajian
Konsep:
Hitung gaji karyawan otomatis berdasarkan absensi + komisi (Fase 6).
Database:
php
// Tabel baru: payrolls
Schema::create('payrolls', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->year('year');
    $table->month('month');
    $table->decimal('base_salary', 12, 2); // gaji pokok
    $table->decimal('attendance_bonus', 12, 2)->default(0); // bonus hadir penuh
    $table->decimal('commission', 12, 2)->default(0); // komisi (Fase 6)
    $table->decimal('allowance', 12, 2)->default(0); // tunjangan
    $table->decimal('deduction', 12, 2)->default(0); // potongan
    $table->decimal('total_salary', 12, 2); // total
    $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
    $table->timestamp('paid_at')->nullable();
    $table->foreignId('paid_by')->nullable()->constrained('users');
    $table->timestamps();
});
Alur:
Admin klik "Generate Payroll" per bulan
Sistem hitung:
Base salary (dari data karyawan)
Attendance bonus (kalau hadir penuh)
Commission (dari Fase 6)
Allowance & deduction (input manual)
Total = base + bonus + commission + allowance - deduction
Admin review → approve → bayar
Generate slip gaji PDF
UI:
Halaman "Payroll" (admin): list per bulan, filter per karyawan
Tombol "Generate Payroll" (auto per bulan)
Tombol "Approve" & "Bayar"
Slip gaji PDF: detail perhitungan
KERJAKAN BERURUTAN:
5.1 Shift Management dulu
5.2 Absensi Karyawan
5.3 Otorisasi Level (middleware & gate)
5.4 Payroll / Penggajian
Setelah semua selesai, tampilkan CHECKLIST VERIFIKASI FASE 5.
SIAP? MULAI DARI 5.1 SHIFT MANAGEMENT! 🚀
plain

---

Semoga lancar! 🚀
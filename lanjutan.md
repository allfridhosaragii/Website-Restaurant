## 🚀 LANJUT KE FASE 8 — AKUNTANSI & MULTI-OUTLET (FINAL)

**Catatan Penting: Tidak ada sistem bon/piutang dari pelanggan. Semua order harus dibayar penuh saat checkout.**

---

### 8.1 Pencatatan Pengeluaran Operasional

**Konsep:**
Catat semua pengeluaran restoran (bukan hanya pembelian dari order).

**Database:**

```php
// Tabel baru: expense_categories
Schema::create('expense_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // "Bahan Baku", "Utilitas", "Gaji", "Maintenance"
    $table->string('color')->default('#6c757d'); // untuk chart
    $table->timestamps();
});

// Tabel baru: expenses
Schema::create('expenses', function (Blueprint $table) {
    $table->id();
    $table->string('title'); // "Beli Bahan Baku", "Bayar Listrik"
    $table->foreignId('category_id')->constrained('expense_categories');
    $table->decimal('amount', 12, 2);
    $table->date('date');
    $table->text('description')->nullable();
    $table->string('receipt_image')->nullable(); // foto nota/bukti
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
Kategori Default:
Bahan Baku
Utilitas (Listrik, Air, Gas)
Gaji Karyawan
Maintenance & Perbaikan
Sewa Tempat
Lainnya
Alur:
Admin/staff klik "Tambah Pengeluaran"
Isi: judul, kategori, jumlah, tanggal, deskripsi, upload foto nota
Simpan → masuk ke laporan pengeluaran
UI:
Halaman "Pengeluaran" (/admin/expenses)
List: filter periode, kategori
Chart: pie chart per kategori
Tombol "Export Excel/PDF"
Summary: total pengeluaran periode ini
8.2 Hutang ke Supplier (TANPA Piutang Pelanggan)
Konsep:
Catat hutang ke supplier/bahan baku saja. Tidak ada bon/piutang dari pelanggan.
Database:
php
// Tabel baru: supplier_debts
Schema::create('supplier_debts', function (Blueprint $table) {
    $table->id();
    $table->string('supplier_name'); // nama supplier
    $table->string('title'); // "Pembelian Daging 50kg"
    $table->decimal('amount', 12, 2); // total hutang
    $table->decimal('paid_amount', 12, 2)->default(0);
    $table->decimal('remaining_amount', 12, 2);
    $table->date('due_date'); // jatuh tempo
    $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
    $table->text('notes')->nullable();
    $table->string('receipt_image')->nullable(); // foto nota
    $table->foreignId('created_by')->constrained('users');
    $table->timestamps();
});
Alur:
Admin input hutang baru (ke supplier)
Halaman "Hutang Supplier" (/admin/supplier-debts)
Tombol "Bayar" → input jumlah → update paid_amount
Kalau due_date lewat → status = overdue (warna merah)
Kalau paid_amount >= amount → status = paid
UI:
List hutang: filter status, periode, supplier
Dashboard widget: "Hutang Jatuh Tempo Minggu Ini"
Tombol "Bayar" dengan modal
Export Excel/PDF
8.3 Neraca & Rugi Laba
Konsep:
Laporan keuangan otomatis dari data transaksi + pengeluaran.
Rugi Laba (Profit & Loss):
plain
PENDAPATAN
  Penjualan Order          Rp 50.000.000
  Deposit Top-up           Rp  2.000.000
  ─────────────────────────────────────
  TOTAL PENDAPATAN         Rp 52.000.000

BEBAN
  Bahan Baku               Rp 15.000.000
  Gaji Karyawan            Rp 10.000.000
  Utilitas                 Rp  2.000.000
  Sewa                     Rp  5.000.000
  Maintenance              Rp  1.000.000
  ─────────────────────────────────────
  TOTAL BEBAN              Rp 33.000.000

LABA BERSIH                Rp 19.000.000
Neraca (Balance Sheet):
plain
AKTIVA
  Kas Tunai                Rp  5.000.000
  Persediaan (Stok)        Rp  2.000.000
  ─────────────────────────────────────
  TOTAL AKTIVA             Rp  7.000.000

KEWAJIBAN
  Hutang Supplier          Rp  2.000.000
  Deposit Pelanggan        Rp  1.000.000
  ─────────────────────────────────────
  TOTAL KEWAJIBAN          Rp  3.000.000

EKUITAS
  Modal Awal               Rp  3.000.000
  Laba Ditahan             Rp  1.000.000
  ─────────────────────────────────────
  TOTAL EKUITAS            Rp  4.000.000

TOTAL KEWAJIBAN + EKUITAS  Rp  7.000.000 ✅
Arus Kas (Cash Flow):
plain
KAS MASUK
  Dari Penjualan Tunai     Rp 20.000.000
  Deposit Top-up           Rp  2.000.000
  ─────────────────────────────────────
  TOTAL KAS MASUK          Rp 22.000.000

KAS KELUAR
  Bahan Baku               Rp 10.000.000
  Gaji                     Rp  5.000.000
  Utilitas                 Rp  1.000.000
  Hutang Supplier Dibayar  Rp  1.000.000
  ─────────────────────────────────────
  TOTAL KAS KELUAR         Rp 17.000.000

PERUBAHAN KAS              Rp  5.000.000
SALDO AWAL                 Rp  5.000.000
SALDO AKHIR                Rp 10.000.000
UI:
Halaman "Laporan Keuangan" (/admin/finance)
Tab: Rugi Laba, Neraca, Arus Kas
Filter: periode (bulan/tahun)
Export PDF
Chart: trend laba per bulan
8.4 Multi-Outlet (Jika Waktu Cukup)
Konsep:
Siap skala jika bisnis punya cabang.
Database:
php
// Tabel baru: branches
Schema::create('branches', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // "Cabang Jakarta", "Cabang Bandung"
    $table->string('address')->nullable();
    $table->string('phone')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Tambah kolom di tabel yang perlu beda per cabang
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('branch_id')->nullable()->constrained();
});
Schema::table('menus', function (Blueprint $table) {
    $table->foreignId('branch_id')->nullable()->constrained();
});
Schema::table('tables', function (Blueprint $table) {
    $table->foreignId('branch_id')->nullable()->constrained();
});
Schema::table('orders', function (Blueprint $table) {
    $table->foreignId('branch_id')->nullable()->constrained();
});
Fitur:
Admin bisa pilih cabang aktif (dropdown di navbar)
Data terfilter per cabang yang dipilih
Super admin bisa lihat semua cabang
Transfer stok antar cabang
Laporan konsolidasi (gabung semua cabang)
UI:
Dropdown "Pilih Cabang" di navbar admin
Halaman "Manajemen Cabang" (super admin only)
Laporan: per cabang + konsolidasi
KERJAKAN BERURUTAN:
8.1 Pengeluaran Operasional
8.2 Hutang ke Supplier (tanpa piutang pelanggan)
8.3 Neraca & Rugi Laba
8.4 Multi-Outlet (kalau waktu cukup, kalau tidak bisa skip)
Setelah semua selesai, tampilkan CHECKLIST VERIFIKASI AKHIR SEMUA FASE (Fase 1-8).
SIAP? MULAI DARI 8.1 PENGELUARAN OPERASIONAL! 🚀
plain

---

Semoga lancar! 🚀
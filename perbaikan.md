Seluruh Fase 5 (Manajemen Karyawan) sudah selesai dengan sangat baik! 🎉

Selfie check-in via MediaDevices API adalah fitur yang modern. Smart redirect per role juga UX yang bagus. Slip gaji thermal layout sesuai kebutuhan POS.

---

## 🚀 LANJUT KE FASE 6 — CRM, PROMO & LAPORAN

**Spesifikasi:**

### 6.1 Membership / VIP

**Konsep:**
Tier pelanggan berdasarkan total transaksi. Auto-upgrade.

**Database:**

```php
// Tambah kolom di users
Schema::table('users', function (Blueprint $table) {
    $table->enum('membership_tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
    $table->decimal('total_spent', 12, 2)->default(0); // akumulasi total transaksi
    $table->timestamp('tier_upgraded_at')->nullable();
});

// Tabel baru: membership_tiers
Schema::create('membership_tiers', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Bronze, Silver, Gold, Platinum
    $table->decimal('min_spent', 12, 2); // minimal total spent untuk tier ini
    $table->decimal('discount_percent', 5, 2)->default(0); // diskon otomatis
    $table->integer('point_multiplier')->default(1); // 1x, 2x, 3x poin
    $table->text('benefits')->nullable(); // deskripsi benefit
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
Tier Default:
Table
Tier	Min Spent	Diskon	Point Multiplier
Bronze	Rp 0	0%	1x
Silver	Rp 500.000	5%	1.5x
Gold	Rp 2.000.000	10%	2x
Platinum	Rp 5.000.000	15%	3x
Alur:
Setiap order completed → update users.total_spent
Cek apakah total_spent mencapai tier berikutnya
Kalau ya → auto-upgrade tier → notifikasi ke pelanggan
Diskon tier auto-apply saat checkout (tambahan dari diskon lain)
Point multiplier: kalau Gold, transaksi Rp 100.000 = 200 poin (2x)
UI:
Profile pelanggan: tampilkan tier, progress bar ke tier berikutnya
Badge tier di navbar/header
Admin: CRUD tier settings
6.2 Promo Buy X Get Y
Konsep:
Beli X item, gratis Y item. Auto-apply saat checkout.
Database:
php
// Tabel baru: promos
Schema::create('promos', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // "Beli 2 Nasi Goreng Gratis 1 Es Teh"
    $table->enum('type', ['buy_x_get_y', 'discount_percent', 'discount_fixed', 'bundle'])->default('buy_x_get_y');
    $table->foreignId('buy_menu_id')->constrained('menus'); // menu yang harus dibeli
    $table->integer('buy_quantity'); // minimal berapa
    $table->foreignId('get_menu_id')->nullable()->constrained('menus'); // menu gratis (null = sama dengan buy_menu)
    $table->integer('get_quantity')->default(1); // gratis berapa
    $table->enum('get_type', ['free', 'discount'])->default('free'); // gratis atau diskon
    $table->decimal('get_discount_percent', 5, 2)->nullable(); // kalau get_type = discount
    $table->dateTime('start_date');
    $table->dateTime('end_date');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
Alur:
Checkout → sistem cek semua promo aktif
Kalau cart memenuhi syarat (buy X) → auto-tambah item gratis ke order
Item gratis: price = 0, tapi tercatat di order_items
Tampilkan di struk: "Es Teh (Promo) ........ Rp 0"
UI:
Admin: CRUD promo
Kasir: badge "Promo Aktif" di menu yang ada promo
Customer: banner promo di homepage
6.3 Voucher System
Sudah ada di Fase 1.5 (enhance):
Enhancement:
Usage limit per user (contoh: 1x per pelanggan)
Usage limit global (contoh: maks 100 orang)
Tipe baru: gratis_item (voucher untuk 1 menu gratis)
Generate voucher massal (bulk generate untuk event/marketing)
Database (enhance tabel discounts):
php
Schema::table('discounts', function (Blueprint $table) {
    $table->integer('per_user_limit')->default(0); // 0 = unlimited
    $table->integer('global_limit')->default(0); // 0 = unlimited
    $table->foreignId('free_menu_id')->nullable()->constrained('menus'); // kalau tipe = gratis_item
});
6.4 Laporan Lengkap
Dashboard Enhancement:
Table
Laporan	Chart	Export
Penjualan Harian/Bulanan/Tahunan	Line chart	Excel, PDF
Produk Terlaris	Bar chart	Excel, PDF
Peak Hour	Area chart	Excel, PDF
Metode Pembayaran	Pie chart	Excel, PDF
Penjualan per Karyawan	Bar chart	Excel, PDF
Penjualan per Meja	Bar chart	Excel, PDF
Diskon & Promo Usage	Table	Excel, PDF
Stok Movement	Table	Excel, PDF
Install package:
bash
composer require maatwebsite/excel
composer require barryvdh/laravel-dompdf # sudah ada
UI:
Halaman "Laporan" (admin)
Filter: tanggal range, periode (hari/minggu/bulan/tahun)
Tombol "Export Excel" & "Export PDF"
Chart pakai Chart.js (CDN)
6.5 Rating & Review
Database:
php
// Tabel baru: reviews
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('order_id')->constrained();
    $table->foreignId('menu_id')->nullable()->constrained(); // review per menu atau per order
    $table->tinyInteger('rating'); // 1-5
    $table->text('comment')->nullable();
    $table->text('admin_reply')->nullable();
    $table->timestamp('replied_at')->nullable();
    $table->boolean('is_approved')->default(true); // auto-approve atau moderasi
    $table->timestamps();
});
Alur:
Order completed → pelanggan bisa kasih rating & review (maks 7 hari)
Rating 1-5 bintang + comment text
Tampilkan di menu detail (rata-rata rating, list review)
Admin bisa reply review
Kalau rating < 3 → notifikasi ke admin (komplain)
UI:
Customer: modal rating setelah order completed
Menu detail: tampilkan rating bintang + jumlah review
Admin: halaman "Review" (list, filter, reply)
KERJAKAN BERURUTAN:
6.1 Membership / VIP dulu
6.2 Promo Buy X Get Y
6.3 Voucher System (enhance)
6.4 Laporan Lengkap
6.5 Rating & Review
Setelah semua selesai, tampilkan CHECKLIST VERIFIKASI FASE 6.
SIAP? MULAI DARI 6.1 MEMBERSHIP / VIP! 🚀
plain

---

Semoga lancar! 🚀
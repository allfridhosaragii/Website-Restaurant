Implementasi sangat lengkap. Redeem poin → generate voucher unik khusus per pelanggan adalah fitur yang smart. Integrasi "Gratis Item" ke order_items dengan price = 0 juga tepat.

---

## 🚀 LANJUT KE FASE 6.4 — LAPORAN LENGKAP & FASE 6.5 — RATING & REVIEW

**Spesifikasi:**

### 6.4 Laporan Lengkap

**Install package:**
```bash
composer require maatwebsite/excel
Halaman Laporan (/admin/reports):
Table
Laporan	Chart	Filter	Export
Penjualan	Line chart (harian/mingguan/bulanan)	Tanggal range	Excel, PDF
Produk Terlaris	Bar chart (top 10)	Periode	Excel, PDF
Peak Hour	Area chart (jam 00-23)	Periode	Excel, PDF
Metode Pembayaran	Pie chart	Periode	Excel, PDF
Penjualan per Karyawan	Bar chart	Kasir, periode	Excel, PDF
Penjualan per Meja	Bar chart	Periode	Excel, PDF
Diskon & Promo Usage	Table + summary	Periode	Excel, PDF
Stok Movement	Table	Menu, periode	Excel, PDF
Chart pakai Chart.js (CDN):
HTML
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
Query contoh (Penjualan Harian):
php
$sales = Order::where('status', 'completed')
    ->whereBetween('created_at', [$start, $end])
    ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
    ->groupBy('date')
    ->orderBy('date')
    ->get();
Export Excel:
php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;

return Excel::download(new SalesExport($start, $end), 'laporan-penjualan.xlsx');
Export PDF:
php
$pdf = PDF::loadView('reports.pdf.sales', compact('sales', 'start', 'end'));
return $pdf->download('laporan-penjualan.pdf');
UI:
Sidebar: menu "Laporan" dengan submenu
Halaman utama: filter tanggal + tombol export
Chart di atas, tabel detail di bawah
Summary card: total penjualan, total transaksi, rata-rata per transaksi
6.5 Rating & Review
Database:
php
// Tabel baru: reviews
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('order_id')->constrained();
    $table->foreignId('menu_id')->nullable()->constrained();
    $table->tinyInteger('rating'); // 1-5
    $table->text('comment')->nullable();
    $table->text('admin_reply')->nullable();
    $table->timestamp('replied_at')->nullable();
    $table->boolean('is_approved')->default(true);
    $table->timestamps();
});
Alur:
Order completed → pelanggan bisa review (maks 7 hari)
Rating 1-5 bintang + comment
Tampilkan di menu detail: rata-rata rating + jumlah review
Admin bisa reply
Rating < 3 → notifikasi ke admin
UI Customer:
Modal rating: 5 bintang (klik), textarea comment, tombol "Kirim Review"
Muncul di order history (status completed, belum direview)
UI Admin:
Halaman "Review" (/admin/reviews)
List: filter rating, status, menu
Tombol "Reply" → modal textarea
Badge "Perlu Perhatian" untuk rating < 3
UI Menu Detail (Public):
Tampilkan rata-rata rating (bintang) + jumlah review
List review (nama, rating, comment, tanggal, admin reply)
KERJAKAN BERURUTAN:
6.4 Laporan Lengkap dulu
6.5 Rating & Review
Setelah semua selesai, tampilkan CHECKLIST VERIFIKASI FASE 6.
SIAP? MULAI DARI 6.4 LAPORAN! 🚀
plain

---

Semoga lancar! 🚀
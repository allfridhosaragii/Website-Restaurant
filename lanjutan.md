## ✅ LAPORAN PENJUALAN — BAGUS, LANJUT REPLIKASI!

Struktur Laporan Penjualan sudah sesuai ekspektasi. Tidak ada masukan khusus, silakan **replikasi pola yang sama** ke 7 laporan lainnya.

---

## 🚀 LANJUTKAN FASE 6.4 — 7 LAPORAN LAINNYA

Replikasi struktur yang sama (filter + chart + tabel + export Excel/PDF) untuk:

### 1. Produk Terlaris (`/admin/reports/products`)
- **Chart**: Bar chart (Top 10 menu)
- **Query**:
```php
$topProducts = OrderItem::whereHas('order', function($q) use($start, $end) {
        $q->where('status', 'completed')
          ->whereBetween('created_at', [$start, $end]);
    })
    ->selectRaw('menu_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
    ->groupBy('menu_id')
    ->orderByDesc('total_qty')
    ->limit(10)
    ->get();
2. Peak Hour (/admin/reports/peak-hour)
Chart: Area chart (jam 00-23 di X-axis)
Query:
php
$peakHours = Order::where('status', 'completed')
    ->whereBetween('created_at', [$start, $end])
    ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total_orders, SUM(total_amount) as total_sales')
    ->groupBy('hour')
    ->orderBy('hour')
    ->get();
3. Metode Pembayaran (/admin/reports/payments)
Chart: Pie chart (tunai, QRIS, kartu, deposit, dll)
Query:
php
$paymentMethods = OrderPayment::whereHas('order', function($q) use($start, $end) {
        $q->where('status', 'completed')
          ->whereBetween('created_at', [$start, $end]);
    })
    ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
    ->groupBy('payment_method')
    ->get();
4. Penjualan per Karyawan (/admin/reports/employees)
Chart: Bar chart
Query: group by cashier_id atau waiter_id
5. Penjualan per Meja (/admin/reports/tables)
Chart: Bar chart
Query: group by table_id
6. Diskon & Promo Usage (/admin/reports/discounts)
Tabel: list diskon yang dipakai, berapa kali, total nilai
Query: dari discount_usages + orders
7. Stok Movement (/admin/reports/stocks)
Tabel: list perubahan stok (decrement/increment)
Query: dari orders + refunds + order_items (hitung per menu)
⚡ TIPS EFEISIENSI
Untuk mempercepat, buat base layout/template untuk semua laporan:
resources/views/admin/reports/_layout.blade.php
Isi: filter section, chart section, tabel section, export buttons
Tiap laporan extends layout ini, hanya isi content yang beda
🚀 SETELAH 6.4 SELESAI, LANJUT KE 6.5 RATING & REVIEW
Singkat saja:
Migration reviews table
Modal rating di customer order history
Tampil rating di menu detail
Admin page /admin/reviews (list + reply)
JANGAN TUNDA — LANGSUNG REPLIKASI 7 LAPORAN LAINNYA! 🚀
plain

---

Semoga lancar! 🚀
<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return Order::with('cashier')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->whereNotNull('cashier_id')
            ->selectRaw('cashier_id, SUM(total_amount) as total_sales, COUNT(*) as total_orders')
            ->groupBy('cashier_id')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Total Pesanan Ditangani',
            'Total Pendapatan (Rp)'
        ];
    }

    public function map($item): array
    {
        return [
            $item->cashier ? $item->cashier->name : 'Unknown',
            $item->total_orders,
            $item->total_sales
        ];
    }
}

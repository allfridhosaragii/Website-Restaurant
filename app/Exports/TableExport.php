<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TableExport implements FromCollection, WithHeadings, WithMapping
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
        return Order::with('table')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->whereNotNull('table_id')
            ->selectRaw('table_id, SUM(total_amount) as total_sales, COUNT(*) as total_orders')
            ->groupBy('table_id')
            ->orderByDesc('total_sales')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Meja',
            'Total Pesanan',
            'Total Pendapatan (Rp)'
        ];
    }

    public function map($item): array
    {
        return [
            $item->table ? 'Meja ' . $item->table->table_number : 'Unknown',
            $item->total_orders,
            $item->total_sales
        ];
    }
}

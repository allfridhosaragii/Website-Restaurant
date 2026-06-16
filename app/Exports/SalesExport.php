<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
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
        return Order::where('status', 'completed')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID Pesanan',
            'Tanggal Transaksi',
            'Tipe Pesanan',
            'Pelanggan',
            'Kasir',
            'Subtotal',
            'Pajak',
            'Diskon',
            'Total',
            'Status'
        ];
    }

    public function map($order): array
    {
        return [
            $order->id,
            $order->created_at->format('Y-m-d H:i:s'),
            $order->order_type,
            $order->customer ? $order->customer->name : 'Walk-in Customer',
            $order->cashier ? $order->cashier->name : '-',
            $order->subtotal,
            $order->tax_amount,
            $order->discount_amount,
            $order->total_amount,
            $order->status
        ];
    }
}

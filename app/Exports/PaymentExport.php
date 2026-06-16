<?php

namespace App\Exports;

use App\Models\OrderPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PaymentExport implements FromCollection, WithHeadings, WithMapping
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
        return OrderPayment::whereHas('order', function($q) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$this->start, $this->end]);
            })
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Metode Pembayaran',
            'Total Transaksi',
            'Total Pendapatan (Rp)'
        ];
    }

    public function map($item): array
    {
        return [
            ucwords(str_replace('_', ' ', $item->payment_method)),
            $item->count,
            $item->total
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\DiscountUsage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DiscountExport implements FromCollection, WithHeadings, WithMapping
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
        return DiscountUsage::with(['discount', 'order'])
            ->whereHas('order', function($q) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$this->start, $this->end]);
            })
            ->selectRaw('discount_id, COUNT(*) as usage_count, SUM(discount_amount) as total_discount')
            ->groupBy('discount_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Diskon',
            'Tipe',
            'Nilai',
            'Total Digunakan',
            'Total Potongan (Rp)'
        ];
    }

    public function map($item): array
    {
        return [
            $item->discount ? $item->discount->name : 'Unknown',
            $item->discount ? $item->discount->type : '-',
            $item->discount ? $item->discount->value : 0,
            $item->usage_count,
            $item->total_discount
        ];
    }
}

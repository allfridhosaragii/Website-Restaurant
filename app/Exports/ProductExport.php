<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;
    protected $rank = 1;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function collection()
    {
        return OrderItem::whereHas('order', function($q) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$this->start, $this->end]);
            })
            ->selectRaw('menu_id, SUM(quantity) as total_qty, SUM(price * quantity) as total_revenue')
            ->groupBy('menu_id')
            ->orderByDesc('total_qty')
            ->with('menu.category')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Nama Menu',
            'Kategori',
            'Total Terjual',
            'Total Pendapatan (Rp)'
        ];
    }

    public function map($item): array
    {
        return [
            $this->rank++,
            $item->menu ? $item->menu->name : 'Unknown Menu',
            $item->menu && $item->menu->category ? $item->menu->category->name : '-',
            $item->total_qty,
            $item->total_revenue
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StockExport implements FromCollection, WithHeadings, WithMapping
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
        return OrderItem::whereHas('order', function($q) {
                $q->where('status', 'completed')
                  ->whereBetween('created_at', [$this->start, $this->end]);
            })
            ->selectRaw('menu_id, SUM(quantity) as total_decreased')
            ->groupBy('menu_id')
            ->orderByDesc('total_decreased')
            ->with('menu.category')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Menu',
            'Kategori',
            'Total Pengurangan (Terjual)',
            'Sisa Stok Saat Ini'
        ];
    }

    public function map($item): array
    {
        return [
            $item->menu ? $item->menu->name : 'Unknown',
            $item->menu && $item->menu->category ? $item->menu->category->name : '-',
            $item->total_decreased,
            $item->menu ? $item->menu->stock : 0
        ];
    }
}

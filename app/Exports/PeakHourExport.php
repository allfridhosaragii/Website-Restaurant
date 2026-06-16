<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PeakHourExport implements FromCollection, WithHeadings, WithMapping
{
    protected $start;
    protected $end;
    protected $hourlyData;

    public function __construct($start, $end)
    {
        $this->start = $start;
        $this->end = $end;
        $this->hourlyData = array_fill(0, 24, ['orders' => 0, 'sales' => 0]);
        
        $peakHours = Order::where('status', 'completed')
            ->whereBetween('created_at', [$this->start, $this->end])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total_orders, SUM(total_amount) as total_sales')
            ->groupBy('hour')
            ->get();
            
        foreach ($peakHours as $ph) {
            $this->hourlyData[(int)$ph->hour]['orders'] = $ph->total_orders;
            $this->hourlyData[(int)$ph->hour]['sales'] = $ph->total_sales;
        }
    }

    public function collection()
    {
        $collection = [];
        for ($i = 0; $i < 24; $i++) {
            if ($this->hourlyData[$i]['orders'] > 0) {
                $collection[] = (object)[
                    'hour' => str_pad($i, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($i, 2, '0', STR_PAD_LEFT) . ':59',
                    'orders' => $this->hourlyData[$i]['orders'],
                    'sales' => $this->hourlyData[$i]['sales'],
                ];
            }
        }
        return collect($collection);
    }

    public function headings(): array
    {
        return [
            'Waktu (Jam)',
            'Total Transaksi',
            'Total Pendapatan (Rp)'
        ];
    }

    public function map($item): array
    {
        return [
            $item->hour,
            $item->orders,
            $item->sales
        ];
    }
}

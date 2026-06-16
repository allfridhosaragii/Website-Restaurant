<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Shift extends Model
{
    protected $fillable = [
        'user_id',
        'clock_in',
        'clock_out',
        'opening_cash',
        'closing_cash',
        'expected_cash',
        'cash_difference',
        'notes',
        'status',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'opening_cash' => 'decimal:2',
        'closing_cash' => 'decimal:2',
        'expected_cash' => 'decimal:2',
        'cash_difference' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Calculate the expected cash: opening_cash + total cash sales during this shift.
     */
    public function calculateExpectedCash(): float
    {
        $cashSales = DB::table('orders')
            ->where('shift_id', $this->id)
            ->whereIn('payment_method', ['cash', 'split'])
            ->where('status', 'completed')
            ->sum('total');

        // For split payments, only count the cash portion
        $splitCashPortion = DB::table('order_payments')
            ->join('orders', 'orders.id', '=', 'order_payments.order_id')
            ->where('orders.shift_id', $this->id)
            ->where('orders.payment_method', 'split')
            ->where('order_payments.payment_method', 'cash')
            ->sum('order_payments.amount');

        $pureCashSales = DB::table('orders')
            ->where('shift_id', $this->id)
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total');

        return (float) $this->opening_cash + (float) $pureCashSales + (float) $splitCashPortion;
    }

    /**
     * Total orders count in this shift.
     */
    public function getTotalOrdersAttribute(): int
    {
        return DB::table('orders')->where('shift_id', $this->id)->count();
    }

    /**
     * Total revenue in this shift.
     */
    public function getTotalRevenueAttribute(): float
    {
        return (float) DB::table('orders')
            ->where('shift_id', $this->id)
            ->where('status', 'completed')
            ->sum('total');
    }

    /**
     * Duration in human-readable format.
     */
    public function getDurationAttribute(): string
    {
        $end = $this->clock_out ?? now();
        $minutes = $this->clock_in->diffInMinutes($end);
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;
        return "{$hours}j {$mins}m";
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableMovement extends Model
{
    protected $fillable = [
        'order_id',
        'from_table_id',
        'to_table_id',
        'moved_by',
        'reason',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function fromTable()
    {
        return $this->belongsTo(Table::class, 'from_table_id');
    }

    public function toTable()
    {
        return $this->belongsTo(Table::class, 'to_table_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'moved_by');
    }
}

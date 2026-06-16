<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierDebt extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'supplier_name',
        'title',
        'amount',
        'paid_amount',
        'remaining_amount',
        'due_date',
        'status',
        'notes',
        'receipt_image',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $guarded = ['id'];

    protected $fillable = [
        'title',
        'category_id',
        'amount',
        'date',
        'description',
        'receipt_image',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

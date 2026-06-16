<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableGroup extends Model
{
    protected $fillable = [
        'name',
        'table_ids',
        'status',
        'created_by',
    ];

    protected $casts = [
        'table_ids' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

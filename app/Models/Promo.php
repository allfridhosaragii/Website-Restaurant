<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $fillable = [
        'name',
        'type',
        'buy_menu_id',
        'buy_quantity',
        'get_menu_id',
        'get_quantity',
        'get_type',
        'get_discount_percent',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function buyMenu()
    {
        return $this->belongsTo(Menu::class, 'buy_menu_id');
    }

    public function getMenu()
    {
        return $this->belongsTo(Menu::class, 'get_menu_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableLayout extends Model
{
    protected $fillable = [
        'name',
        'grid_width',
        'grid_height',
        'is_active',
    ];

    public function tables()
    {
        return $this->hasMany(Table::class, 'table_layout_id');
    }
}

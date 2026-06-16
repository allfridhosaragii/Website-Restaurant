<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

    public function tableGroup()
    {
        return $this->belongsTo(TableGroup::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}

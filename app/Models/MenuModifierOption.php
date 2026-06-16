<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuModifierOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_modifier_id',
        'name',
        'price',
        'sort_order',
    ];

    public function modifier()
    {
        return $this->belongsTo(MenuModifier::class, 'menu_modifier_id');
    }
}

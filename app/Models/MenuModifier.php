<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuModifier extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_id',
        'name',
        'type',
        'is_required',
        'max_select',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function options()
    {
        return $this->hasMany(MenuModifierOption::class)->orderBy('sort_order');
    }
}

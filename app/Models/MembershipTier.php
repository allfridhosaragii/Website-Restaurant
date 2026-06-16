<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipTier extends Model
{
    protected $fillable = [
        'name',
        'min_spent',
        'discount_percent',
        'point_multiplier',
        'benefits',
        'sort_order',
    ];

    /**
     * Get the next tier above this one.
     */
    public function nextTier()
    {
        return self::where('sort_order', '>', $this->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();
    }
}

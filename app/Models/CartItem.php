<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'menu_id',
        'quantity',
        'signature',
        'modifiers'
    ];

    protected $casts = [
        'modifiers' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
}
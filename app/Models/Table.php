<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Table extends Model
{
    use HasFactory;
    protected $fillable = [
        'number',
        'capacity',
        'zone',
        'shape',
        'is_premium',
        'is_active',
        'table_layout_id',
        'position_x',
        'position_y',
        'width',
        'height',
        'status',
        'qr_code_token',
        'qr_generated_at',
    ];
    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'table_id');
    }
    public function layout()
    {
        return $this->belongsTo(TableLayout::class, 'table_layout_id');
    }
}
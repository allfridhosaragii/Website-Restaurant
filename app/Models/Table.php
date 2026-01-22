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
    ];
    protected $casts = [
        'is_premium' => 'boolean',
        'is_active' => 'boolean',
    ];
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'table_id');
    }
}
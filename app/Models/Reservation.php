<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Reservation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'date',
        'time',
        'guests',
        'table_id',
        'notes',
        'payment_proof',
        'status',
        'admin_notes',
        'deposit_amount',
        'deposit_status',
        'deposit_proof',
        'end_time',
        'reminded_at',
        'checked_in_by',
        'checked_in_at',
    ];
    protected $casts = [
        'date' => 'date',
        'end_time' => 'datetime',
        'reminded_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
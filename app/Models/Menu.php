<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'image_url',
        'category',
        'is_available',
        'slug',
        'daily_stock',
        'max_daily_stock',
        'stock_updated_at',
    ];
    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'decimal:2',
        'stock_updated_at' => 'date',
    ];
    public function decreaseStock(int $quantity = 1): bool
    {
        if ($this->daily_stock >= $quantity) {
            $this->daily_stock -= $quantity;
            if ($this->daily_stock <= 0) {
                $this->is_available = false;
            }
            return $this->save();
        }
        return false;
    }
    public function increaseStock(int $quantity = 1): bool
    {
        $this->daily_stock = min($this->daily_stock + $quantity, $this->max_daily_stock);
        return $this->save();
    }
    public function resetDailyStock(): bool
    {
        $this->daily_stock = $this->max_daily_stock;
        $this->is_available = true;
        $this->stock_updated_at = now()->toDateString();
        return $this->save();
    }
    public function needsStockReset(): bool
    {
        return !$this->stock_updated_at || $this->stock_updated_at->lt(today());
    }
    public function getStockStatusAttribute(): string
    {
        if ($this->daily_stock <= 0) return 'Habis';
        if ($this->daily_stock <= 5) return 'Hampir Habis';
        if ($this->daily_stock <= 10) return 'Terbatas';
        return 'Tersedia';
    }
    public function getStockColorAttribute(): string
    {
        if ($this->daily_stock <= 0) return 'danger';
        if ($this->daily_stock <= 5) return 'warning';
        if ($this->daily_stock <= 10) return 'info';
        return 'success';
    }
}
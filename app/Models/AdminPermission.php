<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AdminPermission extends Model
{
    protected $fillable = [
        'user_id',
        'permission_key',
        'is_enabled',
    ];
    protected $casts = [
        'is_enabled' => 'boolean',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public static function getAvailablePermissions(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'menus' => 'Daftar Menu',
            'categories' => 'Kategori',
            'inventory' => 'Stok Harian',
            'orders' => 'Pesanan',
            'reservations' => 'Reservasi',
            'tables' => 'Meja',
            'statistics' => 'Statistik',
            'developer' => 'Developer',
            'reports' => 'Laporan',
            'cms_dashboard' => 'CMS Dashboard',
            'cms_pages' => 'CMS Pages',
            'cms_media' => 'Media Library',
            'cms_settings' => 'Site Settings',
            'users' => 'Pengguna',
            'project' => 'Project',
        ];
    }
    public static function createDefaultPermissions(int $userId, bool $allEnabled = true): void
    {
        foreach (self::getAvailablePermissions() as $key => $label) {
            self::create([
                'user_id' => $userId,
                'permission_key' => $key,
                'is_enabled' => $allEnabled,
            ]);
        }
    }
}
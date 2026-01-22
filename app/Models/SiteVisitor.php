<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SiteVisitor extends Model
{
    protected $fillable = [
        'session_id',
        'page_url',
        'page_title',
        'ip_address',
        'browser',
        'browser_version',
        'device_type',
        'operating_system',
        'screen_resolution',
        'user_id',
        'user_email',
        'entry_time',
        'exit_time',
        'last_heartbeat',
        'duration_seconds',
        'is_active',
    ];
    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'last_heartbeat' => 'datetime',
        'is_active' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public static function getActiveVisitors()
    {
        return self::where('is_active', true)
            ->where('last_heartbeat', '>=', now()->subSeconds(60))
            ->orderBy('entry_time', 'desc')
            ->get();
    }
    public static function getTodayVisitors()
    {
        return self::whereDate('entry_time', today())
            ->orderBy('entry_time', 'desc')
            ->get();
    }
    public static function clearAllData()
    {
        return self::truncate();
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MaintenanceVisitor extends Model
{
    protected $fillable = [
        'session_id',
        'ip_address',
        'browser',
        'browser_version',
        'device_type',
        'operating_system',
        'screen_resolution',
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
    public function updateDuration(): void
    {
        if ($this->entry_time) {
            $endTime = $this->exit_time ?? now();
            $this->duration_seconds = $this->entry_time->diffInSeconds($endTime);
            $this->save();
        }
    }
    public function getFormattedDurationAttribute(): string
    {
        $seconds = $this->duration_seconds;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        if ($hours > 0) {
            return sprintf('%d jam %d menit %d detik', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%d menit %d detik', $minutes, $secs);
        }
        return sprintf('%d detik', $secs);
    }
}
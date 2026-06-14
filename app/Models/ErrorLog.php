<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ErrorLog extends Model
{
    protected $fillable = [
        'type',
        'message',
        'file',
        'line',
        'trace',
        'url',
        'method',
        'user_id',
        'ip_address',
        'request_data',
        'screenshot_url',
        'user_agent',
        'browser',
        'device_type',
        'screen_size',
        'is_resolved',
        'resolved_at',
        'resolved_by',
    ];
    protected $casts = [
        'request_data' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];
    protected $appends = ['markdown'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
    public static function logException(\Throwable $e, $request = null)
    {
        try {
            $data = [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ];
            if ($request) {
                $data['url'] = $request->fullUrl();
                $data['method'] = $request->method();
                $data['ip_address'] = $request->ip();
                $data['user_id'] = $request->user()?->id;
                $requestData = $request->except(['password', 'password_confirmation', '_token']);
                $data['request_data'] = array_slice($requestData, 0, 20); 
            }
            return self::create($data);
        } catch (\Exception $ex) {
            \Log::error('Failed to log error: ' . $ex->getMessage());
            return null;
        }
    }
    public function resolve()
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);
    }
    public function getMarkdownAttribute()
    {
        $md = "**Error Log Details**\n\n";
        $md .= "**Message:** `{$this->message}`\n";
        $md .= "**File:** `{$this->file}:{$this->line}`\n";
        $md .= "**URL:** [{$this->url}]({$this->url})\n";
        $md .= "**IP Address:** `{$this->ip_address}`\n";
        $md .= "**Browser:** `{$this->browser}` ($this->device_type)\n";
        if ($this->screenshot_url) {
            $md .= "**Screenshot:** [View Screenshot]({$this->screenshot_url})\n";
        }
        $md .= "\n**Stack Trace:**\n```\n{$this->trace}\n```\n";
        if ($this->request_data) {
            $md .= "\n**Request Data:**\n```json\n" . json_encode($this->request_data, JSON_PRETTY_PRINT) . "\n```\n";
        }
        return $md;
    }
}
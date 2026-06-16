<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_in_photo',
        'check_out_photo',
        'status',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Whether this attendance is late (check-in after 08:00).
     */
    public function isLate(): bool
    {
        return $this->check_in > '08:00:00';
    }

    /**
     * Badge colour for status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'present'  => 'success',
            'late'     => 'warning',
            'absent'   => 'danger',
            'leave'    => 'info',
            'sick'     => 'secondary',
            default    => 'light',
        };
    }
}

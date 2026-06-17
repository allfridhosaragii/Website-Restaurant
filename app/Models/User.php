<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; 
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'google_id',
        'is_admin',
        'status',
        'role',
        'profile_photo_path',
        'points',
        'base_salary',
        'membership_tier',
        'total_spent',
        'tier_upgraded_at',
    ];
    public function transactions()
    {
        return $this->hasMany(PointTransaction::class);
    }
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
    public function adminPermissions(): HasMany
    {
        return $this->hasMany(AdminPermission::class);
    }
    public function hasAdminPermission(string $key): bool
    {
        if ($this->isSuperAdmin() || $this->role === 'admin') {
            return true;
        }

        if ($this->role === 'manager') {
            // Manager cannot access live dashboard and finance
            if (in_array($key, ['dashboard_live', 'finance'])) {
                return false;
            }
            return true;
        }
        
        if ($this->role === 'waiter') {
            return in_array($key, ['pos', 'waiter']);
        }
        
        if ($this->role === 'cashier') {
            return in_array($key, ['pos', 'orders', 'reservations', 'shifts']);
        }

        $permission = $this->adminPermissions()->where('permission_key', $key)->first();
        if (!$permission) {
            return false;
        }
        return $permission->is_enabled;
    }
    public function isAdmin(): bool
    {
        if ($this->email === 'pedoprimasaragi@gmail.com') {
            return true;
        }
        return $this->is_admin === true || $this->role === 'viewer';
    }
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }
    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === null;
    }
    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }
    public function isSuperAdmin(): bool
    {
        return $this->email === 'pedoprimasaragi@gmail.com';
    }
    public function isOffline(): bool
    {
        return $this->status === 'offline';
    }
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return $this->profile_photo_path;
        }
        $name = urlencode($this->name);
        return 'https://ui-avatars.com/api/?name='.$name.'&color=7F9CF5&background=EBF4FF';
    }
    public function isCashier(): bool { return $this->role === 'cashier'; }
    public function isWaiter(): bool { return $this->role === 'waiter'; }
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isCustomer(): bool { return !$this->is_admin && in_array($this->role, ['customer', null, '']); }
    public function shifts() { return $this->hasMany(\App\Models\Shift::class); }
    public function attendances() { return $this->hasMany(\App\Models\Attendance::class); }
    public function activeShift() { return $this->shifts()->where('status', 'active')->first(); }

    public function getTierAttribute()
    {
        return \App\Models\MembershipTier::whereRaw('LOWER(name) = ?', [strtolower($this->membership_tier ?? 'bronze')])->first();
    }
}
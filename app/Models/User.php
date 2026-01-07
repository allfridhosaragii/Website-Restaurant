<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens; // Sanctum enabled
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'google_id',
        'is_admin',
        'status',
        'role',
    ];
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
        // Super admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Check if user has specific permission
        $permission = $this->adminPermissions()->where('permission_key', $key)->first();
        
        // If no permission record exists, default to true (backward compatibility)
        if (!$permission) {
            return true;
        }

        return $permission->is_enabled;
    }

    public function isAdmin(): bool
    {
        // Super admin always has admin access
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
}
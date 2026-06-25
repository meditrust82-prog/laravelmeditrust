<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use App\Models\Concerns\HasFrontendCompatibility;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasFrontendCompatibility;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'login_attempts', 'lock_until',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['_id', 'loginAttempts', 'lockUntil', 'isAdmin'];

    protected $casts = [
        'login_attempts' => 'integer',
        'lock_until' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $user) {
            if ($user->isDirty('password') && $user->password && !str_starts_with($user->password, '$2y$')) {
                $user->password = Hash::make($user->password);
            }
        });
    }

    public function refreshTokens()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function isLocked(): bool
    {
        return $this->lock_until && $this->lock_until->isFuture();
    }

    public function comparePassword(string $candidate): bool
    {
        return Hash::check($candidate, $this->password);
    }

    public function incLoginAttempts(): void
    {
        $this->login_attempts = ($this->login_attempts ?? 0) + 1;
        if ($this->login_attempts >= 10) {
            $this->lock_until = now()->addMinutes(15);
        }
        $this->save();
    }

    public function resetLoginAttempts(): void
    {
        $this->login_attempts = 0;
        $this->lock_until = null;
        $this->save();
    }

    public function getLoginAttemptsAttribute()
    {
        return $this->attributes['login_attempts'] ?? 0;
    }

    public function getLockUntilAttribute()
    {
        return $this->attributes['lock_until'] ?? null;
    }

    public function getIsAdminAttribute()
    {
        return ($this->role ?? null) === 'admin';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefreshToken extends Model
{
    protected $fillable = [
        'user_id', 'token', 'expires_at', 'replaced_by', 'revoked_at', 'user_agent', 'ip',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at ? now()->greaterThanOrEqualTo($this->expires_at) : false;
    }

    public function getIsRevokedAttribute(): bool
    {
        return !is_null($this->revoked_at);
    }

    public static function generate(): string
    {
        return bin2hex(random_bytes(40));
    }
}

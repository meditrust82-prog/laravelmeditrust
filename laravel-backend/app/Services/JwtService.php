<?php

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class JwtService
{
    protected $secret;
    protected $ttl; // minutes

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', env('APP_KEY'));
        $this->ttl = (int) env('JWT_TTL_MINUTES', 60);
    }

    public function issueToken(User $user): string
    {
        $now = time();
        $exp = $now + ($this->ttl * 60);
        $payload = [
            'sub' => $user->id,
            'iat' => $now,
            'exp' => $exp,
            'email' => $user->email,
        ];
        return JWT::encode($payload, $this->secret, 'HS256');
    }

    public function decodeToken(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, 'HS256'));
    }

    public function getUserIdFromToken(string $token): ?int
    {
        $payload = $this->decodeToken($token);
        return isset($payload->sub) ? (int)$payload->sub : null;
    }
}

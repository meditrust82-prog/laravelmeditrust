<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\RefreshToken;
use App\Services\JwtService;

class AuthController extends Controller
{
    public function login(Request $req)
    {
        $data = $req->validate([
            'email' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim(strtolower($data['email']));
        try {
            $user = User::whereRaw('LOWER(email) = ?', [$identifier])
                ->orWhereRaw('LOWER(username) = ?', [$identifier])
                ->first();

            $dummy = '$2y$12$C9uL3W6QK6Q8f6Q6L1bQPO8R2KzM6qS8b0m2m4x2QJg2M9X6Q0c7e';
            $passwordOk = Hash::check($data['password'], $user?->password ?? $dummy);

            if (!$user || !$passwordOk) {
                if ($user && env('APP_ENV') !== 'local') {
                    $user->incLoginAttempts();
                }
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            if ($user->isLocked()) {
                return response()->json(['error' => 'Account locked due to too many failed attempts.'], 423);
            }

            $user->resetLoginAttempts();
            $jwt = new JwtService();
            $accessToken = $jwt->issueToken($user);
            $refreshToken = RefreshToken::generate();
            RefreshToken::create([
                'user_id' => $user->id,
                'token' => $refreshToken,
                'expires_at' => now()->addDays(7),
                'user_agent' => substr((string) $req->userAgent(), 0, 300),
                'ip' => $req->ip(),
            ]);

            $payload = ['user' => $this->formatUser($user)];
            $secure = env('APP_ENV') === 'production';
            $cookieDomain = parse_url(env('APP_URL'), PHP_URL_HOST) ?: null;

            return response()->json($payload)
                ->cookie('access_token', $accessToken, 15, '/', $cookieDomain, $secure, true, false, 'Lax')
                ->cookie('refresh_token', $refreshToken, 10080, '/api/v1/auth/refresh', $cookieDomain, $secure, true, false, 'Lax');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
    }

    public function refresh(Request $req)
    {
        $rawToken = $req->cookie('refresh_token') ?: $req->input('refreshToken');
        if (!$rawToken) {
            return response()->json(['error' => 'No refresh token'], 401);
        }

        $record = RefreshToken::where('token', $rawToken)->first();
        if (!$record) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
        if ($record->is_revoked) {
            RefreshToken::where('user_id', $record->user_id)->whereNull('revoked_at')->update(['revoked_at' => now()]);
            return response()->json(['error' => 'Token reuse detected. All sessions revoked.'], 401);
        }
        if ($record->is_expired) {
            return response()->json(['error' => 'Refresh token expired'], 401);
        }

        $record->revoked_at = now();
        $record->replaced_by = 'rotating';
        $record->save();

        $user = User::find($record->user_id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 401);
        }

        $jwt = new JwtService();
        $newAccess = $jwt->issueToken($user);
        $newRefresh = RefreshToken::generate();
        $record->replaced_by = $newRefresh;
        $record->save();

        RefreshToken::create([
            'user_id' => $user->id,
            'token' => $newRefresh,
            'expires_at' => now()->addDays(7),
            'user_agent' => substr((string) $req->userAgent(), 0, 300),
            'ip' => $req->ip(),
        ]);

        $secure = env('APP_ENV') === 'production';
        $cookieDomain = parse_url(env('APP_URL'), PHP_URL_HOST) ?: null;

        return response()->json(['user' => $this->formatUser($user)])
            ->cookie('access_token', $newAccess, 15, '/', $cookieDomain, $secure, true, false, 'Lax')
            ->cookie('refresh_token', $newRefresh, 10080, '/api/v1/auth/refresh', $cookieDomain, $secure, true, false, 'Lax');
    }

    public function logout(Request $req)
    {
        $rawToken = $req->cookie('refresh_token') ?: $req->input('refreshToken');
        if ($rawToken) {
            RefreshToken::where('token', $rawToken)->delete();
        }

        return response()->json(['message' => 'Logged out successfully'])
            ->withoutCookie('access_token')
            ->withoutCookie('refresh_token', '/api/v1/auth/refresh');
    }

    public function me(Request $req)
    {
        $user = auth()->user();
        return response()->json(['user' => $user ? $this->formatUser($user) : null]);
    }

    public function updateProfile(Request $req)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $req->validate([
            'name' => ['sometimes', 'string', 'max:100'],
            'username' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'email'],
            'currentPassword' => ['nullable', 'string'],
            'newPassword' => ['nullable', 'string', 'min:8'],
        ]);

        if (array_key_exists('name', $data)) {
            $user->name = trim($data['name']);
        }
        if (array_key_exists('username', $data)) {
            $user->username = $data['username'] ? strtolower(trim($data['username'])) : null;
        }
        if (array_key_exists('email', $data)) {
            $user->email = strtolower(trim($data['email']));
        }
        if (!empty($data['newPassword'])) {
            if (empty($data['currentPassword']) || !Hash::check($data['currentPassword'], $user->password)) {
                return response()->json(['error' => 'Current password is incorrect'], 401);
            }
            $user->password = $data['newPassword'];
        }

        $user->save();
        return response()->json(['user' => $this->formatUser($user)]);
    }

    protected function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'role' => $user->role,
            'is_admin' => $user->role === 'admin',
            'createdAt' => optional($user->created_at)?->toIso8601String(),
            'updatedAt' => optional($user->updated_at)?->toIso8601String(),
        ];
    }
}

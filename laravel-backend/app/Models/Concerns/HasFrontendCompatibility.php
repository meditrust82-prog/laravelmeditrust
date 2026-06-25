<?php

namespace App\Models\Concerns;

trait HasFrontendCompatibility
{
    public function getIdAttribute($value)
    {
        return $value ?? ($this->attributes['id'] ?? null);
    }

    protected function frontendAttribute(string $key, mixed $fallback = null): mixed
    {
        return $this->attributes[$key] ?? $fallback;
    }

    protected function frontendDateAttribute(string $key): ?string
    {
        $value = $this->attributes[$key] ?? null;
        return $value ? $this->asDateTime($value)->toIso8601String() : null;
    }

    protected function frontendJsonAttribute(string $key): mixed
    {
        $value = $this->attributes[$key] ?? null;
        return is_string($value) ? (json_decode($value, true) ?: []) : ($value ?: []);
    }
}

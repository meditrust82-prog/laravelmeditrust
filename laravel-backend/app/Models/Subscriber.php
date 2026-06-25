<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Subscriber extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = ['name', 'phone', 'source', 'active'];

    protected $casts = [
        'active' => 'boolean',
    ];

    protected $appends = ['_id', 'createdAt', 'updatedAt'];

    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

<?php

namespace App\Models;

use App\Models\Concerns\HasFrontendCompatibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'title', 'category', 'description', 'images', 'year', 'location', 'client',
        'featured', 'sort_order', 'published',
    ];

    protected $casts = [
        'images' => 'array',
        'featured' => 'boolean',
        'published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['_id', 'createdAt', 'updatedAt'];

    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

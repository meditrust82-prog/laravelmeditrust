<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Blog extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'image', 'author', 'category',
        'tags', 'published', 'meta_title', 'meta_desc',
    ];

    protected $casts = [
        'tags' => 'array',
        'published' => 'boolean',
    ];

    protected $appends = ['_id', 'metaTitle', 'metaDesc', 'createdAt', 'updatedAt'];

    public function getMetaTitleAttribute($value) { return $this->frontendAttribute('meta_title', $value); }
    public function getMetaDescAttribute($value) { return $this->frontendAttribute('meta_desc', $value); }
    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

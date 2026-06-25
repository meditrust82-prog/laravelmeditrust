<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Testimonial extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'name', 'position', 'organization', 'content', 'rating', 'photo_url', 'source', 'visible', 'order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'visible' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = ['_id', 'photoUrl', 'createdAt', 'updatedAt'];

    public function getPhotoUrlAttribute($value) { return $this->frontendAttribute('photo_url', $value); }
    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

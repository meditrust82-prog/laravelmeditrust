<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Banner extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'title', 'subtitle', 'image_url', 'link_url', 'link_label', 'bg_color', 'text_color',
        'placement', 'priority', 'clicks', 'active', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'priority' => 'integer',
        'clicks' => 'integer',
        'active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = ['_id', 'imageUrl', 'linkUrl', 'linkLabel', 'bgColor', 'textColor', 'startsAt', 'endsAt', 'createdAt', 'updatedAt'];

    public function getImageUrlAttribute($value) { return $this->frontendAttribute('image_url', $value); }
    public function getLinkUrlAttribute($value) { return $this->frontendAttribute('link_url', $value); }
    public function getLinkLabelAttribute($value) { return $this->frontendAttribute('link_label', $value); }
    public function getBgColorAttribute($value) { return $this->frontendAttribute('bg_color', $value); }
    public function getTextColorAttribute($value) { return $this->frontendAttribute('text_color', $value); }
    public function getStartsAtAttribute($value) { return $this->frontendDateAttribute('starts_at'); }
    public function getEndsAtAttribute($value) { return $this->frontendDateAttribute('ends_at'); }
    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

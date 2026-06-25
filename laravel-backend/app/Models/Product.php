<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Product extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'name', 'slug', 'description', 'specifications', 'brand', 'price',
        'original_price', 'cost', 'category', 'images', 'stock', 'featured',
        'meta_title', 'meta_description', 'meta_keywords', 'badges',
    ];

    protected $casts = [
        'images' => 'array',
        'badges' => 'array',
        'featured' => 'boolean',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock' => 'integer',
    ];

    protected $appends = ['_id', 'image', 'allImages', 'categorySlug', 'quantity', 'originalPrice', 'metaTitle', 'metaDescription', 'metaKeywords', 'createdAt', 'updatedAt'];

    public function getImageAttribute()
    {
        return $this->images[0]['url'] ?? $this->images[0]['path'] ?? null;
    }

    public function getAllImagesAttribute()
    {
        return collect($this->images ?? [])
            ->map(fn ($img) => $img['url'] ?? $img['path'] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    public function getCategorySlugAttribute()
    {
        return $this->category ? str($this->category)->slug()->toString() : null;
    }

    public function getQuantityAttribute()
    {
        return $this->stock;
    }

    public function getOriginalPriceAttribute($value)
    {
        return $this->frontendAttribute('original_price', $value);
    }

    public function getMetaTitleAttribute($value)
    {
        return $this->frontendAttribute('meta_title', $value);
    }

    public function getMetaDescriptionAttribute($value)
    {
        return $this->frontendAttribute('meta_description', $value);
    }

    public function getMetaKeywordsAttribute($value)
    {
        return $this->frontendAttribute('meta_keywords', $value);
    }

    public function getCreatedAtAttribute($value)
    {
        return $this->frontendDateAttribute('created_at');
    }

    public function getUpdatedAtAttribute($value)
    {
        return $this->frontendDateAttribute('updated_at');
    }
}

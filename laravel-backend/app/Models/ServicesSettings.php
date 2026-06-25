<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class ServicesSettings extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $table = 'services_settings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['id', 'hero_label', 'hero_title', 'hero_desc', 'services'];

    protected $casts = ['services' => 'array'];

    protected $appends = ['_id', 'heroLabel', 'heroTitle', 'heroDesc'];

    public function getHeroLabelAttribute($value) { return $this->frontendAttribute('hero_label', $value); }
    public function getHeroTitleAttribute($value) { return $this->frontendAttribute('hero_title', $value); }
    public function getHeroDescAttribute($value) { return $this->frontendAttribute('hero_desc', $value); }
}

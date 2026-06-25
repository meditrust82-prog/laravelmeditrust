<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class HomepageSettings extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $table = 'homepage_settings';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'hero_badge', 'hero_title', 'hero_subtitle', 'hero_primary_btn', 'hero_secondary_btn',
        'stats', 'trusted_by', 'why_choose_us',
    ];

    protected $casts = [
        'stats' => 'array',
        'trusted_by' => 'array',
        'why_choose_us' => 'array',
    ];

    protected $appends = ['_id', 'heroBadge', 'heroTitle', 'heroSubtitle', 'heroPrimaryBtn', 'heroSecondaryBtn', 'trustedBy', 'whyChooseUs'];

    public function getHeroBadgeAttribute($value) { return $this->frontendAttribute('hero_badge', $value); }
    public function getHeroTitleAttribute($value) { return $this->frontendAttribute('hero_title', $value); }
    public function getHeroSubtitleAttribute($value) { return $this->frontendAttribute('hero_subtitle', $value); }
    public function getHeroPrimaryBtnAttribute($value) { return $this->frontendAttribute('hero_primary_btn', $value); }
    public function getHeroSecondaryBtnAttribute($value) { return $this->frontendAttribute('hero_secondary_btn', $value); }
    public function getTrustedByAttribute($value) { return $this->frontendJsonAttribute('trusted_by'); }
    public function getWhyChooseUsAttribute($value) { return $this->frontendJsonAttribute('why_choose_us'); }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomepageSettings;
use App\Models\AboutSettings;
use App\Models\LegalSettings;
use App\Models\ServicesSettings;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        HomepageSettings::updateOrCreate(['id' => 'singleton'], [
            'hero_badge' => "Nepal's Trusted Surgical Equipment Partner",
            'hero_title' => "Surgical Equipment\nBuilt for Excellence",
            'hero_subtitle' => 'Supplying certified surgical instruments, operating room equipment, and medical devices to hospitals and clinics across Nepal — with expert support from day one.',
            'hero_primary_btn' => 'Browse Products',
            'hero_secondary_btn' => 'Request a Quote',
        ]);

        AboutSettings::updateOrCreate(['id' => 'singleton'], []);
        LegalSettings::updateOrCreate(['id' => 'singleton'], [
            'privacy_title' => 'Privacy Policy',
            'terms_title' => 'Terms & Conditions',
        ]);
        ServicesSettings::updateOrCreate(['id' => 'singleton'], []);
    }
}

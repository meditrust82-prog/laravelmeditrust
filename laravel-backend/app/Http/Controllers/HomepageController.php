<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomepageSettings;

class HomepageController extends Controller
{
    protected function singleton(): HomepageSettings
    {
        return HomepageSettings::firstOrCreate(['id' => 'singleton'], [
            'hero_badge' => "Nepal's Trusted Surgical Equipment Partner",
            'hero_title' => "Surgical Equipment\nBuilt for Excellence",
            'hero_subtitle' => 'Supplying certified surgical instruments, operating room equipment, and medical devices to hospitals and clinics across Nepal — with expert support from day one.',
            'hero_primary_btn' => 'Browse Products',
            'hero_secondary_btn' => 'Request a Quote',
        ]);
    }

    public function index()
    {
        return response()->json($this->singleton());
    }

    public function update(Request $req)
    {
        $data = $req->only(['heroBadge','heroTitle','heroSubtitle','heroPrimaryBtn','heroSecondaryBtn','stats','trustedBy','whyChooseUs']);
        $doc = $this->singleton();
        if (array_key_exists('heroBadge', $data)) $doc->hero_badge = $data['heroBadge'];
        if (array_key_exists('heroTitle', $data)) $doc->hero_title = $data['heroTitle'];
        if (array_key_exists('heroSubtitle', $data)) $doc->hero_subtitle = $data['heroSubtitle'];
        if (array_key_exists('heroPrimaryBtn', $data)) $doc->hero_primary_btn = $data['heroPrimaryBtn'];
        if (array_key_exists('heroSecondaryBtn', $data)) $doc->hero_secondary_btn = $data['heroSecondaryBtn'];
        if (array_key_exists('stats', $data)) $doc->stats = $data['stats'];
        if (array_key_exists('trustedBy', $data)) $doc->trusted_by = $data['trustedBy'];
        if (array_key_exists('whyChooseUs', $data)) $doc->why_choose_us = $data['whyChooseUs'];
        $doc->save();
        return response()->json($doc);
    }
}

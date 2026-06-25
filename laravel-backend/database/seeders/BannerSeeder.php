<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        Banner::create([
            'placement' => 'announcement',
            'title' => 'Special discount on ICU Ventilators! Get 15% off until this weekend.',
            'link_url' => '/products',
            'link_label' => 'Shop Now',
            'payload' => json_encode(['discount' => 15, 'category' => 'ICU Equipment']),
        ]);
        
        Banner::create([
            'placement' => 'hero',
            'title' => 'Reliable Medical Equipment',
            'link_url' => '/products',
            'link_label' => 'Explore Products',
            'payload' => json_encode(['subtitle' => 'For hospitals across Nepal']),
        ]);
    }
}

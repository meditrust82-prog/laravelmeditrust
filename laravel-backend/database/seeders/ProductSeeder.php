<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'ICU Ventilator MV200',
                'category' => 'ICU Equipment',
                'brand' => 'MedTech',
                'price' => 1250000,
                'original_price' => 1500000,
                'description' => 'Advanced ICU Ventilator for critical care with full monitoring capabilities.',
                'specifications' => json_encode(['Tidal Volume' => '20-2000 ml', 'Modes' => 'VCV, PCV, SIMV, PSV']),
                'stock' => 10,
                'featured' => true,
                'badges' => ['CE Certified', 'ISO 9001'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1516549655169-df83a0774514?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 'alt' => 'ICU Ventilator'],
                ],
            ],
            [
                'name' => 'Portable ECG Machine 12-Channel',
                'category' => 'Diagnostic',
                'brand' => 'HeartCare',
                'price' => 45000,
                'original_price' => 55000,
                'description' => 'Compact and portable 12-channel ECG machine with digital display and printer.',
                'specifications' => json_encode(['Channels' => '12', 'Display' => '7-inch LCD']),
                'stock' => 25,
                'featured' => true,
                'badges' => ['Bestseller'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1551076805-e1869033e561?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 'alt' => 'ECG Machine'],
                ],
            ],
            [
                'name' => 'Surgical Instruments Set (General Surgery)',
                'category' => 'Surgical Instruments',
                'brand' => 'SurgiPro',
                'price' => 85000,
                'original_price' => 90000,
                'description' => 'Comprehensive set of surgical instruments for general surgery procedures.',
                'specifications' => json_encode(['Material' => 'Stainless Steel', 'Pieces' => '54']),
                'stock' => 50,
                'featured' => false,
                'badges' => ['High Quality'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1583324113626-70df0f4deaab?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 'alt' => 'Surgical Instruments'],
                ],
            ],
            [
                'name' => 'Multiparameter Patient Monitor',
                'category' => 'Patient Monitoring',
                'brand' => 'VitalSense',
                'price' => 75000,
                'original_price' => 85000,
                'description' => 'Advanced patient monitor for ICU/CCU with ECG, SpO2, NIBP, RESP, and TEMP.',
                'specifications' => json_encode(['Screen' => '12.1" TFT', 'Parameters' => 'ECG, SpO2, NIBP, RESP, 2-TEMP']),
                'stock' => 15,
                'featured' => true,
                'badges' => ['CE Certified'],
                'images' => [
                    ['url' => 'https://images.unsplash.com/photo-1530497610245-94d3c16cda28?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80', 'alt' => 'Patient Monitor'],
                ],
            ]
        ];

        foreach ($products as $p) {
            $p['slug'] = Str::slug($p['name']);
            Product::create($p);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        Quote::create([
            'product_name' => 'ICU Ventilator MV200',
            'product_slug' => 'icu-ventilator-mv200',
            'name' => 'Dr. Jane Doe',
            'hospital_name' => 'Kathmandu City Hospital',
            'phone' => '+977-9812345678',
            'email' => 'jane.doe@example.com',
            'message' => 'Looking for a quotation for 2 ICU ventilators.',
            'qty' => 2,
            'source' => 'web',
            'status' => 'pending',
        ]);
    }
}

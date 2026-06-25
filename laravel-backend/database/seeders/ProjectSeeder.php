<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            ['title' => 'Bir Hospital ICU Upgrade', 'category' => 'ICU Setup', 'description' => 'Complete ICU equipment supply and installation for 20-bed intensive care unit at Bir Hospital, including ventilators, patient monitors, and infusion pumps.', 'year' => '2023'],
            ['title' => 'Nepal Mediciti Laboratory Setup', 'category' => 'Laboratory', 'description' => 'Full laboratory equipment setup including hematology analyzers, chemistry analyzers, and microscopy equipment.', 'year' => '2022'],
            ['title' => 'Grande Hospital OT Installation', 'category' => 'OT Setup', 'description' => 'Operation theatre equipment supply including OT lights, tables, anesthesia machines, and electrosurgical units.', 'year' => '2023'],
            ['title' => 'Patan Hospital Diagnostic Center', 'category' => 'Diagnostic Center', 'description' => 'Complete diagnostic center setup with X-ray, ultrasound, and ECG machines for outpatient diagnostics.', 'year' => '2022'],
            ['title' => 'Teaching Hospital Equipment Supply', 'category' => 'Hospital Setup', 'description' => 'Comprehensive medical equipment supply for multiple departments including emergency, surgery, and radiology.', 'year' => '2021'],
            ['title' => 'Norvic Hospital Expansion', 'category' => 'Hospital Setup', 'description' => 'Medical equipment for new wing expansion including patient monitoring systems and nursing station equipment.', 'year' => '2023'],
        ];

        foreach ($projects as $index => $project) {
            Project::updateOrCreate(
                ['title' => $project['title']],
                $project + ['images' => [], 'sort_order' => $index, 'published' => true]
            );
        }
    }
}

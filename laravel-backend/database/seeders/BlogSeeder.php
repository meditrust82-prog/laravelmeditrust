<?php

namespace Database\Seeders;

use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $content = <<<'HTML'
<p>Opening a clinic in Nepal is equal parts exciting and overwhelming. Beyond the licences and the location, the single biggest decision is <strong>what equipment to buy first</strong>. Order too little and you turn patients away; order too much and you tie up capital in machines you barely use.</p>
<p>This checklist walks you through the essential medical equipment for a new clinic — organised by priority, with practical tips on what matters most at each stage.</p>

<h2>Start with the diagnostic essentials</h2>
<p>Every patient journey begins with measurement. These four items are non-negotiable on day one, because almost every consultation depends on them.</p>
<h3>Stethoscope &amp; sphygmomanometer</h3>
<p>Invest in a reliable stethoscope (a cardiology-grade model if your budget allows) and a well-calibrated blood-pressure monitor. Digital BP monitors are faster and easier to read, but keep a manual aneroid as a backup for power cuts.</p>
<h3>Thermometer &amp; pulse oximeter</h3>
<p>An infrared or digital thermometer speeds up triage, while a fingertip pulse oximeter is essential for respiratory screening — a lesson reinforced strongly over the last few years.</p>
<h3>Weighing scale &amp; height measurement</h3>
<p>Accurate weight is the foundation of paediatric dosing and BMI checks. Choose a durable platform scale that handles both infants (with a baby tray) and adults.</p>

<h2>Patient monitoring &amp; life support</h2>
<p>Once diagnostics are covered, the next tier protects patients who are unstable or need observation.</p>
<ul>
<li><strong>Patient monitor</strong> — a multi-parameter unit (SpO₂, NIBP, ECG, temperature) covers most observation beds.</li>
<li><strong>Oxygen concentrator</strong> — more practical than cylinders in most clinics; pair it with a flow meter and a few cylinders for emergencies.</li>
<li><strong>Suction machine</strong> — essential for airway management and minor procedures.</li>
<li><strong>Nebulizer &amp; autoclave</strong> — for respiratory therapy and instrument sterilisation respectively.</li>
</ul>

<h2>Furniture, sterilisation &amp; storage</h2>
<p>Good equipment is undermined by poor infrastructure. Budget for the unglamorous but critical items:</p>
<ol>
<li>Examination couches and screens for patient privacy</li>
<li>Instrument trolleys and a proper autoclave for sterilisation</li>
<li>Medicine cabinets and a lockable controlled-drug cupboard</li>
<li>Sharps containers and biohazard bins for safe waste disposal</li>
</ol>

<h2>Budget planning &amp; procurement tips</h2>
<p>Most first-time clinic owners overspend on the big machines and underspend on consumables. A useful rule of thumb is the <strong>70/30 split</strong>: about 70% of your equipment budget on core diagnostics and monitoring, and 30% on furniture, storage and consumables.</p>
<blockquote>Buy equipment for the patients you actually see, not the patients you hope to see.</blockquote>
<p>Finally, always verify that your supplier provides <strong>warranty, after-sales service and genuine parts</strong> in Nepal. A cheaper import with no local support can end up costing far more in downtime than the money you saved.</p>
HTML;

        $tags = ['Clinic Setup', 'Medical Equipment', 'Nepal', 'Buying Guide', 'Diagnostics'];

        $wordCount = count(preg_split('/\s+/u', trim(html_entity_decode(strip_tags($content)))));
        $readingTime = max(1, (int) ceil($wordCount / 200));

        Blog::updateOrCreate(
            ['slug' => 'essential-medical-equipment-checklist-for-a-new-clinic-in-nepal'],
            [
                'title' => 'Essential Medical Equipment Checklist for a New Clinic in Nepal',
                'excerpt' => 'A practical, prioritised guide to equipping a new clinic — from core diagnostics and patient monitoring to furniture, sterilisation and smart budget planning.',
                'content' => $content,
                'category' => 'Buying Guides',
                'tags' => $tags,
                'published' => true,
                'published_at' => now(),
                'scheduled_at' => null,
                'author' => 'Meditrust Nepal',
                'author_credentials' => 'Medical Equipment Procurement Team',
                'author_bio' => 'The Meditrust Nepal team helps hospitals, clinics and diagnostic centres across Nepal source certified medical equipment with reliable after-sales support.',
                'reviewer_name' => 'Dr. Anish Shrestha',
                'reviewer_designation' => 'Consultant, Healthcare Operations',
                'reviewer_credentials' => 'MBBS, MD (Hospital Administration)',
                'reviewed_at' => now()->toDateString(),
                'word_count' => $wordCount,
                'reading_time' => $readingTime,
                'meta_title' => 'Essential Medical Equipment Checklist for a New Clinic in Nepal',
                'meta_desc' => 'A prioritised checklist of essential medical equipment for opening a clinic in Nepal — diagnostics, monitoring, furniture and budget tips.',
                'focus_keyword' => 'medical equipment for clinic Nepal',
                'secondary_keywords' => ['clinic setup checklist', 'patient monitor Nepal', 'oxygen concentrator Nepal', 'medical equipment supplier Nepal'],
                'search_intent' => 'Commercial investigation',
                'country' => 'Nepal',
                'primary_question' => 'What medical equipment do I need to open a clinic in Nepal?',
                'direct_answer' => 'Start with core diagnostics (stethoscope, BP monitor, thermometer, pulse oximeter, weighing scale), then patient monitoring and life support (patient monitor, oxygen concentrator, suction machine), and finally furniture, sterilisation and storage.',
                'key_takeaways' => [
                    'Prioritise diagnostic essentials before buying big machines.',
                    'A multi-parameter patient monitor covers most observation beds.',
                    'Budget roughly 70% for core equipment and 30% for furniture and consumables.',
                    'Always verify warranty and local after-sales support before purchasing.',
                ],
                'faqs' => [
                    ['q' => 'How much does it cost to equip a basic clinic in Nepal?', 'a' => 'A basic single-room clinic can be equipped for a few lakh NPR, while a multi-room clinic with monitoring and life-support equipment typically runs into higher ranges depending on brand and certification.'],
                    ['q' => 'Should I buy an oxygen concentrator or cylinders?', 'a' => 'For most clinics a concentrator is more practical and cost-effective for continuous use; keep a small number of cylinders as emergency backup.'],
                    ['q' => 'Do you provide after-sales service in Nepal?', 'a' => 'Yes — Meditrust Nepal offers warranty, genuine spare parts and local after-sales support for the equipment we supply.'],
                ],
                'sources' => [
                    ['title' => 'WHO — Essential medical devices for primary health care', 'url' => 'https://www.who.int/publications/i/item/9789240019812', 'publisher' => 'World Health Organization', 'type' => 'guideline'],
                    ['title' => 'Ministry of Health and Population, Nepal — Health facility standards', 'url' => 'https://mohp.gov.np', 'publisher' => 'MoHP Nepal', 'type' => 'standard'],
                ],
            ]
        );

        $this->command->info('Seeded blog: ' . 'Essential Medical Equipment Checklist for a New Clinic in Nepal');
    }
}

@extends('layouts.app')

@section('title', 'Blog — Medical Equipment Insights | Meditrust Nepal')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="text-blue-300 font-semibold text-sm uppercase tracking-wider">Our Blog</span>
        <h1 class="text-4xl font-bold text-white mt-3 mb-4">Insights &amp; Articles</h1>
        <p class="text-blue-100 max-w-2xl">Stay updated with the latest news, tips, and insights about medical equipment and healthcare technology in Nepal.</p>
    </div>
</section>

{{-- Blog grid --}}
<section class="py-16 bg-gray-50 dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @php
            $blogs = $blogsData['blogs'] ?? [];
            $defaultPosts = [
                ['slug'=>'icu-ventilator-price-nepal-2025',      'title'=>'ICU Ventilator Price in Nepal 2025 — Complete Buying Guide',                  'excerpt'=>'Compare ICU ventilator prices in Nepal. Learn what affects cost, which brands are CE certified, and how to request a quote.', 'category'=>'ICU Equipment',          'author'=>'Meditrust Nepal', 'created_at'=>'2025-01-10', 'image'=>null],
                ['slug'=>'patient-monitor-buying-guide-nepal',    'title'=>'Patient Monitor Buying Guide for Hospitals in Nepal',                          'excerpt'=>'How to choose the right patient monitor for your hospital in Nepal. Compare bedside vs transport monitors, parameters, brands and prices.', 'category'=>'Monitoring Equipment',   'author'=>'Meditrust Nepal', 'created_at'=>'2025-02-05', 'image'=>null],
                ['slug'=>'how-to-set-up-icu-nepal',              'title'=>'How to Set Up an ICU in Nepal — Complete Equipment Checklist',                  'excerpt'=>'Planning an ICU? This complete checklist covers every essential piece of medical equipment, staffing ratios, and procurement tips.', 'category'=>'Hospital Setup',          'author'=>'Meditrust Nepal', 'created_at'=>'2025-02-20', 'image'=>null],
                ['slug'=>'surgical-instruments-nepal',            'title'=>'Surgical Instruments in Nepal — CE Certified Sources &amp; Prices',             'excerpt'=>'Where to buy CE certified surgical instruments in Nepal. Compare prices for laparoscopic sets, orthopedic instruments and more.', 'category'=>'Surgical Equipment',      'author'=>'Meditrust Nepal', 'created_at'=>'2025-03-01', 'image'=>null],
                ['slug'=>'medical-equipment-maintenance-nepal',   'title'=>'Medical Equipment Maintenance in Nepal — AMC, PMC &amp; Best Practices',        'excerpt'=>'How to maintain medical equipment in Nepali hospitals. Learn about AMC, Preventive Maintenance, and common equipment failures.', 'category'=>'Maintenance',             'author'=>'Meditrust Nepal', 'created_at'=>'2025-03-15', 'image'=>null],
            ];
            if (empty($blogs)) $blogs = $defaultPosts;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($blogs as $blog)
                @php
                    $blogSlug    = $blog['slug'] ?? $blog['_id'] ?? $blog['id'] ?? '';
                    $blogTitle   = $blog['title'] ?? 'Untitled';
                    $blogExcerpt = $blog['excerpt'] ?? \Illuminate\Support\Str::limit(strip_tags($blog['content'] ?? ''), 140);
                    $blogImage   = $blog['image'] ?? null;
                    $blogCat     = $blog['category'] ?? '';
                    $blogAuthor  = $blog['author'] ?? 'Meditrust Nepal';
                    $blogDate    = $blog['created_at'] ?? $blog['createdAt'] ?? null;
                @endphp
                <a href="/blog/{{ $blogSlug }}"
                   class="bg-white dark:bg-dark-card rounded-xl overflow-hidden shadow-sm hover:shadow-md group border border-gray-100 dark:border-dark-border flex flex-col transition-all">
                    <div class="h-48 bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        @if($blogImage)
                            <img src="{{ $blogImage }}" alt="{{ $blogTitle }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" />
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/30 dark:to-primary-800/30">
                                <svg class="w-16 h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center text-xs text-gray-500 gap-3 mb-3 flex-wrap">
                            @if($blogCat)
                                <span class="bg-primary-50 text-primary-600 px-2 py-0.5 rounded font-medium dark:bg-primary-900/30 dark:text-primary-400">{{ $blogCat }}</span>
                            @endif
                            @if($blogDate)
                                <span>📅 {{ \Carbon\Carbon::parse($blogDate)->format('M j, Y') }}</span>
                            @endif
                        </div>
                        <h2 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors line-clamp-2 mb-2">{{ $blogTitle }}</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm line-clamp-2 flex-1">{{ $blogExcerpt }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs text-gray-400">👤 {{ $blogAuthor }}</span>
                            <span class="text-primary-600 text-sm font-medium flex items-center gap-1 group-hover:gap-2 transition-all">Read More →</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if(empty($blogs))
            <div class="text-center py-16 text-gray-400">No blog posts available yet. Check back soon.</div>
        @endif
    </div>
</section>

@endsection

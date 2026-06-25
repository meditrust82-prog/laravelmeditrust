@extends('layouts.app')

@section('title', ($blogData['title'] ?? 'Article') . ' | Meditrust Nepal')

@section('content')
@php
    $blog = $blogData ?? null;
    $hasError = empty($blog) || !empty($blog['error']);
    $blogTitle   = $blog['title']   ?? 'Article';
    $blogExcerpt = $blog['excerpt'] ?? '';
    $blogContent = $blog['content'] ?? '';
    $blogCat     = $blog['category'] ?? '';
    $blogAuthor  = $blog['author']   ?? 'Meditrust Nepal';
    $blogDate    = $blog['created_at'] ?? $blog['createdAt'] ?? null;
    $blogImage   = $blog['image']    ?? null;
    $blogSlug    = $blog['slug']     ?? $slug ?? '';
    $shareUrl    = url('/blog/' . $blogSlug);
    $waText      = urlencode('Interesting read: ' . $blogTitle . ' — ' . $shareUrl);
@endphp

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="/blog" class="inline-flex items-center text-blue-200 hover:text-white mb-5 text-sm font-medium transition-colors">
            ← Back to Blog
        </a>
        @if(!$hasError)
            @if($blogCat)
                <span class="inline-block bg-white/20 text-white text-xs font-semibold px-3 py-1 rounded-full mb-3">{{ $blogCat }}</span>
            @endif
            <h1 class="text-3xl sm:text-4xl font-bold text-white leading-tight mb-4">{{ $blogTitle }}</h1>
            @if($blogExcerpt)
                <p class="text-blue-100 max-w-2xl leading-relaxed">{{ $blogExcerpt }}</p>
            @endif
            <div class="flex items-center gap-4 mt-5 text-blue-200 text-sm flex-wrap">
                <span>👤 {{ $blogAuthor }}</span>
                @if($blogDate)
                    <span>📅 {{ \Carbon\Carbon::parse($blogDate)->format('F j, Y') }}</span>
                @endif
            </div>
        @endif
    </div>
</section>

<section class="py-12 bg-white dark:bg-dark-surface min-h-[50vh]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        @if($hasError)
            <div class="text-center py-20 bg-gray-50 dark:bg-dark-card rounded-2xl border border-gray-100 dark:border-dark-border">
                <div class="text-6xl mb-4">📰</div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Article Not Found</h2>
                <p class="text-gray-500 mb-6">This article could not be loaded. It may have been moved or deleted.</p>
                <a href="/blog" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors">Back to Blog</a>
            </div>
        @else
            {{-- Featured Image --}}
            @if($blogImage)
                <div class="mb-8 rounded-2xl overflow-hidden shadow-sm border border-gray-100 dark:border-dark-border">
                    <img src="{{ $blogImage }}" alt="{{ $blogTitle }}" class="w-full h-72 object-cover" />
                </div>
            @endif

            {{-- Article Body --}}
            <article class="prose prose-lg prose-primary max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
                {!! $blogContent !!}
            </article>

            {{-- Share Bar --}}
            <div class="mt-12 pt-8 border-t border-gray-100 dark:border-dark-border">
                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">Share this article:</p>
                <div class="flex gap-3 flex-wrap">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode($shareUrl) }}&text={{ urlencode($blogTitle) }}"
                       target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-2 bg-sky-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-sky-600 transition-colors">
                        Twitter
                    </a>
                    <a href="https://wa.me/?text={{ $waText }}"
                       target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-600 transition-colors">
                        WhatsApp
                    </a>
                </div>
            </div>

            {{-- CTA --}}
            <div class="mt-10 bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex-1">
                    <p class="font-bold text-gray-900 dark:text-white">Need equipment mentioned in this article?</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Request a quotation from Meditrust Nepal — same-day response via WhatsApp.</p>
                </div>
                <a href="https://wa.me/9779818100515?text={{ urlencode('Hello, I read your article about ' . $blogTitle . ' and would like a quotation.') }}"
                   target="_blank" rel="noopener noreferrer"
                   class="flex-shrink-0 bg-green-500 text-white px-5 py-3 rounded-xl font-semibold hover:bg-green-600 transition-colors text-sm">
                    Get a Quote
                </a>
            </div>
        @endif
    </div>
</section>

@endsection

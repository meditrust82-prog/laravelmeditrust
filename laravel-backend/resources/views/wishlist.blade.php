@extends('layouts.app')

@section('title', 'My Wishlist | Meditrust Nepal')

@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="/products" class="inline-flex items-center text-blue-200 hover:text-white mb-4 text-sm font-medium transition-colors">
            ← Back to Products
        </a>
        <h1 class="text-3xl font-bold text-white flex items-center gap-3">
            ❤️ My Wishlist
            <span id="wishlist-count" class="text-lg font-normal text-blue-200 hidden"></span>
        </h1>
    </div>
</section>

{{-- Content --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 min-h-[60vh]">

    {{-- Empty State --}}
    <div id="wishlist-empty" class="text-center py-24" style="display:none;">
        <div class="text-8xl mb-6">🤍</div>
        <h2 class="text-2xl font-bold text-gray-700 dark:text-gray-200 mb-3">Your wishlist is empty</h2>
        <p class="text-gray-400 dark:text-gray-500 mb-8">Save products you're interested in and come back to them later.</p>
        <a href="/products" class="inline-flex items-center gap-2 bg-primary-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors">
            Browse Products
        </a>
    </div>

    {{-- Wishlist Grid + WhatsApp CTA (rendered by app.js via #wishlist-list) --}}
    <div id="wishlist-list"></div>

</section>

@endsection

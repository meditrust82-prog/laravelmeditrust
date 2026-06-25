@extends('layouts.app')

@section('title', 'Equipment for ' . ucwords(str_replace('-', ' ', request()->segment(1))) . ' | Meditrust Nepal')

@section('content')
    <section class="py-6 sm:py-12 bg-gray-50 dark:bg-dark-surface min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <nav class="flex text-sm text-gray-500 mb-4">
                    <a href="/" class="hover:text-primary-600">Home</a>
                    <span class="mx-2">/</span>
                    <a href="/products" class="hover:text-primary-600">Products</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 font-medium line-clamp-1 dark:text-gray-300">Hospital Type</span>
                </nav>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ ucwords(str_replace('-', ' ', request()->segment(1))) }}</h1>
                <p class="text-gray-500 dark:text-gray-400">Curated medical equipment for this facility type.</p>
            </div>

            @php $products = $productsData['products'] ?? []; @endphp
            @include('partials.product-grid', ['products' => $products])
        </div>
    </section>
@endsection

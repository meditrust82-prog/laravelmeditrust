@extends('layouts.app')

@section('title', 'Products | Meditrust Nepal')

@section('content')
@php
    $products = $productsData['products'] ?? [];
    $pagination = [
        'total' => $productsData['total'] ?? 0,
        'page' => $productsData['page'] ?? 1,
        'pages' => $productsData['pages'] ?? 1,
    ];
    $search = $query['search'] ?? '';
    $category = $query['category'] ?? '';
    $sort = $query['sort'] ?? '';
@endphp

<section class="py-6 sm:py-12 bg-gray-50 dark:bg-dark-surface min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header & Breadcrumb -->
        <div class="mb-6">
            <nav class="flex text-sm text-gray-500 mb-4">
                <a href="/" class="hover:text-primary-600">Home</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium">Products</span>
                @if($category)
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 font-medium">{{ $category }}</span>
                @endif
            </nav>
        </div>

        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
            <!-- Sidebar (Categories) -->
            <div class="hidden lg:block w-64 flex-shrink-0">
                <div class="sticky top-24 space-y-4">
                    <div class="rounded-xl shadow-sm border p-5 bg-white dark:bg-dark-card dark:border-dark-border">
                        <h3 class="font-bold text-sm text-gray-900 dark:text-white mb-3">Filter By Category</h3>
                        <ul class="space-y-1">
                            <li>
                                <a href="/products" class="block px-3 py-2.5 rounded-[10px] text-sm font-medium {{ $category === '' ? 'bg-brand-cyan/10 text-brand-cyan' : 'text-gray-600 hover:bg-gray-50' }}">All Categories</a>
                            </li>
                            @php
                                $categoriesList = $productsData['categories'] ?? [];
                                if (empty($categoriesList)) {
                                    $categoriesList = ['Surgical Instruments', 'ICU Equipment', 'Diagnostic Equipment', 'Hospital Furniture', 'Laboratory Equipment', 'Sterilization Equipment', 'Imaging Equipment'];
                                }
                            @endphp
                            @foreach($categoriesList as $cat)
                            <li>
                                <a href="/products?category={{ urlencode($cat) }}" class="block px-3 py-2.5 rounded-[10px] text-sm font-medium {{ $category === $cat ? 'bg-brand-cyan/10 text-brand-cyan' : 'text-gray-600 hover:bg-gray-50' }}">
                                    {{ $cat }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- CTA -->
                    <div class="rounded-xl p-6 mt-6 text-white" style="background: linear-gradient(135deg, #7CC62D 0%, #06B6D4 100%)">
                        <h3 class="font-semibold mb-2">Need Help Choosing?</h3>
                        <p class="text-white/80 text-sm mb-4">Our medical equipment specialists are here to guide you.</p>
                        <a href="/contact" class="bg-white text-brand-cyan px-4 py-2 rounded-[10px] text-sm font-semibold inline-block hover:bg-gray-50 transition-colors">
                            Contact Us
                        </a>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="flex-1 min-w-0">
                <!-- Toolbar: Search + Sort -->
                <div class="flex flex-col sm:flex-row gap-3 mb-6">
                    <form method="GET" action="/products" class="flex gap-2 flex-1">
                        @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search Products" class="w-full pl-4 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 dark:bg-dark-card dark:border-dark-border" />
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors">
                            Search
                        </button>
                    </form>

                    <div class="relative">
                        <form method="GET" action="/products" id="sortForm">
                            @if($search) <input type="hidden" name="search" value="{{ $search }}"> @endif
                            @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
                            <select name="sort" onchange="document.getElementById('sortForm').submit()" class="pl-4 pr-8 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300 bg-white dark:bg-dark-card dark:border-dark-border appearance-none cursor-pointer">
                                <option value="" {{ $sort === '' ? 'selected' : '' }}>Default</option>
                                <option value="name" {{ $sort === 'name' ? 'selected' : '' }}>Name A–Z</option>
                                <option value="-name" {{ $sort === '-name' ? 'selected' : '' }}>Name Z–A</option>
                                <option value="price" {{ $sort === 'price' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="-price" {{ $sort === '-price' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="-created_at" {{ $sort === '-created_at' ? 'selected' : '' }}>Newest First</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <p class="text-gray-600 text-sm">
                        {{ $pagination['total'] }} Products
                        @if($category)
                            <span class="text-gray-400 ml-1">In Category <span class="text-gray-600 font-medium">{{ $category }}</span></span>
                        @endif
                    </p>
                </div>

                <!-- Product Cards -->
                @include('partials.product-grid', ['products' => $products])


                <!-- Pagination -->
                @if($pagination['pages'] > 1)
                    <div class="mt-8 flex justify-center gap-2 flex-wrap">
                        @for($i = 1; $i <= $pagination['pages']; $i++)
                            @php
                                $query['page'] = $i;
                            @endphp
                            <a href="/products?{{ http_build_query($query) }}" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $pagination['page'] == $i ? 'bg-primary-600 text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' }}">
                                {{ $i }}
                            </a>
                        @endfor
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection

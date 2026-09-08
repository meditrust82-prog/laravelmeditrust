@extends('layouts.app')

@php
    $product = $productData['product'] ?? $productData;
    $relatedProducts = $productData['relatedProducts'] ?? [];
    $slug = $product['slug'] ?? $product['_id'] ?? $product['id'] ?? null;
    $name = $product['name'] ?? 'Unknown Product';
    $price = isset($product['price']) ? (float)$product['price'] : 0;
    $originalPrice = isset($product['originalPrice']) ? (float)$product['originalPrice'] : 0;
    $category = $product['category'] ?? 'Medical Equipment';
    $images = $product['allImages'] ?? $product['images'] ?? [];
    if (empty($images) && !empty($product['image'])) {
        $images = [$product['image']];
    }
    $mainImage = $images[0]['url'] ?? $images[0] ?? $product['image'] ?? null;
    $description = $product['description'] ?? '<p>No description available.</p>';
    $metaTitle = $product['metaTitle'] ?? $product['meta_title'] ?? ($name . ' — Buy in Nepal | Meditrust Nepal');
    $rawDesc = $product['metaDescription'] ?? $product['meta_description'] ?? strip_tags($description);
    $metaDesc = mb_substr(trim(preg_replace('/\s+/', ' ', html_entity_decode((string)$rawDesc, ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 0, 160) ?: "Buy {$name} from Meditrust Nepal. CE & ISO certified, competitive pricing, 24/7 support.";
    $canonicalUrl = 'https://meditrustnepal.com/products/' . ($slug ?: '');
    $socialImg = is_array($mainImage) ? ($mainImage['url'] ?? '') : $mainImage;
    if ($socialImg) {
        if (str_contains($socialImg, 'res.cloudinary.com')) {
            $socialImg = preg_replace('/\/upload\/(?:[a-zA-Z0-9_:,]+\/)?/', '/upload/f_jpg,q_auto,w_1200,h_630,c_pad,b_white/', $socialImg, 1);
        } elseif (!str_starts_with($socialImg, 'http')) {
            $socialImg = 'https://meditrustnepal.com/' . ltrim($socialImg, '/');
        }
    } else {
        $socialImg = 'https://meditrustnepal.com/logo.png';
    }
@endphp

@section('title', ($name ?: 'Product') . ' | Meditrust Nepal')

@section('meta')
@if ($product)
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    <meta property="og:type" content="product" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDesc }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:image" content="{{ $socialImg }}" />
    <meta property="og:image:secure_url" content="{{ $socialImg }}" />
    <meta property="og:image:type" content="image/jpeg" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDesc }}" />
    <meta name="twitter:image" content="{{ $socialImg }}" />
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Product",
      "@id": "{{ $canonicalUrl }}#product",
      "name": {{ json_encode($name) }},
      "description": {{ json_encode($metaDesc) }},
      "image": [{{ json_encode($socialImg) }}],
      "offers": {
        "@type": "Offer",
        "url": "{{ $canonicalUrl }}",
        "priceCurrency": "NPR",
        "price": {{ $price ?: 0 }},
        "availability": "https://schema.org/InStock"
      }
    }
    </script>
@endif
@endsection

@section('content')

<section class="py-6 sm:py-12 bg-gray-50 dark:bg-dark-surface min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if (!$product)
            <div class="text-center py-16 px-4 bg-white border border-gray-100 rounded-2xl dark:bg-dark-card dark:border-dark-border">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Product Not Found</h2>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">The product details could not be loaded. Please try again or browse other products in our catalog.</p>
                <a href="/products" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-primary-700 transition-colors">Back to Products</a>
            </div>
        @else
            @php
                $slug = $product['slug'] ?? $product['_id'] ?? $product['id'] ?? null;
                $name = $product['name'] ?? 'Unknown Product';
                $price = isset($product['price']) ? (float)$product['price'] : 0;
                $originalPrice = isset($product['originalPrice']) ? (float)$product['originalPrice'] : 0;
                $category = $product['category'] ?? 'Medical Equipment';
                $images = $product['allImages'] ?? $product['images'] ?? [];
                if (empty($images) && !empty($product['image'])) {
                    $images = [$product['image']];
                }
                $mainImage = $images[0]['url'] ?? $images[0] ?? $product['image'] ?? null;
                $description = $product['description'] ?? '<p>No description available.</p>';
            @endphp
            
            <!-- Breadcrumb -->
            <nav class="flex text-sm text-gray-500 mb-8">
                <a href="/" class="hover:text-primary-600">Home</a>
                <span class="mx-2">/</span>
                <a href="/products" class="hover:text-primary-600">Products</a>
                <span class="mx-2">/</span>
                <a href="/products?category={{ urlencode($category) }}" class="hover:text-primary-600">{{ $category }}</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium line-clamp-1 dark:text-gray-300">{{ $name }}</span>
            </nav>

            @php
                $productJson = json_encode([
                    'id'          => $slug,
                    'slug'        => $slug,
                    'name'        => $name,
                    'category'    => $category,
                    'price'       => $price ?: null,
                    'originalPrice' => $originalPrice ?: null,
                    'image'       => is_array($mainImage) ? ($mainImage['url'] ?? null) : $mainImage,
                    'description' => \Illuminate\Support\Str::limit(strip_tags($description), 200),
                ]);
            @endphp
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-dark-card dark:border-dark-border" data-product-detail='{{ $productJson }}'>
                <div class="flex flex-col lg:flex-row">
                    <!-- Image Gallery -->
                    <div class="w-full lg:w-1/2 p-6 lg:p-10 border-b lg:border-b-0 lg:border-r border-gray-100 dark:border-dark-border bg-gray-50 dark:bg-gray-800/50 flex flex-col">
                        <div class="relative flex-1 min-h-[300px] flex items-center justify-center bg-white rounded-xl overflow-hidden border border-gray-100 p-4 dark:bg-dark-surface dark:border-dark-border">
                            @if ($mainImage)
                                <img src="{{ is_array($mainImage) ? ($mainImage['url'] ?? '') : $mainImage }}" alt="{{ $name }}" class="max-w-full max-h-[400px] object-contain" />
                            @else
                                <span class="text-gray-400 font-medium">No image available</span>
                            @endif
                            @if($originalPrice > $price && $originalPrice > 0)
                                <span class="absolute top-4 right-4 text-sm font-bold text-white bg-red-500 px-3 py-1 rounded-full shadow-sm">
                                    {{ round((1 - $price / $originalPrice) * 100) }}% OFF
                                </span>
                            @endif
                        </div>
                        <!-- Thumbnails (placeholder logic for now) -->
                        @if(count($images) > 1)
                            <div class="flex gap-3 mt-4 overflow-x-auto pb-2 no-scrollbar">
                                @foreach($images as $img)
                                    @php $imgUrl = is_array($img) ? ($img['url'] ?? '') : $img; @endphp
                                    <div class="w-20 h-20 rounded-lg border-2 border-transparent hover:border-primary-500 cursor-pointer overflow-hidden bg-white flex-shrink-0">
                                        <img src="{{ $imgUrl }}" class="w-full h-full object-cover" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="w-full lg:w-1/2 p-6 lg:p-10 flex flex-col">
                        <div class="mb-2">
                            <a href="/products?category={{ urlencode($category) }}" class="text-primary-600 font-semibold text-sm hover:underline">{{ $category }}</a>
                        </div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4 leading-tight dark:text-white">{{ $name }}</h1>
                        
                        @if ($price > 0)
                            <div class="flex items-end gap-3 mb-6">
                                <span class="text-3xl font-extrabold text-primary-700">NRS {{ number_format($price) }}</span>
                                @if ($originalPrice > $price)
                                    <span class="text-lg text-gray-400 line-through mb-1">NRS {{ number_format($originalPrice) }}</span>
                                @endif
                            </div>
                        @else
                            <div class="mb-6">
                                <span class="text-xl font-bold text-gray-700 dark:text-gray-300">Price on Request</span>
                            </div>
                        @endif

                        <div class="prose prose-sm sm:prose max-w-none text-gray-600 mb-8 dark:text-gray-300">
                            {!! $description !!}
                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-100 dark:border-dark-border space-y-4">
                            <!-- Qty & Add to Cart -->
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden h-12 bg-white dark:bg-dark-surface dark:border-dark-border">
                                    <button type="button" class="px-3 h-full text-gray-600 hover:bg-gray-100 transition-colors font-bold" data-action="decrease-qty">−</button>
                                    <span id="product-qty" class="w-12 text-center text-gray-900 font-semibold dark:text-white">1</span>
                                    <button type="button" class="px-3 h-full text-gray-600 hover:bg-gray-100 transition-colors font-bold" data-action="increase-qty">+</button>
                                </div>
                                <button type="button" data-action="add-cart" class="flex-1 min-w-[140px] h-12 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md">
                                    🛒 Add to Quote
                                </button>
                                <button type="button" id="product-share-btn" class="h-12 px-4 border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-xl flex items-center justify-center gap-2 transition-all dark:border-dark-border dark:text-gray-300 dark:hover:bg-gray-800 shadow-sm" title="Share this product">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                    <span>Share</span>
                                </button>
                                <button type="button" data-action="toggle-wishlist" class="h-12 px-4 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl flex items-center justify-center transition-all dark:border-dark-border dark:text-gray-300 dark:hover:bg-gray-800" title="Add to Wishlist">
                                    Wishlist
                                </button>
                                <button type="button" data-action="toggle-compare" class="h-12 px-4 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl flex items-center justify-center transition-all dark:border-dark-border dark:text-gray-300 dark:hover:bg-gray-800" title="Compare">
                                    Compare
                                </button>
                            </div>
                            <!-- WhatsApp -->
                            <a href="https://wa.me/9779818100515?text={{ urlencode('I am interested in ' . $name . '. Please send a quotation.') }}" target="_blank" rel="noopener noreferrer" class="w-full h-12 bg-[#25D366] hover:bg-[#1da851] text-white font-semibold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm">
                                Chat on WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Section (If present) -->
            @if(count($relatedProducts) > 0)
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Products</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        @foreach($relatedProducts as $rel)
                            <!-- Mini Product Card -->
                            <a href="/products/{{ $rel['slug'] ?? $rel['id'] ?? '' }}" class="bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100 p-4 transition-all block dark:bg-dark-card dark:border-dark-border">
                                <div class="aspect-square bg-gray-50 rounded-lg mb-3 overflow-hidden border border-gray-100 flex items-center justify-center dark:bg-gray-800">
                                    @if(!empty($rel['images'][0]['url']) || !empty($rel['image']))
                                        <img src="{{ $rel['images'][0]['url'] ?? $rel['image'] }}" alt="{{ $rel['name'] }}" class="w-full h-full object-cover" />
                                    @else
                                        <span class="text-gray-400 text-xs">No image</span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 dark:text-white">{{ $rel['name'] ?? 'Product' }}</h3>
                                @if(isset($rel['price']) && $rel['price'] > 0)
                                    <p class="text-primary-700 font-bold mt-1 text-sm">NRS {{ number_format($rel['price']) }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Recently Viewed Section (Rendered by JS) -->
            <div id="recently-viewed-list"></div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var shareBtn = document.getElementById('product-share-btn');
                if (!shareBtn) return;
                shareBtn.addEventListener('click', async function() {
                    var url = window.location.href;
                    var title = @json($name);
                    var text = @json($name . ' — ' . $metaDesc);
                    if (navigator.share) {
                        try {
                            await navigator.share({ title: title, text: text, url: url });
                            return;
                        } catch(e) {}
                    }
                    try {
                        await navigator.clipboard.writeText(url);
                        alert('Product link copied to clipboard!');
                    } catch(e) {
                        prompt('Copy product link:', url);
                    }
                });
            });
            </script>
        @endif
    </div>
</section>

@endsection

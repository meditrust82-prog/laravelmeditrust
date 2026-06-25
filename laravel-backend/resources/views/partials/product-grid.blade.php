@props(['products'])

<div class="grid grid-cols-2 md:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-6">
    @forelse ($products as $product)
        @php
            $slug        = $product['slug'] ?? $product['_id'] ?? $product['id'] ?? null;
            $id          = $product['id'] ?? $product['_id'] ?? $slug;
            $name        = $product['name'] ?? 'Unknown Product';
            $price       = isset($product['price']) ? (float)$product['price'] : 0;
            $origPrice   = isset($product['originalPrice']) ? (float)$product['originalPrice'] : 0;
            $catName     = $product['category'] ?? '';
            $rawImage    = $product['image'] ?? ($product['images'][0]['url'] ?? ($product['images'][0] ?? null));
            $image       = is_array($rawImage) ? ($rawImage['url'] ?? null) : $rawImage;
            $description = strip_tags($product['shortDescription'] ?? $product['description'] ?? '');
            $productUrl  = url('/products/' . $slug);

            // JSON payload consumed by app.js hydrateProductCards()
            $productJson = json_encode([
                'id'          => $id,
                'slug'        => $slug,
                'name'        => $name,
                'category'    => $catName,
                'price'       => $price ?: null,
                'originalPrice' => $origPrice ?: null,
                'image'       => $image,
                'description' => \Illuminate\Support\Str::limit($description, 200),
                'url'         => $productUrl,
            ]);
        @endphp

        {{-- .product-card + data-product enables app.js hydrateProductCards() --}}
        <div class="product-card bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md group border border-gray-100 flex flex-col dark:bg-dark-card dark:border-dark-border transition-all"
             data-product='{{ $productJson }}'>

            {{-- Image --}}
            <a href="{{ $productUrl }}" class="block relative h-32 sm:h-48 bg-gray-100 overflow-hidden">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $name }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                         loading="lazy" />
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                        <span class="text-primary-300 font-bold text-sm">No Image</span>
                    </div>
                @endif

                @if($catName)
                    <span class="absolute top-2 left-2 bg-primary-600 text-white text-[10px] sm:text-xs px-2 py-0.5 rounded-full leading-tight pointer-events-none">
                        {{ $catName }}
                    </span>
                @endif

                @if($origPrice > $price && $origPrice > 0)
                    <span class="absolute top-2 right-2 z-10 text-xs font-bold text-white bg-red-500 px-2 py-0.5 rounded-full shadow pointer-events-none">
                        {{ round((1 - $price / $origPrice) * 100) }}% OFF
                    </span>
                @endif

                <span class="absolute bottom-1.5 right-1.5 bg-black/40 backdrop-blur-sm text-white text-[8px] sm:text-[10px] font-semibold tracking-wide px-1.5 py-0.5 rounded pointer-events-none select-none">
                    Meditrust Nepal
                </span>
            </a>

            {{-- Body --}}
            <div class="p-3 sm:p-5 flex flex-col flex-1">
                <a href="{{ $productUrl }}">
                    <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors text-sm sm:text-base line-clamp-2 leading-snug">
                        {{ $name }}
                    </h3>
                </a>

                @if($description)
                    <p class="text-gray-500 text-xs sm:text-sm line-clamp-1 mt-1 sm:mt-2">
                        {{ \Illuminate\Support\Str::limit($description, 60) }}
                    </p>
                @endif

                @if($price > 0)
                    <div class="mt-1 sm:mt-2 flex items-center gap-2 flex-wrap">
                        <p class="text-sm sm:text-lg font-bold text-primary-700">NRS {{ number_format($price) }}</p>
                        @if($origPrice > $price)
                            <span class="text-xs sm:text-sm text-gray-400 line-through">NRS {{ number_format($origPrice) }}</span>
                        @endif
                    </div>
                @endif

                {{-- Action buttons — data-action hooked by app.js hydrateProductCards() --}}
                <div class="mt-auto pt-3 sm:pt-4 flex gap-1.5 sm:gap-2">
                    <a href="{{ $productUrl }}"
                       class="flex-1 text-center bg-primary-600 text-white py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium hover:bg-primary-700 transition-colors">
                        View Details
                    </a>

                    <button type="button"
                            data-action="toggle-compare"
                            class="flex justify-center items-center px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-800 dark:text-gray-300"
                            title="Add To Compare">⇄</button>

                    <button type="button"
                            data-action="add-cart"
                            class="flex items-center justify-center px-2 sm:px-3 py-1.5 sm:py-2 bg-green-600 text-white rounded-lg text-xs sm:text-sm font-medium hover:bg-green-700 transition-colors"
                            title="Add To Quote Cart">🛒</button>

                    <button type="button"
                            data-action="toggle-wishlist"
                            class="flex items-center justify-center px-2 sm:px-3 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm transition-colors bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-400 dark:bg-gray-800 dark:text-gray-300"
                            title="Save to Wishlist">♡</button>
                </div>
            </div>
        </div>

    @empty
        <div class="col-span-full bg-white rounded-xl p-8 text-center border border-gray-100 dark:bg-dark-card dark:border-dark-border">
            <p class="text-gray-500">No products found.</p>
        </div>
    @endforelse
</div>

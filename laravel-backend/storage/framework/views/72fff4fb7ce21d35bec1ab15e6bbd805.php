<?php $__env->startSection('title', ($productData['product']['name'] ?? 'Product Not Found') . ' | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $product = $productData['product'] ?? null;
    $relatedProducts = $productData['relatedProducts'] ?? [];
?>

<section class="py-6 sm:py-12 bg-gray-50 dark:bg-dark-surface min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if(!$product): ?>
            <div class="text-center py-16 px-4 bg-white border border-gray-100 rounded-2xl dark:bg-dark-card dark:border-dark-border">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Product Not Found</h2>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">The product details could not be loaded. Please try again or browse other products in our catalog.</p>
                <a href="/products" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-xl font-medium hover:bg-primary-700 transition-colors">Back to Products</a>
            </div>
        <?php else: ?>
            <?php
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
            ?>
            
            <!-- Breadcrumb -->
            <nav class="flex text-sm text-gray-500 mb-8">
                <a href="/" class="hover:text-primary-600">Home</a>
                <span class="mx-2">/</span>
                <a href="/products" class="hover:text-primary-600">Products</a>
                <span class="mx-2">/</span>
                <a href="/products?category=<?php echo e(urlencode($category)); ?>" class="hover:text-primary-600"><?php echo e($category); ?></a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 font-medium line-clamp-1 dark:text-gray-300"><?php echo e($name); ?></span>
            </nav>

            <?php
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
            ?>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-dark-card dark:border-dark-border" data-product-detail='<?php echo e($productJson); ?>'>
                <div class="flex flex-col lg:flex-row">
                    <!-- Image Gallery -->
                    <div class="w-full lg:w-1/2 p-6 lg:p-10 border-b lg:border-b-0 lg:border-r border-gray-100 dark:border-dark-border bg-gray-50 dark:bg-gray-800/50 flex flex-col">
                        <div class="relative flex-1 min-h-[300px] flex items-center justify-center bg-white rounded-xl overflow-hidden border border-gray-100 p-4 dark:bg-dark-surface dark:border-dark-border">
                            <?php if($mainImage): ?>
                                <img src="<?php echo e(is_array($mainImage) ? ($mainImage['url'] ?? '') : $mainImage); ?>" alt="<?php echo e($name); ?>" class="max-w-full max-h-[400px] object-contain" />
                            <?php else: ?>
                                <span class="text-gray-400 font-medium">No image available</span>
                            <?php endif; ?>
                            <?php if($originalPrice > $price && $originalPrice > 0): ?>
                                <span class="absolute top-4 right-4 text-sm font-bold text-white bg-red-500 px-3 py-1 rounded-full shadow-sm">
                                    <?php echo e(round((1 - $price / $originalPrice) * 100)); ?>% OFF
                                </span>
                            <?php endif; ?>
                        </div>
                        <!-- Thumbnails (placeholder logic for now) -->
                        <?php if(count($images) > 1): ?>
                            <div class="flex gap-3 mt-4 overflow-x-auto pb-2 no-scrollbar">
                                <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php $imgUrl = is_array($img) ? ($img['url'] ?? '') : $img; ?>
                                    <div class="w-20 h-20 rounded-lg border-2 border-transparent hover:border-primary-500 cursor-pointer overflow-hidden bg-white flex-shrink-0">
                                        <img src="<?php echo e($imgUrl); ?>" class="w-full h-full object-cover" />
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product Details -->
                    <div class="w-full lg:w-1/2 p-6 lg:p-10 flex flex-col">
                        <div class="mb-2">
                            <a href="/products?category=<?php echo e(urlencode($category)); ?>" class="text-primary-600 font-semibold text-sm hover:underline"><?php echo e($category); ?></a>
                        </div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4 leading-tight dark:text-white"><?php echo e($name); ?></h1>
                        
                        <?php if($price > 0): ?>
                            <div class="flex items-end gap-3 mb-6">
                                <span class="text-3xl font-extrabold text-primary-700">NRS <?php echo e(number_format($price)); ?></span>
                                <?php if($originalPrice > $price): ?>
                                    <span class="text-lg text-gray-400 line-through mb-1">NRS <?php echo e(number_format($originalPrice)); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="mb-6">
                                <span class="text-xl font-bold text-gray-700 dark:text-gray-300">Price on Request</span>
                            </div>
                        <?php endif; ?>

                        <div class="prose prose-sm sm:prose max-w-none text-gray-600 mb-8 dark:text-gray-300">
                            <?php echo $description; ?>

                        </div>

                        <div class="mt-auto pt-6 border-t border-gray-100 dark:border-dark-border space-y-4">
                            <!-- Qty & Add to Cart -->
                            <div class="flex flex-wrap items-center gap-4">
                                <div class="flex items-center border border-gray-300 rounded-xl overflow-hidden h-12 bg-white dark:bg-dark-surface dark:border-dark-border">
                                    <button type="button" class="px-3 h-full text-gray-600 hover:bg-gray-100 transition-colors font-bold" data-action="decrease-qty">−</button>
                                    <span id="product-qty" class="w-12 text-center text-gray-900 font-semibold dark:text-white">1</span>
                                    <button type="button" class="px-3 h-full text-gray-600 hover:bg-gray-100 transition-colors font-bold" data-action="increase-qty">+</button>
                                </div>
                                <button type="button" data-action="add-cart" class="flex-1 h-12 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md">
                                    🛒 Add to Quote
                                </button>
                                <button type="button" data-action="toggle-wishlist" class="h-12 px-4 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl flex items-center justify-center transition-all dark:border-dark-border dark:text-gray-300 dark:hover:bg-gray-800" title="Add to Wishlist">
                                    Wishlist
                                </button>
                                <button type="button" data-action="toggle-compare" class="h-12 px-4 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl flex items-center justify-center transition-all dark:border-dark-border dark:text-gray-300 dark:hover:bg-gray-800" title="Compare">
                                    Compare
                                </button>
                            </div>
                            <!-- WhatsApp -->
                            <a href="https://wa.me/9779818100515?text=<?php echo e(urlencode('I am interested in ' . $name . '. Please send a quotation.')); ?>" target="_blank" rel="noopener noreferrer" class="w-full h-12 bg-[#25D366] hover:bg-[#1da851] text-white font-semibold rounded-xl flex items-center justify-center gap-2 transition-all shadow-sm">
                                Chat on WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products Section (If present) -->
            <?php if(count($relatedProducts) > 0): ?>
                <div class="mt-16">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Related Products</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
                        <?php $__currentLoopData = $relatedProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <!-- Mini Product Card -->
                            <a href="/products/<?php echo e($rel['slug'] ?? $rel['id'] ?? ''); ?>" class="bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100 p-4 transition-all block dark:bg-dark-card dark:border-dark-border">
                                <div class="aspect-square bg-gray-50 rounded-lg mb-3 overflow-hidden border border-gray-100 flex items-center justify-center dark:bg-gray-800">
                                    <?php if(!empty($rel['images'][0]['url']) || !empty($rel['image'])): ?>
                                        <img src="<?php echo e($rel['images'][0]['url'] ?? $rel['image']); ?>" alt="<?php echo e($rel['name']); ?>" class="w-full h-full object-cover" />
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">No image</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 dark:text-white"><?php echo e($rel['name'] ?? 'Product'); ?></h3>
                                <?php if(isset($rel['price']) && $rel['price'] > 0): ?>
                                    <p class="text-primary-700 font-bold mt-1 text-sm">NRS <?php echo e(number_format($rel['price'])); ?></p>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Recently Viewed Section (Rendered by JS) -->
            <div id="recently-viewed-list"></div>

        <?php endif; ?>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/product-detail.blade.php ENDPATH**/ ?>
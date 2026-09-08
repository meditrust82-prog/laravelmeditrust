<?php $__env->startSection('title', 'Brand: ' . ucwords(str_replace('-', ' ', $brandSlug ?? 'All')) . ' | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>
    <section class="py-6 sm:py-12 bg-gray-50 dark:bg-dark-surface min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <nav class="flex text-sm text-gray-500 mb-4">
                    <a href="/" class="hover:text-primary-600">Home</a>
                    <span class="mx-2">/</span>
                    <a href="/products" class="hover:text-primary-600">Products</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 font-medium line-clamp-1 dark:text-gray-300">Brand</span>
                </nav>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-2"><?php echo e(ucwords(str_replace('-', ' ', $brandSlug ?? 'All'))); ?></h1>
                <p class="text-gray-500 dark:text-gray-400">Products matched to the selected brand in our medical equipment catalog.</p>
            </div>

            <?php $products = $productsData['products'] ?? []; ?>
            <?php echo $__env->make('partials.product-grid', ['products' => $products], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/brand.blade.php ENDPATH**/ ?>
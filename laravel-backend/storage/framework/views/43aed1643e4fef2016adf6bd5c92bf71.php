<?php $__env->startSection('title', 'Compare Medical Equipment | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>


<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="/products" class="inline-flex items-center text-blue-200 hover:text-white mb-4 text-sm font-medium transition-colors">
            ← Back to Products
        </a>
        <h1 class="text-3xl font-bold text-white">Compare Products</h1>
        <p class="text-blue-200 mt-1 text-sm">Side-by-side comparison — up to 3 products</p>
    </div>
</section>


<section class="py-12 bg-white dark:bg-dark-surface min-h-[50vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div id="compare-empty" class="text-center py-20 bg-gray-50 dark:bg-dark-card rounded-2xl border border-gray-100 dark:border-dark-border" style="display:none;">
            <div class="text-6xl mb-4">⇄</div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No products to compare</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Add products from the catalog to compare their specifications side by side.</p>
            <a href="/products" class="inline-block bg-primary-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors">
                Browse Products
            </a>
        </div>

        
        <div id="compare-list"></div>

    </div>
</section>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/compare.blade.php ENDPATH**/ ?>
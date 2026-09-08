<?php $__env->startSection('title', 'About Meditrust Nepal | Medical Equipment Supplier Nepal'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $cms = $about['data'] ?? $about ?? null;
    $heroLabel  = $cms['heroLabel']  ?? 'About Meditrust Nepal';
    $heroTitle  = $cms['heroTitle']  ?? 'Trusted medical equipment partner for hospitals and clinics in Nepal';
    $heroDesc   = $cms['heroDesc']   ?? 'Founded in 2009, Meditrust Nepal supplies CE & ISO certified surgical instruments, ICU equipment, diagnostic devices and more, backed by installation and service support.';
    $storyPara1 = $cms['storyPara1'] ?? 'Founded in 2009, Meditrust Nepal started with a simple mission: to make high-quality medical equipment accessible to healthcare institutions across Nepal.';
    $storyPara2 = $cms['storyPara2'] ?? 'We partner with internationally certified manufacturers and provide full installation, training and after-sales support to hospitals across Nepal.';
    $mission    = $cms['missionText'] ?? 'To provide reliable, certified medical equipment and support to hospitals, clinics and health institutions across Nepal.';
    $vision     = $cms['visionText']  ?? 'To become Nepal's most trusted medical equipment partner, improving patient care through quality technology and service.';
?>


<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-block bg-white/10 text-blue-200 text-sm font-medium px-4 py-1.5 rounded-full mb-5"><?php echo e($heroLabel); ?></span>
            <h1 class="text-4xl sm:text-5xl font-bold text-white leading-tight mb-5"><?php echo e($heroTitle); ?></h1>
            <p class="text-blue-100 text-lg leading-relaxed max-w-2xl"><?php echo e($heroDesc); ?></p>
        </div>
    </div>
</section>


<section class="bg-white dark:bg-dark-card border-b border-gray-100 dark:border-dark-border">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100 dark:divide-dark-border">
            <?php $__currentLoopData = [['15+','Years in operation'],['500+','Products supplied'],['200+','Hospitals served'],['24/7','After-sales support']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$num,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="py-8 px-6 text-center">
                    <p class="text-3xl font-extrabold text-primary-700"><?php echo e($num); ?></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e($label); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-16 bg-gray-50 dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Our Story</h2>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-5"><?php echo e($storyPara1); ?></p>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed"><?php echo e($storyPara2); ?></p>
            </div>
            <div class="space-y-6">
                <div class="bg-white dark:bg-dark-card rounded-2xl p-7 shadow-sm border border-gray-100 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-xl">🎯</div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Our Mission</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed"><?php echo e($mission); ?></p>
                </div>
                <div class="bg-white dark:bg-dark-card rounded-2xl p-7 shadow-sm border border-gray-100 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-xl">🔭</div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Our Vision</h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed"><?php echo e($vision); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="py-16 bg-white dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Why Choose Meditrust Nepal?</h2>
            <p class="mt-4 text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">We are committed to quality, service and trusted healthcare partnerships across Nepal.</p>
        </div>
        <?php
            $features = [
                ['🏅','CE & ISO Certified',     'We supply internationally certified medical equipment with safety and quality guaranteed.'],
                ['🔧','Expert Support',          'Our technical team provides installation, commissioning and after-sales maintenance.'],
                ['🚚','Nationwide Delivery',     'We serve hospitals and clinics throughout Kathmandu, Pokhara, Biratnagar and beyond.'],
                ['💡','Customer Focus',          'We tailor medical equipment solutions to fit each facility\'s clinical and budget needs.'],
                ['📋','Regulatory Compliance',   'All products come with DDA-compliant documentation, CE marking and ISO certifications.'],
                ['📞','Rapid Response',          'Same-day quotations and technical advice via WhatsApp, phone or email.'],
            ];
        ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $title, $desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-50 dark:bg-dark-card rounded-2xl p-6 border border-gray-100 dark:border-dark-border hover:shadow-md transition-shadow">
                    <div class="text-3xl mb-3"><?php echo e($icon); ?></div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2"><?php echo e($title); ?></h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed"><?php echo e($desc); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-16 bg-primary-700">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to equip your facility?</h2>
        <p class="text-blue-100 mb-8">Contact our team for a same-day quotation tailored to your hospital's needs.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/contact" class="bg-white text-primary-700 font-bold px-8 py-3 rounded-xl hover:bg-blue-50 transition-colors">Get in Touch</a>
            <a href="https://wa.me/9779818100515?text=<?php echo e(urlencode('Hello Meditrust Nepal, I would like to inquire about medical equipment.')); ?>"
               target="_blank" rel="noopener noreferrer"
               class="bg-green-500 text-white font-bold px-8 py-3 rounded-xl hover:bg-green-600 transition-colors">
                WhatsApp Us
            </a>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/about.blade.php ENDPATH**/ ?>
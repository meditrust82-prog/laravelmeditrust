<?php $__env->startSection('title', 'Our Services | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $cms = $servicesSettings['data'] ?? $servicesSettings ?? null;
    $services = [
        ['icon'=>'🏥','badge'=>'Clinical Equipment','title'=>'Turnkey Supply & Installation',   'desc'=>'Complete hospital equipment procurement for operating rooms, ICUs, laboratories and emergency departments — delivered, installed and commissioned.'],
        ['icon'=>'💬','badge'=>'Consultation',      'title'=>'Biomedical Consultation',        'desc'=>'Expert engineering guidance for device selection, procurement planning, budget optimization and DDA/CE regulatory compliance.'],
        ['icon'=>'🔧','badge'=>'After Sales',       'title'=>'Service & Maintenance',          'desc'=>'Annual Maintenance Contracts (AMC), Preventive Maintenance (PMC), 24/7 breakdown support and genuine spare parts.'],
        ['icon'=>'🚚','badge'=>'Logistics',         'title'=>'Fast Delivery Across Nepal',     'desc'=>'Reliable shipment and installation in Kathmandu, Pokhara, Biratnagar, Bharatpur, and across Eastern, Western and Far-Western regions.'],
        ['icon'=>'🎓','badge'=>'Training',          'title'=>'Biomedical Staff Training',      'desc'=>'On-site training for clinical and technical staff on the safe operation and daily maintenance of procured equipment.'],
        ['icon'=>'📋','badge'=>'Compliance',        'title'=>'Regulatory Documentation',       'desc'=>'We provide complete CE marking, ISO 13485 and DDA-required import documentation for every piece of equipment we supply.'],
    ];
?>


<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-block bg-white/10 text-blue-200 text-sm font-medium px-4 py-1.5 rounded-full mb-5">Our Services</span>
            <h1 class="text-4xl sm:text-5xl font-bold text-white leading-tight mb-5">Medical Services &amp; Support</h1>
            <p class="text-blue-100 text-lg leading-relaxed max-w-2xl">Meditrust Nepal provides end-to-end service for hospitals, clinics and diagnostic centers — from equipment supply and installation to training and maintenance.</p>
        </div>
    </div>
</section>


<section class="py-16 bg-gray-50 dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $svc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white dark:bg-dark-card rounded-2xl p-7 shadow-sm border border-gray-100 dark:border-dark-border hover:shadow-md transition-all hover:-translate-y-0.5 group">
                    <div class="text-4xl mb-4"><?php echo e($svc['icon']); ?></div>
                    <span class="inline-block bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 text-xs font-semibold px-3 py-1 rounded-full mb-3"><?php echo e($svc['badge']); ?></span>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3 group-hover:text-primary-600 transition-colors"><?php echo e($svc['title']); ?></h2>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed"><?php echo e($svc['desc']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-16 bg-white dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">How We Work</h2>
            <p class="mt-4 text-gray-500 dark:text-gray-400 max-w-xl mx-auto">A simple, transparent process from first inquiry to fully commissioned equipment.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php $__currentLoopData = [['1','Inquiry','Contact us via WhatsApp, phone, or our online form with your requirements.'],['2','Quotation','We send a detailed quotation within 24 hours, including specifications and pricing.'],['3','Delivery','Equipment is shipped and delivered to your facility across Nepal.'],['4','Installation','Our team installs, commissions and trains your staff on all equipment.']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$step,$title,$desc]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative bg-gray-50 dark:bg-dark-card rounded-2xl p-6 border border-gray-100 dark:border-dark-border text-center">
                    <div class="w-12 h-12 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-xl font-black mx-auto mb-4"><?php echo e($step); ?></div>
                    <h3 class="font-bold text-gray-900 dark:text-white mb-2"><?php echo e($title); ?></h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed"><?php echo e($desc); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="py-16 bg-primary-700">
    <div class="max-w-3xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Need a service quote?</h2>
        <p class="text-blue-100 mb-8">Talk to our biomedical team today for equipment supply, maintenance, or training support.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/contact" class="bg-white text-primary-700 font-bold px-8 py-3 rounded-xl hover:bg-blue-50 transition-colors">Contact Us</a>
            <a href="https://wa.me/9779818100515?text=<?php echo e(urlencode('Hello, I would like to inquire about Meditrust Nepal services.')); ?>"
               target="_blank" rel="noopener noreferrer"
               class="bg-green-500 text-white font-bold px-8 py-3 rounded-xl hover:bg-green-600 transition-colors">
                WhatsApp Us
            </a>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/services.blade.php ENDPATH**/ ?>
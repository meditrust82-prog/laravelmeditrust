<?php $__env->startSection('title', 'Our Projects | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <span class="text-blue-300 font-semibold text-sm uppercase tracking-wider">Our Projects</span>
        <h1 class="text-4xl font-bold text-white mt-3 mb-4">Completed Projects</h1>
        <p class="text-blue-100 max-w-2xl">Explore our successfully completed projects across hospitals and healthcare institutions in Nepal.</p>
    </div>
</section>

<section class="py-16 bg-gray-50 dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        
        <div class="flex flex-wrap gap-3 mb-10" id="project-filters">
            <?php
                $categories = ['All', 'Hospital Setup', 'Laboratory', 'ICU Setup', 'OT Setup', 'Diagnostic Center'];
            ?>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button type="button"
                        class="project-filter px-5 py-2.5 rounded-full text-sm font-medium transition-all border <?php echo e($cat === 'All' ? 'bg-primary-600 text-white shadow-md border-primary-600' : 'bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600 border-gray-200 dark:bg-dark-card dark:text-gray-300 dark:border-dark-border'); ?>"
                        data-cat="<?php echo e($cat); ?>">
                    <?php echo e($cat); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php
            $defaultProjects = [
                ['id'=>'1','title'=>'Bir Hospital ICU Upgrade',         'category'=>'ICU Setup',         'description'=>'Complete ICU equipment supply and installation for 20-bed intensive care unit, including ventilators, patient monitors, and infusion pumps.', 'images'=>[], 'year'=>'2023'],
                ['id'=>'2','title'=>'Nepal Mediciti Laboratory Setup',  'category'=>'Laboratory',         'description'=>'Full laboratory equipment setup including hematology analyzers, chemistry analyzers, and microscopy equipment.',                              'images'=>[], 'year'=>'2022'],
                ['id'=>'3','title'=>'Grande Hospital OT Installation',  'category'=>'OT Setup',          'description'=>'Operation theatre equipment supply including OT lights, tables, anesthesia machines, and electrosurgical units.',                           'images'=>[], 'year'=>'2023'],
                ['id'=>'4','title'=>'Patan Hospital Diagnostic Center', 'category'=>'Diagnostic Center', 'description'=>'Complete diagnostic center setup with X-ray, ultrasound, and ECG machines for outpatient diagnostics.',                                      'images'=>[], 'year'=>'2022'],
                ['id'=>'5','title'=>'Teaching Hospital Equipment Supply','category'=>'Hospital Setup',    'description'=>'Comprehensive medical equipment supply for multiple departments including emergency, surgery, and radiology.',                               'images'=>[], 'year'=>'2021'],
                ['id'=>'6','title'=>'Norvic Hospital Expansion',        'category'=>'Hospital Setup',    'description'=>'Medical equipment for new wing expansion including patient monitoring systems and nursing station equipment.',                               'images'=>[], 'year'=>'2023'],
            ];
            // Prefer API data if loaded via projects controller
            $projects = $projectsData['projects'] ?? $defaultProjects;
            if (empty($projects)) $projects = $defaultProjects;
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="project-grid">
            <?php $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $pTitle = $project['title'] ?? 'Project';
                    $pCat   = $project['category'] ?? '';
                    $pDesc  = $project['description'] ?? '';
                    $pYear  = $project['year'] ?? '';
                    $pImgs  = $project['images'] ?? [];
                    $pImg   = $pImgs[0] ?? null;
                ?>
                <div class="project-card bg-white dark:bg-dark-card rounded-xl overflow-hidden shadow-sm hover:shadow-md group border border-gray-100 dark:border-dark-border cursor-pointer transition-all"
                     data-category="<?php echo e($pCat); ?>"
                     data-title="<?php echo e($pTitle); ?>"
                     data-desc="<?php echo e($pDesc); ?>"
                     data-year="<?php echo e($pYear); ?>"
                     data-img="<?php echo e($pImg ?? ''); ?>">
                    <div class="relative h-56 bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <?php if($pImg): ?>
                            <img src="<?php echo e($pImg); ?>" alt="<?php echo e($pTitle); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" />
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/40 dark:to-primary-800/40">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-primary-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-primary-500 text-sm font-medium"><?php echo e($pCat); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                            <span class="text-white font-medium text-sm">View Project</span>
                        </div>
                        <?php if($pYear): ?>
                            <span class="absolute top-3 right-3 bg-white/90 text-gray-700 text-xs px-3 py-1 rounded-full font-medium"><?php echo e($pYear); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <span class="text-primary-600 text-xs font-medium"><?php echo e($pCat); ?></span>
                        <h3 class="font-semibold text-gray-900 dark:text-white mt-1 group-hover:text-primary-600 transition-colors"><?php echo e($pTitle); ?></h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 line-clamp-2"><?php echo e($pDesc); ?></p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div id="project-empty" class="hidden text-center py-16">
            <p class="text-gray-500">No projects found in this category.</p>
        </div>
    </div>
</section>


<div id="project-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/80">
    <div class="bg-white dark:bg-dark-card rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto" id="modal-content">
        <div class="relative">
            <div id="modal-image-wrap" class="h-80 bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/40 dark:to-primary-800/40 rounded-t-2xl overflow-hidden flex items-center justify-center">
                <svg class="w-24 h-24 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <button id="modal-close" class="absolute top-4 right-4 w-10 h-10 bg-white/90 rounded-full flex items-center justify-center hover:bg-white shadow-md text-xl">✕</button>
        </div>
        <div class="p-8">
            <div class="flex items-center gap-3 mb-4">
                <span id="modal-cat" class="bg-primary-100 text-primary-700 text-sm font-medium px-3 py-1 rounded-full"></span>
                <span id="modal-year" class="text-gray-500 text-sm"></span>
            </div>
            <h2 id="modal-title" class="text-2xl font-bold text-gray-900 dark:text-white mb-4"></h2>
            <p id="modal-desc" class="text-gray-600 dark:text-gray-300 leading-relaxed"></p>
        </div>
    </div>
</div>

<script>
(function() {
    // Filter
    let activeCategory = 'All';
    document.querySelectorAll('.project-filter').forEach(btn => {
        btn.addEventListener('click', () => {
            activeCategory = btn.dataset.cat;
            document.querySelectorAll('.project-filter').forEach(b => {
                const active = b.dataset.cat === activeCategory;
                b.className = `project-filter px-5 py-2.5 rounded-full text-sm font-medium transition-all border ${active ? 'bg-primary-600 text-white shadow-md border-primary-600' : 'bg-white text-gray-600 hover:bg-primary-50 hover:text-primary-600 border-gray-200'}`;
            });
            let visible = 0;
            document.querySelectorAll('.project-card').forEach(card => {
                const show = activeCategory === 'All' || card.dataset.category === activeCategory;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('project-empty').classList.toggle('hidden', visible > 0);
        });
    });

    // Lightbox
    const modal = document.getElementById('project-modal');
    document.querySelectorAll('.project-card').forEach(card => {
        card.addEventListener('click', () => {
            document.getElementById('modal-title').textContent = card.dataset.title;
            document.getElementById('modal-cat').textContent   = card.dataset.category;
            document.getElementById('modal-year').textContent  = card.dataset.year;
            document.getElementById('modal-desc').textContent  = card.dataset.desc;
            const imgWrap = document.getElementById('modal-image-wrap');
            if (card.dataset.img) {
                imgWrap.innerHTML = `<img src="${card.dataset.img}" alt="${card.dataset.title}" class="w-full h-full object-cover" />`;
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });
    });
    document.getElementById('modal-close').addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    });
})();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/projects.blade.php ENDPATH**/ ?>
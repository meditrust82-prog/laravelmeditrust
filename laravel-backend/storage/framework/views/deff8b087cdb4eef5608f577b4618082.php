<?php $__env->startSection('title', 'Contact Meditrust Nepal | Get a Medical Equipment Quote'); ?>

<?php $__env->startSection('content'); ?>


<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="inline-block bg-white/10 text-blue-200 text-sm font-medium px-4 py-1.5 rounded-full mb-5">Contact Meditrust Nepal</span>
            <h1 class="text-4xl sm:text-5xl font-bold text-white leading-tight mb-5">Get a quote or ask for support</h1>
            <p class="text-blue-100 text-lg leading-relaxed">Reach us by phone, WhatsApp or send a message below and we'll respond quickly with a quotation or help you choose the right equipment.</p>
        </div>
    </div>
</section>


<section class="py-16 bg-gray-50 dark:bg-dark-surface">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            
            <div class="lg:col-span-3 bg-white dark:bg-dark-card rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-dark-border">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Send us a message</h2>

                <div id="contact-success" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-5 mb-6">
                    <p class="font-semibold text-green-700 dark:text-green-400">✅ Message sent!</p>
                    <p class="text-green-600 dark:text-green-500 text-sm mt-1">Our team will respond via WhatsApp or email within a few hours.</p>
                </div>

                <form id="contact-form" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                            <input type="text" name="name" required placeholder="Dr. Ram Sharma"
                                   class="w-full px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-surface text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Hospital / Clinic</label>
                            <input type="text" name="hospitalName" placeholder="Bir Hospital"
                                   class="w-full px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-surface text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400 transition" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Phone *</label>
                            <input type="tel" name="phone" required placeholder="+977-98XXXXXXXX"
                                   class="w-full px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-surface text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400 transition" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
                            <input type="email" name="email" required placeholder="you@hospital.com"
                                   class="w-full px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-surface text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400 transition" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Message *</label>
                        <textarea name="message" rows="5" required placeholder="Tell us what equipment or service you need…"
                                  class="w-full px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-surface text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400 transition resize-vertical"></textarea>
                    </div>
                    <div id="contact-error" class="hidden text-red-600 dark:text-red-400 text-sm"></div>
                    <button type="submit" id="contact-submit"
                            class="w-full bg-primary-600 text-white py-3 rounded-xl font-bold hover:bg-primary-700 transition-colors disabled:opacity-60">
                        Send Inquiry
                    </button>
                </form>
            </div>

            
            <div class="lg:col-span-2 space-y-5">
                <?php $__currentLoopData = [
                    ['📍','bg-blue-100 text-blue-700','Address','Kathmandu, Nepal','',false],
                    ['📞','bg-green-100 text-green-700','Phone','+977-9818100515','tel:+9779818100515',true],
                    ['✉️','bg-purple-100 text-purple-700','Email','meditrust82@gmail.com','mailto:meditrust82@gmail.com',true],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon,$cls,$label,$value,$href,$link]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-dark-border flex items-start gap-4">
                        <div class="w-12 h-12 <?php echo e($cls); ?> rounded-xl flex items-center justify-center text-xl flex-shrink-0"><?php echo e($icon); ?></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-0.5"><?php echo e($label); ?></p>
                            <?php if($link): ?>
                                <a href="<?php echo e($href); ?>" class="font-semibold text-gray-900 dark:text-white hover:text-primary-600 transition-colors"><?php echo e($value); ?></a>
                            <?php else: ?>
                                <p class="font-semibold text-gray-900 dark:text-white"><?php echo e($value); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <a href="https://wa.me/9779818100515?text=<?php echo e(urlencode('Hello Meditrust Nepal, I would like to request a quote.')); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="flex items-center gap-4 bg-green-500 text-white rounded-2xl p-6 shadow-sm hover:bg-green-600 transition-colors">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">💬</div>
                    <div>
                        <p class="font-bold">Chat on WhatsApp</p>
                        <p class="text-green-100 text-sm">Fastest way to get a quote</p>
                    </div>
                </a>

                
                <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-dark-border">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-3">Business Hours</h3>
                    <div class="space-y-1.5 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex justify-between"><span>Sun – Fri</span><span class="font-medium">9:00 AM – 6:00 PM</span></div>
                        <div class="flex justify-between"><span>Saturday</span><span class="font-medium">10:00 AM – 4:00 PM</span></div>
                        <div class="flex justify-between"><span>WhatsApp</span><span class="font-medium text-green-600">24/7</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    const form     = document.getElementById('contact-form');
    const errorEl  = document.getElementById('contact-error');
    const successEl = document.getElementById('contact-success');
    const submitBtn = document.getElementById('contact-submit');

    const buildMessage = (v) =>
        `*Quotation Request — Meditrust Nepal*\n\nName: ${v.name}\nHospital: ${v.hospitalName || 'N/A'}\nPhone: ${v.phone}\nEmail: ${v.email}\nMessage: ${v.message}`;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const values = Object.fromEntries(new FormData(form).entries());
        if (!values.name.trim() || !values.phone.trim() || !values.message.trim()) {
            errorEl.textContent = 'Please fill in name, phone and message.';
            errorEl.classList.remove('hidden');
            return;
        }
        errorEl.classList.add('hidden');
        submitBtn.textContent = 'Sending…';
        submitBtn.disabled = true;

        try {
            await fetch('/api/v1/quotes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({
                    productName:  'General Inquiry',
                    name:         values.name,
                    hospitalName: values.hospitalName,
                    phone:        values.phone,
                    email:        values.email,
                    message:      values.message,
                    source:       'contact_form',
                }),
            });
        } catch (_) { /* non-fatal — WhatsApp still opens */ }

        const waText = encodeURIComponent(buildMessage(values));
        window.open(`https://wa.me/9779818100515?text=${waText}`, '_blank');
        form.reset();
        successEl.classList.remove('hidden');
        submitBtn.textContent = '✅ Sent!';
        setTimeout(() => {
            submitBtn.textContent = 'Send Inquiry';
            submitBtn.disabled = false;
        }, 5000);
    });
})();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/contact.blade.php ENDPATH**/ ?>
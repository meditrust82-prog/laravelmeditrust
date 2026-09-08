<?php $__env->startSection('title', 'Privacy Policy | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Privacy Policy</h1>
        <p class="text-blue-200 text-sm">Last updated: January 2025</p>
    </div>
</section>

<section class="py-12 bg-white dark:bg-dark-surface">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg prose-primary max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
            <?php echo $legal['privacy'] ?? '
                <h2>Our Commitment to Your Privacy</h2>
                <p>Meditrust Nepal ("we", "our", "us") respects your privacy and is committed to protecting your personal information. This Privacy Policy explains how we collect, use, and safeguard data when you use our website or services.</p>

                <h3>Information We Collect</h3>
                <ul>
                    <li><strong>Contact information:</strong> Name, phone number, email address, and hospital/organization name when you submit a quote request, contact form, or bulk inquiry.</li>
                    <li><strong>Usage data:</strong> Pages visited, time spent, browser type, and device information collected anonymously via analytics tools.</li>
                    <li><strong>Cookies:</strong> We use cookies to remember your preferences (e.g., dark mode) and to improve your browsing experience.</li>
                </ul>

                <h3>How We Use Your Information</h3>
                <ul>
                    <li>To respond to your quotation requests and inquiries</li>
                    <li>To send product updates and offers (only if you have subscribed)</li>
                    <li>To improve our website and services</li>
                    <li>To comply with legal obligations</li>
                </ul>

                <h3>Data Sharing</h3>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share data with trusted service providers (e.g., WhatsApp for communication, Cloudinary for image hosting) solely to deliver our services.</p>

                <h3>Data Retention</h3>
                <p>We retain your contact information for as long as needed to fulfill your inquiry or as required by law. You may request deletion at any time.</p>

                <h3>Your Rights</h3>
                <p>You have the right to access, correct, or delete your personal data held by us. Contact us at <a href="mailto:info@meditrustnepal.com">info@meditrustnepal.com</a> or via WhatsApp at +977-9818100515.</p>

                <h3>Contact</h3>
                <p>For any privacy-related questions, reach out to us at:<br />
                Meditrust Nepal, Kathmandu, Nepal<br />
                📞 +977-9818100515<br />
                ✉️ info@meditrustnepal.com</p>
            '; ?>

        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/privacy-policy.blade.php ENDPATH**/ ?>
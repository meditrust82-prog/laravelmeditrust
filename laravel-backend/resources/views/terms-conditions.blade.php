@extends('layouts.app')

@section('title', 'Terms and Conditions | Meditrust Nepal')

@section('content')

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-3">Terms and Conditions</h1>
        <p class="text-blue-200 text-sm">Last updated: January 2025</p>
    </div>
</section>

<section class="py-12 bg-white dark:bg-dark-surface">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="prose prose-lg prose-primary max-w-none dark:prose-invert text-gray-700 dark:text-gray-300">
            {!! $legal['terms'] ?? '
                <h2>Terms of Use</h2>
                <p>By accessing and using the Meditrust Nepal website (meditrustnepal.com), you agree to be bound by these Terms and Conditions. If you do not agree, please do not use our website.</p>

                <h3>Use of the Website</h3>
                <ul>
                    <li>This website is intended for informational purposes and to facilitate medical equipment inquiries.</li>
                    <li>You must not use the website for any unlawful purpose or in a way that infringes the rights of others.</li>
                    <li>Product prices and specifications are indicative. Final pricing is confirmed upon quotation.</li>
                </ul>

                <h3>Quotations and Orders</h3>
                <p>Quote requests submitted through our website, WhatsApp, or forms are not binding orders. An order is confirmed only upon written agreement and receipt of advance payment as specified in our quotation.</p>

                <h3>Intellectual Property</h3>
                <p>All content on this website — including text, images, logos, and product descriptions — is the property of Meditrust Nepal and may not be reproduced without written permission.</p>

                <h3>Limitation of Liability</h3>
                <p>Meditrust Nepal is not liable for any indirect, incidental, or consequential damages arising from the use of our website or products. Our liability is limited to the value of the product or service in question.</p>

                <h3>Product Information</h3>
                <p>We strive to ensure all product information is accurate. However, specifications, availability, and prices may change without notice. Always confirm details with our team before placing an order.</p>

                <h3>Governing Law</h3>
                <p>These terms are governed by the laws of Nepal. Any disputes shall be subject to the exclusive jurisdiction of courts in Kathmandu, Nepal.</p>

                <h3>Changes to Terms</h3>
                <p>We reserve the right to update these terms at any time. Continued use of the website after changes constitutes acceptance of the revised terms.</p>

                <h3>Contact</h3>
                <p>For any queries regarding these terms:<br />
                Meditrust Nepal, Kathmandu, Nepal<br />
                📞 +977-9818100515<br />
                ✉️ info@meditrustnepal.com</p>
            ' !!}
        </div>
    </div>
</section>

@endsection

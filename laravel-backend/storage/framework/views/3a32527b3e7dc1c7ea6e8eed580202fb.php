<?php $__env->startSection('title', 'Track Request #' . ($trackingId ?? '') . ' | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-12">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">Track Your Request</h1>
        <p class="text-blue-200">Real-time status for your order or quote request</p>
    </div>
</section>

<section class="max-w-2xl mx-auto px-4 py-12 min-h-[60vh]">

    <?php if(isset($trackingId) && $trackingId): ?>
        
        <div class="bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
                    <span class="text-primary-700 font-bold text-sm">#</span>
                </div>
                <div>
                    <p class="font-bold text-gray-900 dark:text-white">Tracking ID: <?php echo e($trackingId); ?></p>
                    <p class="text-xs text-gray-400">Your request is being processed</p>
                </div>
            </div>

            <?php if(isset($order) && $order): ?>
                <?php
                    $statusMap = [
                        'pending'    => ['label'=>'Pending',     'color'=>'bg-blue-100 text-blue-700',   'desc'=>'Your request has been received. Our team will contact you shortly.'],
                        'contacted'  => ['label'=>'In Progress', 'color'=>'bg-yellow-100 text-yellow-700','desc'=>'Our team has reviewed your request and is preparing your quotation.'],
                        'converted'  => ['label'=>'Completed',   'color'=>'bg-green-100 text-green-700', 'desc'=>'Your quotation has been sent. Please check your WhatsApp or call us.'],
                        'cancelled'  => ['label'=>'Cancelled',   'color'=>'bg-red-100 text-red-700',     'desc'=>'This request has been cancelled. Contact us if you need assistance.'],
                    ];
                    $status = $order['orderStatus'] ?? $order['status'] ?? 'pending';
                    $info = $statusMap[$status] ?? $statusMap['pending'];
                ?>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold <?php echo e($info['color']); ?> mb-3"><?php echo e($info['label']); ?></span>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4"><?php echo e($info['desc']); ?></p>

                <?php if(!empty($order['items'])): ?>
                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm mb-2">Items:</h3>
                    <ul class="space-y-1 mb-4">
                        <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="text-sm text-gray-600 dark:text-gray-400">• <?php echo e($item['name'] ?? 'Item'); ?> × <?php echo e($item['quantity'] ?? 1); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Could not load order details. Please contact us directly.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <form id="tracking-form" class="bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm mb-6">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Enter Tracking ID or Phone Number</label>
        <div class="flex gap-3">
            <input type="text" id="tracking-input" placeholder="e.g. 9818100515 or 1024"
                   value="<?php echo e($trackingId ?? ''); ?>"
                   class="flex-1 px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400" required />
            <button type="submit" class="flex items-center gap-2 bg-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-700 transition disabled:opacity-60">
                🔍 <span id="search-label">Search</span>
            </button>
        </div>
    </form>

    <div id="tracking-result" class="space-y-4"></div>
    <div id="tracking-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-red-700 dark:text-red-400 text-sm mb-4"></div>

    
    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800 rounded-2xl p-5 flex items-start gap-4 mt-6">
        <span class="text-primary-600 dark:text-brand-cyan text-xl mt-0.5 flex-shrink-0">📞</span>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Need faster updates?</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Call or WhatsApp us directly for real-time order status.</p>
            <a href="tel:+977-9818100515" class="text-primary-600 dark:text-brand-cyan font-bold text-sm">+977-9818100515</a>
        </div>
    </div>
</section>

<script>
const STATUS_INFO = {
    new:       { label: 'Received',    color: 'bg-blue-100 text-blue-700',    desc: 'Your quote request has been received. Our team will contact you shortly.' },
    pending:   { label: 'Received',    color: 'bg-blue-100 text-blue-700',    desc: 'Your request has been received. Our team will contact you shortly.' },
    contacted: { label: 'In Progress', color: 'bg-yellow-100 text-yellow-700', desc: 'Our team has reviewed your request and is preparing your quotation.' },
    converted: { label: 'Completed',   color: 'bg-green-100 text-green-700',  desc: 'Your quotation has been sent. Please check your WhatsApp or call us.' },
    lost:      { label: 'Closed',      color: 'bg-gray-100 text-gray-600',    desc: 'This inquiry has been closed. Please contact us if you need further assistance.' },
    cancelled: { label: 'Cancelled',   color: 'bg-red-100 text-red-700',      desc: 'This request has been cancelled. Contact us if you need assistance.' },
};

document.getElementById('tracking-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const input   = document.getElementById('tracking-input').value.trim();
    const label   = document.getElementById('search-label');
    const result  = document.getElementById('tracking-result');
    const errorEl = document.getElementById('tracking-error');
    if (!input) return;

    label.textContent = 'Searching…';
    result.innerHTML  = '';
    errorEl.classList.add('hidden');

    try {
        const isOrderId = /^\d{1,7}$/.test(input);
        let quotes = [];

        if (isOrderId) {
            const res  = await fetch(`/api/v1/orders/tracking/${encodeURIComponent(input)}`);
            const data = await res.json();
            if (data.orderId) {
                quotes = [{ _id: data.orderId, productName: `Order #${data.orderId}`, status: data.orderStatus || data.status, createdAt: data.lastUpdated || data.created_at, message: data.status }];
            }
        } else {
            const res  = await fetch(`/api/v1/quotes/my?phone=${encodeURIComponent(input)}`);
            const data = await res.json();
            quotes = data.quotes || [];
        }

        if (!quotes.length) {
            errorEl.textContent = 'No quotes or orders found for that number. Please check and try again.';
            errorEl.classList.remove('hidden');
        } else {
            result.innerHTML = `<p class="text-sm text-gray-500 mb-3">${quotes.length} quote${quotes.length !== 1 ? 's' : ''} found</p>`;
            quotes.forEach(q => {
                const info = STATUS_INFO[q.status] || STATUS_INFO.new;
                const card = document.createElement('div');
                card.className = 'bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-5 shadow-sm';
                card.innerHTML = `
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">${q.productName || 'Quote Request'}</p>
                            ${q.hospitalName ? `<p class="text-xs text-gray-400 mt-0.5">${q.hospitalName}</p>` : ''}
                        </div>
                        <span class="text-xs font-bold px-3 py-1 rounded-full flex-shrink-0 ${info.color}">${info.label}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${info.desc}</p>
                    <p class="text-xs text-gray-400">${q.createdAt ? 'Submitted: ' + new Date(q.createdAt).toLocaleDateString('en-NP', { year: 'numeric', month: 'long', day: 'numeric' }) : ''}</p>
                `;
                result.appendChild(card);
            });
        }
    } catch (err) {
        errorEl.textContent = 'Could not find any quotes or orders. Please check your input and try again.';
        errorEl.classList.remove('hidden');
    } finally {
        label.textContent = 'Search';
    }
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/tracking.blade.php ENDPATH**/ ?>
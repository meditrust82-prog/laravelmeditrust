<?php $__env->startSection('title', 'Hospital Bulk Inquiry | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <a href="/products" class="inline-flex items-center text-blue-200 hover:text-white mb-4 text-sm transition-colors">
            ← Back to Products
        </a>
        <h1 class="text-3xl font-bold text-white">Hospital Bulk Inquiry</h1>
        <p class="text-blue-200 mt-2">List all equipment you need — we'll send you one consolidated quote.</p>
    </div>
</section>

<section class="max-w-4xl mx-auto px-4 py-10 min-h-[70vh]">

    
    <div id="bulk-success" class="hidden text-center py-24">
        <div class="text-6xl mb-6">✅</div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Inquiry Sent!</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-8">Your bulk inquiry has been sent via WhatsApp. Our team will respond within 24 hours.</p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="/products" class="bg-primary-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-700 transition-colors">Browse More Products</a>
            <button id="bulk-reset" class="border border-gray-300 dark:border-dark-border px-6 py-3 rounded-xl font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-card transition-colors">New Inquiry</button>
        </div>
    </div>

    <form id="bulk-form" class="space-y-8">

        
        <div class="bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">Your Details</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
                    <input type="text" name="name" placeholder="Dr. Ram Sharma" required
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-dark-border rounded-lg text-sm bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hospital / Organization</label>
                    <input type="text" name="hospital" placeholder="Bir Hospital, Kathmandu"
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-dark-border rounded-lg text-sm bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
                    <input type="tel" name="phone" placeholder="+977-98XXXXXXXX" required
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-dark-border rounded-lg text-sm bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                    <input type="email" name="email" placeholder="procurement@hospital.com"
                           class="w-full px-4 py-2.5 border border-gray-200 dark:border-dark-border rounded-lg text-sm bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400" />
                </div>
            </div>
        </div>

        
        <div class="bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">Equipment List</h2>
                <span id="item-count" class="text-xs text-gray-400">0 items added</span>
            </div>

            <div class="hidden sm:grid grid-cols-12 gap-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2 px-1">
                <div class="col-span-6">Equipment Name</div>
                <div class="col-span-2 text-center">Qty</div>
                <div class="col-span-3">Notes / Specs</div>
                <div class="col-span-1"></div>
            </div>

            <div id="item-rows" class="space-y-2"></div>

            <button type="button" id="add-row"
                    class="mt-4 flex items-center gap-2 text-primary-600 text-sm font-semibold hover:text-primary-700 transition-colors">
                + Add another item
            </button>
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit"
                    class="w-full flex items-center justify-center gap-3 bg-green-500 text-white py-4 rounded-xl font-bold text-lg hover:bg-green-600 transition-colors shadow-md">
                📲 Send Bulk Inquiry via WhatsApp
            </button>
            <p class="text-center text-xs text-gray-400 dark:text-gray-500">
                Submitting opens WhatsApp with your inquiry pre-filled. Our team responds within 24 hours.
            </p>
        </div>
    </form>
</section>

<script>
(function() {
    let rows = [newRow(), newRow(), newRow()];

    function newRow() {
        return { id: Date.now() + Math.random(), name: '', qty: 1, notes: '' };
    }

    function countFilled() {
        return rows.filter(r => r.name.trim()).length;
    }

    function renderRows() {
        const container = document.getElementById('item-rows');
        document.getElementById('item-count').textContent = countFilled() + ' item' + (countFilled() !== 1 ? 's' : '') + ' added';
        container.innerHTML = '';
        rows.forEach((row, idx) => {
            const div = document.createElement('div');
            div.className = 'grid grid-cols-12 gap-2 items-center';
            div.innerHTML = `
                <div class="col-span-12 sm:col-span-6">
                    <input type="text" placeholder="Item ${idx + 1} — e.g. ICU Ventilator" value="${row.name}"
                           class="w-full px-3 py-2 border border-gray-200 dark:border-dark-border rounded-lg text-sm bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400"
                           data-field="name" data-id="${row.id}" />
                </div>
                <div class="col-span-4 sm:col-span-2">
                    <input type="number" min="1" value="${row.qty}"
                           class="w-full px-3 py-2 border border-gray-200 dark:border-dark-border rounded-lg text-sm text-center bg-white dark:bg-dark-card text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-400"
                           data-field="qty" data-id="${row.id}" />
                </div>
                <div class="col-span-7 sm:col-span-3">
                    <input type="text" placeholder="Brand, spec, budget..." value="${row.notes}"
                           class="w-full px-3 py-2 border border-gray-200 dark:border-dark-border rounded-lg text-sm bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400"
                           data-field="notes" data-id="${row.id}" />
                </div>
                <div class="col-span-1 flex justify-center">
                    ${rows.length > 1 ? `<button type="button" class="text-red-400 hover:text-red-600 p-1 transition-colors text-lg" data-remove="${row.id}">🗑️</button>` : ''}
                </div>
            `;
            container.appendChild(div);
        });

        // Bind inputs
        container.querySelectorAll('[data-field]').forEach(input => {
            input.addEventListener('input', () => {
                const id = parseFloat(input.dataset.id);
                const field = input.dataset.field;
                const row = rows.find(r => r.id === id);
                if (row) {
                    row[field] = field === 'qty' ? parseInt(input.value) || 1 : input.value;
                    document.getElementById('item-count').textContent = countFilled() + ' item' + (countFilled() !== 1 ? 's' : '') + ' added';
                }
            });
        });
        container.querySelectorAll('[data-remove]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = parseFloat(btn.dataset.remove);
                rows = rows.filter(r => r.id !== id);
                renderRows();
            });
        });
    }

    document.getElementById('add-row').addEventListener('click', () => {
        rows.push(newRow());
        renderRows();
    });

    document.getElementById('bulk-reset').addEventListener('click', () => {
        rows = [newRow(), newRow(), newRow()];
        renderRows();
        document.getElementById('bulk-success').classList.add('hidden');
        document.getElementById('bulk-form').classList.remove('hidden');
    });

    document.getElementById('bulk-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const form = e.target;
        const name     = form.querySelector('[name="name"]').value.trim();
        const hospital = form.querySelector('[name="hospital"]').value.trim();
        const phone    = form.querySelector('[name="phone"]').value.trim();
        const email    = form.querySelector('[name="email"]').value.trim();
        const filled   = rows.filter(r => r.name.trim());

        if (!name || !phone) { alert('Please fill in your name and phone number.'); return; }
        if (!filled.length)  { alert('Please add at least one item.'); return; }

        const lines = [
            '*Bulk Equipment Inquiry — Meditrust Nepal*', '',
            '*Contact Details:*',
            `Name: ${name}`,
            `Hospital/Organization: ${hospital || 'N/A'}`,
            `Phone: ${phone}`,
            `Email: ${email || 'N/A'}`,
            '',
            `*Required Equipment (${filled.length} items):*`,
            ...filled.map((r, i) => `${i + 1}. ${r.name} — Qty: ${r.qty}${r.notes ? ` (${r.notes})` : ''}`),
            '', 'Please provide a consolidated quotation.',
        ];
        window.open('https://wa.me/9779818100515?text=' + encodeURIComponent(lines.join('\n')), '_blank');
        document.getElementById('bulk-form').classList.add('hidden');
        document.getElementById('bulk-success').classList.remove('hidden');
    });

    renderRows();
})();
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/bulk-inquiry.blade.php ENDPATH**/ ?>
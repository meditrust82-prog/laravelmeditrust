@extends('layouts.app')

@section('title', 'Track My Quote | Meditrust Nepal')

@section('content')

<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-700 py-12">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h1 class="text-3xl font-bold text-white mb-2">Track Your Quote</h1>
        <p class="text-blue-200">Enter your phone number to see the status of your quotation requests</p>
    </div>
</section>

<section class="max-w-2xl mx-auto px-4 py-12 min-h-[60vh]">

    <form id="quote-search-form" class="bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm mb-6">
        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Phone Number Used When Requesting a Quote
        </label>
        <div class="flex gap-3">
            <input type="tel" id="quote-phone" placeholder="98XXXXXXXX"
                   class="flex-1 px-4 py-3 border border-gray-200 dark:border-dark-border rounded-xl bg-white dark:bg-dark-card text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-400"
                   required />
            <button type="submit" id="quote-submit" class="flex items-center gap-2 bg-primary-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-700 transition disabled:opacity-60">
                🔍 Search
            </button>
        </div>
    </form>

    <div id="quote-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-red-700 dark:text-red-400 text-sm mb-4"></div>
    <div id="quote-empty" class="hidden text-center py-12 text-gray-400 dark:text-gray-500 text-sm">
        No quotes found for that phone number.
    </div>
    <div id="quote-result" class="space-y-4"></div>

    {{-- Contact CTA --}}
    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800 rounded-2xl p-5 flex items-start gap-4 mt-8">
        <span class="text-primary-600 dark:text-brand-cyan text-xl mt-0.5 flex-shrink-0">📞</span>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white text-sm mb-1">Need faster updates?</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Call or WhatsApp us directly for real-time quote status.</p>
            <a href="tel:+977-9818100515" class="text-primary-600 dark:text-brand-cyan font-bold text-sm">+977-9818100515</a>
        </div>
    </div>
</section>

<script>
const STATUS_INFO = {
    new:       { label: 'Received',    color: 'bg-blue-100 text-blue-700',    desc: 'Your quote request has been received. Our team will contact you shortly.' },
    contacted: { label: 'In Progress', color: 'bg-yellow-100 text-yellow-700', desc: 'Our team has reviewed your request and is preparing your quotation.' },
    converted: { label: 'Completed',   color: 'bg-green-100 text-green-700',  desc: 'Your quotation has been sent. Please check your WhatsApp or call us.' },
    lost:      { label: 'Closed',      color: 'bg-gray-100 text-gray-600',    desc: 'This inquiry has been closed. Please contact us if you need further assistance.' },
};

document.getElementById('quote-search-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const phone    = document.getElementById('quote-phone').value.trim();
    const btn      = document.getElementById('quote-submit');
    const errorEl  = document.getElementById('quote-error');
    const emptyEl  = document.getElementById('quote-empty');
    const resultEl = document.getElementById('quote-result');

    if (!phone) return;
    btn.textContent = '🔍 Searching…';
    btn.disabled    = true;
    errorEl.classList.add('hidden');
    emptyEl.classList.add('hidden');
    resultEl.innerHTML = '';

    try {
        const isOrderId = /^\d{1,7}$/.test(phone);
        let quotes = [];

        if (isOrderId) {
            const res  = await fetch(`/api/v1/orders/tracking/${encodeURIComponent(phone)}`);
            const data = await res.json();
            if (data.orderId) {
                quotes = [{
                    _id:         data.orderId,
                    productName: `Order #${data.orderId} — ${(data.items || []).map(i => i.name).join(', ')}`,
                    hospitalName: data.customer?.name,
                    status:      data.orderStatus || data.status || 'new',
                    createdAt:   data.lastUpdated || data.created_at,
                }];
            }
        } else {
            const res  = await fetch(`/api/v1/quotes/my?phone=${encodeURIComponent(phone)}`);
            const data = await res.json();
            quotes = data.quotes || [];
        }

        if (!quotes.length) {
            emptyEl.classList.remove('hidden');
            return;
        }

        resultEl.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">${quotes.length} quote${quotes.length !== 1 ? 's' : ''} found</p>`;

        quotes.forEach(q => {
            const info = STATUS_INFO[q.status] || STATUS_INFO.new;
            const card = document.createElement('div');
            card.className = 'bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl p-5 shadow-sm';
            card.innerHTML = `
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">${q.productName || 'Quote Request'}</p>
                        ${q.hospitalName ? `<p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">${q.hospitalName}</p>` : ''}
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-full flex-shrink-0 ${info.color}">${info.label}</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${info.desc}</p>
                ${q.createdAt ? `<p class="text-xs text-gray-400 dark:text-gray-500">Submitted: ${new Date(q.createdAt).toLocaleDateString('en-NP', { year: 'numeric', month: 'long', day: 'numeric' })}</p>` : ''}
            `;
            resultEl.appendChild(card);
        });

    } catch {
        errorEl.textContent = 'Could not find any quotes or orders for that number. Please check and try again.';
        errorEl.classList.remove('hidden');
    } finally {
        btn.textContent = '🔍 Search';
        btn.disabled    = false;
    }
});
</script>

@endsection

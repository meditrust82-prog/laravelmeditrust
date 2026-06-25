@extends('layouts.app')

@section('title', 'Quote Builder & Cart | Meditrust Nepal')

@section('content')
<!-- Hero Header -->
<section class="bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 py-12 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Quote Builder</h1>
        <p class="text-primary-200 text-sm sm:text-base mt-2 max-w-2xl">
            Review your selected medical equipment, adjust quantities, and submit your inquiry directly to our sales team.
        </p>
    </div>
</section>

<!-- Content Area -->
<section class="py-12 bg-gray-50 dark:bg-dark-surface min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Selected Items -->
            <div class="lg:col-span-2 bg-white dark:bg-dark-card border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm flex flex-col">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-primary-600 rounded-full"></span>
                    Selected Equipment (<span id="cart-qty-count">0</span>)
                </h2>
                
                <!-- Items list -->
                <div id="cart-items" class="space-y-4 flex-1">
                    <!-- Dynamically populated -->
                </div>
                
                <!-- Empty State -->
                <div id="cart-empty" class="hidden text-center py-16 px-4">
                    <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4 text-primary-500 text-2xl">
                        🛒
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                    <p class="text-gray-500 mb-6 text-sm max-w-xs mx-auto">Add medical equipment from our catalog to build your custom quote request.</p>
                    <a href="/products" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 rounded-xl transition-all shadow-sm">
                        Browse Catalog
                    </a>
                </div>
            </div>

            <!-- Right Column: Contact Details Form -->
            <div class="bg-white dark:bg-dark-card border border-gray-100 dark:border-dark-border rounded-2xl p-6 shadow-sm h-fit">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                    Inquiry Details
                </h2>
                
                <form id="cart-form" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Full Name *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 outline-none text-sm dark:bg-dark-surface dark:border-dark-border dark:text-white transition-all" placeholder="Dr. John Doe">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Hospital / Institution Name</label>
                        <input type="text" name="hospital_name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 outline-none text-sm dark:bg-dark-surface dark:border-dark-border dark:text-white transition-all" placeholder="Kathmandu General Hospital">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Mobile / WhatsApp Number *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 outline-none text-sm dark:bg-dark-surface dark:border-dark-border dark:text-white transition-all" placeholder="98XXXXXXXX">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Email Address *</label>
                        <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 outline-none text-sm dark:bg-dark-surface dark:border-dark-border dark:text-white transition-all" placeholder="john.doe@hospital.com">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Inquiry Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 outline-none text-sm dark:bg-dark-surface dark:border-dark-border dark:text-white transition-all" placeholder="Please mention if you require shipping, installation support, or specific power requirements."></textarea>
                    </div>

                    <div class="pt-4 space-y-3">
                        <button id="cart-submit" type="submit" class="w-full h-12 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-xl transition-all shadow-md shadow-primary-500/10 flex items-center justify-center gap-2">
                            <span>Place Quote Order</span>
                        </button>
                        
                        <a id="wa-link" href="#" target="_blank" rel="noopener noreferrer" class="w-full h-12 bg-[#25D366] hover:bg-[#1da851] text-white font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-brands fa-whatsapp text-lg"></i> Send via WhatsApp
                        </a>
                    </div>
                </form>
                
                <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                    * Submitting this request creates a temporary quote reservation in our system. A Meditrust sales manager will verify product availability, finalize custom discounts, and email you a formal proposal.
                </p>
            </div>
            
        </div>
    </div>
</section>

<script>
(() => {
    const cartKey = 'meditrust_cart';
    const itemsEl = document.getElementById('cart-items');
    const emptyEl = document.getElementById('cart-empty');
    const form = document.getElementById('cart-form');
    const waLink = document.getElementById('wa-link');
    const cartQtyCount = document.getElementById('cart-qty-count');

    const loadCart = () => {
        try {
            return JSON.parse(localStorage.getItem(cartKey) || '[]');
        } catch (e) {
            return [];
        }
    };

    const saveCart = (cart) => {
        localStorage.setItem(cartKey, JSON.stringify(cart));
    };

    const formatPrice = (value) => {
        return value ? value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : '0';
    };

    const render = () => {
        const cart = loadCart();
        itemsEl.innerHTML = '';
        
        // Update quantities badge
        const totalQty = cart.reduce((sum, item) => sum + (item.qty || 1), 0);
        cartQtyCount.textContent = totalQty;

        if (!cart.length) {
            itemsEl.classList.add('hidden');
            emptyEl.classList.remove('hidden');
            waLink.classList.add('opacity-50', 'pointer-events-none');
            waLink.textContent = 'Add items to cart first';
            return;
        }
        
        itemsEl.classList.remove('hidden');
        emptyEl.classList.add('hidden');
        waLink.classList.remove('opacity-50', 'pointer-events-none');

        let total = 0;
        cart.forEach((item, index) => {
            const lineTotal = (item.price || 0) * item.qty;
            total += lineTotal;
            const card = document.createElement('div');
            card.className = "flex items-center gap-4 p-4 border border-gray-100 dark:border-dark-border rounded-xl bg-gray-50 dark:bg-dark-surface hover:shadow-sm transition-all";
            card.innerHTML = `
                <img src="${item.image || '/assets/placeholder-image.jpg'}" alt="${item.name}" class="h-16 w-16 object-cover rounded-lg border border-slate-100 bg-white">
                <div class="flex-1 min-w-0">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white truncate">${item.name || 'Unnamed product'}</h4>
                    <span class="text-xs text-gray-400 font-medium">${item.category || 'Medical Equipment'}</span>
                    <div class="flex items-center gap-2 mt-2">
                        <button type="button" data-action="decrease" data-index="${index}" class="w-8 h-8 rounded-lg border border-gray-200 dark:border-dark-border bg-white dark:bg-dark-card flex items-center justify-center text-sm font-bold hover:bg-gray-50 text-gray-700 dark:text-gray-300 transition-all">-</button>
                        <span class="w-6 text-center text-sm font-bold text-gray-800 dark:text-gray-200">${item.qty}</span>
                        <button type="button" data-action="increase" data-index="${index}" class="w-8 h-8 rounded-lg border border-gray-200 dark:border-dark-border bg-white dark:bg-dark-card flex items-center justify-center text-sm font-bold hover:bg-gray-50 text-gray-700 dark:text-gray-300 transition-all">+</button>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1.5 justify-between h-full">
                    <button type="button" data-action="remove" data-index="${index}" class="text-xs text-red-500 hover:text-red-700 font-bold transition-all"><i class="fa-regular fa-trash-can mr-1"></i> Remove</button>
                    <span class="text-sm font-bold text-primary-700 dark:text-primary-400 mt-auto">NRS ${formatPrice(lineTotal)}</span>
                </div>
            `;
            itemsEl.appendChild(card);
        });

        // Summary footer
        const summary = document.createElement('div');
        summary.className = "border-t border-gray-100 dark:border-dark-border pt-4 mt-6 flex flex-col sm:flex-row items-center justify-between gap-4";
        summary.innerHTML = `
            <button id="clear-cart" type="button" class="px-4 py-2 border border-red-100 hover:bg-red-50 text-red-500 rounded-lg text-xs font-bold transition-all">
                <i class="fa-solid fa-trash-arrow-up mr-1.5"></i> Clear Cart list
            </button>
            <div class="text-right">
                <span class="text-xs text-gray-400 font-medium">Estimated Total Price:</span>
                <div class="text-xl font-extrabold text-slate-800 dark:text-white">NRS ${formatPrice(total)}</div>
            </div>
        `;
        itemsEl.appendChild(summary);

        document.getElementById('clear-cart').addEventListener('click', () => {
            saveCart([]);
            render();
            updateWhatsAppLink();
        });
    };

    itemsEl.addEventListener('click', (event) => {
        const button = event.target.closest('button');
        if (!button) return;
        const action = button.getAttribute('data-action');
        const index = parseInt(button.getAttribute('data-index'), 10);
        const cart = loadCart();
        if (!cart[index]) return;
        
        if (action === 'remove') {
            cart.splice(index, 1);
        } else if (action === 'decrease') {
            cart[index].qty = Math.max(1, cart[index].qty - 1);
        } else if (action === 'increase') {
            cart[index].qty += 1;
        }
        
        saveCart(cart);
        render();
        updateWhatsAppLink();
        
        // Notify global app.js to reload counts
        if (typeof window.dispatchEvent === 'function') {
            window.dispatchEvent(new Event('storage'));
        }
    });

    const buildMessage = (cart, form) => {
        let msg = '*🛒 New Quote Request - Meditrust Nepal*\n\n';
        cart.forEach((item, idx) => {
            msg += `${idx + 1}. ${item.name} x${item.qty}`;
            if (item.price) msg += ` - NRS ${formatPrice(item.price * item.qty)}`;
            msg += '\n';
        });
        if (form.notes.trim()) {
            msg += `\n*Notes*: ${form.notes.trim()}\n`;
        }
        msg += `\n*Customer Details*:\n👤 Name: ${form.name}\n🏥 Hospital: ${form.hospital_name || 'N/A'}\n📞 Phone: ${form.phone}\n📧 Email: ${form.email}`;
        return msg;
    };

    const updateWhatsAppLink = () => {
        const cart = loadCart();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        if (cart.length === 0) {
            waLink.href = '#';
            waLink.innerText = 'Add items to cart first';
            waLink.style.opacity = '0.6';
            waLink.style.pointerEvents = 'none';
            return;
        }
        
        const text = encodeURIComponent(buildMessage(cart, data));
        waLink.href = `https://wa.me/9779818100515?text=${text}`;
        waLink.innerHTML = '<i class="fa-brands fa-whatsapp text-lg"></i> Send via WhatsApp';
        waLink.style.opacity = '1';
        waLink.style.pointerEvents = 'auto';
    };

    form.addEventListener('input', updateWhatsAppLink);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const cart = loadCart();
        if (!cart.length) { alert('Your cart is empty.'); return; }
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        if (!data.name.trim() || !data.phone.trim() || !data.email.trim()) {
            alert('Please fill in your name, phone and email to place the order.');
            return;
        }

        const submitBtn = document.getElementById('cart-submit');
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Placing order...';
        submitBtn.disabled = true;

        try {
            // Replicate correct payload shape to pass OrderController validation
            const response = await fetch('/api/v1/orders', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    items: cart.map(item => ({ product: item.id, quantity: item.qty })),
                    shippingAddress: {
                        name: data.name.trim(),
                        phone: data.phone.trim(),
                        addressLine: data.hospital_name.trim() || 'N/A',
                        city: 'Kathmandu',
                    },
                    notes: `Email: ${data.email.trim()}. ${data.notes.trim()}`,
                }),
            });
            
            if (response.ok) {
                // Open WhatsApp
                const text = encodeURIComponent(buildMessage(cart, data));
                window.open(`https://wa.me/9779818100515?text=${text}`, '_blank');
                
                // Reset
                saveCart([]);
                render();
                form.reset();
                submitBtn.innerHTML = '✅ Order Placed!';
                
                if (typeof window.dispatchEvent === 'function') {
                    window.dispatchEvent(new Event('storage'));
                }
            } else {
                const err = await response.json();
                alert(err.error || 'Failed to place order.');
            }
        } catch (err) {
            console.error('Order save error:', err);
            // Fallback WhatsApp redirect anyway
            const text = encodeURIComponent(buildMessage(cart, data));
            window.open(`https://wa.me/9779818100515?text=${text}`, '_blank');
        } finally {
            setTimeout(() => { 
                submitBtn.innerHTML = 'Place Quote Order'; 
                submitBtn.disabled = false; 
            }, 4000);
            updateWhatsAppLink();
        }
    });

    render();
    updateWhatsAppLink();
})();
</script>
@endsection

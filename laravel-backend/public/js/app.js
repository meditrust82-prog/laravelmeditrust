(function () {
  const CART_KEY = 'meditrust_cart';
  const WISHLIST_KEY = 'mt_wishlist';
  const COMPARE_KEY = 'meditrust_compare';
  const THEME_KEY = 'meditrust-theme';
  const NEWSLETTER_KEY = 'mt_newsletter_dismissed';
  const WA_PHONE = '9779818100515';
  const API_BASE = '/api/v1';

  const parseJSON = (value, fallback = null) => {
    try {
      return value ? JSON.parse(value) : fallback;
    } catch (err) {
      return fallback;
    }
  };

  const saveJSON = (key, value) => {
    try {
      localStorage.setItem(key, JSON.stringify(value));
    } catch (err) {
      console.warn('Unable to save localStorage:', err);
    }
  };

  const loadJSON = (key, fallback) => parseJSON(localStorage.getItem(key), fallback);

  const getCart = () => loadJSON(CART_KEY, []);
  const getWishlist = () => loadJSON(WISHLIST_KEY, []);
  const getCompare = () => loadJSON(COMPARE_KEY, []);

  const formatNumber = (value) => {
    if (value == null) return '0';
    return String(value).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  };

  const toastContainer = (() => {
    let el = document.getElementById('toast-container');
    if (!el) {
      el = document.createElement('div');
      el.id = 'toast-container';
      el.style.position = 'fixed';
      el.style.zIndex = '9999';
      el.style.top = '1rem';
      el.style.right = '1rem';
      el.style.display = 'flex';
      el.style.flexDirection = 'column';
      el.style.gap = '0.75rem';
      el.style.maxWidth = '320px';
      document.body.appendChild(el);
    }
    return el;
  })();

  const showToast = (message, type = 'info') => {
    const el = document.createElement('div');
    el.textContent = message;
    el.style.padding = '0.9rem 1rem';
    el.style.borderRadius = '16px';
    el.style.boxShadow = '0 14px 45px rgba(15,23,42,0.12)';
    el.style.background = type === 'success' ? '#0f766e' : type === 'warning' ? '#f59e0b' : type === 'danger' ? '#dc2626' : '#111827';
    el.style.color = '#fff';
    el.style.opacity = '0';
    el.style.transition = 'opacity 240ms ease, transform 240ms ease';
    toastContainer.appendChild(el);
    requestAnimationFrame(() => {
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    });
    setTimeout(() => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(-8px)';
      setTimeout(() => el.remove(), 240);
    }, 3000);
  };

  const getTheme = () => {
    const saved = localStorage.getItem(THEME_KEY);
    if (saved) return saved;
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
  };

  const applyTheme = (theme) => {
    const root = document.documentElement;
    if (theme === 'dark') {
      root.classList.add('dark');
    } else {
      root.classList.remove('dark');
    }
    localStorage.setItem(THEME_KEY, theme);
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.textContent = theme === 'dark' ? '☀️' : '🌙';
      button.title = theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode';
    });
  };

  const toggleTheme = () => {
    const next = getTheme() === 'dark' ? 'light' : 'dark';
    applyTheme(next);
  };

  const getProductId = (product) => product.id || product.slug || null;
  const getProductSlug = (product) => product.slug || product.id || null;

  const isInCart = (product) => getCart().some((item) => getProductId(item) === getProductId(product));
  const isInWishlist = (product) => getWishlist().some((item) => item.slug === getProductSlug(product));
  const isInCompare = (product) => getCompare().some((item) => getProductId(item) === getProductId(product));

  const addToCart = (product, qty = 1) => {
    if (!product || !getProductId(product)) return;
    const cart = getCart();
    const existing = cart.find((item) => getProductId(item) === getProductId(product));
    if (existing) {
      existing.qty = Math.min((existing.qty || 1) + qty, 99);
      showToast(`Updated ${product.name} quantity in cart.`, 'success');
    } else {
      if (cart.length >= 20) {
        showToast('Cart is full (max 20 items).', 'warning');
        return;
      }
      cart.push({
        id: getProductId(product),
        slug: getProductSlug(product),
        name: product.name,
        category: product.category,
        price: product.price,
        image: product.image,
        qty: qty,
      });
      showToast(`${product.name} added to quote cart.`, 'success');
    }
    saveJSON(CART_KEY, cart);
    updatePageState();
    window.dispatchEvent(new Event('storage'));
  };

  const removeFromCart = (productId) => {
    const cart = getCart().filter((item) => getProductId(item) !== productId);
    saveJSON(CART_KEY, cart);
    updatePageState();
    window.dispatchEvent(new Event('storage'));
  };

  const toggleWishlist = (product) => {
    if (!product || !getProductSlug(product)) return;
    const list = getWishlist();
    const existing = list.find((item) => item.slug === getProductSlug(product));
    if (existing) {
      const next = list.filter((item) => item.slug !== getProductSlug(product));
      saveJSON(WISHLIST_KEY, next);
      showToast(`${product.name} removed from wishlist.`, 'info');
    } else {
      const next = [...list, { slug: getProductSlug(product), name: product.name, category: product.category, price: product.price, image: product.image }];
      saveJSON(WISHLIST_KEY, next);
      showToast(`${product.name} added to wishlist.`, 'success');
    }
    updatePageState();
    window.dispatchEvent(new Event('storage'));
  };

  const toggleCompare = (product) => {
    if (!product || !getProductId(product)) return;
    const list = getCompare();
    const existing = list.find((item) => getProductId(item) === getProductId(product));
    if (existing) {
      const next = list.filter((item) => getProductId(item) !== getProductId(product));
      saveJSON(COMPARE_KEY, next);
      showToast(`${product.name} removed from compare.`, 'info');
    } else {
      if (list.length >= 3) {
        showToast('You can only compare up to 3 products.', 'warning');
        return;
      }
      saveJSON(COMPARE_KEY, [...list, { id: getProductId(product), slug: getProductSlug(product), name: product.name, category: product.category, price: product.price, description: product.description, image: product.image }]);
      showToast(`${product.name} added to compare.`, 'success');
    }
    updatePageState();
    window.dispatchEvent(new Event('storage'));
  };

  const updatePageState = () => {
    renderComparePage();
    renderWishlistPage();
    hydrateProductCards();
    hydrateProductDetail();
    renderMobileTabBar();
    if (typeof renderRecentlyViewed === 'function') renderRecentlyViewed();
    if (typeof renderCompareDrawer === 'function') renderCompareDrawer();
  };

  const createMobileTabBar = () => {
    let bar = document.getElementById('mobile-tabbar');
    const shouldShow = window.innerWidth < 1024;
    if (!shouldShow) {
      if (bar) bar.remove();
      return;
    }
    if (!bar) {
      bar = document.createElement('div');
      bar.id = 'mobile-tabbar';
      bar.className = 'mobile-tabbar';
      bar.innerHTML = `
        <a href="/home">Home</a>
        <a href="/products">Products</a>
        <a href="/wishlist" class="tab-badge" data-badge-id="wishlist">Wishlist</a>
        <a href="/cart" class="tab-badge" data-badge-id="cart">Cart</a>
        <a href="https://wa.me/${WA_PHONE}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
      `;
      document.body.appendChild(bar);
    }
    const wishlistCount = getWishlist().length;
    const cartCount = getCart().reduce((sum, item) => sum + (item.qty || 1), 0);
    bar.querySelectorAll('[data-badge-id]').forEach((link) => {
      const badgeType = link.dataset.badgeId;
      let count = 0;
      if (badgeType === 'wishlist') count = wishlistCount;
      if (badgeType === 'cart') count = cartCount;
      if (count) {
        link.dataset.badge = count > 9 ? '9+' : count;
      } else {
        delete link.dataset.badge;
      }
    });
  };

  const hydrateProductCards = () => {
    document.querySelectorAll('.product-card').forEach((card) => {
      const product = parseJSON(card.dataset.product, null);
      if (!product) return;

      // Wire up all [data-action] buttons
      card.querySelectorAll('[data-action]').forEach((button) => {
        // Remove previous listener to avoid duplicates on re-hydration
        const fresh = button.cloneNode(true);
        button.parentNode.replaceChild(fresh, button);
        fresh.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const action = fresh.dataset.action;
          if (action === 'add-cart')        { addToCart(product, 1); hydrateProductCards(); }
          if (action === 'toggle-wishlist') { toggleWishlist(product); hydrateProductCards(); }
          if (action === 'toggle-compare') { toggleCompare(product); hydrateProductCards(); }
          if (action === 'share') {
            navigator.clipboard.writeText(product.url || window.location.href);
            showToast('Product link copied to clipboard.');
          }
        });
      });

      // Reflect current state visually
      const inCart      = isInCart(product);
      const inCompare   = isInCompare(product);
      const inWishlist  = isInWishlist(product);

      const cartBtn     = card.querySelector('[data-action="add-cart"]');
      const compareBtn  = card.querySelector('[data-action="toggle-compare"]');
      const wishlistBtn = card.querySelector('[data-action="toggle-wishlist"]');

      if (cartBtn) {
        cartBtn.title       = inCart ? 'Already in quote cart' : 'Add to quote cart';
        cartBtn.style.background = inCart ? '#15803d' : '';
        cartBtn.textContent = inCart ? '✓' : '🛒';
      }
      if (compareBtn) {
        compareBtn.classList.toggle('bg-blue-100',    inCompare);
        compareBtn.classList.toggle('text-blue-700',  inCompare);
        compareBtn.classList.toggle('bg-gray-100',    !inCompare);
        compareBtn.classList.toggle('text-gray-700',  !inCompare);
        compareBtn.title = inCompare ? 'Remove from compare' : 'Add to compare';
      }
      if (wishlistBtn) {
        wishlistBtn.textContent = inWishlist ? '❤️' : '♡';
        wishlistBtn.classList.toggle('text-red-500', inWishlist);
        wishlistBtn.classList.toggle('text-gray-500', !inWishlist);
        wishlistBtn.title = inWishlist ? 'Remove from wishlist' : 'Save to wishlist';
      }
    });
  };

  const hydrateProductDetail = () => {
    const detail = document.querySelector('[data-product-detail]');
    if (!detail) return;
    const product = parseJSON(detail.dataset.productDetail, null);
    if (!product) return;
    
    // Track recently viewed
    const RECENTLY_VIEWED_KEY = 'mt_recently_viewed';
    let rv = loadJSON(RECENTLY_VIEWED_KEY, []);
    rv = rv.filter(p => p.slug !== product.slug);
    rv.unshift({ slug: product.slug, name: product.name, category: product.category, price: product.price, image: product.image });
    if (rv.length > 6) rv = rv.slice(0, 6);
    saveJSON(RECENTLY_VIEWED_KEY, rv);

    const qtyEl = document.getElementById('product-qty');
    let qty = qtyEl ? Number(qtyEl.textContent) || 1 : 1;
    const updateQtyDisplay = () => {
      if (qtyEl) qtyEl.textContent = String(Math.max(1, qty));
    };
    updateQtyDisplay();
    detail.querySelectorAll('[data-action]').forEach((button) => {
      button.onclick = () => {
        const action = button.dataset.action;
        if (action === 'add-cart') addToCart(product, qty);
        if (action === 'toggle-wishlist') toggleWishlist(product);
        if (action === 'toggle-compare') toggleCompare(product);
        if (action === 'share') {
          const url = window.location.href;
          navigator.clipboard.writeText(url);
          showToast('Product link copied to clipboard.');
        }
        if (action === 'decrease-qty') {
          qty = Math.max(1, qty - 1);
          updateQtyDisplay();
        }
        if (action === 'increase-qty') {
          qty = Math.max(1, qty + 1);
          updateQtyDisplay();
        }
      };
    });
    const compareBtn = detail.querySelector('[data-action="toggle-compare"]');
    const wishlistBtn = detail.querySelector('[data-action="toggle-wishlist"]');
    if (compareBtn) compareBtn.textContent = isInCompare(product) ? 'Remove compare' : 'Compare';
    if (wishlistBtn) wishlistBtn.textContent = isInWishlist(product) ? 'Remove wishlist' : 'Wishlist';
  };

  const renderWishlistPage = () => {
    const container = document.getElementById('wishlist-list');
    const emptyEl = document.getElementById('wishlist-empty');
    const countEl = document.getElementById('wishlist-count');
    if (!container || !emptyEl) return;

    const list = getWishlist();

    if (countEl) {
      if (list.length) {
        countEl.textContent = `(${list.length} item${list.length !== 1 ? 's' : ''})`;
        countEl.classList.remove('hidden');
      } else {
        countEl.classList.add('hidden');
      }
    }

    if (!list.length) {
      container.innerHTML = '';
      emptyEl.style.display = 'block';
      return;
    }
    emptyEl.style.display = 'none';

    const grid = document.createElement('div');
    grid.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';

    list.forEach((item) => {
      const card = document.createElement('div');
      card.className = 'bg-white dark:bg-dark-surface border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all group flex flex-col';
      card.innerHTML = `
        <a href="/products/${item.slug}" class="block">
          ${item.image
            ? `<div class="h-44 overflow-hidden bg-gray-50 dark:bg-dark-card">
                 <img src="${item.image}" alt="${item.name}" loading="lazy" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-300 p-2" />
               </div>`
            : `<div class="h-44 bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center text-4xl">❤️</div>`}
        </a>
        <div class="p-4 flex flex-col flex-1">
          ${item.category ? `<p class="text-xs text-primary-600 font-medium mb-1">${item.category}</p>` : ''}
          <a href="/products/${item.slug}" class="font-semibold text-gray-900 dark:text-white hover:text-primary-600 transition line-clamp-2 flex-1">${item.name}</a>
          ${item.price
            ? `<p class="text-primary-700 font-bold mt-2">NRS ${formatNumber(item.price)}</p>`
            : `<p class="text-gray-400 text-sm mt-2">Price on request</p>`}
          <div class="flex gap-2 mt-4">
            <button class="btn-add-cart flex-1 flex items-center justify-center gap-2 bg-primary-600 text-white py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition">
              🛒 Add to Cart
            </button>
            <button class="btn-remove-wish p-2 border border-gray-200 dark:border-dark-border rounded-lg text-red-400 hover:bg-red-50 hover:border-red-200 transition" title="Remove from wishlist">
              🗑️
            </button>
          </div>
        </div>
      `;

      card.querySelector('.btn-add-cart').onclick = () => {
        addToCart(item, 1);
        card.querySelector('.btn-add-cart').textContent = '✅ Added!';
      };

      card.querySelector('.btn-remove-wish').onclick = () => {
        toggleWishlist(item);
      };

      grid.appendChild(card);
    });

    const waText = encodeURIComponent('Hello, I would like to request a quotation for the following items:\n\n' + list.map((p, i) => `${i + 1}. ${p.name}`).join('\n'));
    const footer = document.createElement('div');
    footer.className = 'mt-10 text-center';
    footer.innerHTML = `
      <a href="https://wa.me/${WA_PHONE}?text=${waText}" target="_blank" rel="noopener noreferrer"
         class="inline-flex items-center gap-3 bg-green-500 text-white px-8 py-4 rounded-xl font-bold hover:bg-green-600 transition shadow-md text-lg">
          Request Quote for All ${list.length} Item${list.length !== 1 ? 's' : ''} via WhatsApp
      </a>
    `;

    container.innerHTML = '';
    container.appendChild(grid);
    container.appendChild(footer);
  };

  const renderComparePage = () => {
    const container = document.getElementById('compare-list');
    const emptyEl = document.getElementById('compare-empty');
    if (!container || !emptyEl) return;

    const list = getCompare();

    if (!list.length) {
      container.innerHTML = '';
      emptyEl.style.display = 'block';
      return;
    }
    emptyEl.style.display = 'none';

    // Clear All button
    const toolbar = document.createElement('div');
    toolbar.className = 'flex justify-end mb-4';
    toolbar.innerHTML = `
      <button id="compare-clear-all" class="text-red-500 hover:text-red-700 font-medium text-sm flex items-center gap-2">
        🗑️ Clear All
      </button>
    `;

    const scroll = document.createElement('div');
    scroll.className = 'overflow-x-auto';

    const inner = document.createElement('div');
    inner.className = 'min-w-[700px]';

    const table = document.createElement('table');
    table.className = 'w-full border-collapse border border-gray-200 dark:border-dark-border';

    const totalCols = 3;
    const emptyCount = totalCols - list.length;

    // Header row: images + basic info
    const headerRow = document.createElement('tr');

    const thLabel = document.createElement('th');
    thLabel.className = 'w-40 bg-gray-50 dark:bg-dark-card border border-gray-200 dark:border-dark-border p-4 text-left font-semibold text-gray-900 dark:text-white align-top';
    thLabel.textContent = 'Product';
    headerRow.appendChild(thLabel);

    list.forEach(product => {
      const td = document.createElement('td');
      td.className = 'border border-gray-200 dark:border-dark-border p-6 align-top w-1/3';
      const inCart = isInCart(product);
      td.innerHTML = `
        <div class="relative">
          <button class="btn-remove-compare absolute -top-2 -right-2 text-gray-400 hover:text-red-500 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-full w-8 h-8 flex items-center justify-center shadow-sm text-xs" data-id="${product.id}">
            🗑️
          </button>
          <div class="h-48 bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden mb-4">
            ${product.image
              ? `<img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover" />`
              : `<div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">No Image</div>`}
          </div>
          <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2">${product.name}</h3>
          <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">${product.category || ''}</p>
          ${product.price ? `<p class="text-xl font-bold text-primary-700 mb-4">NRS ${formatNumber(product.price)}</p>` : ''}
          <button class="btn-add-cart w-full py-2.5 rounded-lg font-semibold flex items-center justify-center gap-2 transition-colors ${inCart ? 'bg-green-100 text-green-700' : 'bg-primary-600 text-white hover:bg-primary-700'}" data-id="${product.id}" data-slug="${product.slug || ''}">
            ${inCart ? '✅ In Quote Cart' : '🛒 Add to Quote'}
          </button>
        </div>
      `;
      headerRow.appendChild(td);
    });

    // Fill empty slots
    for (let i = 0; i < emptyCount; i++) {
      const td = document.createElement('td');
      td.className = 'border border-gray-200 dark:border-dark-border p-6 align-middle text-center bg-gray-50 dark:bg-dark-card w-1/3';
      td.innerHTML = `
        <div class="border-2 border-dashed border-gray-300 dark:border-dark-border rounded-xl h-48 flex items-center justify-center text-gray-400 flex-col mb-4 bg-white dark:bg-dark-surface">
          <span class="text-sm font-medium mb-2">Add another product</span>
          <a href="/products" class="text-primary-600 font-semibold hover:underline text-sm">Browse</a>
        </div>
      `;
      headerRow.appendChild(td);
    }

    // Description row
    const makeRow = (label, cells) => {
      const row = document.createElement('tr');
      const th = document.createElement('th');
      th.className = 'bg-gray-50 dark:bg-dark-card border border-gray-200 dark:border-dark-border p-4 text-left font-semibold text-gray-900 dark:text-white align-top text-sm';
      th.textContent = label;
      row.appendChild(th);
      cells.forEach(html => {
        const td = document.createElement('td');
        td.className = 'border border-gray-200 dark:border-dark-border p-4 align-top text-gray-600 dark:text-gray-300 text-sm';
        td.innerHTML = html;
        row.appendChild(td);
      });
      for (let i = 0; i < emptyCount; i++) {
        const td = document.createElement('td');
        td.className = 'border border-gray-200 dark:border-dark-border p-4 bg-gray-50 dark:bg-dark-card';
        row.appendChild(td);
      }
      return row;
    };

    const tbody = document.createElement('tbody');
    tbody.appendChild(headerRow);
    tbody.appendChild(makeRow('Description', list.map(p => p.description || '<span class="text-gray-400">N/A</span>')));
    tbody.appendChild(makeRow('Specifications', list.map(p => p.specifications
      ? p.specifications.split('\\n').join('<br/>')
      : '<span class="text-gray-400">N/A</span>'
    )));

    table.appendChild(tbody);
    inner.appendChild(table);
    scroll.appendChild(inner);

    container.innerHTML = '';
    container.appendChild(toolbar);
    container.appendChild(scroll);

    // Event delegation
    container.querySelector('#compare-clear-all').onclick = () => {
      saveJSON(COMPARE_KEY, []);
      updatePageState();
      window.dispatchEvent(new Event('storage'));
    };

    container.querySelectorAll('.btn-remove-compare').forEach(btn => {
      btn.onclick = () => {
        const id = btn.dataset.id;
        const product = list.find(p => String(p.id) === String(id));
        if (product) {
          toggleCompare(product);
        }
      };
    });

    container.querySelectorAll('.btn-add-cart').forEach(btn => {
      btn.onclick = () => {
        const productId = btn.dataset.id;
        const product = list.find(p => String(p.id) === String(productId));
        if (!product) return;
        addToCart(product, 1);
      };
    });
  };

  const createWhatsAppWidget = () => {
    if (document.getElementById('whatsapp-widget')) return;
    const widget = document.createElement('div');
    widget.id = 'whatsapp-widget';
    widget.className = 'whatsapp-widget';
    widget.innerHTML = `
      <div class="whatsapp-panel" aria-hidden="true">
        <div class="whatsapp-header">
          <div>
            <strong>Meditrust Nepal</strong>
            <p>Chat with us on WhatsApp</p>
          </div>
          <button type="button" class="whatsapp-close" aria-label="Close chat">×</button>
        </div>
        <form class="whatsapp-form">
          <textarea name="message" placeholder="Ask us about equipment, quotes or delivery." required></textarea>
          <button type="submit">Send</button>
        </form>
      </div>
      <button type="button" class="whatsapp-toggle" aria-label="Open WhatsApp chat">💬</button>
    `;
    document.body.appendChild(widget);
    const panel = widget.querySelector('.whatsapp-panel');
    const toggle = widget.querySelector('.whatsapp-toggle');
    const close = widget.querySelector('.whatsapp-close');
    const form = widget.querySelector('.whatsapp-form');
    const setOpen = (value) => {
      widget.dataset.open = value ? 'true' : 'false';
      panel.setAttribute('aria-hidden', value ? 'false' : 'true');
    };
    toggle.onclick = () => setOpen(true);
    close.onclick = () => setOpen(false);
    form.onsubmit = (event) => {
      event.preventDefault();
      const message = form.elements.message.value.trim();
      if (!message) return;
      const url = `https://wa.me/${WA_PHONE}?text=${encodeURIComponent(message)}`;
      window.open(url, '_blank');
      showToast('Opening WhatsApp chat...');
      setOpen(false);
      form.reset();
    };
  };

  const createBackToTop = () => {
    let button = document.getElementById('back-to-top');
    if (!button) {
      button = document.createElement('button');
      button.id = 'back-to-top';
      button.type = 'button';
      button.textContent = '↑';
      button.className = 'back-to-top';
      button.style.display = 'none';
      document.body.appendChild(button);
      button.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    window.addEventListener('scroll', () => {
      button.style.display = window.scrollY > 360 ? 'flex' : 'none';
    });
  };

  const createNewsletterPopup = () => {
    const last = Number(localStorage.getItem(NEWSLETTER_KEY) || 0);
    if (last && Date.now() - last < 14 * 24 * 60 * 60 * 1000) return;
    if (document.getElementById('newsletter-popup')) return;

    let popup = null;
    const buildPopup = () => {
      if (popup) return popup;
      popup = document.createElement('div');
      popup.id = 'newsletter-popup';
      popup.className = 'newsletter-popup';
      popup.innerHTML = `
        <div class="newsletter-card relative bg-white dark:bg-dark-surface p-6 md:p-8 rounded-2xl shadow-2xl max-w-md w-[calc(100%-2rem)] mx-auto animate-fade-in-up border border-gray-100 dark:border-dark-border text-center flex flex-col gap-4">
          <button type="button" class="newsletter-close absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-white transition" aria-label="Close">✕</button>
          <div class="newsletter-content flex flex-col gap-3">
            <div class="text-4xl mb-2">🏥</div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Stay Updated on Medical Equipment</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Get notified about new arrivals, exclusive offers, and healthcare equipment tips for Nepal's hospitals and clinics.</p>
            <form class="newsletter-form flex flex-col gap-3 mt-2">
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">✈️</span>
                <input name="phone" type="tel" placeholder="Your phone or Telegram number" class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 dark:border-dark-border bg-gray-50 dark:bg-dark-card focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition text-sm" required />
              </div>
              <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 rounded-xl shadow-sm hover:shadow-md transition">Subscribe via Telegram →</button>
            </form>
            <button type="button" class="newsletter-close mt-2 text-xs text-gray-400 hover:text-gray-600 transition underline">No thanks, I'll browse without updates</button>
          </div>
        </div>
      `;
      const close = popup.querySelector('.newsletter-close');
      const form = popup.querySelector('.newsletter-form');
      const dismiss = () => {
        if (popup) popup.remove();
        localStorage.setItem(NEWSLETTER_KEY, String(Date.now()));
      };
      close.onclick = dismiss;
      form.onsubmit = async (event) => {
        event.preventDefault();
        const phone = form.elements.phone.value.trim();
        if (!phone) return;
        try {
          await fetch(`${API_BASE}/notify/newsletter`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: 'Subscriber', phone }),
          });
        } catch (_err) {
          // ignore failure
        }
        showToast("You're subscribed! We'll reach you on Telegram.", 'success');
        dismiss();
      };
      return popup;
    };

    const show = () => {
      const node = buildPopup();
      document.body.appendChild(node);
    };

    const leaveHandler = (e) => {
      if (e.clientY <= 0) {
        show();
        window.removeEventListener('mouseleave', leaveHandler);
      }
    };
    window.addEventListener('mouseleave', leaveHandler);
    setTimeout(() => show(), 35000);
  };

  const createAbandonedCartPopup = () => {
    if (document.getElementById('abandoned-cart-popup')) return;
    const cart = getCart();
    if (!cart.length) return;
    const shown = { value: false };
    const show = () => {
      if (shown.value) return;
      shown.value = true;
      const popup = document.createElement('div');
      popup.id = 'abandoned-cart-popup';
      popup.className = 'abandoned-cart-popup';
      const details = cart.map((item, idx) => `<div class="abandoned-item"><span>${idx + 1}.</span> ${item.name}</div>`).join('');
      popup.innerHTML = `
        <div class="abandoned-card">
          <button type="button" class="abandoned-close">×</button>
          <h3>Wait — don't leave yet!</h3>
          <p>You have <strong>${cart.length} item${cart.length !== 1 ? 's' : ''}</strong> in your quote list.</p>
          <div class="abandoned-items">${details}</div>
          <div class="abandoned-actions">
            <a href="https://wa.me/${WA_PHONE}?text=${encodeURIComponent('Hello Meditrust Nepal! I had ' + cart.length + ' item(s) in my cart. Please help me complete my order.') }" target="_blank" rel="noopener noreferrer" class="button-primary">Get Quote via WhatsApp</a>
            <a href="/cart" class="button-secondary">View Cart</a>
          </div>
        </div>
      `;
      document.body.appendChild(popup);
      popup.querySelector('.abandoned-close').onclick = () => popup.remove();
    };
    const onLeave = (e) => {
      if (e.clientY <= 0 && !shown.value) show();
    };
    window.addEventListener('mouseleave', onLeave);
  };

  const createAnnouncementBar = async () => {
    try {
      const response = await fetch(`${API_BASE}/banners?placement=announcement`);
      const data = await response.json();
      const banners = data.banners || [];
      if (!banners.length) return;
      const banner = banners[0];
      const bar = document.createElement('div');
      bar.className = 'announcement-bar';
      bar.innerHTML = `
        <div class="announcement-inner">
          <span>${banner.title || 'Stay updated with Meditrust Nepal'}</span>
          ${banner.linkUrl ? `<a href="${banner.linkUrl}" target="${banner.linkUrl.startsWith('http') ? '_blank' : '_self'}" rel="noopener noreferrer">${banner.linkLabel || 'Learn more →'}</a>` : ''}
          <button type="button" class="announcement-close" aria-label="Dismiss announcement">×</button>
        </div>
      `;
      document.body.prepend(bar);
      bar.querySelector('.announcement-close').onclick = () => bar.remove();
    } catch (err) {
      // no announcement
    }
  };

  const handleAddParam = async () => {
    const params = new URLSearchParams(window.location.search);
    const addSlug = params.get('add');
    if (!addSlug) return;
    
    // Remove the param without reloading
    window.history.replaceState({}, document.title, window.location.pathname);
    
    try {
      const res = await fetch(`${API_BASE}/products/${encodeURIComponent(addSlug)}`);
      const data = await res.json();
      const product = data.product || data;
      if (!product || (!product.id && !product.slug)) return;
      
      const path = window.location.pathname;
      if (path.includes('/wishlist')) {
        if (!isInWishlist(product)) toggleWishlist(product);
      } else if (path.includes('/compare')) {
        if (!isInCompare(product)) toggleCompare(product);
      } else if (path.includes('/cart')) {
        addToCart(product, 1);
      }
    } catch (err) {
      console.error('Error fetching product for ?add=', err);
    }
  };

  const renderRecentlyViewed = () => {
    const container = document.getElementById('recently-viewed-list');
    if (!container) return;
    const RECENTLY_VIEWED_KEY = 'mt_recently_viewed';
    let list = loadJSON(RECENTLY_VIEWED_KEY, []);
    
    const detail = document.querySelector('[data-product-detail]');
    if (detail) {
      const product = parseJSON(detail.dataset.productDetail, null);
      if (product) {
        list = list.filter(p => p.slug !== product.slug);
      }
    }
    
    if (!list.length) {
      container.innerHTML = '';
      return;
    }

    container.innerHTML = `
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Recently Viewed</h2>
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 sm:gap-6">
        ${list.map(rel => `
          <a href="/products/${rel.slug}" class="bg-white rounded-xl shadow-sm hover:shadow-md border border-gray-100 p-4 transition-all block dark:bg-dark-card dark:border-dark-border">
            <div class="aspect-square bg-gray-50 rounded-lg mb-3 overflow-hidden border border-gray-100 flex items-center justify-center dark:bg-gray-800">
              ${rel.image 
                ? `<img src="${rel.image}" alt="${rel.name}" class="w-full h-full object-cover" />`
                : `<span class="text-gray-400 text-xs">No image</span>`
              }
            </div>
            <h3 class="font-semibold text-gray-900 text-sm line-clamp-2 dark:text-white">${rel.name || 'Product'}</h3>
            ${rel.price ? `<p class="text-primary-700 font-bold mt-1 text-sm">NRS ${formatNumber(rel.price)}</p>` : ''}
          </a>
        `).join('')}
      </div>
    `;
  };

  const renderCompareDrawer = () => {
    if (window.location.pathname.includes('/compare')) {
      const existing = document.getElementById('compare-drawer');
      if (existing) existing.remove();
      return;
    }
    const list = getCompare();
    let drawer = document.getElementById('compare-drawer');
    
    if (!list.length) {
      if (drawer) drawer.remove();
      return;
    }
    
    if (!drawer) {
      drawer = document.createElement('div');
      drawer.id = 'compare-drawer';
      drawer.className = 'fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-[0_-4px_20px_rgba(0,0,0,0.1)] z-40 transform transition-transform duration-300 dark:bg-dark-surface dark:border-dark-border';
      document.body.appendChild(drawer);
    }
    
    drawer.innerHTML = `
      <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex-1 w-full flex items-center justify-start gap-4 overflow-x-auto pb-2 md:pb-0 no-scrollbar" id="compare-drawer-items">
            ${list.map(product => `
              <div class="relative flex items-center bg-gray-50 dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-lg p-2 min-w-[200px] max-w-[250px]">
                <button data-id="${product.id}" class="drawer-remove-btn absolute -top-2 -right-2 bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-xs hover:bg-red-600 shadow">×</button>
                <div class="w-12 h-12 bg-white rounded flex-shrink-0 overflow-hidden mr-3">
                  ${product.image ? `<img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover" />` : `<div class="w-full h-full flex items-center justify-center bg-gray-100">⚖️</div>`}
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">${product.name}</p>
                  ${product.price ? `<p class="text-xs text-primary-600 font-bold">NRS ${formatNumber(product.price)}</p>` : ''}
                </div>
              </div>
            `).join('')}
            ${list.length < 3 ? `
              <div class="flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-dark-border rounded-lg p-2 min-w-[200px] h-[66px] text-gray-400 text-sm font-medium">
                Add up to ${3 - list.length} more
              </div>
            ` : ''}
          </div>
          <div class="flex items-center gap-2 w-full md:w-auto">
            <button id="drawer-clear-btn" class="px-3 py-2 text-sm font-medium text-gray-500 hover:text-red-500 transition-colors whitespace-nowrap">Clear All</button>
            <a href="/compare" class="flex-1 md:flex-none px-6 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors flex items-center justify-center gap-2 ${list.length < 2 ? 'opacity-50 pointer-events-none' : ''}">
              ⚖️ Compare ${list.length} items
            </a>
          </div>
        </div>
      </div>
    `;

    drawer.querySelectorAll('.drawer-remove-btn').forEach(btn => {
      btn.onclick = () => {
        const product = list.find(p => String(p.id) === String(btn.dataset.id));
        if (product) toggleCompare(product);
      };
    });
    const clearBtn = drawer.querySelector('#drawer-clear-btn');
    if (clearBtn) {
      clearBtn.onclick = () => {
        saveJSON(COMPARE_KEY, []);
        updatePageState();
        window.dispatchEvent(new Event('storage'));
      };
    }
  };

  const createAiFinderWidget = () => {
    if (document.getElementById('ai-finder-widget')) return;
    const btn = document.createElement('button');
    btn.id = 'ai-finder-widget';
    btn.className = 'fixed bottom-24 right-4 z-40 flex items-center gap-2 bg-[#7CC62D] text-white px-4 py-2.5 rounded-full shadow-lg text-sm font-bold hover:bg-[#68a822] transition';
    btn.innerHTML = '🤖 AI Finder';
    document.body.appendChild(btn);

    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center hidden';
    modal.innerHTML = `
      <div class="absolute inset-0 bg-black/40" id="ai-finder-backdrop"></div>
      <div class="relative bg-white dark:bg-dark-surface w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden flex flex-col mx-4" style="max-height: 85vh;">
        <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-gray-100 dark:border-dark-border">
          <div class="flex items-center gap-2">
            <span class="text-xl">🤖</span>
            <div>
              <h3 class="text-sm font-bold text-gray-900 dark:text-white">AI Product Finder</h3>
              <p class="text-xs text-gray-500">Describe what you need</p>
            </div>
          </div>
          <button id="ai-finder-close" class="text-gray-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold leading-none">×</button>
        </div>
        <div class="overflow-y-auto p-5 flex-1">
          <div class="flex gap-2">
            <input type="text" id="ai-finder-input" placeholder="e.g. ventilator for ICU..." class="flex-1 text-sm px-3 py-2 rounded-lg border border-primary-200 bg-white dark:bg-dark-card dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-400 placeholder-gray-400" />
            <button id="ai-finder-search" class="px-3 py-2 bg-primary-600 text-white rounded-lg text-sm font-semibold hover:bg-primary-700 transition">Find</button>
          </div>
          <div id="ai-finder-suggestions" class="mt-3 flex flex-wrap gap-1.5">
            ${['ICU ventilator', 'Portable ECG machine', 'Surgical instruments', 'Patient monitor'].map(s => 
              `<button class="ai-suggestion-btn text-xs bg-white dark:bg-dark-card border border-primary-200 text-primary-700 dark:text-primary-400 px-2.5 py-1 rounded-full hover:bg-primary-50 transition">${s}</button>`
            ).join('')}
          </div>
          <div id="ai-finder-results" class="mt-4 space-y-2 hidden"></div>
        </div>
      </div>
    `;
    document.body.appendChild(modal);

    const close = () => modal.classList.add('hidden');
    btn.onclick = () => { modal.classList.remove('hidden'); document.getElementById('ai-finder-input').focus(); };
    document.getElementById('ai-finder-backdrop').onclick = close;
    document.getElementById('ai-finder-close').onclick = close;

    const input = document.getElementById('ai-finder-input');
    const searchBtn = document.getElementById('ai-finder-search');
    const resultsContainer = document.getElementById('ai-finder-results');
    const suggestionsContainer = document.getElementById('ai-finder-suggestions');

    const search = async (query) => {
      query = query.trim();
      if (!query) return;
      searchBtn.disabled = true;
      searchBtn.textContent = '...';
      suggestionsContainer.classList.add('hidden');
      resultsContainer.classList.remove('hidden');
      resultsContainer.innerHTML = '<div class="animate-pulse bg-gray-100 dark:bg-dark-card rounded-lg h-14 w-full"></div>';

      try {
        const res = await fetch(\`\${API_BASE}/ai/finder\`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ query }),
        });
        const data = await res.json();
        if (data.results && data.results.length > 0) {
          resultsContainer.innerHTML = \`
            <p class="text-xs text-gray-500 font-medium">Best matches:</p>
            \${data.results.map(r => \`
              <a href="/products/\${r.slug}" class="flex items-start gap-3 bg-white dark:bg-dark-card rounded-xl p-3 border border-primary-100 dark:border-dark-border hover:border-primary-300 hover:shadow-sm transition group">
                \${r.image ? \`<img src="\${r.image}" class="w-12 h-12 rounded-lg object-contain flex-shrink-0 bg-gray-50 dark:bg-gray-800" />\` : ''}
                <div class="flex-1 min-w-0">
                  <p class="text-xs text-primary-600 font-medium">\${r.category}</p>
                  <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition truncate">\${r.name}</p>
                  <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">\${r.reason}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                  \${r.price ? \`<p class="text-xs font-bold text-primary-700">NPR \${formatNumber(r.price)}</p>\` : ''}
                  <span class="text-[10px] font-semibold text-green-600 bg-green-50 px-1.5 py-0.5 rounded-full">\${r.match}%</span>
                </div>
              </a>
            \`).join('')}
            <button id="ai-finder-reset" class="text-xs text-gray-400 hover:text-gray-600 underline mt-1 block">Search again</button>
          \`;
          document.getElementById('ai-finder-reset').onclick = () => {
            input.value = '';
            resultsContainer.classList.add('hidden');
            suggestionsContainer.classList.remove('hidden');
            input.focus();
          };
        } else {
          resultsContainer.innerHTML = '<p class="text-xs text-gray-500 mt-3 text-center">No matches found.</p>';
        }
      } catch (e) {
        resultsContainer.innerHTML = '<p class="text-xs text-red-500 mt-3">Could not get recommendations. Please try again.</p>';
      } finally {
        searchBtn.disabled = false;
        searchBtn.textContent = 'Find';
      }
    };

    searchBtn.onclick = () => search(input.value);
    input.onkeydown = (e) => e.key === 'Enter' && search(input.value);
    modal.querySelectorAll('.ai-suggestion-btn').forEach(b => {
      b.onclick = () => { input.value = b.textContent; search(b.textContent); };
    });
  };

  const init = () => {
    applyTheme(getTheme());
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.addEventListener('click', toggleTheme);
    });
    hydrateProductCards();
    hydrateProductDetail();
    renderComparePage();
    renderWishlistPage();
    renderRecentlyViewed();
    renderCompareDrawer();
    createAiFinderWidget();
    createAnnouncementBar();
    createNewsletterPopup();
    createAbandonedCartPopup();
    createWhatsAppWidget();
    createBackToTop();
    createMobileTabBar();
    window.addEventListener('resize', createMobileTabBar);
    
    handleAddParam();
  };

  document.addEventListener('DOMContentLoaded', init);
})();

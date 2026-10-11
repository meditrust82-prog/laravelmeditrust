const https = require('https');

const TELEGRAM_BOT_TOKEN = process.env.TELEGRAM_BOT_TOKEN || '7989226843:AAEw6t_QlIjwHQ8Y5DL7ETdjRj83xo0T-I0';
const TELEGRAM_CHAT_ID = process.env.TELEGRAM_CHAT_ID || '8746818071';

const escapeHtml = (str = '') =>
  String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

const nowNepal = () =>
  new Date().toLocaleString('en-US', {
    timeZone: 'Asia/Kathmandu',
    dateStyle: 'medium',
    timeStyle: 'short',
  });

/**
 * Sends an HTML message to the configured Telegram chat.
 * Fails safely without throwing errors that interrupt the main API request.
 */
const sendTelegram = (text) => {
  if (!TELEGRAM_BOT_TOKEN || !TELEGRAM_CHAT_ID) {
    console.warn('[Telegram] Token or Chat ID missing');
    return Promise.resolve(false);
  }

  const payload = JSON.stringify({
    chat_id: TELEGRAM_CHAT_ID,
    text,
    parse_mode: 'HTML',
    disable_web_page_preview: true,
  });

  return new Promise((resolve) => {
    const req = https.request(
      {
        hostname: 'api.telegram.org',
        path: `/bot${TELEGRAM_BOT_TOKEN}/sendMessage`,
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Content-Length': Buffer.byteLength(payload),
        },
        timeout: 8000,
      },
      (res) => {
        let resData = '';
        res.on('data', (chunk) => { resData += chunk; });
        res.on('end', () => {
          if (res.statusCode >= 200 && res.statusCode < 300) {
            resolve(true);
          } else {
            console.warn(`[Telegram] HTTP ${res.statusCode}: ${resData}`);
            resolve(false);
          }
        });
      }
    );

    req.on('timeout', () => {
      req.destroy();
      console.warn('[Telegram] Request timeout');
      resolve(false);
    });

    req.on('error', (err) => {
      console.warn('[Telegram] Network error:', err.message);
      resolve(false);
    });

    req.write(payload);
    req.end();
  });
};

/**
 * 1. Newsletter / Subscriber notification
 */
const notifySubscriber = async ({ name, phone, email, source }) => {
  const message = [
    `📬 <b>NEW NEWSLETTER SUBSCRIBER</b>`,
    `━━━━━━━━━━━━━━━━━━`,
    `👤 <b>Name:</b> ${escapeHtml(name)}`,
    `📞 <b>Phone:</b> <code>${escapeHtml(phone)}</code>`,
    email ? `📧 <b>Email:</b> ${escapeHtml(email)}` : null,
    `🌐 <b>Source:</b> ${escapeHtml(source || 'Website')}`,
    `🕒 <b>Time:</b> ${nowNepal()}`,
    ``,
    `🔗 <a href="https://www.meditrustnepal.com/admin/subscribers">View in Admin Panel</a>`,
  ].filter(Boolean).join('\n');

  return sendTelegram(message);
};

/**
 * 2. Quote Request notification
 */
const notifyQuote = async ({ productName, productSlug, qty, name, hospitalName, phone, email, message, source }) => {
  const messageText = [
    `🏥 <b>NEW QUOTE REQUEST</b>`,
    `━━━━━━━━━━━━━━━━━━`,
    `📦 <b>Product:</b> ${escapeHtml(productName || 'General Inquiry')}`,
    qty ? `🔢 <b>Quantity:</b> ${qty}` : null,
    `👤 <b>Contact:</b> ${escapeHtml(name)}`,
    hospitalName ? `🏥 <b>Hospital / Clinic:</b> ${escapeHtml(hospitalName)}` : null,
    `📞 <b>Phone:</b> <code>${escapeHtml(phone)}</code>`,
    email ? `📧 <b>Email:</b> ${escapeHtml(email)}` : null,
    message ? `💬 <b>Requirement:</b> <i>${escapeHtml(message)}</i>` : null,
    `🌐 <b>Source:</b> ${escapeHtml(source || 'Website')}`,
    `🕒 <b>Time:</b> ${nowNepal()}`,
    ``,
    `🔗 <a href="https://www.meditrustnepal.com/admin/quotes">Manage in Admin Quotes</a>`,
  ].filter(Boolean).join('\n');

  return sendTelegram(messageText);
};

/**
 * 3. Review / Testimonial notification
 */
const notifyReview = async ({ name, position, organization, rating, content, source, visible }) => {
  const stars = '⭐'.repeat(Math.max(1, Math.min(5, Number(rating) || 5)));
  const statusBadge = visible ? '🟢 Published' : '🟡 Pending Review';

  const messageText = [
    `⭐ <b>NEW REVIEW / TESTIMONIAL</b>`,
    `━━━━━━━━━━━━━━━━━━`,
    `👤 <b>Author:</b> ${escapeHtml(name)}`,
    position || organization ? `💼 <b>Role:</b> ${escapeHtml([position, organization].filter(Boolean).join(' at '))}` : null,
    `🌟 <b>Rating:</b> ${stars} (${rating || 5}/5)`,
    `💬 <b>Review:</b>\n"<i>${escapeHtml(content)}</i>"`,
    `🏷️ <b>Status:</b> ${statusBadge}`,
    source ? `🌐 <b>Source:</b> ${escapeHtml(source)}` : null,
    `🕒 <b>Time:</b> ${nowNepal()}`,
    ``,
    `🔗 <a href="https://www.meditrustnepal.com/admin/reviews">Manage in Admin Reviews</a>`,
  ].filter(Boolean).join('\n');

  return sendTelegram(messageText);
};

/**
 * 4. Order notification
 */
const notifyOrder = async ({ orderId, user, items, totalPrice, shippingAddress, notes }) => {
  const itemCount = Array.isArray(items) ? items.reduce((sum, it) => sum + (it.quantity || 1), 0) : 1;
  const itemsSummary = Array.isArray(items)
    ? items.slice(0, 3).map((it) => `• ${escapeHtml(it.product?.name || it.name || 'Item')} × ${it.quantity || 1}`).join('\n')
    : '';

  const messageText = [
    `🛒 <b>NEW ORDER RECEIVED</b>`,
    `━━━━━━━━━━━━━━━━━━`,
    `🆔 <b>Order ID:</b> <code>${String(orderId)}</code>`,
    `💰 <b>Total Amount:</b> NPR ${Number(totalPrice).toLocaleString()}`,
    `📦 <b>Total Items:</b> ${itemCount}`,
    itemsSummary ? `\n<b>Items:</b>\n${itemsSummary}${items.length > 3 ? `\n<i>...and ${items.length - 3} more</i>` : ''}` : null,
    user?.name || user?.email ? `👤 <b>Customer:</b> ${escapeHtml(user.name || user.email)}` : null,
    user?.phone ? `📞 <b>Phone:</b> <code>${escapeHtml(user.phone)}</code>` : null,
    shippingAddress?.city ? `📍 <b>Delivery:</b> ${escapeHtml([shippingAddress.street, shippingAddress.city].filter(Boolean).join(', '))}` : null,
    notes ? `📝 <b>Notes:</b> <i>${escapeHtml(notes)}</i>` : null,
    `🕒 <b>Time:</b> ${nowNepal()}`,
    ``,
    `🔗 <a href="https://www.meditrustnepal.com/admin/orders">View in Admin Orders</a>`,
  ].filter(Boolean).join('\n');

  return sendTelegram(messageText);
};

module.exports = {
  sendTelegram,
  notifySubscriber,
  notifyQuote,
  notifyReview,
  notifyOrder,
};

const router = require('express').Router();
const Subscriber = require('../models/Subscriber');
const { protect, adminOnly } = require('../middlewares/auth');
const { sendTelegram, notifySubscriber, notifyQuote } = require('../utils/telegram');

// POST /api/v1/notify/newsletter  — public
router.post('/newsletter', async (req, res) => {
  try {
    const { name, phone, email, source } = req.body;
    if (!name || !phone) return res.status(400).json({ error: 'name and phone required' });
    // Upsert — prevent duplicates by phone
    await Subscriber.findOneAndUpdate(
      { phone },
      { name, phone, email, source: source || 'home_newsletter', active: true },
      { upsert: true, new: true }
    );
    notifySubscriber({ name, phone, email, source }).catch((err) => {
      console.warn('[Telegram Subscriber Notify]', err.message);
    });
    res.json({ ok: true });
  } catch (err) {
    console.error('Newsletter error:', err);
    res.status(500).json({ error: 'Failed to subscribe' });
  }
});

// GET /api/v1/notify/subscribers  — admin only
router.get('/subscribers', protect, adminOnly, async (req, res) => {
  try {
    const { page = 1, limit = 100 } = req.query;
    const [subscribers, total] = await Promise.all([
      Subscriber.find().sort({ createdAt: -1 }).skip((page - 1) * limit).limit(Number(limit)).lean(),
      Subscriber.countDocuments(),
    ]);
    res.json({ subscribers, total });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

// DELETE /api/v1/notify/subscribers/:id  — admin only
router.delete('/subscribers/:id', protect, adminOnly, async (req, res) => {
  try {
    await Subscriber.findByIdAndDelete(req.params.id);
    res.json({ ok: true });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

// POST /api/v1/notify/quote  (compatibility quote alert)
router.post('/quote', async (req, res) => {
  try {
    const { name, phone, product, message } = req.body;
    if (!name || !phone) return res.status(400).json({ error: 'name and phone required' });
    notifyQuote({
      productName: product,
      name,
      phone,
      message,
      source: 'Notify API',
    }).catch((err) => {
      console.warn('[Telegram Quote Notify]', err.message);
    });
    res.json({ ok: true });
  } catch (err) {
    console.error('Telegram notify error:', err);
    res.status(500).json({ error: 'Failed to send notification' });
  }
});

module.exports = { router, sendTelegram };

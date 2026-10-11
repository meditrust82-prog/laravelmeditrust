const router = require('express').Router();
const { body, param } = require('express-validator');
const rateLimit = require('express-rate-limit');
const { protect, adminOnly } = require('../middlewares/auth');
const validate = require('../middlewares/validate');
const Testimonial = require('../models/Testimonial');
const { notifyReview } = require('../utils/telegram');

const adminGuard = [protect, adminOnly];

const reviewLimiter = rateLimit({
  windowMs: 60 * 1000,
  max: 5,
  standardHeaders: true,
  legacyHeaders: false,
  message: { error: 'Too many reviews submitted. Please wait a minute.' },
});

// GET /api/v1/testimonials  — public, only visible ones
router.get('/', async (req, res) => {
  try {
    const all = req.query.all === '1'; // admin: fetch all including hidden
    const filter = all ? {} : { visible: true };
    const testimonials = await Testimonial.find(filter)
      .sort({ order: 1, createdAt: -1 })
      .lean();
    res.json({ testimonials });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// POST /api/v1/testimonials/submit — public review submission
router.post('/submit', reviewLimiter, [
  body('name').trim().notEmpty().withMessage('Name is required').isLength({ max: 100 }),
  body('content').trim().notEmpty().withMessage('Review text is required').isLength({ max: 1000 }),
  body('rating').optional().isInt({ min: 1, max: 5 }),
  validate,
], async (req, res) => {
  try {
    const { name, position, organization, content, rating, source } = req.body;
    const testimonial = await Testimonial.create({
      name,
      position: position || '',
      organization: organization || '',
      content,
      rating: Number(rating) || 5,
      source: source || 'Customer Website Review',
      visible: false, // requires admin approval in /admin/reviews
      order: 0,
    });

    notifyReview({
      name,
      position,
      organization,
      content,
      rating: Number(rating) || 5,
      source: source || 'Customer Website Review',
      visible: false,
    }).catch((err) => {
      console.warn('[Telegram Review Notify]', err.message);
    });

    res.status(201).json({ ok: true, testimonialId: testimonial._id, message: 'Thank you! Your review has been submitted for verification.' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// POST /api/v1/testimonials  — admin only
router.post('/', adminGuard, [
  body('name').trim().notEmpty().withMessage('Name is required').isLength({ max: 100 }),
  body('content').trim().notEmpty().withMessage('Review text is required').isLength({ max: 1000 }),
  body('rating').optional().isInt({ min: 1, max: 5 }),
  validate,
], async (req, res) => {
  try {
    const { name, position, organization, content, rating, photoUrl, source, visible, order } = req.body;
    const testimonial = await Testimonial.create({
      name, position, organization, content,
      rating: rating || 5,
      photoUrl, source,
      visible: visible !== false,
      order: order || 0,
    });

    notifyReview({
      name,
      position,
      organization,
      content,
      rating: rating || 5,
      source: source || 'Admin Panel',
      visible: visible !== false,
    }).catch((err) => {
      console.warn('[Telegram Review Notify]', err.message);
    });

    res.status(201).json({ testimonial });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// PUT /api/v1/testimonials/:id  — admin only
router.put('/:id', adminGuard, [
  param('id').isMongoId(),
  body('name').optional().trim().notEmpty().isLength({ max: 100 }),
  body('content').optional().trim().notEmpty().isLength({ max: 1000 }),
  body('rating').optional().isInt({ min: 1, max: 5 }),
  validate,
], async (req, res) => {
  try {
    const { name, position, organization, content, rating, photoUrl, source, visible, order } = req.body;
    const testimonial = await Testimonial.findByIdAndUpdate(
      req.params.id,
      { name, position, organization, content, rating, photoUrl, source, visible, order },
      { new: true, runValidators: true }
    );
    if (!testimonial) return res.status(404).json({ error: 'Testimonial not found' });
    res.json({ testimonial });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

// DELETE /api/v1/testimonials/:id  — admin only
router.delete('/:id', adminGuard, [
  param('id').isMongoId(), validate,
], async (req, res) => {
  try {
    await Testimonial.findByIdAndDelete(req.params.id);
    res.json({ ok: true });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
});

module.exports = router;

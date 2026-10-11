const router = require('express').Router();
const Blog = require('../models/Blog');
const { protect, adminOnly } = require('../middlewares/auth');
const multerUpload = require('../utils/multer');
const { uploadToCloudinary } = require('../utils/cloudinary');

const slugify = (s) => s.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');

// GET /api/v1/blogs  — public
router.get('/', async (req, res) => {
  try {
    const { page = 1, limit = 20, category } = req.query;
    const filter = { published: true };
    if (category) filter.category = category;
    const [blogs, total] = await Promise.all([
      Blog.find(filter).sort({ createdAt: -1 }).skip((page - 1) * limit).limit(Number(limit)).select('-content').lean(),
      Blog.countDocuments(filter),
    ]);
    res.json({ blogs, total });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

// GET /api/v1/blogs/all  — admin (includes unpublished)
router.get('/all', protect, adminOnly, async (req, res) => {
  try {
    const blogs = await Blog.find().sort({ createdAt: -1 }).lean();
    res.json({ blogs, total: blogs.length });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

// POST /api/v1/blogs/upload — admin media/image upload
router.post('/upload', protect, adminOnly, (req, res, next) => {
  multerUpload.any()(req, res, (err) => {
    if (err) {
      return res.status(400).json({ error: err.message || 'Image upload error' });
    }
    next();
  });
}, async (req, res) => {
  try {
    const file = req.files?.[0] || req.file;
    if (!file) {
      return res.status(400).json({ error: 'No image file provided' });
    }
    const uploaded = await uploadToCloudinary([file]);
    const url = uploaded[0]?.url || uploaded[0]?.path;
    if (!url) {
      return res.status(500).json({ error: 'Failed to upload image to Cloudinary' });
    }
    res.json({ url, secure_url: url });
  } catch (err) {
    res.status(500).json({ error: err.message || 'Image upload failed' });
  }
});

// GET /api/v1/blogs/:slug  — public
router.get('/:slug', async (req, res) => {
  try {
    const blog = await Blog.findOne({ slug: req.params.slug, published: true }).lean();
    if (!blog) return res.status(404).json({ error: 'Post not found' });
    res.json(blog);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

// POST /api/v1/blogs  — admin
router.post('/', protect, adminOnly, async (req, res) => {
  try {
    const data = { ...req.body };
    if (!data.title) return res.status(400).json({ error: 'Title is required' });
    data.slug = data.slug || slugify(data.title);
    const blog = await Blog.create(data);
    res.status(201).json(blog);
  } catch (err) {
    if (err.code === 11000) return res.status(400).json({ error: 'A post with this title already exists' });
    res.status(500).json({ error: err.message });
  }
});

// PUT /api/v1/blogs/:id  — admin
router.put('/:id', protect, adminOnly, async (req, res) => {
  try {
    const data = { ...req.body };
    if (data.title && !data.slug) {
      data.slug = slugify(data.title);
    }
    const blog = await Blog.findByIdAndUpdate(req.params.id, data, { new: true, runValidators: true });
    if (!blog) return res.status(404).json({ error: 'Post not found' });
    res.json(blog);
  } catch (err) { res.status(500).json({ error: err.message }); }
});

// DELETE /api/v1/blogs/:id  — admin
router.delete('/:id', protect, adminOnly, async (req, res) => {
  try {
    await Blog.findByIdAndDelete(req.params.id);
    res.json({ ok: true });
  } catch (err) { res.status(500).json({ error: err.message }); }
});

module.exports = router;

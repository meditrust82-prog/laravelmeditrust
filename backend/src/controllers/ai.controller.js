const Product = require('../models/Product');

const GROQ_URL = 'https://api.groq.com/openai/v1/chat/completions';
const CANDIDATE_MODELS = [
  process.env.GROQ_MODEL || 'qwen/qwen3.6-27b',
  'openai/gpt-oss-120b',
  'openai/gpt-oss-20b',
];

const cleanKey = (key) => (key || '').trim().replace(/^["']+|["']+$/g, '');

const modelPayload = (model, payload) => {
  const options = { ...payload, model };
  const reasoningEffort = process.env.GROQ_REASONING_EFFORT;

  if (model.startsWith('openai/gpt-oss')) {
    options.include_reasoning = false;
    options.reasoning_effort = reasoningEffort || 'low';
  } else if (model.startsWith('qwen/')) {
    options.reasoning_effort = reasoningEffort || 'none';
    options.reasoning_format = 'hidden';
  }

  return options;
};

const callGroqWithFallback = async (apiKey, payload) => {
  const token = cleanKey(apiKey);
  let lastError = 'Groq error';
  let lastStatus = 500;

  for (const model of CANDIDATE_MODELS) {
    try {
      const response = await fetch(GROQ_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify(modelPayload(model, payload)),
      });
      const data = await response.json();
      if (response.ok && data.choices?.[0]) return { ok: true, data };

      lastStatus = response.status || 500;
      lastError = data.error?.message || data.error || 'Groq error';
      if (lastStatus === 401 || (typeof lastError === 'string' && lastError.toLowerCase().includes('invalid api key'))) break;
    } catch (error) {
      lastError = error.message;
    }
  }

  return { ok: false, status: lastStatus, error: lastError };
};

const groqCall = async (apiKey, messages, max_tokens = 800, temperature = 0.5) => {
  const result = await callGroqWithFallback(apiKey, { messages, max_tokens, temperature });
  if (!result.ok) throw new Error(result.error);
  return result.data.choices?.[0]?.message?.content || '';
};

const chat = async (req, res, next) => {
  try {
    const apiKey = process.env.GROQ_API_KEY;
    if (!apiKey) return res.status(503).json({ error: 'AI service not configured' });
    const { messages, temperature = 0.6, max_tokens = 800 } = req.body;
    if (!Array.isArray(messages) || messages.length === 0) return res.status(400).json({ error: 'messages array is required' });

    const result = await callGroqWithFallback(apiKey, { messages, temperature, max_tokens });
    if (!result.ok) return res.status(result.status).json({ error: result.error });
    res.json(result.data);
  } catch (error) {
    next(error);
  }
};

const recommend = async (req, res, next) => {
  try {
    const apiKey = process.env.GROQ_API_KEY;
    if (!apiKey) return res.status(503).json({ error: 'AI service not configured' });
    const { productSlug, category, userContext } = req.body;

    let candidates = [];
    if (category) {
      candidates = await Product.find(
        {
          category: { $regex: `^${category.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}$`, $options: 'i' },
          ...(productSlug ? { slug: { $ne: productSlug } } : {}),
        },
        'name slug category price description brand badges stock quantity'
      ).limit(30).lean();
    }
    if (candidates.length < 6) {
      const extra = await Product.find(
        {
          ...(productSlug ? { slug: { $ne: productSlug } } : {}),
          ...(candidates.length ? { _id: { $nin: candidates.map((candidate) => candidate._id) } } : {}),
        },
        'name slug category price description brand badges stock quantity'
      ).limit(30).lean();
      candidates = [...candidates, ...extra];
    }
    if (candidates.length === 0) return res.json({ recommendations: [] });

    const catalog = candidates.slice(0, 40).map((product, index) =>
      `[${index}] ${product.name} | Category: ${product.category || 'N/A'} | Price: ${product.price ? `NPR ${product.price}` : 'POA'} | Brand: ${product.brand || 'N/A'} | ${product.badges?.join(', ') || ''}`
    ).join('\n');
    const systemMsg = 'You are a medical equipment recommendation engine for Meditrust Nepal. Return exactly 3 recommendations as a JSON array with index, reason, and match.';
    const userMsg = `User context: ${userContext || 'browsing medical equipment'}\nCurrent product category: ${category || 'general'}\n\nCatalog:\n${catalog}\n\nReturn top 3 recommendations as JSON array only.`;
    const raw = await groqCall(apiKey, [{ role: 'system', content: systemMsg }, { role: 'user', content: userMsg }], 400, 0.3);

    let parsed = [];
    try {
      const match = raw.match(/\[[\s\S]*\]/);
      parsed = match ? JSON.parse(match[0]) : [];
    } catch { parsed = []; }
    const recommendations = parsed.filter((item) => typeof item.index === 'number' && candidates[item.index]).slice(0, 3).map((item) => {
      const product = candidates[item.index];
      return { name: product.name, slug: product.slug, category: product.category, price: product.price, brand: product.brand, image: product.images?.[0]?.url || product.images?.[0]?.path || null, reason: item.reason, match: item.match };
    });
    res.json({ recommendations });
  } catch (error) {
    next(error);
  }
};

const finder = async (req, res, next) => {
  try {
    const apiKey = process.env.GROQ_API_KEY;
    if (!apiKey) return res.status(503).json({ error: 'AI service not configured' });
    const { query } = req.body;
    if (!query?.trim()) return res.status(400).json({ error: 'query is required' });

    const allProducts = await Product.find({}, 'name slug category price description brand badges').limit(100).lean();
    const catalog = allProducts.map((product, index) => `[${index}] ${product.name} | ${product.category || 'N/A'} | ${product.price ? `NPR ${product.price}` : 'POA'} | ${product.brand || ''}`).join('\n');
    const raw = await groqCall(apiKey, [
      { role: 'system', content: 'You are a medical equipment finder. Return matching products as a JSON array with index, reason, and match.' },
      { role: 'user', content: `User need: "${query}"\n\nCatalog:\n${catalog}\n\nReturn best matches as JSON array.` },
    ], 500, 0.3);

    let parsed = [];
    try {
      const match = raw.match(/\[[\s\S]*\]/);
      parsed = match ? JSON.parse(match[0]) : [];
    } catch { parsed = []; }
    const results = parsed.filter((item) => typeof item.index === 'number' && allProducts[item.index]).slice(0, 4).map((item) => {
      const product = allProducts[item.index];
      return { name: product.name, slug: product.slug, category: product.category, price: product.price, brand: product.brand, image: product.images?.[0]?.url || product.images?.[0]?.path || null, reason: item.reason, match: item.match };
    });
    res.json({ results });
  } catch (error) {
    next(error);
  }
};

module.exports = { chat, recommend, finder };

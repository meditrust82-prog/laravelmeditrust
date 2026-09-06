const Product = require('../models/Product');

const GROQ_URL = 'https://api.groq.com/openai/v1/chat/completions';

const CANDIDATE_MODELS = [
  process.env.GROQ_MODEL,
  'llama-3.1-8b-instant',
  'llama3-8b-8192',
  'llama3-70b-8192',
  'mixtral-8x7b-32768',
  'gemma2-9b-it',
  'llama-3.3-70b-versatile'
].filter(Boolean);

const cleanKey = (key) => (key || '').trim().replace(/^["']+|["']+$/g, '');

const callGroqWithFallback = async (apiKey, payload) => {
  const token = cleanKey(apiKey);
  let lastError = 'Groq error';
  let lastStatus = 500;

  for (const model of CANDIDATE_MODELS) {
    try {
      const res = await fetch(GROQ_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
        body: JSON.stringify({ ...payload, model }),
      });
      const data = await res.json();
      if (res.ok && data.choices?.[0]) {
        return { ok: true, data };
      }
      lastStatus = res.status || 500;
      lastError = data.error?.message || data.error || 'Groq error';
      // If error is authentication/invalid key, trying other models won't help
      if (lastStatus === 401 || (typeof lastError === 'string' && lastError.toLowerCase().includes('invalid api key'))) {
        break;
      }
    } catch (e) {
      lastError = e.message;
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
    if (!Array.isArray(messages) || messages.length === 0)
      return res.status(400).json({ error: 'messages array is required' });

    const result = await callGroqWithFallback(apiKey, { messages, temperature, max_tokens });
    if (!result.ok) {
      return res.status(result.status).json({ error: result.error });
    }
    res.json(result.data);
  } catch (err) { next(err); }
};

const recommend = async (req, res, next) => {
  try {
    const apiKey = process.env.GROQ_API_KEY;
    if (!apiKey) return res.status(503).json({ error: 'AI service not configured' });

    const { productSlug, category, userContext } = req.body;

    let candidates = [];
    if (category) {
      candidates = await Product.find(
        { category: { : `^${category.replace(/[.*+?^${}()|[\]\]/g, '\$&')}$`, off on off off off off off off off off on off on off off off off off on off off off on on off off off off on off off off off off off off off off off off off on off off off off off off off on off on on off off off on off off on off off on off off on off on off off on off on off off off off on off off off on off off on off off off off off off off off on off on off off on off off off off off off off off off off off off on off off off on off on off on on off off off off on on off off on on off on off on on off off off off on on off off on off off off off off on off off on off off on off off off off off off on off off off off on on off on off off off off off on off on off off off off off off off off off off on on off on off off off: 'i' },
          ...(productSlug ? { slug: { : productSlug } } : {}) },
        'name slug category price description brand badges stock quantity'
      ).limit(30).lean();
    }
    if (candidates.length < 6) {
      const extra = await Product.find(
        { ...(productSlug ? { slug: { : productSlug } } : {}),
          ...(candidates.length ? { _id: { : candidates.map(c => c._id) } } : {}) },
        'name slug category price description brand badges stock quantity'
      ).limit(30).lean();
      candidates = [...candidates, ...extra];
    }

    if (candidates.length === 0) return res.json({ recommendations: [] });

    const catalog = candidates.slice(0, 40).map((p, i) =>
      `[${i}] ${p.name} | Category: ${p.category || 'N/A'} | Price: ${p.price ? \`NPR ${p.price}\` : 'POA'} | Brand: ${p.brand || 'N/A'} | ${p.badges?.join(', ') || ''}`
    ).join('
');

    const systemMsg = `You are a medical equipment recommendation engine for Meditrust Nepal. 
Given a user's context and a product catalog, return EXACTLY 3 recommendations as a JSON array.
Each item must have: index (catalog index), reason (1 sentence why it fits the user), match (integer 0-100).
Respond with ONLY valid JSON like: [{"index":0,"reason":"...","match":92},{"index":2,"reason":"...","match":85},{"index":5,"reason":"...","match":78}]`;

    const userMsg = `User context: ${userContext || 'browsing medical equipment'}
Current product category: ${category || 'general'}

Catalog:
${catalog}

Return top 3 recommendations as JSON array only.`;

    const raw = await groqCall(apiKey, [
      { role: 'system', content: systemMsg },
      { role: 'user', content: userMsg },
    ], 400, 0.3);

    let parsed = [];
    try {
      const match = raw.match(/\[[\s\S]*\]/);
      parsed = match ? JSON.parse(match[0]) : [];
    } catch { parsed = []; }

    const recommendations = parsed
      .filter(r => typeof r.index === 'number' && candidates[r.index])
      .slice(0, 3)
      .map(r => {
        const p = candidates[r.index];
        return {
          name: p.name,
          slug: p.slug,
          category: p.category,
          price: p.price,
          brand: p.brand,
          image: p.images?.[0]?.url || p.images?.[0]?.path || null,
          reason: r.reason,
          match: r.match,
        };
      });

    res.json({ recommendations });
  } catch (err) { next(err); }
};

const finder = async (req, res, next) => {
  try {
    const apiKey = process.env.GROQ_API_KEY;
    if (!apiKey) return res.status(503).json({ error: 'AI service not configured' });

    const { query } = req.body;
    if (!query?.trim()) return res.status(400).json({ error: 'query is required' });

    const allProducts = await Product.find(
      {}, 'name slug category price description brand badges'
    ).limit(100).lean();

    const catalog = allProducts.map((p, i) =>
      `[${i}] ${p.name} | ${p.category || 'N/A'} | ${p.price ? \`NPR ${p.price}\` : 'POA'} | ${p.brand || ''}`
    ).join('
');

    const systemMsg = `You are a medical equipment finder for Meditrust Nepal.
Given the user's need, return the best matching products as JSON array (max 4 items).
Each item: {"index": number, "reason": "1 sentence", "match": 0-100}
Respond with ONLY the JSON array.`;

    const raw = await groqCall(apiKey, [
      { role: 'system', content: systemMsg },
      { role: 'user', content: `User need: "${query}"

Catalog:
${catalog}

Return best matches as JSON array.` },
    ], 500, 0.3);

    let parsed = [];
    try {
      const m = raw.match(/\[[\s\S]*\]/);
      parsed = m ? JSON.parse(m[0]) : [];
    } catch { parsed = []; }

    const results = parsed
      .filter(r => typeof r.index === 'number' && allProducts[r.index])
      .slice(0, 4)
      .map(r => {
        const p = allProducts[r.index];
        return {
          name: p.name, slug: p.slug, category: p.category,
          price: p.price, brand: p.brand,
          image: p.images?.[0]?.url || p.images?.[0]?.path || null,
          reason: r.reason, match: r.match,
        };
      });

    res.json({ results });
  } catch (err) { next(err); }
};

module.exports = { chat, recommend, finder };

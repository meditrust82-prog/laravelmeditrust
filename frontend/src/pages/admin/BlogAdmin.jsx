import React, { useState, useEffect, useCallback, useRef, useMemo } from 'react';
import { useLocation } from 'react-router-dom';
import { toast } from 'react-toastify';
import {
  FaPlus, FaEdit, FaTrash, FaEye, FaSearch, FaTimes, FaUpload, FaLink,
  FaCheck, FaCopy, FaSave, FaChevronDown, FaChevronRight, FaRegCheckCircle,
  FaRegCircle, FaQuestionCircle, FaMapMarkerAlt, FaUserMd, FaClock, FaGlobeAsia,
  FaExternalLinkAlt, FaStickyNote, FaImage, FaHeading, FaListUl, FaMagic,
} from 'react-icons/fa';
import api from '../../api';
import RichTextEditor from '../../components/ui/RichTextEditor';
import { computeReadiness, computeSeoScore, scoreColor } from '../../utils/seoScore';

/* ──────────────────────────────────────────────────────────────
   Helpers
   ────────────────────────────────────────────────────────────── */
const emptyPost = {
  title: '', slug: '', excerpt: '', content: '', image: '', altText: '', caption: '', socialImage: '',
  category: '', tags: [],
  published: true, scheduledAt: '',
  author: 'Meditrust Nepal', authorBio: '', authorPhoto: '', authorCredentials: '', authorUrl: '',
  reviewerName: '', reviewerDesignation: '', reviewerCredentials: '', reviewerUrl: '', reviewedAt: '',
  metaTitle: '', metaDesc: '', focusKeyword: '', secondaryKeywords: [], searchIntent: 'informational',
  canonical: '', robots: 'index,follow', ogTitle: '', ogDesc: '',
  primaryQuestion: '', directAnswer: '', keyTakeaways: [], faqs: [],
  country: 'Nepal', locations: [], entities: [], targetAudience: [],
  sources: [], relatedBlogs: [], relatedProducts: [],
};

const TABS = [
  { id: 'basic', label: 'Basic' },
  { id: 'content', label: 'Content' },
  { id: 'seo', label: 'SEO' },
  { id: 'aeogeo', label: 'AEO/GEO' },
  { id: 'author', label: 'Author' },
  { id: 'media', label: 'Media' },
  { id: 'faq', label: 'FAQ' },
  { id: 'sources', label: 'Sources' },
];

const toLocalInput = (iso) => {
  if (!iso) return '';
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return '';
  const p = (n) => String(n).padStart(2, '0');
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}T${p(d.getHours())}:${p(d.getMinutes())}`;
};

const statusOf = (f) => (f.scheduledAt ? 'scheduled' : f.published ? 'published' : 'draft');

const asArray = (v) => (Array.isArray(v) ? v : []);

const postToForm = (p) => ({
  ...emptyPost,
  ...p,
  tags: asArray(p.tags),
  secondaryKeywords: asArray(p.secondaryKeywords),
  keyTakeaways: asArray(p.keyTakeaways),
  faqs: asArray(p.faqs),
  locations: asArray(p.locations),
  entities: asArray(p.entities),
  targetAudience: asArray(p.targetAudience),
  sources: asArray(p.sources),
  relatedBlogs: asArray(p.relatedBlogs),
  relatedProducts: asArray(p.relatedProducts),
  scheduledAt: toLocalInput(p.scheduledAt),
  published: !!p.published,
});

/* ──────────────────────────────────────────────────────────────
   Small building blocks
   ────────────────────────────────────────────────────────────── */
const Label = ({ children, required }) => (
  <label className="block text-xs font-semibold text-gray-500 uppercase mb-1">
    {children} {required && <span className="text-red-500">*</span>}
  </label>
);

const inputCls = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300';
const areaCls = 'w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300';

const TagInput = ({ value, onChange, placeholder }) => {
  const [text, setText] = useState('');
  const values = asArray(value);
  const add = () => {
    const t = text.trim();
    if (t && !values.includes(t)) onChange([...values, t]);
    setText('');
  };
  return (
    <div className="border border-gray-200 rounded-lg px-2 py-1.5 flex flex-wrap gap-1.5 items-center focus-within:ring-2 focus-within:ring-primary-300">
      {values.map((v) => (
        <span key={v} className="bg-primary-50 text-primary-700 text-xs px-2 py-1 rounded-full flex items-center gap-1">
          {v}
          <button type="button" onClick={() => onChange(values.filter((x) => x !== v))} className="text-primary-400 hover:text-red-500"><FaTimes className="text-[10px]" /></button>
        </span>
      ))}
      <input
        value={text}
        onChange={(e) => setText(e.target.value)}
        onKeyDown={(e) => { if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); add(); } }}
        onBlur={add}
        className="flex-1 min-w-[120px] text-sm px-2 py-0.5 outline-none"
        placeholder={values.length ? '' : placeholder}
      />
    </div>
  );
};

const ScoreBadge = ({ score }) => {
  const c = scoreColor(score);
  return (
    <span className={`inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full ${c.bg} ${c.text}`}>
      {score}
    </span>
  );
};

/* ──────────────────────────────────────────────────────────────
   Readiness panel
   ────────────────────────────────────────────────────────────── */
const ReadinessPanel = ({ report }) => {
  const c = scoreColor(report.score);
  return (
    <div className="bg-white rounded-xl border border-gray-100 p-4">
      <div className="flex items-center justify-between mb-3">
        <h3 className="font-semibold text-gray-900 text-sm">Content Readiness</h3>
        <span className={`text-2xl font-bold ${c.text}`}>{report.score}<span className="text-sm text-gray-400">/100</span></span>
      </div>
      <div className="space-y-3">
        {report.sections.map((section) => (
          <div key={section.key}>
            <div className="flex items-center justify-between mb-1">
              <span className="text-xs font-semibold text-gray-500">{section.label}</span>
              <span className="text-xs text-gray-400">{section.passed}/{section.total}</span>
            </div>
            <div className="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div className={`h-full rounded-full transition-all ${section.score >= 85 ? 'bg-green-500' : section.score >= 50 ? 'bg-amber-500' : 'bg-red-400'}`} style={{ width: `${section.score}%` }} />
            </div>
            <ul className="mt-2 space-y-1">
              {section.checks.map((chk, i) => (
                <li key={i} className="flex items-start gap-2 text-xs">
                  {chk.pass
                    ? <FaCheck className="text-green-500 mt-0.5 shrink-0" />
                    : <FaTimes className="text-red-400 mt-0.5 shrink-0" />}
                  <span className={chk.pass ? 'text-gray-600' : 'text-gray-500'}>
                    {chk.label}
                    {!chk.pass && chk.hint && <span className="text-gray-400"> — {chk.hint}</span>}
                  </span>
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
    </div>
  );
};

/* ──────────────────────────────────────────────────────────────
   Internal link picker
   ────────────────────────────────────────────────────────────── */
const InternalLinkPicker = ({ editorRef, onClose }) => {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    (async () => {
      setLoading(true);
      try {
        const [blogsRes, productsRes] = await Promise.all([
          api.get('/blogs/all'),
          api.get('/products?limit=200'),
        ]);
        const blogs = (blogsRes.data.blogs || []).map((b) => ({ slug: b.slug, title: b.title, type: 'blog', href: `/blog/${b.slug}` }));
        const products = (productsRes.data.products || []).map((p) => ({ slug: p.slug || p.id, title: p.name, type: 'product', href: `/products/${p.slug || p.id}` }));
        setResults([...blogs, ...products]);
      } catch {
        toast.error('Could not load link candidates');
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const filtered = results.filter((r) =>
    !query || (r.title || '').toLowerCase().includes(query.toLowerCase()) || (r.slug || '').toLowerCase().includes(query.toLowerCase())
  ).slice(0, 30);

  const insert = (r) => {
    editorRef.current?.insertLink(r.href, r.title);
    toast.success('Link inserted');
    onClose();
  };

  return (
    <div className="border border-gray-200 rounded-lg p-3 bg-gray-50">
      <div className="flex items-center gap-2 mb-2">
        <FaLink className="text-primary-500" />
        <span className="text-sm font-semibold text-gray-700">Insert Internal Link</span>
        <button type="button" onClick={onClose} className="ml-auto text-gray-400 hover:text-gray-600"><FaTimes /></button>
      </div>
      <input
        value={query}
        onChange={(e) => setQuery(e.target.value)}
        placeholder="Search blog or product…"
        className={`${inputCls} mb-2`}
        autoFocus
      />
      <div className="max-h-56 overflow-y-auto space-y-1">
        {loading && <p className="text-xs text-gray-400 px-1 py-2">Loading…</p>}
        {!loading && filtered.length === 0 && <p className="text-xs text-gray-400 px-1 py-2">No matches.</p>}
        {filtered.map((r) => (
          <button
            key={`${r.type}-${r.slug}`}
            type="button"
            onClick={() => insert(r)}
            className="w-full text-left px-3 py-2 rounded-lg hover:bg-white border border-transparent hover:border-gray-200 flex items-center gap-2"
          >
            <span className={`text-[9px] uppercase font-bold px-1.5 py-0.5 rounded ${r.type === 'blog' ? 'bg-primary-100 text-primary-700' : 'bg-purple-100 text-purple-700'}`}>
              {r.type}
            </span>
            <span className="text-sm text-gray-700 line-clamp-1">{r.title}</span>
          </button>
        ))}
      </div>
    </div>
  );
};

/* ──────────────────────────────────────────────────────────────
   Main component
   ────────────────────────────────────────────────────────────── */
const BlogAdmin = () => {
  const location = useLocation();
  const [posts, setPosts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [editing, setEditing] = useState(null); // null | 'new' | id
  const [form, setForm] = useState(emptyPost);
  const [activeTab, setActiveTab] = useState('basic');
  const [saving, setSaving] = useState(false);
  const [showReadiness, setShowReadiness] = useState(false);
  const [publishModal, setPublishModal] = useState(false);
  const [linkPicker, setLinkPicker] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [products, setProducts] = useState([]);
  const editorRef = useRef(null);

  // AI writer
  const [aiPrompt, setAiPrompt] = useState('');
  const [aiTone, setAiTone] = useState('professional and helpful');
  const [aiAudience, setAiAudience] = useState('');
  const [aiInstructions, setAiInstructions] = useState('');
  const [aiLoading, setAiLoading] = useState(false);

  // List filters
  const [search, setSearch] = useState('');
  const [filterCategory, setFilterCategory] = useState('');
  const [filterStatus, setFilterStatus] = useState('');
  const [filterScore, setFilterScore] = useState('');
  const [selected, setSelected] = useState(new Set());
  const [bulkCategory, setBulkCategory] = useState('');
  const [bulkTags, setBulkTags] = useState('');

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get('/blogs/all');
      setPosts(res.data.blogs || []);
    } catch { toast.error('Failed to load blog posts'); }
    finally { setLoading(false); }
  }, []);

  useEffect(() => { load(); }, [load]);

  // Product catalog for the content-block "Product Recommendation" inserter.
  useEffect(() => {
    api.get('/products?limit=200')
      .then((res) => setProducts(res.data.products || []))
      .catch(() => {});
  }, []);

  // Open "new" if navigated to /admin/blog/new
  useEffect(() => {
    if (location.pathname.endsWith('/new')) { openNew(); }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [location.pathname]);

  const openNew = () => { setForm(emptyPost); setEditing('new'); setActiveTab('basic'); setShowReadiness(false); };
  const openEdit = (p) => { setForm(postToForm(p)); setEditing(p._id ?? p.id); setActiveTab('basic'); setShowReadiness(false); };
  const cancel = () => { setEditing(null); setForm(emptyPost); setSelected(new Set()); };

  const field = (key, value) => setForm((f) => ({ ...f, [key]: value }));
  const setFields = (patch) => setForm((f) => ({ ...f, ...patch }));
  const updateNested = (key, index, patch) => setForm((f) => {
    const arr = asArray(f[key]).map((item, i) => (i === index ? (typeof item === 'object' && item !== null ? { ...item, ...patch } : patch) : item));
    return { ...f, [key]: arr };
  });

  const status = statusOf(form);
  const report = useMemo(() => computeReadiness(form), [form]);

  const setStatus = (next) => {
    if (next === 'draft') setFields({ published: false, scheduledAt: '' });
    else if (next === 'published') setFields({ published: true, scheduledAt: '' });
    else if (next === 'scheduled') {
      const scheduledAt = form.scheduledAt || toLocalInput(new Date(Date.now() + 86400000).toISOString());
      setFields({ published: true, scheduledAt });
    }
  };

  const buildPayload = () => {
    const f = form;
    return {
      title: f.title, slug: f.slug, excerpt: f.excerpt, content: f.content,
      image: f.image, altText: f.altText, caption: f.caption, socialImage: f.socialImage,
      category: f.category, tags: f.tags,
      author: f.author, authorBio: f.authorBio, authorPhoto: f.authorPhoto, authorCredentials: f.authorCredentials, authorUrl: f.authorUrl,
      reviewerName: f.reviewerName, reviewerDesignation: f.reviewerDesignation, reviewerCredentials: f.reviewerCredentials, reviewerUrl: f.reviewerUrl, reviewedAt: f.reviewedAt,
      metaTitle: f.metaTitle, metaDesc: f.metaDesc, focusKeyword: f.focusKeyword, secondaryKeywords: f.secondaryKeywords,
      searchIntent: f.searchIntent, canonical: f.canonical, robots: f.robots, ogTitle: f.ogTitle, ogDesc: f.ogDesc,
      primaryQuestion: f.primaryQuestion, directAnswer: f.directAnswer, keyTakeaways: f.keyTakeaways, faqs: f.faqs,
      country: f.country, locations: f.locations, entities: f.entities, targetAudience: f.targetAudience,
      sources: f.sources, relatedBlogs: f.relatedBlogs, relatedProducts: f.relatedProducts,
      published: f.published,
      scheduledAt: f.scheduledAt ? new Date(f.scheduledAt).toISOString() : null,
    };
  };

  const persist = async (publishState, { silent = false } = {}) => {
    if (!form.title.trim()) { toast.error('Title is required'); return false; }
    if (!form.content.trim()) { toast.error('Content is required'); return false; }
    setSaving(true);
    try {
      const payload = {
        ...buildPayload(),
        published: publishState,
        scheduledAt: publishState && form.scheduledAt ? new Date(form.scheduledAt).toISOString() : null,
      };
      if (editing === 'new') {
        const res = await api.post('/blogs', payload);
        setPosts((prev) => [res.data, ...prev]);
        if (!silent) toast.success(publishState ? 'Published' : 'Saved as draft');
      } else {
        const res = await api.put(`/blogs/${editing}`, payload);
        setPosts((prev) => prev.map((p) => (p._id === editing || p.id === editing ? res.data : p)));
        if (!silent) toast.success(publishState ? 'Published' : 'Saved');
      }
      cancel();
      return true;
    } catch (err) {
      toast.error(err.response?.data?.error || 'Failed to save');
      return false;
    } finally {
      setSaving(false);
    }
  };

  const saveDraft = () => persist(false);

  const aiGenerate = async () => {
    const prompt = aiPrompt.trim()
      || [
        form.title.trim() && `Create a complete researched article about: ${form.title.trim()}`,
        form.category.trim() && `Category: ${form.category.trim()}`,
        form.tags.length && `Cover these topics: ${form.tags.join(', ')}`,
        form.excerpt.trim() && `Context: ${form.excerpt.trim()}`,
      ].filter(Boolean).join('\n');
    if (!prompt) { toast.error('Describe your blog topic first'); return; }
    setAiLoading(true);
    try {
      let d;
      try {
        const res = await api.post('/ai/blog-generate', {
          prompt,
          tone: aiTone,
          audience: aiAudience.trim() || undefined,
          instructions: aiInstructions.trim() || undefined,
        });
        d = res.data || {};
      } catch (error) {
        // The legacy Render API exposes chat but not the newer blog endpoint.
        if (error.response?.status !== 404) throw error;
        const chatRes = await api.post('/ai/chat', {
          messages: [
            {
              role: 'system',
              content: 'Create a researched medical-equipment blog post for Meditrust Nepal. Return ONLY valid JSON with these keys: title, slug, excerpt, content (HTML using h2,h3,p,ul,ol,li,strong,em), category, tags (array), metaTitle, metaDesc, focusKeyword, secondaryKeywords (array), searchIntent, canonical, robots, ogTitle, ogDesc, primaryQuestion, directAnswer, keyTakeaways (array), country, locations (array), entities (array), targetAudience (array), faqs (array of objects with q and a), author, authorCredentials, authorBio, authorUrl, altText, caption, sources (array of objects with title,url,publisher,type). Use authoritative sources and never invent URLs, credentials, statistics, or medical claims. Leave unknown URLs blank.',
            },
            {
              role: 'user',
              content: `Topic: ${prompt}\nTone: ${aiTone}\nAudience: ${aiAudience.trim() || 'medical professionals and buyers in Nepal'}\nInstructions: ${aiInstructions.trim() || 'Write accurate, useful content with practical guidance and FAQs.'}`,
            },
          ],
          temperature: 0.7,
          max_tokens: 4000,
        });
        const text = chatRes.data?.choices?.[0]?.message?.content || '';
        const cleaned = text.replace(/^```(?:json)?\s*/i, '').replace(/\s*```$/, '').trim();
        const match = cleaned.match(/\{[\s\S]*\}/);
        d = JSON.parse(match?.[0] || cleaned);
      }
      setFields({
        title: d.title || form.title,
        slug: d.slug || form.slug,
        excerpt: d.excerpt || form.excerpt,
        content: d.content || form.content,
        category: d.category || form.category,
        tags: Array.isArray(d.tags) ? d.tags : form.tags,
        metaTitle: d.metaTitle || d.title || form.metaTitle,
        metaDesc: d.metaDesc || d.excerpt || form.metaDesc,
        focusKeyword: d.focusKeyword || form.focusKeyword,
        secondaryKeywords: Array.isArray(d.secondaryKeywords) ? d.secondaryKeywords : form.secondaryKeywords,
        searchIntent: d.searchIntent || form.searchIntent,
        canonical: d.canonical || form.canonical,
        robots: d.robots || form.robots,
        ogTitle: d.ogTitle || d.metaTitle || d.title || form.ogTitle,
        ogDesc: d.ogDesc || d.metaDesc || d.excerpt || form.ogDesc,
        primaryQuestion: d.primaryQuestion || form.primaryQuestion,
        directAnswer: d.directAnswer || form.directAnswer,
        keyTakeaways: Array.isArray(d.keyTakeaways) ? d.keyTakeaways : form.keyTakeaways,
        country: d.country || form.country,
        locations: Array.isArray(d.locations) ? d.locations : form.locations,
        entities: Array.isArray(d.entities) ? d.entities : form.entities,
        targetAudience: Array.isArray(d.targetAudience) ? d.targetAudience : form.targetAudience,
        faqs: Array.isArray(d.faqs) ? d.faqs : form.faqs,
        author: d.author || form.author,
        authorCredentials: d.authorCredentials || form.authorCredentials,
        authorBio: d.authorBio || form.authorBio,
        authorUrl: d.authorUrl || form.authorUrl,
        altText: d.altText || form.altText,
        caption: d.caption || form.caption,
        sources: Array.isArray(d.sources) ? d.sources : form.sources,
      });
      // If the content editor is mounted, push the HTML in directly.
      editorRef.current?.setContent?.(d.content || '');
      toast.success('Draft generated — review and publish');
    } catch (e) {
      const message = e.response?.status === 404
        ? 'AI draft route not found. Check that the backend API is running and VITE_API_URL is correct.'
        : e.response?.data?.error || 'AI generation failed';
      toast.error(message);
    } finally {
      setAiLoading(false);
    }
  };

  const requestPublish = () => {
    // Always show the checklist before publishing.
    setPublishModal(true);
  };

  const confirmPublish = async () => {
    await persist(true);
    setPublishModal(false);
  };

  const uploadImage = async (file, key) => {
    if (!file) return;
    setUploading(true);
    try {
      const fd = new FormData();
      fd.append('image', file);
      const res = await api.post('/blogs/upload', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      field(key, res.data.url);
      toast.success('Image uploaded');
    } catch {
      toast.error('Upload failed');
    } finally {
      setUploading(false);
    }
  };

  // For content images inserted via the rich-text editor — returns the URL.
  const uploadContentImage = async (file) => {
    if (!file) return null;
    const fd = new FormData();
    fd.append('image', file);
    const res = await api.post('/blogs/upload', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
    return res.data.url;
  };

  /* ── List actions ── */
  const remove = async (id) => {
    if (!window.confirm('Delete this post?')) return;
    try {
      await api.delete(`/blogs/${id}`);
      setPosts((prev) => prev.filter((p) => (p._id ?? p.id) !== id));
      toast.success('Deleted');
    } catch { toast.error('Failed to delete'); }
  };

  const duplicate = async (p) => {
    const id = p._id ?? p.id;
    try {
      const res = await api.post(`/blogs/${id}/duplicate`);
      setPosts((prev) => [res.data, ...prev]);
      toast.success('Duplicated (saved as draft)');
    } catch { toast.error('Failed to duplicate'); }
  };

  const toggle = async (p) => {
    const id = p._id ?? p.id;
    try {
      const res = await api.put(`/blogs/${id}`, { published: !p.published });
      setPosts((prev) => prev.map((x) => (x._id === id || x.id === id ? res.data : x)));
    } catch { toast.error('Failed to update'); }
  };

  const bulkRun = async (fn, label) => {
    const ids = [...selected];
    if (!ids.length) return;
    try {
      await Promise.all(ids.map((id) => fn(id)));
      toast.success(label);
      setSelected(new Set());
      await load();
    } catch { toast.error('Bulk action failed'); }
  };

  const bulkPublish = () => bulkRun((id) => api.put(`/blogs/${id}`, { published: true }), 'Published');
  const bulkUnpublish = () => bulkRun((id) => api.put(`/blogs/${id}`, { published: false }), 'Unpublished');
  const bulkDelete = () => {
    if (!window.confirm(`Delete ${selected.size} post(s)?`)) return;
    bulkRun((id) => api.delete(`/blogs/${id}`), 'Deleted');
  };
  const bulkChangeCategory = () => {
    if (!bulkCategory) return;
    bulkRun((id) => api.put(`/blogs/${id}`, { category: bulkCategory }), 'Category updated');
  };
  const bulkAddTags = () => {
    const tags = bulkTags.split(',').map((t) => t.trim()).filter(Boolean);
    if (!tags.length) return;
    bulkRun(async (id) => {
      const p = posts.find((x) => (x._id ?? x.id) === id);
      const merged = [...new Set([...(p?.tags || []), ...tags])];
      return api.put(`/blogs/${id}`, { tags: merged });
    }, 'Tags added');
  };

  const toggleSelect = (id) => {
    setSelected((prev) => {
      const next = new Set(prev);
      next.has(id) ? next.delete(id) : next.add(id);
      return next;
    });
  };
  const toggleSelectAll = () => {
    setSelected((prev) => (prev.size === filtered.length ? new Set() : new Set(filtered.map((p) => p._id ?? p.id))));
  };

  /* ── Filtering (client-side) ── */
  const categories = useMemo(() => [...new Set(posts.map((p) => p.category).filter(Boolean))].sort(), [posts]);

  const filtered = useMemo(() => {
    let list = posts;
    if (search) {
      const q = search.toLowerCase();
      list = list.filter((p) => (p.title || '').toLowerCase().includes(q) || (p.excerpt || '').toLowerCase().includes(q) || (p.category || '').toLowerCase().includes(q));
    }
    if (filterCategory) list = list.filter((p) => p.category === filterCategory);
    if (filterStatus) list = list.filter((p) => (p.status || statusOf(p)) === filterStatus);
    if (filterScore) {
      list = list.filter((p) => {
        const s = computeSeoScore(p);
        if (filterScore === 'excellent') return s >= 85;
        if (filterScore === 'good') return s >= 70 && s < 85;
        return s < 70;
      });
    }
    return list;
  }, [posts, search, filterCategory, filterStatus, filterScore]);

  /* ──────────────────────────────────────────────────────────────
     EDITOR
     ────────────────────────────────────────────────────────────── */
  if (editing) {
    return (
      <div>
        {/* Header */}
        <div className="flex flex-wrap items-center justify-between gap-3 mb-4">
          <div className="flex items-center gap-3">
            <button onClick={cancel} className="text-gray-500 hover:text-gray-700"><FaTimes /></button>
            <h1 className="text-xl font-bold text-gray-900">{editing === 'new' ? 'New Blog Post' : 'Edit Post'}</h1>
          </div>
          <div className="flex items-center gap-2">
            <button onClick={() => setShowReadiness((v) => !v)} className="text-sm px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center gap-2">
              <FaRegCheckCircle /> Readiness: <ScoreBadge score={report.score} />
            </button>
            <button onClick={saveDraft} disabled={saving} className="text-sm px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 flex items-center gap-2">
              <FaSave /> {saving ? 'Saving…' : 'Save Draft'}
            </button>
            <button onClick={requestPublish} disabled={saving} className="text-sm px-5 py-2 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 flex items-center gap-2">
              <FaCheck /> {status === 'scheduled' ? 'Schedule' : 'Publish'}
            </button>
          </div>
        </div>

        {/* AI Blog Writer */}
        <div className="bg-gradient-to-r from-indigo-50/70 to-primary-50/70 border border-primary-100 rounded-xl p-4 mb-4">
          <div className="flex items-center gap-2 mb-2">
            <FaMagic className="text-primary-600" />
            <span className="text-sm font-bold text-gray-800">AI Blog Writer</span>
            <span className="text-xs text-gray-400 hidden sm:inline">— describe a topic and AI fills in title, content, SEO, AEO & FAQs</span>
          </div>
          <textarea
            value={aiPrompt}
            onChange={(e) => setAiPrompt(e.target.value)}
            rows={3}
            className={areaCls}
            placeholder="e.g. Write a complete buying guide for digital blood pressure monitors in Nepal for home users — cover how to choose, accuracy tips, and FAQs."
          />
          <div className="flex flex-wrap items-center gap-2 mt-2">
            <select value={aiTone} onChange={(e) => setAiTone(e.target.value)} className="text-sm px-2 py-1.5 border border-gray-200 rounded-lg bg-white">
              <option value="professional and helpful">Tone: Professional</option>
              <option value="friendly and simple">Tone: Friendly & simple</option>
              <option value="authoritative and clinical">Tone: Authoritative</option>
              <option value="persuasive and commercial">Tone: Persuasive</option>
            </select>
            <input
              value={aiAudience}
              onChange={(e) => setAiAudience(e.target.value)}
              className="text-sm px-2 py-1.5 border border-gray-200 rounded-lg flex-1 min-w-[160px]"
              placeholder="Target audience (e.g. clinic owners in Kathmandu)"
            />
            <input
              value={aiInstructions}
              onChange={(e) => setAiInstructions(e.target.value)}
              className="text-sm px-2 py-1.5 border border-gray-200 rounded-lg flex-1 min-w-[220px]"
              placeholder="Custom instructions (optional)"
            />
            <button
              type="button"
              onClick={aiGenerate}
              disabled={aiLoading}
              className="ml-auto text-sm px-4 py-2 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 disabled:opacity-60 flex items-center gap-2"
            >
              <FaMagic /> {aiLoading ? 'Generating…' : 'Generate Draft'}
            </button>
          </div>
          {aiLoading && <p className="text-xs text-gray-400 mt-2">AI is writing your article — this can take 20–40 seconds…</p>}
        </div>

        <div className="flex flex-col lg:flex-row gap-5">
          {/* Tabs + form */}
          <div className="flex-1 min-w-0">
            <div className="flex flex-wrap gap-1 mb-4 border-b border-gray-200">
              {TABS.map((t) => (
                <button
                  key={t.id}
                  onClick={() => setActiveTab(t.id)}
                  className={`px-3 py-2 text-sm font-medium border-b-2 -mb-px transition ${
                    activeTab === t.id ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700'
                  }`}
                >
                  {t.label}
                </button>
              ))}
            </div>

            <div className="bg-white rounded-xl border border-gray-100 p-5 space-y-4">
              {/* ── BASIC ── */}
              {activeTab === 'basic' && (
                <>
                  <div>
                    <Label required>Blog Title / H1</Label>
                    <input value={form.title} onChange={(e) => field('title', e.target.value)} className={inputCls} placeholder="Best Blood Pressure Monitors in Nepal: Complete Buying Guide 2026" />
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>URL Slug</Label>
                      <input value={form.slug} onChange={(e) => field('slug', e.target.value)} className={inputCls} placeholder="best-blood-pressure-monitors-nepal" />
                      <p className="text-[11px] text-gray-400 mt-1">Leave blank to auto-generate from title.</p>
                    </div>
                    <div>
                      <Label>Category</Label>
                      <input value={form.category} onChange={(e) => field('category', e.target.value)} className={inputCls} placeholder="Medical Equipment" list="blog-categories" />
                      <datalist id="blog-categories">
                        {categories.map((c) => <option key={c} value={c} />)}
                      </datalist>
                    </div>
                  </div>
                  <div>
                    <Label>Tags</Label>
                    <TagInput value={form.tags} onChange={(v) => field('tags', v)} placeholder="Blood Pressure Monitor, BP Machine, Healthcare…" />
                  </div>
                  <div>
                    <Label>Short Description / Excerpt</Label>
                    <textarea value={form.excerpt} onChange={(e) => field('excerpt', e.target.value)} rows={2} className={areaCls} placeholder="Shown on blog cards and in search snippets…" />
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>Status</Label>
                      <select value={status} onChange={(e) => setStatus(e.target.value)} className={inputCls}>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="scheduled">Scheduled</option>
                      </select>
                    </div>
                    {status === 'scheduled' && (
                      <div>
                        <Label>Schedule Date & Time</Label>
                        <input type="datetime-local" value={form.scheduledAt} onChange={(e) => field('scheduledAt', e.target.value)} className={inputCls} />
                      </div>
                    )}
                  </div>
                </>
              )}

              {/* ── CONTENT ── */}
              {activeTab === 'content' && (
                <>
                  <div className="flex items-center justify-between mb-1">
                    <Label>Article Content</Label>
                    {form.content && (
                      <span className="flex items-center gap-1 text-xs text-gray-400">
                        <FaClock /> ~{Math.max(1, Math.ceil(form.content.replace(/<[^>]*>/g, ' ').split(/\s+/).filter(Boolean).length / 200))} min read
                      </span>
                    )}
                  </div>
                  <button type="button" onClick={() => setLinkPicker((v) => !v)} className="mb-2 text-xs px-3 py-1.5 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 flex items-center gap-1.5">
                    <FaLink /> Insert Internal Link
                  </button>
                  {linkPicker && <div className="mb-2"><InternalLinkPicker editorRef={editorRef} onClose={() => setLinkPicker(false)} /></div>}
                  <RichTextEditor ref={editorRef} value={form.content} onChange={(v) => field('content', v)} placeholder="Write your article…" uploadImage={uploadContentImage} products={products} />
                  <p className="text-[11px] text-gray-400 mt-1">Use H2/H3 for structure, lists and blockquotes for scannability — this powers the table of contents and answer-engine parsing.</p>
                </>
              )}

              {/* ── SEO ── */}
              {activeTab === 'seo' && (
                <>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>SEO Title</Label>
                      <input value={form.metaTitle} onChange={(e) => field('metaTitle', e.target.value)} className={inputCls} placeholder="50–60 characters" maxLength={70} />
                      <p className="text-[11px] text-gray-400 mt-1">{form.metaTitle.length}/70</p>
                    </div>
                    <div>
                      <Label>Focus Keyword</Label>
                      <input value={form.focusKeyword} onChange={(e) => field('focusKeyword', e.target.value)} className={inputCls} placeholder="blood pressure monitor nepal" />
                    </div>
                  </div>
                  <div>
                    <Label>Meta Description</Label>
                    <textarea value={form.metaDesc} onChange={(e) => field('metaDesc', e.target.value)} rows={2} className={areaCls} maxLength={170} placeholder="~150–160 characters" />
                    <p className="text-[11px] text-gray-400 mt-1">{form.metaDesc.length}/170</p>
                  </div>
                  <div>
                    <Label>Secondary Keywords</Label>
                    <TagInput value={form.secondaryKeywords} onChange={(v) => field('secondaryKeywords', v)} placeholder="digital bp monitor, home blood pressure machine…" />
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>Search Intent</Label>
                      <select value={form.searchIntent} onChange={(e) => field('searchIntent', e.target.value)} className={inputCls}>
                        <option value="informational">Informational</option>
                        <option value="commercial">Commercial</option>
                        <option value="transactional">Transactional</option>
                        <option value="navigational">Navigational</option>
                      </select>
                    </div>
                    <div>
                      <Label>Canonical URL</Label>
                      <input value={form.canonical} onChange={(e) => field('canonical', e.target.value)} className={inputCls} placeholder="Leave blank for auto" />
                    </div>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>Open Graph Title</Label>
                      <input value={form.ogTitle} onChange={(e) => field('ogTitle', e.target.value)} className={inputCls} placeholder="Social sharing title" />
                    </div>
                    <div>
                      <Label>Open Graph Description</Label>
                      <input value={form.ogDesc} onChange={(e) => field('ogDesc', e.target.value)} className={inputCls} placeholder="Social sharing description" />
                    </div>
                  </div>
                </>
              )}

              {/* ── AEO / GEO ── */}
              {activeTab === 'aeogeo' && (
                <>
                  <div className="bg-primary-50/50 border border-primary-100 rounded-lg p-4 space-y-3">
                    <p className="text-xs font-bold uppercase text-primary-700">Answer Engine Optimization</p>
                    <div>
                      <Label>Primary Question</Label>
                      <input value={form.primaryQuestion} onChange={(e) => field('primaryQuestion', e.target.value)} className={inputCls} placeholder="What is the best blood pressure monitor for home use in Nepal?" />
                    </div>
                    <div>
                      <Label>Direct Answer (40–80 words)</Label>
                      <textarea value={form.directAnswer} onChange={(e) => field('directAnswer', e.target.value)} rows={3} className={areaCls} placeholder="A concise answer suitable for featured snippets and AI summaries…" />
                    </div>
                    <div>
                      <Label>Key Takeaways</Label>
                      <TagInput value={form.keyTakeaways} onChange={(v) => field('keyTakeaways', v)} placeholder="Upper-arm monitors are more accurate…" />
                    </div>
                  </div>

                  <div className="bg-purple-50/50 border border-purple-100 rounded-lg p-4 space-y-3">
                    <p className="text-xs font-bold uppercase text-purple-700">Generative Engine Optimization</p>
                    <div className="grid sm:grid-cols-2 gap-4">
                      <div>
                        <Label>Primary Country</Label>
                        <input value={form.country} onChange={(e) => field('country', e.target.value)} className={inputCls} placeholder="Nepal" />
                      </div>
                    </div>
                    <div>
                      <Label>Locations / Local Areas</Label>
                      <TagInput value={form.locations} onChange={(v) => field('locations', v)} placeholder="Kathmandu, Pokhara, Lalitpur…" />
                    </div>
                    <div>
                      <Label>Related Entities</Label>
                      <TagInput value={form.entities} onChange={(v) => field('entities', v)} placeholder="Blood Pressure, Hypertension, Digital BP Monitor…" />
                    </div>
                    <div>
                      <Label>Target Audience</Label>
                      <TagInput value={form.targetAudience} onChange={(v) => field('targetAudience', v)} placeholder="Patients, Caregivers, Doctors…" />
                    </div>
                  </div>
                </>
              )}

              {/* ── AUTHOR ── */}
              {activeTab === 'author' && (
                <>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>Author</Label>
                      <input value={form.author} onChange={(e) => field('author', e.target.value)} className={inputCls} placeholder="Meditrust Nepal Editorial Team" />
                    </div>
                    <div>
                      <Label>Author Credentials</Label>
                      <input value={form.authorCredentials} onChange={(e) => field('authorCredentials', e.target.value)} className={inputCls} placeholder="e.g. Registered Nurse, Biomedical Engineer" />
                    </div>
                  </div>
                  <div>
                    <Label>Author Bio</Label>
                    <textarea value={form.authorBio} onChange={(e) => field('authorBio', e.target.value)} rows={2} className={areaCls} />
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>Author Photo URL</Label>
                      <input value={form.authorPhoto} onChange={(e) => field('authorPhoto', e.target.value)} className={inputCls} placeholder="https://…" />
                    </div>
                    <div>
                      <Label>Author URL</Label>
                      <input value={form.authorUrl} onChange={(e) => field('authorUrl', e.target.value)} className={inputCls} placeholder="https://…" />
                    </div>
                  </div>

                  <div className="border-t border-gray-100 pt-4 mt-2">
                    <p className="text-xs font-bold uppercase text-gray-500 mb-3 flex items-center gap-1.5"><FaUserMd /> Medical / Expert Review</p>
                    <div className="grid sm:grid-cols-2 gap-4">
                      <div>
                        <Label>Reviewer Name</Label>
                        <input value={form.reviewerName} onChange={(e) => field('reviewerName', e.target.value)} className={inputCls} placeholder="Dr. …" />
                      </div>
                      <div>
                        <Label>Reviewer Designation</Label>
                        <input value={form.reviewerDesignation} onChange={(e) => field('reviewerDesignation', e.target.value)} className={inputCls} placeholder="Consultant Cardiologist" />
                      </div>
                      <div>
                        <Label>Reviewer Credentials</Label>
                        <input value={form.reviewerCredentials} onChange={(e) => field('reviewerCredentials', e.target.value)} className={inputCls} placeholder="MBBS, MD" />
                      </div>
                      <div>
                        <Label>Review Date</Label>
                        <input type="date" value={form.reviewedAt} onChange={(e) => field('reviewedAt', e.target.value)} className={inputCls} />
                      </div>
                      <div className="sm:col-span-2">
                        <Label>Reviewer URL</Label>
                        <input value={form.reviewerUrl} onChange={(e) => field('reviewerUrl', e.target.value)} className={inputCls} placeholder="https://…" />
                      </div>
                    </div>
                  </div>
                </>
              )}

              {/* ── MEDIA ── */}
              {activeTab === 'media' && (
                <>
                  <div>
                    <Label>Featured Image</Label>
                    <div className="flex items-start gap-3">
                      {form.image && <img src={form.image} alt="" className="w-24 h-24 object-cover rounded-lg border border-gray-200" />}
                      <div className="flex-1 space-y-2">
                        <input value={form.image} onChange={(e) => field('image', e.target.value)} className={inputCls} placeholder="https://… or upload" />
                        <label className="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 cursor-pointer">
                          <FaUpload /> {uploading ? 'Uploading…' : 'Upload Image'}
                          <input type="file" accept="image/*" className="hidden" onChange={(e) => uploadImage(e.target.files[0], 'image')} />
                        </label>
                      </div>
                    </div>
                  </div>
                  <div className="grid sm:grid-cols-2 gap-4">
                    <div>
                      <Label>Image Alt Text</Label>
                      <input value={form.altText} onChange={(e) => field('altText', e.target.value)} className={inputCls} placeholder="Automatic upper-arm blood pressure monitor" />
                    </div>
                    <div>
                      <Label>Image Caption</Label>
                      <input value={form.caption} onChange={(e) => field('caption', e.target.value)} className={inputCls} placeholder="Optional caption" />
                    </div>
                  </div>
                  <div>
                    <Label>Social Image (1200×630 recommended)</Label>
                    <div className="flex items-start gap-3">
                      {form.socialImage && <img src={form.socialImage} alt="" className="w-24 h-12 object-cover rounded border border-gray-200" />}
                      <div className="flex-1 space-y-2">
                        <input value={form.socialImage} onChange={(e) => field('socialImage', e.target.value)} className={inputCls} placeholder="https://… or upload" />
                        <label className="inline-flex items-center gap-2 text-sm px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 cursor-pointer">
                          <FaUpload /> {uploading ? 'Uploading…' : 'Upload Social Image'}
                          <input type="file" accept="image/*" className="hidden" onChange={(e) => uploadImage(e.target.files[0], 'socialImage')} />
                        </label>
                      </div>
                    </div>
                  </div>
                </>
              )}

              {/* ── FAQ ── */}
              {activeTab === 'faq' && (
                <>
                  <p className="text-xs text-gray-500">These questions render visibly on the page and generate FAQPage schema.</p>
                  <div className="space-y-3">
                    {form.faqs.map((f, i) => (
                      <div key={i} className="border border-gray-200 rounded-lg p-3 space-y-2">
                        <div className="flex items-center gap-2">
                          <FaQuestionCircle className="text-primary-400" />
                          <input value={f.q || ''} onChange={(e) => updateNested('faqs', i, { q: e.target.value })} className={`${inputCls} font-medium`} placeholder="Question" />
                          <button type="button" onClick={() => field('faqs', form.faqs.filter((_, x) => x !== i))} className="text-gray-400 hover:text-red-500"><FaTrash /></button>
                        </div>
                        <textarea value={f.a || ''} onChange={(e) => updateNested('faqs', i, { a: e.target.value })} rows={2} className={areaCls} placeholder="Answer" />
                      </div>
                    ))}
                  </div>
                  <button type="button" onClick={() => field('faqs', [...form.faqs, { q: '', a: '' }])} className="text-sm px-3 py-2 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 flex items-center gap-2">
                    <FaPlus /> Add FAQ
                  </button>
                </>
              )}

              {/* ── SOURCES ── */}
              {activeTab === 'sources' && (
                <>
                  <p className="text-xs text-gray-500">Cite authoritative references (WHO, research papers, manufacturers…).</p>
                  <div className="space-y-3">
                    {form.sources.map((s, i) => (
                      <div key={i} className="border border-gray-200 rounded-lg p-3 space-y-2">
                        <div className="flex items-center gap-2">
                          <FaExternalLinkAlt className="text-gray-400" />
                          <input value={s.title || ''} onChange={(e) => updateNested('sources', i, { title: e.target.value })} className={`${inputCls} font-medium`} placeholder="Source title" />
                          <button type="button" onClick={() => field('sources', form.sources.filter((_, x) => x !== i))} className="text-gray-400 hover:text-red-500"><FaTrash /></button>
                        </div>
                        <div className="grid sm:grid-cols-2 gap-2">
                          <input value={s.url || ''} onChange={(e) => updateNested('sources', i, { url: e.target.value })} className={inputCls} placeholder="https://…" />
                          <input value={s.publisher || ''} onChange={(e) => updateNested('sources', i, { publisher: e.target.value })} className={inputCls} placeholder="Publisher / Organization" />
                          <select value={s.type || ''} onChange={(e) => updateNested('sources', i, { type: e.target.value })} className={inputCls}>
                            <option value="">Source type…</option>
                            {['Government', 'WHO', 'Medical Organization', 'Research Paper', 'Journal', 'Manufacturer', 'Expert', 'News', 'Other'].map((t) => <option key={t} value={t}>{t}</option>)}
                          </select>
                          <input type="date" value={s.accessedAt || ''} onChange={(e) => updateNested('sources', i, { accessedAt: e.target.value })} className={inputCls} />
                        </div>
                      </div>
                    ))}
                  </div>
                  <button type="button" onClick={() => field('sources', [...form.sources, { title: '', url: '', publisher: '', type: '', accessedAt: '' }])} className="text-sm px-3 py-2 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 flex items-center gap-2">
                    <FaPlus /> Add Source
                  </button>
                </>
              )}
            </div>
          </div>

          {/* Readiness sidebar */}
          {showReadiness && (
            <div className="lg:w-80 shrink-0">
              <ReadinessPanel report={report} />
            </div>
          )}
        </div>

        {/* Publish checklist modal */}
        {publishModal && (
          <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={() => setPublishModal(false)}>
            <div className="bg-white rounded-2xl max-w-lg w-full max-h-[85vh] overflow-y-auto p-6" onClick={(e) => e.stopPropagation()}>
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-bold text-gray-900">Publish Checklist</h3>
                <button onClick={() => setPublishModal(false)} className="text-gray-400 hover:text-gray-600"><FaTimes /></button>
              </div>
              <ReadinessPanel report={report} />
              <div className="mt-5 flex gap-3">
                <button onClick={() => setPublishModal(false)} className="flex-1 px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50">Back to editor</button>
                <button onClick={confirmPublish} disabled={saving} className="flex-1 px-4 py-2 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700 disabled:opacity-60">
                  {saving ? 'Saving…' : (status === 'scheduled' ? 'Schedule' : 'Publish anyway')}
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
    );
  }

  /* ──────────────────────────────────────────────────────────────
     LIST VIEW
     ────────────────────────────────────────────────────────────── */
  return (
    <div>
      <div className="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Blog Posts</h1>
          <p className="text-gray-500 text-sm mt-1">SEO / AEO / GEO-optimized articles</p>
        </div>
        <button onClick={openNew} className="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-primary-700 transition flex items-center gap-2">
          <FaPlus className="text-xs" /> New Post
        </button>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap items-center gap-2 mb-4">
        <div className="relative">
          <FaSearch className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs" />
          <input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search blogs…" className="pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm w-56 focus:outline-none focus:ring-2 focus:ring-primary-300" />
        </div>
        <select value={filterCategory} onChange={(e) => setFilterCategory(e.target.value)} className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
          <option value="">Category: All</option>
          {categories.map((c) => <option key={c} value={c}>{c}</option>)}
        </select>
        <select value={filterStatus} onChange={(e) => setFilterStatus(e.target.value)} className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
          <option value="">Status: All</option>
          <option value="published">Published</option>
          <option value="draft">Draft</option>
          <option value="scheduled">Scheduled</option>
        </select>
        <select value={filterScore} onChange={(e) => setFilterScore(e.target.value)} className="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
          <option value="">SEO Score: All</option>
          <option value="excellent">85+ Excellent</option>
          <option value="good">70–84 Good</option>
          <option value="poor">&lt;70 Needs work</option>
        </select>
      </div>

      {/* Bulk actions */}
      {selected.size > 0 && (
        <div className="flex flex-wrap items-center gap-2 mb-4 px-3 py-2 bg-primary-50 border border-primary-100 rounded-lg">
          <span className="text-sm font-semibold text-primary-700">{selected.size} selected</span>
          <button onClick={bulkPublish} className="text-xs px-3 py-1.5 rounded bg-primary-600 text-white hover:bg-primary-700">Publish</button>
          <button onClick={bulkUnpublish} className="text-xs px-3 py-1.5 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Unpublish</button>
          <button onClick={bulkDelete} className="text-xs px-3 py-1.5 rounded bg-red-50 text-red-600 hover:bg-red-100">Delete</button>
          <select value={bulkCategory} onChange={(e) => setBulkCategory(e.target.value)} className="text-xs px-2 py-1.5 rounded border border-gray-200">
            <option value="">Change category…</option>
            {categories.map((c) => <option key={c} value={c}>{c}</option>)}
          </select>
          <button onClick={bulkChangeCategory} className="text-xs px-3 py-1.5 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Apply Category</button>
          <input value={bulkTags} onChange={(e) => setBulkTags(e.target.value)} placeholder="Add tags (comma)" className="text-xs px-2 py-1.5 rounded border border-gray-200 w-44" />
          <button onClick={bulkAddTags} className="text-xs px-3 py-1.5 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Add Tags</button>
        </div>
      )}

      <div className="bg-white rounded-xl border border-gray-100 overflow-hidden">
        {loading ? (
          <div className="p-12 text-center text-gray-400 text-sm">Loading…</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full text-sm">
              <thead className="bg-gray-50 border-b border-gray-100">
                <tr>
                  <th className="px-4 py-3 w-8">
                    <input type="checkbox" checked={selected.size === filtered.length && filtered.length > 0} onChange={toggleSelectAll} />
                  </th>
                  {['Title', 'Category', 'Author', 'SEO', 'Status', 'Date', ''].map((h) => (
                    <th key={h} className="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody className="divide-y divide-gray-50">
                {filtered.length === 0 && (
                  <tr><td colSpan={8} className="px-4 py-12 text-center text-gray-400">No blog posts found.</td></tr>
                )}
                {filtered.map((p) => {
                  const id = p._id ?? p.id;
                  const st = p.status || statusOf(p);
                  return (
                    <tr key={id} className="hover:bg-gray-50">
                      <td className="px-4 py-3">
                        <input type="checkbox" checked={selected.has(id)} onChange={() => toggleSelect(id)} />
                      </td>
                      <td className="px-4 py-3">
                        <p className="font-medium text-gray-900 line-clamp-1 max-w-xs">{p.title}</p>
                        <p className="text-xs text-gray-400 mt-0.5 line-clamp-1">{p.excerpt}</p>
                      </td>
                      <td className="px-4 py-3 text-gray-500 whitespace-nowrap">{p.category || '—'}</td>
                      <td className="px-4 py-3 text-gray-500 whitespace-nowrap">{p.author}</td>
                      <td className="px-4 py-3"><ScoreBadge score={computeSeoScore(p)} /></td>
                      <td className="px-4 py-3">
                        <button
                          onClick={() => toggle(p)}
                          className={`text-[10px] px-2 py-0.5 rounded-full font-semibold ${
                            st === 'published' ? 'bg-green-100 text-green-700' : st === 'scheduled' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500'
                          }`}
                        >
                          {st === 'published' ? 'Published' : st === 'scheduled' ? 'Scheduled' : 'Draft'}
                        </button>
                      </td>
                      <td className="px-4 py-3 text-xs text-gray-400 whitespace-nowrap">{new Date(p.publishedAt || p.createdAt).toLocaleDateString()}</td>
                      <td className="px-4 py-3">
                        <div className="flex items-center gap-1">
                          <a href={`/blog/${p.slug}`} target="_blank" rel="noopener noreferrer" className="text-gray-400 hover:text-primary-600 p-1" title="View"><FaEye className="text-xs" /></a>
                          <button onClick={() => openEdit(p)} className="text-gray-400 hover:text-primary-600 p-1" title="Edit"><FaEdit className="text-xs" /></button>
                          <button onClick={() => duplicate(p)} className="text-gray-400 hover:text-primary-600 p-1" title="Duplicate"><FaCopy className="text-xs" /></button>
                          <button onClick={() => remove(id)} className="text-gray-400 hover:text-red-500 p-1" title="Delete"><FaTrash className="text-xs" /></button>
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}
      </div>
    </div>
  );
};

export default BlogAdmin;

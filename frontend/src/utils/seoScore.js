/**
 * SEO / AEO / GEO readiness scoring for Meditrust blog posts.
 *
 * The goal (per Google's people-first guidance) is NOT keyword density —
 * it's "is this genuinely useful and well-structured content that both
 * humans and AI/answer engines can parse?" Scores reward completeness
 * and clarity, never keyword stuffing.
 */

const SITE_DOMAIN = 'meditrustnepal.com';

export function stripHtml(html = '') {
  if (!html) return '';
  return String(html)
    .replace(/<[^>]*>/g, ' ')
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/\s+/g, ' ')
    .trim();
}

export function wordCount(html = '') {
  const text = stripHtml(html);
  if (!text) return 0;
  return text.split(/\s+/).length;
}

export function readingTimeFromHtml(html = '') {
  const words = wordCount(html);
  return Math.max(1, Math.ceil(words / 200));
}

function hasH2OrH3(content = '') {
  return /<(h2|h3)[\s>]/i.test(content);
}

function internalLinks(content = '') {
  // href="/..." (root-relative internal), /blog/ or /products/ paths
  const matches = String(content).match(/href=["'](\/[^"']*)["']/gi) || [];
  return matches.filter((m) =>
    /href=["']\/(blog|products|category|about|contact|services)/i.test(m)
  ).length;
}

function externalLinks(content = '') {
  const matches = String(content).match(/href=["'](https?:\/\/[^"']*)["']/gi) || [];
  return matches.filter((m) => !m.includes(SITE_DOMAIN)).length;
}

const asArray = (v) => (Array.isArray(v) ? v : []);

/**
 * Returns a full readiness report:
 *   { score, sections: [{ key, label, score, checks: [{ label, pass, hint }] }] }
 */
export function computeReadiness(post = {}) {
  const content = post.content || '';
  const title = post.title || '';
  const slug = post.slug || '';
  const excerpt = post.excerpt || '';
  const image = post.image || '';
  const alt = post.altText || '';
  const metaTitle = post.metaTitle || '';
  const metaDesc = post.metaDesc || '';
  const focusKeyword = post.focusKeyword || '';
  const primaryQuestion = post.primaryQuestion || '';
  const directAnswer = post.directAnswer || '';
  const faqs = asArray(post.faqs);
  const takeaways = asArray(post.keyTakeaways);
  const locations = asArray(post.locations);
  const entities = asArray(post.entities);
  const audience = asArray(post.targetAudience);
  const sources = asArray(post.sources);
  const author = post.author || '';
  const reviewer = post.reviewerName || '';
  const words = post.wordCount || wordCount(content);

  const sections = [
    {
      key: 'seo',
      label: 'SEO',
      checks: [
        { label: 'H1 / title', pass: !!title, hint: 'Add a clear article title' },
        { label: 'SEO title (30–60 chars)', pass: metaTitle.length >= 20 && metaTitle.length <= 70, hint: 'Optimize the SEO title' },
        { label: 'Meta description (120–160 chars)', pass: metaDesc.length >= 100 && metaDesc.length <= 170, hint: 'Write a compelling meta description' },
        { label: 'SEO-friendly slug', pass: !!slug && !/[_\s]/.test(slug), hint: 'Use a clean hyphenated slug' },
        { label: 'Featured image', pass: !!image, hint: 'Upload a featured image' },
        { label: 'Image alt text', pass: !!alt, hint: 'Add descriptive alt text' },
        { label: 'Focus keyword', pass: !!focusKeyword, hint: 'Set a primary focus keyword' },
        { label: 'Internal link', pass: internalLinks(content) > 0 || asArray(post.relatedBlogs).length > 0 || asArray(post.relatedProducts).length > 0, hint: 'Link to a related blog or product' },
        { label: 'External reference', pass: externalLinks(content) > 0 || sources.length > 0, hint: 'Cite an authoritative source' },
        { label: 'Canonical URL', pass: !!post.canonical, hint: 'Set canonical (or leave auto)' },
      ],
    },
    {
      key: 'aeo',
      label: 'AEO',
      checks: [
        { label: 'Primary question', pass: !!primaryQuestion, hint: 'State the question you are answering' },
        { label: 'Direct answer (40–80 words)', pass: directAnswer.length > 0, hint: 'Give a concise direct answer' },
        { label: 'FAQ section', pass: faqs.length > 0, hint: 'Add visible FAQs' },
        { label: 'Key takeaways', pass: takeaways.length > 0, hint: 'Summarize key takeaways' },
        { label: 'Clear subheadings (H2/H3)', pass: hasH2OrH3(content), hint: 'Structure content with H2/H3' },
        { label: 'Sufficient content (300+ words)', pass: words >= 300, hint: 'Expand to at least 300 words' },
      ],
    },
    {
      key: 'geo',
      label: 'GEO',
      checks: [
        { label: 'Nepal / location context', pass: !!post.country || locations.length > 0, hint: 'Add country or local context' },
        { label: 'Relevant entities', pass: entities.length > 0, hint: 'List related entities/topics' },
        { label: 'Target audience', pass: audience.length > 0, hint: 'Define the target audience' },
        { label: 'Organization info', pass: true, hint: '' },
        { label: 'Language (en)', pass: true, hint: '' },
      ],
    },
    {
      key: 'trust',
      label: 'Trust',
      checks: [
        { label: 'Author', pass: !!author, hint: 'Set an author' },
        { label: 'Medical reviewer', pass: !!reviewer, hint: 'Add a medical reviewer' },
        { label: 'Sources & references', pass: sources.length > 0, hint: 'Add sources' },
        { label: 'Published date', pass: !!post.publishedAt || !!post.createdAt, hint: 'Publish to set the date' },
        { label: 'Updated date', pass: !!post.updatedAt, hint: 'Update the post' },
      ],
    },
  ];

  const withSectionScore = sections.map((section) => {
    const passed = section.checks.filter((c) => c.pass).length;
    const total = section.checks.length;
    return { ...section, passed, total, score: Math.round((passed / total) * 100) };
  });

  const totalPassed = withSectionScore.reduce((sum, s) => sum + s.passed, 0);
  const totalChecks = withSectionScore.reduce((sum, s) => sum + s.total, 0);
  const score = totalChecks ? Math.round((totalPassed / totalChecks) * 100) : 0;

  return { score, sections: withSectionScore, passed: totalPassed, total: totalChecks };
}

/** Convenience: just the 0–100 number. */
export function computeSeoScore(post = {}) {
  return computeReadiness(post).score;
}

function finalizeSections(sections) {
  const withScore = sections.map((section) => {
    const passed = section.checks.filter((c) => c.pass).length;
    const total = section.checks.length;
    return { ...section, passed, total, score: total ? Math.round((passed / total) * 100) : 0 };
  });
  const totalPassed = withScore.reduce((s, x) => s + x.passed, 0);
  const totalChecks = withScore.reduce((s, x) => s + x.total, 0);
  const score = totalChecks ? Math.round((totalPassed / totalChecks) * 100) : 0;
  return { score, sections: withScore, passed: totalPassed, total: totalChecks };
}

/**
 * SEO / AEO / GEO readiness score for a Meditrust product.
 * Rewards completeness and clarity of product information that both shoppers,
 * search engines, and AI answer engines can parse.
 */
export function computeProductReadiness(product = {}) {
  const name = product.name || '';
  const slug = product.slug || '';
  const description = product.description || '';
  const specs = product.specifications || '';
  const category = product.category || '';
  const brand = product.brand || '';
  const price = product.price;
  const images = asArray(product.images);
  const image = product.image || (images[0] && (images[0].url || images[0].path)) || '';
  const alt = !!images.some((i) => i && i.alt) || !!product.altText;
  const badges = asArray(product.badges);
  const metaTitle = product.metaTitle || '';
  const metaDesc = product.metaDescription || '';
  const focusKeyword = product.focusKeyword || '';
  const canonical = product.canonical || '';
  const primaryQuestion = product.primaryQuestion || '';
  const directAnswer = product.directAnswer || '';
  const faqs = asArray(product.faqs);
  const takeaways = asArray(product.keyTakeaways);
  const country = product.country || '';
  const locations = asArray(product.locations);
  const entities = asArray(product.entities);
  const audience = asArray(product.targetAudience);
  const searchIntent = product.searchIntent || '';
  const words = wordCount(description);

  const sections = [
    {
      key: 'seo',
      label: 'SEO',
      checks: [
        { label: 'Product name', pass: !!name, hint: 'Set the product name' },
        { label: 'SEO title (30–60 chars)', pass: metaTitle.length >= 20 && metaTitle.length <= 70, hint: 'Optimize the meta title' },
        { label: 'Meta description (120–160 chars)', pass: metaDesc.length >= 100 && metaDesc.length <= 170, hint: 'Write a meta description' },
        { label: 'SEO-friendly slug', pass: !!slug && !/[_\s]/.test(slug), hint: 'Use a clean hyphenated slug' },
        { label: 'Focus keyword', pass: !!focusKeyword, hint: 'Set a focus keyword' },
        { label: 'Product image', pass: !!image, hint: 'Upload a product image' },
        { label: 'Image alt text', pass: !!alt, hint: 'Add alt text to images' },
        { label: 'Canonical URL', pass: !!canonical, hint: 'Set canonical (or leave auto)' },
      ],
    },
    {
      key: 'aeo',
      label: 'AEO',
      checks: [
        { label: 'Primary question', pass: !!primaryQuestion, hint: 'State the question you answer' },
        { label: 'Direct answer', pass: !!directAnswer, hint: 'Add a concise direct answer' },
        { label: 'FAQ section', pass: faqs.length > 0, hint: 'Add FAQs' },
        { label: 'Key takeaways', pass: takeaways.length > 0, hint: 'Add key takeaways' },
        { label: 'Rich description (100+ words)', pass: words >= 100, hint: 'Expand the description' },
      ],
    },
    {
      key: 'geo',
      label: 'GEO',
      checks: [
        { label: 'Country / location', pass: !!country || locations.length > 0, hint: 'Add country or location context' },
        { label: 'Local locations', pass: locations.length > 0, hint: 'List served locations' },
        { label: 'Relevant entities', pass: entities.length > 0, hint: 'List related entities/topics' },
        { label: 'Target audience', pass: audience.length > 0, hint: 'Define the target audience' },
        { label: 'Search intent', pass: !!searchIntent, hint: 'Set the search intent' },
      ],
    },
    {
      key: 'commerce',
      label: 'Product Info',
      checks: [
        { label: 'Price set', pass: !!price, hint: 'Set the price' },
        { label: 'Category', pass: !!category, hint: 'Set a category' },
        { label: 'Brand', pass: !!brand, hint: 'Set the brand' },
        { label: 'Specifications', pass: !!specs, hint: 'Add specifications' },
        { label: 'Images (≥1)', pass: images.length > 0, hint: 'Upload images' },
        { label: 'Certifications / badges', pass: badges.length > 0, hint: 'Add trust badges' },
      ],
    },
  ];

  return finalizeSections(sections);
}

/** Convenience: product score 0–100. */
export function computeProductScore(product = {}) {
  return computeProductReadiness(product).score;
}

export function scoreColor(score) {
  if (score >= 85) return { text: 'text-green-600', bg: 'bg-green-100', label: 'Excellent' };
  if (score >= 70) return { text: 'text-emerald-600', bg: 'bg-emerald-100', label: 'Good' };
  if (score >= 50) return { text: 'text-amber-600', bg: 'bg-amber-100', label: 'Fair' };
  return { text: 'text-red-600', bg: 'bg-red-100', label: 'Needs work' };
}

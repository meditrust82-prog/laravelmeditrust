import React, { useState, useEffect, useMemo } from 'react';
import { useParams, Link } from 'react-router-dom';
import SeoHead, {
  buildBlogPostingSchema, buildBreadcrumbSchema, buildFAQSchema, buildPersonSchema, buildOrganizationSchema,
} from '../components/SeoHead';
import {
  FaCalendarAlt, FaUser, FaUserMd, FaArrowLeft, FaFacebookF, FaTwitter, FaLinkedinIn, FaWhatsapp,
  FaClock, FaPrint, FaCheckCircle, FaExternalLinkAlt, FaChevronRight, FaShoppingCart, FaStickyNote,
  FaListUl,
} from 'react-icons/fa';
import api from '../api';

const slugify = (text = '') =>
  text.toLowerCase().trim().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-');

/** Decode HTML entities (&amp;, &lt;, etc.) so TOC text and anchor ids render cleanly. */
const decodeHtml = (str = '') => {
  const el = document.createElement('textarea');
  el.innerHTML = str;
  return el.value;
};

/** Add anchor ids to h2/h3 in the HTML and return the resulting TOC entries. */
function processContent(html = '') {
  const headings = [];
  let i = 0;
  const processed = html.replace(/<(h[23])[^>]*>(.*?)<\/\1>/gis, (_match, tag, inner) => {
    const text = decodeHtml(inner.replace(/<[^>]*>/g, '').trim());
    const id = slugify(text) || `section-${i}`;
    i += 1;
    headings.push({ level: tag.toLowerCase(), text, id });
    return `<${tag} id="${id}" style="scroll-margin-top:6rem">${inner}</${tag}>`;
  });
  return { html: processed, headings };
}

const BlogPost = () => {
  const { slug } = useParams();
  const [post, setPost] = useState(null);
  const [relatedPosts, setRelatedPosts] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchPost = async () => {
      setLoading(true);
      try {
        const res = await api.get(`/blogs/${slug}`);
        setPost(res.data.blog || res.data);

        const relRes = await api.get('/blogs?limit=4');
        const blogs = relRes.data.blogs || relRes.data || [];
        setRelatedPosts(blogs.filter((b) => (b.slug || b.id) !== slug).slice(0, 3));
      } catch {
        setPost(null);
      } finally {
        setLoading(false);
      }
    };
    fetchPost();
    window.scrollTo(0, 0);
  }, [slug]);

  const { html, headings } = useMemo(() => processContent(post?.content || ''), [post?.content]);

  const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (Number.isNaN(d.getTime())) return dateStr;
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
  };

  const shareUrl = typeof window !== 'undefined' ? window.location.href : '';
  const readingTime = post?.readingTime || Math.max(1, Math.ceil((post?.content || '').split(/\s+/).length / 200));

  if (loading) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 animate-pulse">
        <div className="h-4 bg-gray-200 rounded w-48 mb-8"></div>
        <div className="h-10 bg-gray-200 rounded w-3/4 mb-4"></div>
        <div className="h-4 bg-gray-200 rounded w-1/3 mb-8"></div>
        <div className="h-80 bg-gray-200 rounded-2xl mb-8"></div>
        <div className="space-y-3">
          {[...Array(8)].map((_, i) => (<div key={i} className="h-4 bg-gray-200 rounded w-full"></div>))}
        </div>
      </div>
    );
  }

  if (!post) {
    return (
      <div className="max-w-4xl mx-auto px-4 py-20 text-center">
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Post Not Found</h2>
        <Link to="/blog" className="btn-primary inline-flex items-center"><FaArrowLeft className="mr-2" /> Back to Blog</Link>
      </div>
    );
  }

  const faqs = Array.isArray(post.faqs) ? post.faqs : [];
  const takeaways = Array.isArray(post.keyTakeaways) ? post.keyTakeaways : [];
  const sources = Array.isArray(post.sources) ? post.sources : [];
  const relatedBlogs = Array.isArray(post.relatedBlogs) ? post.relatedBlogs : [];
  const relatedProducts = Array.isArray(post.relatedProducts) ? post.relatedProducts : [];

  const schemas = [
    buildBlogPostingSchema(post),
    buildBreadcrumbSchema([
      { name: 'Home', url: '/' },
      { name: 'Blog', url: '/blog' },
      ...(post.category ? [{ name: post.category }] : []),
      { name: post.title },
    ]),
  ];
  if (faqs.length) schemas.push(buildFAQSchema(faqs));
  if (post.author) {
    const authorSchema = buildPersonSchema({
      name: post.author,
      jobTitle: post.authorCredentials,
      url: post.authorUrl,
      image: post.authorPhoto,
      description: post.authorBio,
    });
    if (authorSchema) schemas.push(authorSchema);
  }
  if (post.reviewerName) {
    const reviewerSchema = buildPersonSchema({
      name: post.reviewerName,
      jobTitle: post.reviewerDesignation,
      url: post.reviewerUrl,
    });
    if (reviewerSchema) schemas.push(reviewerSchema);
  }
  schemas.push(buildOrganizationSchema());

  return (
    <>
      <SeoHead
        title={post.metaTitle || post.title}
        description={post.metaDesc || post.excerpt || post.title}
        keywords={[post.focusKeyword, ...(Array.isArray(post.secondaryKeywords) ? post.secondaryKeywords : [])].filter(Boolean).join(', ')}
        image={post.socialImage || post.image || undefined}
        type="article"
        canonical={post.canonical || undefined}
        schemas={schemas}
      />

      <article className="py-10 bg-gray-50">
        <div className="max-w-4xl mx-auto px-4">
          {/* Breadcrumb */}
          <nav className="flex items-center flex-wrap text-sm text-gray-500 mb-6">
            <Link to="/" className="hover:text-primary-600">Home</Link>
            <FaChevronRight className="mx-2 text-[10px] text-gray-300" />
            <Link to="/blog" className="hover:text-primary-600">Blog</Link>
            {post.category && (
              <>
                <FaChevronRight className="mx-2 text-[10px] text-gray-300" />
                <span className="text-gray-700">{post.category}</span>
              </>
            )}
          </nav>

          {/* Header */}
          <header className="mb-8">
            {post.category && (
              <span className="inline-block bg-primary-100 text-primary-700 text-sm font-medium px-3 py-1 rounded-full mb-4">{post.category}</span>
            )}
            <h1 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">{post.title}</h1>

            <div className="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-500">
              {post.author && (
                <span className="flex items-center"><FaUser className="mr-2" />{post.author}</span>
              )}
              {post.reviewerName && (
                <span className="flex items-center text-emerald-600"><FaUserMd className="mr-2" />Reviewed by {post.reviewerName}</span>
              )}
              {(post.publishedAt || post.createdAt) && (
                <span className="flex items-center"><FaCalendarAlt className="mr-2" />{formatDate(post.publishedAt || post.createdAt)}</span>
              )}
              {post.updatedAt && (
                <span className="flex items-center text-gray-400">Updated {formatDate(post.updatedAt)}</span>
              )}
              <span className="flex items-center"><FaClock className="mr-1.5" />{readingTime} min read</span>
            </div>
          </header>

          {/* Featured image */}
          {post.image ? (
            <figure className="mb-8">
              <img src={post.image} alt={post.altText || post.title} className="w-full h-80 object-cover rounded-2xl" />
              {post.caption && <figcaption className="text-xs text-gray-400 mt-2 text-center">{post.caption}</figcaption>}
            </figure>
          ) : (
            <div className="w-full h-80 flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 rounded-2xl mb-8">
              <svg className="w-24 h-24 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
            </div>
          )}

          {/* Share / Print */}
          <div className="flex items-center justify-between mb-6">
            <div className="flex space-x-2">
              <a href={`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`} target="_blank" rel="noopener noreferrer" className="w-9 h-9 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors"><FaFacebookF /></a>
              <a href={`https://twitter.com/intent/tweet?url=${shareUrl}&text=${encodeURIComponent(post.title)}`} target="_blank" rel="noopener noreferrer" className="w-9 h-9 bg-sky-500 text-white rounded-lg flex items-center justify-center hover:bg-sky-600 transition-colors"><FaTwitter /></a>
              <a href={`https://www.linkedin.com/sharing/share-offsite/?url=${shareUrl}`} target="_blank" rel="noopener noreferrer" className="w-9 h-9 bg-blue-700 text-white rounded-lg flex items-center justify-center hover:bg-blue-800 transition-colors"><FaLinkedinIn /></a>
              <a href={`https://wa.me/?text=${encodeURIComponent(post.title + ' ' + shareUrl)}`} target="_blank" rel="noopener noreferrer" className="w-9 h-9 bg-green-500 text-white rounded-lg flex items-center justify-center hover:bg-green-600 transition-colors"><FaWhatsapp /></a>
            </div>
            <button onClick={() => window.print()} className="flex items-center text-sm text-gray-500 hover:text-gray-700"><FaPrint className="mr-1.5" /> Print</button>
          </div>

          {/* Quick answer */}
          {post.directAnswer && (
            <div className="bg-primary-50 border border-primary-100 rounded-2xl p-5 mb-6">
              <p className="text-xs font-bold uppercase tracking-wide text-primary-700 mb-2 flex items-center gap-1.5"><FaStickyNote /> Quick Answer</p>
              {post.primaryQuestion && <p className="font-semibold text-gray-900 mb-1">{post.primaryQuestion}</p>}
              <p className="text-gray-700">{post.directAnswer}</p>
            </div>
          )}

          {/* Key takeaways */}
          {takeaways.length > 0 && (
            <div className="bg-white rounded-2xl border border-gray-100 p-5 mb-6">
              <h2 className="font-bold text-gray-900 mb-3">Key Takeaways</h2>
              <ul className="space-y-2">
                {takeaways.map((t, i) => (
                  <li key={i} className="flex items-start gap-2 text-gray-700">
                    <FaCheckCircle className="text-green-500 mt-1 shrink-0" />
                    <span>{t}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {/* Table of contents */}
          {headings.length > 0 && (
            <nav className="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
              <h2 className="flex items-center gap-2 text-base font-bold text-gray-900 mb-4">
                <FaListUl className="text-primary-500" />
                Table of Contents
              </h2>
              <ol className="space-y-0.5">
                {(() => {
                  let sectionNo = 0;
                  return headings.map((h, i) => {
                    const isSub = h.level === 'h3';
                    if (!isSub) sectionNo += 1;
                    return (
                      <li key={i}>
                        <a
                          href={`#${h.id}`}
                          className={
                            isSub
                              ? 'group flex items-start gap-2.5 ml-[1.15rem] border-l-2 border-gray-100 pl-4 py-1.5 text-sm text-gray-500 hover:text-primary-700 hover:border-primary-400 transition-colors'
                              : 'group flex items-start gap-2.5 py-1.5 text-sm font-semibold text-gray-800 hover:text-primary-700 transition-colors'
                          }
                        >
                          {isSub ? (
                            <span className="mt-[7px] h-1.5 w-1.5 shrink-0 rounded-full bg-gray-300 group-hover:bg-primary-500 transition-colors" />
                          ) : (
                            <span className="mt-[3px] flex h-5 w-5 shrink-0 items-center justify-center rounded-md bg-primary-50 text-[11px] font-bold text-primary-600">
                              {sectionNo}
                            </span>
                          )}
                          <span>{h.text}</span>
                        </a>
                      </li>
                    );
                  });
                })()}
              </ol>
            </nav>
          )}

          {/* Content */}
          <div className="bg-white rounded-2xl p-6 md:p-10 shadow-sm border border-gray-100 mb-8">
            <div className="prose prose-lg max-w-none text-gray-700 leading-relaxed" dangerouslySetInnerHTML={{ __html: html }} />
          </div>

          {/* FAQ */}
          {faqs.length > 0 && (
            <div className="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 mb-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-5">Frequently Asked Questions</h2>
              <div className="space-y-4">
                {faqs.map((f, i) => (
                  <div key={i} className="border border-gray-100 rounded-xl p-4">
                    <h3 className="font-semibold text-gray-900 mb-1.5">{f.q}</h3>
                    <p className="text-gray-600 text-sm leading-relaxed">{f.a}</p>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Sources */}
          {sources.length > 0 && (
            <div className="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 mb-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-5">Sources & References</h2>
              <ul className="space-y-3">
                {sources.map((s, i) => (
                  <li key={i} className="text-sm">
                    {s.url ? (
                      <a href={s.url} target="_blank" rel="noopener noreferrer" className="text-primary-600 hover:underline flex items-start gap-2">
                        <FaExternalLinkAlt className="mt-1 shrink-0 text-xs" />
                        <span>{s.title || s.url}{s.publisher ? <span className="text-gray-400"> — {s.publisher}</span> : null}</span>
                      </a>
                    ) : (
                      <span className="text-gray-700">{s.title}{s.publisher ? <span className="text-gray-400"> — {s.publisher}</span> : null}</span>
                    )}
                    {s.type && <span className="ml-5 text-[11px] uppercase tracking-wide text-gray-400">({s.type})</span>}
                  </li>
                ))}
              </ul>
            </div>
          )}

          {/* Related products */}
          {relatedProducts.length > 0 && (
            <div className="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 mb-8">
              <h2 className="text-2xl font-bold text-gray-900 mb-4">Shop Related Equipment</h2>
              <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                {relatedProducts.map((p, i) => (
                  <Link key={i} to={`/products/${p.slug}`} className="flex items-center gap-2 border border-gray-100 rounded-xl px-4 py-3 hover:border-primary-200 hover:bg-primary-50/40 transition-colors">
                    <FaShoppingCart className="text-primary-500" />
                    <span className="text-sm font-medium text-gray-700 line-clamp-2">{p.title || p.name}</span>
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Author box */}
          {post.author && (
            <div className="bg-white rounded-2xl border border-gray-100 p-6 mb-8 flex gap-4">
              {post.authorPhoto ? (
                <img src={post.authorPhoto} alt={post.author} className="w-16 h-16 rounded-full object-cover" />
              ) : (
                <div className="w-16 h-16 rounded-full bg-primary-100 flex items-center justify-center text-primary-600"><FaUser className="text-2xl" /></div>
              )}
              <div>
                <p className="font-semibold text-gray-900">Written by {post.author}</p>
                {post.authorCredentials && <p className="text-xs text-gray-500 mb-1">{post.authorCredentials}</p>}
                {post.authorBio && <p className="text-sm text-gray-600 mt-1 leading-relaxed">{post.authorBio}</p>}
                {post.reviewerName && (
                  <p className="text-xs text-emerald-600 mt-2 flex items-center gap-1.5">
                    <FaUserMd /> Medically reviewed by {post.reviewerName}{post.reviewerDesignation ? `, ${post.reviewerDesignation}` : ''}{post.reviewedAt ? ` (${formatDate(post.reviewedAt)})` : ''}
                  </p>
                )}
              </div>
            </div>
          )}

          {/* CTA */}
          <div className="bg-gradient-to-br from-primary-900 to-primary-700 rounded-2xl p-6 md:p-8 mb-8 text-center">
            <h2 className="text-xl font-bold text-white mb-2">Need medical equipment in Nepal?</h2>
            <p className="text-primary-100 text-sm mb-4">Browse certified medical equipment or request a quote from Meditrust Nepal.</p>
            <div className="flex flex-wrap justify-center gap-3">
              <Link to="/products" className="bg-white text-primary-700 px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-primary-50 transition">Shop Medical Equipment</Link>
              <Link to="/contact" className="border border-white/40 text-white px-5 py-2.5 rounded-lg font-semibold text-sm hover:bg-white/10 transition">Request a Quote</Link>
            </div>
          </div>

          {/* Related articles */}
          {relatedPosts.length > 0 && (
            <div className="mt-10">
              <h2 className="text-2xl font-bold text-gray-900 mb-6">Related Articles</h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {relatedPosts.map((rp) => (
                  <Link key={rp.id} to={`/blog/${rp.slug || rp.id}`} className="bg-white rounded-xl overflow-hidden shadow-sm card-hover group border border-gray-100">
                    <div className="h-36 bg-gray-100 overflow-hidden">
                      {rp.image ? (
                        <img src={rp.image} alt={rp.title} className="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                          <svg className="w-10 h-10 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                          </svg>
                        </div>
                      )}
                    </div>
                    <div className="p-4">
                      <h4 className="font-semibold text-sm text-gray-900 group-hover:text-primary-600 transition-colors line-clamp-2">{rp.title}</h4>
                      <p className="text-xs text-gray-500 mt-1">{formatDate(rp.createdAt)}</p>
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          )}

          <Link to="/blog" className="inline-flex items-center text-primary-600 font-medium hover:underline mt-10">
            <FaArrowLeft className="mr-2" /> Back to All Articles
          </Link>
        </div>
      </article>
    </>
  );
};

export default BlogPost;

import React, { useState, useEffect, useRef, useMemo } from 'react';
import { useParams, Link, useNavigate } from 'react-router-dom';
import SeoHead, { buildProductSchema, buildBreadcrumbSchema, buildProductFAQSchema } from '../components/SeoHead';
import AIRecommendations from '../components/AIRecommendations';
import { FaArrowLeft, FaWhatsapp, FaPhone, FaTimes, FaCheckCircle, FaShoppingCart, FaMinus, FaPlus, FaExchangeAlt, FaHeart, FaRegHeart, FaShareAlt, FaFacebookF, FaTwitter, FaLinkedinIn, FaLink } from 'react-icons/fa';
import { useTranslation } from 'react-i18next';
import { toast } from 'react-toastify';
import api from '../api';
import { useCart } from '../contexts/CartContext';
import { useCompare } from '../contexts/CompareContext';
import { useWishlist } from '../contexts/WishlistContext';
import { useRecentlyViewed } from '../hooks/useRecentlyViewed';
import Breadcrumb from '../components/ui/Breadcrumb';
import { useWhatsApp } from '../contexts/WhatsAppContext';
import { optimizeCloudinaryUrl, cloudinaryPlaceholder, getSocialImageUrl } from '../utils/cloudinary';

const ProductDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [product, setProduct] = useState(null);
  const [relatedProducts, setRelatedProducts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [selectedImage, setSelectedImage] = useState(0);
  const [showZoom, setShowZoom] = useState(false);
  const [zoomPos, setZoomPos] = useState({ x: 50, y: 50 });
  const [qty, setQty] = useState(1);
  const [showQuoteModal, setShowQuoteModal] = useState(false);
  const [showShareMenu, setShowShareMenu] = useState(false);
  const [showActionShareMenu, setShowActionShareMenu] = useState(false);
  const [quoteForm, setQuoteForm] = useState({
    name: '', hospitalName: '', phone: '', email: '', message: '', productName: ''
  });
  const [submitting, setSubmitting] = useState(false);
  const imageRef = useRef(null);
  const shareMenuRef = useRef(null);
  const actionShareMenuRef = useRef(null);
  const { addToCart } = useCart();
  const { toggleCompare, compareList } = useCompare();
  const { toggle: toggleWishlist, isWishlisted } = useWishlist();
  const { items: recentlyViewed, add: addRecentlyViewed } = useRecentlyViewed();
  const { t, i18n } = useTranslation();
  const { setProductName } = useWhatsApp();

  const cleanDescription = useMemo(() => {
    if (!product) return '';
    if (product.metaDescription) return product.metaDescription;
    if (product.description) {
      const stripped = product.description.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
      return stripped.length > 160 ? stripped.slice(0, 157) + '...' : stripped;
    }
    return `Buy ${product.name} from Meditrust Nepal. CE & ISO certified, competitive pricing, and 24/7 technical support across Nepal.`;
  }, [product]);

  const shareUrl = typeof window !== 'undefined'
    ? (product?.slug ? `${window.location.origin}/products/${product.slug}` : window.location.href)
    : '';

  const shareText = product
    ? `${product.name} — ${cleanDescription}`
    : '';

  const handleShare = async () => {
    if (navigator.share) {
      try {
        await navigator.share({
          title: product?.name,
          text: shareText,
          url: shareUrl,
        });
      } catch {}
    } else {
      try {
        await navigator.clipboard.writeText(shareUrl);
        toast.success('Link copied to clipboard!', { autoClose: 1500 });
      } catch {
        toast.error('Unable to copy link. Please copy the URL from your browser.');
      }
    }
    setShowShareMenu(false);
    setShowActionShareMenu(false);
  };

  const shareLinks = [
    { label: 'WhatsApp', icon: FaWhatsapp, className: 'bg-[#25d366]', href: `https://wa.me/?text=${encodeURIComponent(`${shareText}\n${shareUrl}`)}` },
    { label: 'Facebook', icon: FaFacebookF, className: 'bg-[#1877f2]', href: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}` },
    { label: 'X', icon: FaTwitter, className: 'bg-black', href: `https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}` },
    { label: 'LinkedIn', icon: FaLinkedinIn, className: 'bg-[#0a66c2]', href: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(shareUrl)}` },
  ];

  const copyShareLink = async () => {
    try {
      await navigator.clipboard.writeText(shareUrl);
      toast.success('Product link copied!', { autoClose: 1500 });
      setShowShareMenu(false);
      setShowActionShareMenu(false);
    } catch {
      toast.error('Unable to copy link. Please copy the URL from your browser.');
    }
  };

  useEffect(() => {
    const handleClickOutside = (e) => {
      if (shareMenuRef.current && !shareMenuRef.current.contains(e.target)) {
        setShowShareMenu(false);
      }
      if (actionShareMenuRef.current && !actionShareMenuRef.current.contains(e.target)) {
        setShowActionShareMenu(false);
      }
    };
    if (showShareMenu || showActionShareMenu) {
      document.addEventListener('mousedown', handleClickOutside);
      document.addEventListener('touchstart', handleClickOutside);
    }
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
      document.removeEventListener('touchstart', handleClickOutside);
    };
  }, [showShareMenu, showActionShareMenu]);

  const isComparing = (id) => compareList.some(p => p.id === id);

  const getWhatsAppLink = () => {
    if (!product) return '';
    const baseUrl = "https://wa.me/9779818100515";
    const msg = `${t('Detail_Msg')} ${product.name}.\n\n${t('My_Details')}:\n- ${t('Contact_Number')}: \n- ${t('Location')}: `;
    return `${baseUrl}?text=${encodeURIComponent(msg)}`;
  };

  useEffect(() => {
    const fetchProduct = async () => {
      setLoading(true);
      try {
        const res = await api.get(`/products/${id}`);
        const prod = res.data.product || res.data;
        if (!prod || !prod.name) throw new Error('Product not found');
        setProduct(prod);
        setSelectedImage(0);
        setQty(1);
        setQuoteForm(prev => ({ ...prev, productName: prod.name, message: `I am interested in ${prod.name}. Please provide a quotation.` }));
        setProductName(prod.name);
        addRecentlyViewed(prod);

        // Redirect /products/:numericId → /products/:slug (fixes GSC "Alternative page with proper canonical tag")
        if (prod.slug && id !== prod.slug) {
          navigate(`/products/${prod.slug}`, { replace: true });
          return;
        }

        // Fetch related products by category, fallback to latest
        try {
          let related = [];
          if (prod.category) {
            const relRes = await api.get(`/products?category=${encodeURIComponent(prod.category)}&limit=8`);
            related = (relRes.data.products || relRes.data || []).filter(p => p.slug !== prod.slug && p.id !== prod.id).slice(0, 4);
          }
          if (related.length === 0) {
            const fallRes = await api.get(`/products?limit=5`);
            related = (fallRes.data.products || fallRes.data || []).filter(p => p.slug !== prod.slug && p.id !== prod.id).slice(0, 4);
          }
          setRelatedProducts(related);
        } catch {}
      } catch (err) {
        console.error('Error fetching product:', err);
        toast.error('Failed to load product details');
      } finally {
        setLoading(false);
      }
    };
    fetchProduct();
    return () => setProductName(null);
  }, [id]);

  const handleMouseMove = (e) => {
    if (!imageRef.current) return;
    const rect = imageRef.current.getBoundingClientRect();
    const x = ((e.clientX - rect.left) / rect.width) * 100;
    const y = ((e.clientY - rect.top) / rect.height) * 100;
    setZoomPos({ x, y });
  };

  // Close modal on Escape key
  useEffect(() => {
    if (!showQuoteModal) return;
    const handleEsc = (e) => { if (e.key === 'Escape') setShowQuoteModal(false); };
    document.addEventListener('keydown', handleEsc);
    return () => document.removeEventListener('keydown', handleEsc);
  }, [showQuoteModal]);

  const handleQuoteSubmit = async (e) => {
    e.preventDefault();
    if (!quoteForm.name || !quoteForm.phone || !quoteForm.email) {
      toast.error('Please fill in all required fields');
      return;
    }
    setSubmitting(true);
    try {
      // Save order to DB first
      await api.post('/quotes', {
        productName: product?.name,
        productSlug: product?.slug,
        name: quoteForm.name,
        hospitalName: quoteForm.hospitalName,
        phone: quoteForm.phone,
        email: quoteForm.email,
        message: quoteForm.message,
        qty,
        source: 'product_detail',
      });
      // Also send to WhatsApp
      const waMsg = encodeURIComponent(
        `*🛒 New Order — Meditrust Nepal*\n\nProduct: ${product?.name}\nQty: ${qty}\n\nName: ${quoteForm.name}\nHospital: ${quoteForm.hospitalName || 'N/A'}\nPhone: ${quoteForm.phone}\nEmail: ${quoteForm.email || 'N/A'}\nNotes: ${quoteForm.message || 'None'}`
      );
      window.open(`https://wa.me/9779818100515?text=${waMsg}`, '_blank');
      toast.success('Order placed! We will contact you to confirm.');
      setShowQuoteModal(false);
      setQuoteForm(prev => ({ ...prev, name: '', hospitalName: '', phone: '', email: '', message: '' }));
    } catch (err) {
      toast.error('Failed to submit request. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-20">
        <div className="animate-pulse">
          <div className="h-4 bg-gray-200 rounded w-48 mb-8"></div>
          <div className="grid lg:grid-cols-2 gap-12">
            <div className="h-96 bg-gray-200 rounded-2xl"></div>
            <div>
              <div className="h-8 bg-gray-200 rounded w-2/3 mb-4"></div>
              <div className="h-4 bg-gray-200 rounded w-1/3 mb-6"></div>
              <div className="h-4 bg-gray-200 rounded w-full mb-2"></div>
              <div className="h-4 bg-gray-200 rounded w-full mb-2"></div>
              <div className="h-4 bg-gray-200 rounded w-3/4"></div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-20 text-center">
        <h2 className="text-2xl font-bold text-gray-900 mb-4">{t('Product_Not_Found')}</h2>
        <p className="text-gray-500 mb-6">{t('Product_Not_Found_Desc')}</p>
        <Link to="/products" className="btn-primary inline-flex items-center">
          <FaArrowLeft className="mr-2" /> {t('Back_To_Products_Link')}
        </Link>
      </div>
    );
  }

  const rawImages = product.allImages || (product.image ? [product.image] : []);
  const images = rawImages.map(u => optimizeCloudinaryUrl(u, { width: 800 }));
  const socialImage = getSocialImageUrl(product.ogImage || rawImages[0]) || undefined;
  const currentImage = images[selectedImage] || null;

  return (
    <>
      <SeoHead
        title={product.metaTitle || `${product.name} — Buy in Nepal`}
        description={cleanDescription}
        image={socialImage || images[0] || undefined}
        ogTitle={product.ogTitle || undefined}
        ogDesc={cleanDescription}
        ogImage={socialImage || undefined}
        type="product"
        keywords={product.metaKeywords || `${product.name}, ${product.category || 'medical equipment'} Nepal, buy ${product.name} Kathmandu, ${product.brand ? product.brand + ' Nepal' : 'medical equipment Nepal'}`}
        canonical={product.slug ? `/products/${product.slug}` : undefined}
        schemas={[
          buildProductSchema({ ...product, images }),
          buildBreadcrumbSchema([
            { name: 'Home', url: '/' },
            { name: 'Products', url: '/products' },
            ...(product.category ? [{ name: product.category, url: `/products?category=${product.categorySlug}` }] : []),
            { name: product.name },
          ]),
          buildProductFAQSchema(product),
        ]}
      />

      <section className="py-8 pb-24 lg:pb-8 bg-gray-50 dark:bg-dark-bg">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <Breadcrumb items={[
            { label: 'Products', href: '/products' },
            ...(product.category ? [{ label: product.category, href: `/products?category=${product.categorySlug}` }] : []),
            { label: product.name },
          ]} />

          <div className="grid lg:grid-cols-2 gap-12">
            {/* Image Gallery with Zoom */}
            <div>
              {images.length > 0 ? (
                <>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                      ref={imageRef}
                      className="relative w-full aspect-square rounded-2xl overflow-hidden border border-gray-200 cursor-crosshair bg-white"
                      onMouseEnter={() => setShowZoom(true)}
                      onMouseLeave={() => setShowZoom(false)}
                      onMouseMove={handleMouseMove}
                    >
                      {cloudinaryPlaceholder(currentImage) && (
                        <img src={cloudinaryPlaceholder(currentImage)} alt="" aria-hidden className="absolute inset-0 w-full h-full object-contain blur-sm scale-105 transition-opacity" />
                      )}
                      <img src={currentImage} alt={product.name} loading="eager" decoding="async" className="relative w-full h-full object-contain" />
                      <span className="absolute bottom-2 right-2 bg-black/40 backdrop-blur-sm text-white text-[10px] font-semibold tracking-wide px-2 py-0.5 rounded pointer-events-none select-none">
                        Meditrust Nepal
                      </span>
                    </div>

                    {/* Zoom Preview */}
                    <div className="hidden md:block">
                      {showZoom && currentImage ? (
                        <div className="w-full aspect-square rounded-2xl overflow-hidden border border-gray-200 bg-white">
                          <div
                            className="w-full h-full"
                            style={{
                              backgroundImage: `url(${currentImage})`,
                              backgroundSize: '250%',
                              backgroundPosition: `${zoomPos.x}% ${zoomPos.y}%`,
                              backgroundRepeat: 'no-repeat'
                            }}
                          />
                        </div>
                      ) : (
                        <div className="w-full aspect-square rounded-2xl border border-gray-200 bg-gray-50 flex items-center justify-center">
                          <p className="text-sm text-gray-400">Hover over image to zoom</p>
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Thumbnails */}
                  {images.length > 1 && (
                    <div className="flex gap-2 mt-4">
                      {images.map((img, i) => (
                        <button
                          key={i}
                          onClick={() => setSelectedImage(i)}
                          className={`w-16 h-16 rounded-lg overflow-hidden border-2 transition ${
                            selectedImage === i ? 'border-primary-500' : 'border-gray-200 hover:border-gray-400'
                          }`}
                        >
                          <img src={img} alt="" loading="lazy" decoding="async" className="w-full h-full object-cover" />
                        </button>
                      ))}
                    </div>
                  )}
                </>
              ) : (
                <div className="w-full aspect-square rounded-2xl flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100 border border-gray-200">
                  <svg className="w-32 h-32 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
              )}
            </div>

            {/* Details — sticky on desktop */}
            <div className="lg:sticky lg:top-24 lg:self-start">
              {product.category && (
                <Link
                  to={`/products?category=${product.categorySlug}`}
                  className="inline-block bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-sm font-medium px-3 py-1 rounded-full mb-4 hover:bg-primary-200 dark:hover:bg-primary-900/60 transition"
                >
                  {product.category}
                </Link>
              )}
              <h1 className="text-3xl font-bold text-gray-900 dark:text-white mb-4">{product.name}</h1>

              {/* Price */}
              {product.price && (
                <div className="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 mb-6 inline-block">
                  <span className="text-sm text-green-600 dark:text-green-400">Price</span>
                  <div className="flex items-center gap-3 flex-wrap mt-1">
                    <p className="text-3xl font-bold text-green-700 dark:text-green-400">NRS {product.price.toLocaleString()}</p>
                    {product.originalPrice && product.originalPrice > product.price && (
                      <span className="text-lg text-gray-400 line-through">NRS {product.originalPrice.toLocaleString()}</span>
                    )}
                    {product.originalPrice && product.originalPrice > product.price && (
                      <span className="text-sm font-bold text-white bg-red-500 px-2 py-0.5 rounded-lg">{Math.round((1 - product.price / product.originalPrice) * 100)}% OFF</span>
                    )}
                  </div>
                </div>
              )}

              <div
                className="text-gray-600 dark:text-gray-300 leading-relaxed mb-6 prose prose-sm max-w-none prose-a:text-primary-600 prose-a:underline prose-ul:list-disc prose-ol:list-decimal"
                dangerouslySetInnerHTML={{ __html: product.description }}
              />

              {/* Quantity, Add to Cart & Share */}
              <div className="flex flex-wrap sm:flex-nowrap items-center gap-3 mb-6">
                <div className="flex items-center border border-gray-300 dark:border-dark-border rounded-lg bg-white dark:bg-dark-card overflow-hidden flex-shrink-0">
                  <button
                    onClick={() => setQty(q => Math.max(1, q - 1))}
                    className="px-3 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface transition-colors"
                    aria-label="Decrease quantity"
                  >
                    <FaMinus className="text-xs" />
                  </button>
                  <span className="px-4 py-2 font-semibold border-x border-gray-300 dark:border-dark-border text-gray-900 dark:text-white">{qty}</span>
                  <button
                    onClick={() => setQty(q => q + 1)}
                    className="px-3 py-2.5 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface transition-colors"
                    aria-label="Increase quantity"
                  >
                    <FaPlus className="text-xs" />
                  </button>
                </div>
                <button
                  onClick={() => addToCart(product, qty)}
                  className="flex-1 min-w-[150px] bg-primary-600 text-white py-3 px-5 rounded-lg font-semibold hover:bg-primary-700 transition-all flex items-center justify-center gap-2 shadow-sm"
                >
                  <FaShoppingCart /> {t('Add_To_Cart_Label')}
                </button>
                <div className="relative" ref={shareMenuRef}>
                  <button
                    type="button"
                    onClick={() => setShowShareMenu(prev => !prev)}
                    className="h-full py-3 px-4 rounded-lg font-semibold border border-gray-300 dark:border-dark-border text-gray-700 dark:text-gray-200 bg-white dark:bg-dark-card hover:bg-gray-50 dark:hover:bg-dark-surface transition-all flex items-center justify-center gap-2 shadow-sm hover:border-primary-500 flex-shrink-0"
                    title="Share this product"
                    aria-label="Share this product"
                    aria-expanded={showShareMenu}
                  >
                    <FaShareAlt className="text-primary-600 dark:text-brand-cyan" />
                    <span className="text-sm font-medium">Share</span>
                  </button>
                  {showShareMenu && (
                    <div className="absolute right-0 top-full z-30 mt-2 w-64 rounded-xl border border-gray-200 dark:border-dark-border bg-white dark:bg-dark-card p-2 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                      <div className="px-3 py-2 text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-dark-border mb-1">
                        Share Product
                      </div>
                      {typeof navigator !== 'undefined' && navigator.share && (
                        <button
                          type="button"
                          onClick={handleShare}
                          className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-dark-surface hover:text-primary-600 transition-colors"
                        >
                          <span className="flex h-7 w-7 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-brand-cyan"><FaShareAlt className="text-xs" /></span>
                          Share via Device...
                        </button>
                      )}
                      {shareLinks.map(({ label, icon: Icon, className, href }) => (
                        <a
                          key={label}
                          href={href}
                          target="_blank"
                          rel="noopener noreferrer"
                          onClick={() => setShowShareMenu(false)}
                          className="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface transition-colors"
                        >
                          <span className={`flex h-7 w-7 items-center justify-center rounded-full text-xs text-white ${className}`}>
                            <Icon />
                          </span>
                          Share on {label}
                        </a>
                      ))}
                      <button
                        type="button"
                        onClick={copyShareLink}
                        className="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface border-t border-gray-100 dark:border-dark-border mt-1 pt-2 transition-colors"
                      >
                        <span className="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                          <FaLink className="text-xs" />
                        </span>
                        Copy Product Link
                      </button>
                    </div>
                  )}
                </div>
              </div>

              {/* Specifications */}
              {product.specifications && (
                <div className="mb-6">
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-3">{t('Specifications')}</h3>
                  <div
                    className="text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-dark-card rounded-xl p-4 prose prose-sm max-w-none prose-a:text-primary-600 prose-a:underline prose-ul:list-disc prose-ol:list-decimal"
                    dangerouslySetInnerHTML={{ __html: product.specifications }}
                  />
                </div>
              )}

              {/* Video URL */}
              {product.videoUrl && (
                <div className="mb-6">
                  <h3 className="text-lg font-semibold text-gray-900 mb-3">Product Demonstration</h3>
                  <div className="rounded-xl overflow-hidden bg-gray-900 aspect-video w-full border border-gray-200">
                    <iframe
                      src={product.videoUrl.replace('watch?v=', 'embed/').split('&')[0]}
                      title="Product Video"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowFullScreen
                      className="w-full h-full"
                    ></iframe>
                  </div>
                </div>
              )}

              {/* Stock */}
              {product.inStock && (
                <div className="flex items-center gap-2 mb-6">
                  <FaCheckCircle className="text-green-500" />
                  <span className="text-sm text-green-700 font-medium">In Stock</span>
                </div>
              )}

              {/* Action Buttons */}
              <div className="flex flex-wrap gap-3">
                <button
                  onClick={() => setShowQuoteModal(true)}
                  className="border-2 border-primary-600 text-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-primary-50 transition-all flex items-center"
                >
                  Order Now
                </button>
                <button
                  onClick={() => toggleCompare(product)}
                  className={`border border-gray-300 px-6 py-3 rounded-lg font-semibold transition-all flex items-center ${isComparing(product.id) ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50'}`}
                >
                  <FaExchangeAlt className="mr-2" /> {isComparing(product.id) ? t('Remove_From_Compare') : t('Compare')}
                </button>
                <a
                  href={getWhatsAppLink()}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition-all flex items-center shadow-md"
                >
                  <FaWhatsapp className="mr-2 text-lg" /> {t('Order_Via_WhatsApp')}
                </a>
                <a href="tel:+977-9818100515" className="btn-secondary flex items-center">
                  <FaPhone className="mr-2" /> {t('Call_Us_Short')}
                </a>
                <button
                  onClick={() => toggleWishlist(product)}
                  className={`border px-4 py-3 rounded-lg font-semibold transition-all flex items-center gap-2 ${
                    isWishlisted(product.slug) ? 'border-red-300 bg-red-50 text-red-500' : 'border-gray-300 text-gray-500 hover:bg-gray-50'
                  }`}
                  title={isWishlisted(product.slug) ? 'Remove from wishlist' : 'Save to wishlist'}
                >
                  {isWishlisted(product.slug) ? <FaHeart /> : <FaRegHeart />}
                </button>
                <div className="relative" ref={actionShareMenuRef}>
                  <button
                    onClick={() => setShowActionShareMenu(prev => !prev)}
                    className="border border-gray-300 px-4 py-3 rounded-lg font-semibold text-gray-500 hover:bg-gray-50 transition-all flex items-center gap-2"
                    title="Share this product"
                    aria-label="Share this product"
                    aria-expanded={showActionShareMenu}
                  >
                    <FaShareAlt />
                  </button>
                  {showActionShareMenu && (
                    <div className="absolute right-0 top-full z-20 mt-2 w-56 rounded-xl border border-gray-200 dark:border-dark-border bg-white dark:bg-dark-card p-2 shadow-xl">
                      {typeof navigator !== 'undefined' && navigator.share && (
                        <button onClick={handleShare} className="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface">
                          <FaShareAlt className="text-primary-600" /> Share from your device
                        </button>
                      )}
                      {shareLinks.map(({ label, icon: Icon, className, href }) => (
                        <a key={label} href={href} target="_blank" rel="noopener noreferrer" onClick={() => setShowActionShareMenu(false)} className="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface">
                          <span className={`flex h-6 w-6 items-center justify-center rounded-full text-xs text-white ${className}`}><Icon /></span>Share on {label}
                        </a>
                      ))}
                      <button onClick={copyShareLink} className="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-surface border-t border-gray-100 dark:border-dark-border mt-1 pt-2">
                        <FaLink className="text-gray-500" /> Copy product link
                      </button>
                    </div>
                  )}
                </div>
              </div>

              {/* Back in Stock alert — shown when out of stock */}
              {product.inStock === false && (
                <div className="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-center justify-between gap-3">
                  <div>
                    <p className="text-sm font-semibold text-amber-800 dark:text-amber-400">Currently Out of Stock</p>
                    <p className="text-xs text-amber-600 dark:text-amber-500">Get notified on WhatsApp when it's available</p>
                  </div>
                  <a
                    href={`https://wa.me/9779818100515?text=${encodeURIComponent(`Hello! Please notify me on WhatsApp when *${product.name}* is back in stock.`)}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="flex-shrink-0 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-xs font-bold transition"
                  >
                    🔔 Notify Me
                  </a>
                </div>
              )}

              {/* Print / Save Quote */}
              <div className="mt-4">
                <button
                  onClick={() => {
                    const w = window.open('', '_blank');
                    w.document.write(`
                      <html><head><title>Quote — ${product.name}</title>
                      <style>body{font-family:Arial,sans-serif;padding:40px;max-width:600px;margin:auto}h1{font-size:22px;margin-bottom:4px}table{width:100%;border-collapse:collapse;margin-top:20px}td{padding:8px 12px;border:1px solid #e5e7eb;font-size:14px}tr:nth-child(even){background:#f9fafb}.footer{margin-top:30px;font-size:12px;color:#6b7280}</style>
                      </head><body>
                      <h1>${product.name}</h1>
                      <p style="color:#6b7280;font-size:13px">Category: ${product.category || 'Medical Equipment'}</p>
                      ${product.price ? `<p style="font-size:20px;font-weight:bold;color:#1d4ed8;margin:12px 0">NPR ${product.price.toLocaleString()}</p>` : '<p style="color:#6b7280">Price: On Request</p>'}
                      <table>
                        <tr><td><strong>Supplier</strong></td><td>Meditrust Nepal</td></tr>
                        <tr><td><strong>Product</strong></td><td>${product.name}</td></tr>
                        ${product.brand ? `<tr><td><strong>Brand</strong></td><td>${product.brand}</td></tr>` : ''}
                        ${product.category ? `<tr><td><strong>Category</strong></td><td>${product.category}</td></tr>` : ''}
                        ${product.price ? `<tr><td><strong>Unit Price</strong></td><td>NPR ${product.price.toLocaleString()}</td></tr>` : ''}
                        <tr><td><strong>Date</strong></td><td>${new Date().toLocaleDateString('en-NP', { year: 'numeric', month: 'long', day: 'numeric' })}</td></tr>
                      </table>
                      <p class="footer">Meditrust Nepal | +977-9818100515 | meditrustnepal.com<br/>This is a preliminary price quotation. Final pricing subject to confirmation.</p>
                      <script>window.onload=()=>{window.print();}</script>
                      </body></html>
                    `);
                    w.document.close();
                  }}
                  className="text-xs text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-brand-cyan underline transition flex items-center gap-1"
                >
                  🖨️ Print / Save Quote as PDF
                </button>
              </div>

              {/* Trust Badges */}
              {product.badges?.length > 0 && (
                <div className="mt-6 p-4 bg-gray-50 dark:bg-dark-card rounded-xl border border-gray-100 dark:border-dark-border">
                  <div className="grid grid-cols-2 gap-3">
                    {product.badges.map((badge) => (
                      <div key={badge} className="flex items-center gap-2">
                        <FaCheckCircle className="text-green-500 flex-shrink-0" />
                        <span className="text-xs text-gray-600 dark:text-gray-300 font-medium">{badge}</span>
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          </div>

          <AIRecommendations product={product} />

          {/* Recently Viewed */}
          {recentlyViewed.filter(r => r.slug !== product.slug).length > 0 && (
            <div className="mt-10 pt-8 border-t border-gray-100">
              <h3 className="text-lg font-bold text-gray-900 mb-4">Recently Viewed</h3>
              <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                {recentlyViewed.filter(r => r.slug !== product.slug).map(r => (
                  <Link key={r.slug} to={`/products/${r.slug}`} className="group bg-white border border-gray-100 rounded-xl p-3 hover:shadow-md hover:border-primary-200 transition-all flex flex-col items-center text-center">
                    {r.image
                      ? <img src={r.image} alt={r.name} loading="lazy" className="w-16 h-16 object-contain mb-2 group-hover:scale-105 transition-transform" />
                      : <div className="w-16 h-16 bg-gray-100 rounded-lg mb-2 flex items-center justify-center"><FaShoppingCart className="text-gray-300 text-xl" /></div>
                    }
                    <p className="text-xs font-medium text-gray-800 group-hover:text-primary-600 line-clamp-2 leading-tight">{r.name}</p>
                    {r.price && <p className="text-xs text-primary-600 font-semibold mt-1">NPR {r.price.toLocaleString()}</p>}
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Related Products */}
          {relatedProducts.length > 0 && (
            <div className="mt-20 pb-24 lg:pb-0">
              <h2 className="text-2xl font-bold text-gray-900 mb-8">{t('Related_Products')}</h2>
              <div className="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
                {relatedProducts.map((rp) => (
                  <Link key={rp.id} to={`/products/${rp.slug || rp.id}`} className="bg-white rounded-xl overflow-hidden shadow-sm card-hover group border border-gray-100 flex flex-col cursor-pointer">
                    <div className="relative h-32 sm:h-48 bg-gray-100 overflow-hidden">
                      {rp.allImages?.length > 0 ? (
                        <img src={rp.allImages[0]} alt={rp.name} loading="lazy" decoding="async" className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                      ) : rp.image ? (
                        <img src={rp.image} alt={rp.name} loading="lazy" decoding="async" className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                      ) : (
                        <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-50 to-primary-100">
                          <svg className="w-10 h-10 sm:w-16 sm:h-16 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                          </svg>
                        </div>
                      )}
                      {rp.category && (
                        <span className="absolute top-2 left-2 bg-primary-600 text-white text-[10px] sm:text-xs px-2 py-0.5 rounded-full leading-tight">{rp.category}</span>
                      )}
                    </div>
                    <div className="p-3 sm:p-5 flex flex-col flex-1">
                      <h3 className="font-semibold text-gray-900 group-hover:text-primary-600 transition-colors text-sm sm:text-base line-clamp-2 leading-snug">{rp.name}</h3>
                      {rp.price && (
                        <div className="mt-1 sm:mt-2">
                          <div className="flex items-center gap-1.5 flex-wrap">
                            <p className="text-sm sm:text-base font-bold text-primary-700">NRS {rp.price.toLocaleString()}</p>
                            {rp.originalPrice && rp.originalPrice > rp.price && (
                              <span className="text-xs text-gray-400 line-through">NRS {rp.originalPrice.toLocaleString()}</span>
                            )}
                          </div>
                          {rp.originalPrice && rp.originalPrice > rp.price && (
                            <span className="inline-block text-xs font-semibold text-white bg-red-500 px-1.5 py-0.5 rounded mt-0.5">{Math.round((1 - rp.price / rp.originalPrice) * 100)}% OFF</span>
                          )}
                        </div>
                      )}
                      <span className="mt-auto pt-2 text-center bg-primary-600 text-white py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium block">
                        {t('View_Details')}
                      </span>
                    </div>
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Bottom share bar for direct product links */}
          <div className="mt-12 border-t border-gray-200 dark:border-dark-border py-8">
            <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <h2 className="text-lg font-bold text-gray-900 dark:text-white">Share this product</h2>
                <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">Send this product page to your team or customers.</p>
              </div>
              <div className="flex flex-wrap items-center gap-2">
                {shareLinks.map(({ label, icon: Icon, className, href }) => (
                  <a
                    key={label}
                    href={href}
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label={`Share on ${label}`}
                    className={`flex h-10 w-10 items-center justify-center rounded-lg text-white transition-opacity hover:opacity-80 ${className}`}
                  >
                    <Icon />
                  </a>
                ))}
                <button
                  onClick={copyShareLink}
                  aria-label="Copy product link"
                  title="Copy product link"
                  className="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 dark:border-dark-border text-gray-600 dark:text-gray-300 transition-colors hover:bg-gray-50 dark:hover:bg-dark-card"
                >
                  <FaLink />
                </button>
                <button
                  onClick={handleShare}
                  aria-label="Share product from your device"
                  title="Share from your device"
                  className="flex h-10 w-10 items-center justify-center rounded-lg border border-primary-200 dark:border-primary-800 text-primary-600 dark:text-brand-cyan transition-colors hover:bg-primary-50 dark:hover:bg-dark-card"
                >
                  <FaShareAlt />
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Sticky mobile bottom CTA — sits above the MobileTabBar (bottom-16 = 64px) */}
      <div className="lg:hidden fixed bottom-16 inset-x-0 z-40 bg-white border-t border-gray-200 px-4 py-3 flex gap-2 shadow-lg">
        <button
          onClick={() => setShowQuoteModal(true)}
          className="flex-1 bg-primary-600 text-white py-2.5 rounded-lg text-sm font-semibold hover:bg-primary-700 transition-colors"
        >
          Order Now
        </button>
        <a
          href={getWhatsAppLink()}
          target="_blank"
          rel="noopener noreferrer"
          className="flex items-center justify-center px-4 py-2.5 bg-green-500 text-white rounded-lg text-sm font-semibold hover:bg-green-600 transition-colors"
        >
          <FaWhatsapp className="text-lg" />
        </a>
        <a
          href="tel:+977-9818100515"
          className="flex items-center justify-center px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors"
        >
          <FaPhone />
        </a>
      </div>

      {/* Quote Modal */}
      {showQuoteModal && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
          onClick={() => setShowQuoteModal(false)}
        >
          <div
            className="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto"
            onClick={(e) => e.stopPropagation()}
          >
            <div className="p-6 border-b border-gray-100 flex items-center justify-between">
              <h3 className="text-xl font-bold text-gray-900">Place Your Order</h3>
              <button onClick={() => setShowQuoteModal(false)} className="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <FaTimes className="text-gray-500" />
              </button>
            </div>
            <form onSubmit={handleQuoteSubmit} className="p-6 space-y-4">
              <p className="text-sm text-gray-500 mb-2">Product: <span className="font-medium text-gray-900">{product.name}</span></p>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">{t('Your_Name')}</label>
                <input
                  type="text"
                  value={quoteForm.name}
                  onChange={(e) => setQuoteForm({...quoteForm, name: e.target.value})}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">{t('Hospital_Name')}</label>
                <input
                  type="text"
                  value={quoteForm.hospitalName}
                  onChange={(e) => setQuoteForm({...quoteForm, hospitalName: e.target.value})}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">{t('Phone_Number')}</label>
                <input
                  type="tel"
                  value={quoteForm.phone}
                  onChange={(e) => setQuoteForm({...quoteForm, phone: e.target.value})}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">{t('Email_Address')}</label>
                <input
                  type="email"
                  value={quoteForm.email}
                  onChange={(e) => setQuoteForm({...quoteForm, email: e.target.value})}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none"
                  required
                />
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">{t('Message')}</label>
                <textarea
                  rows="3"
                  value={quoteForm.message}
                  onChange={(e) => setQuoteForm({...quoteForm, message: e.target.value})}
                  className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none resize-none"
                ></textarea>
              </div>
              <p className="text-xs text-gray-400 text-center">Order saved to our system &amp; sent to WhatsApp. We'll confirm within 24 hours.</p>
              <button
                type="submit"
                disabled={submitting}
                className="w-full btn-primary disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
              >
                <FaWhatsapp />
                {submitting ? 'Placing Order...' : 'Place Order & Send via WhatsApp'}
              </button>
            </form>
          </div>
        </div>
      )}
    </>
  );
};

export default ProductDetail;

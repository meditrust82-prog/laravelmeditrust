/**
 * Automatically applies f_auto (WebP/AVIF) and q_auto (smart compression)
 * to any Cloudinary URL. Non-Cloudinary URLs are returned unchanged.
 * Reduces image size 30–60% with zero visual quality loss.
 */
export function optimizeCloudinaryUrl(url, { width, height } = {}) {
  if (!url || typeof url !== 'string') return url;
  if (!url.includes('res.cloudinary.com')) return url;

  const transforms = ['f_auto', 'q_auto'];
  if (width) transforms.push(`w_${width}`);
  if (height) transforms.push(`h_${height}`);

  // Insert transforms after /upload/
  return url.replace('/upload/', `/upload/${transforms.join(',')}/`);
}

/**
 * Returns a low-quality placeholder (blur-up) URL for progressive loading.
 */
export function cloudinaryPlaceholder(url) {
  if (!url || !url.includes('res.cloudinary.com')) return null;
  return url.replace('/upload/', '/upload/w_20,q_10,e_blur:500,f_auto/');
}

/**
 * Returns an optimized 1200x630 JPEG image formatted for social crawlers
 * (WhatsApp, Facebook, Twitter/X, LinkedIn) to ensure rich card previews always display.
 */
export function getSocialImageUrl(url) {
  if (!url || typeof url !== 'string') return url;
  if (!url.includes('res.cloudinary.com')) {
    if (url.startsWith('/')) {
      const siteUrl = import.meta.env?.VITE_SITE_URL || 'https://meditrustnepal.com';
      return `${siteUrl}${url}`;
    }
    return url;
  }
  // If already transformed with /upload/, insert social transforms
  if (url.includes('/upload/')) {
    return url.replace(/\/upload\/(?:[a-zA-Z0-9_:,]+\/)?/, '/upload/f_jpg,q_auto,w_1200,h_630,c_pad,b_white/');
  }
  return url;
}

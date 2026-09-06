import { Node } from '@tiptap/core';

/* ─────────────────────────────────────────────────────────────
   Image + Caption (figure)
   ───────────────────────────────────────────────────────────── */
export const FigureImage = Node.create({
  name: 'figureImage',
  group: 'block',
  atom: true,
  draggable: true,

  addAttributes() {
    return {
      src: { default: null },
      alt: { default: null },
      caption: { default: null },
    };
  },

  parseHTML() {
    return [
      {
        tag: 'figure[data-figure]',
        getAttrs: (el) => {
          const img = el.querySelector('img');
          const cap = el.querySelector('figcaption');
          return {
            src: img?.getAttribute('src') || null,
            alt: img?.getAttribute('alt') || null,
            caption: cap?.textContent || null,
          };
        },
      },
    ];
  },

  renderHTML({ node }) {
    const { src, alt, caption } = node.attrs;
    return [
      'figure',
      { 'data-figure': '1', class: 'figure-block' },
      ['img', { src: src || '', alt: alt || '', loading: 'lazy' }],
      ['figcaption', { class: 'figure-caption' }, caption || ''],
    ];
  },

  addCommands() {
    return {
      setFigureImage: (attrs) => ({ commands }) => commands.insertContent({ type: this.name, attrs }),
    };
  },
});

/* ─────────────────────────────────────────────────────────────
   Video / YouTube embed
   ───────────────────────────────────────────────────────────── */
export const VideoEmbed = Node.create({
  name: 'videoEmbed',
  group: 'block',
  atom: true,
  draggable: true,

  addAttributes() {
    return { src: { default: null } };
  },

  parseHTML() {
    return [
      {
        tag: 'div[data-video]',
        getAttrs: (el) => ({
          src: el.getAttribute('data-src') || el.querySelector('iframe')?.getAttribute('src') || null,
        }),
      },
    ];
  },

  renderHTML({ node }) {
    const src = node.attrs.src || '';
    return [
      'div',
      { 'data-video': '1', 'data-src': src, class: 'video-embed' },
      ['iframe', { src, frameborder: '0', allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture', allowfullscreen: 'true', loading: 'lazy' }],
    ];
  },

  addCommands() {
    return {
      setVideoEmbed: (src) => ({ commands }) => commands.insertContent({ type: this.name, attrs: { src } }),
    };
  },
});

/* ─────────────────────────────────────────────────────────────
   Callout — Key Takeaway / Warning / Notice / CTA
   ───────────────────────────────────────────────────────────── */
export const Callout = Node.create({
  name: 'callout',
  group: 'block',
  content: 'block+',
  defining: true,

  addAttributes() {
    return { variant: { default: 'notice' } };
  },

  parseHTML() {
    return [
      {
        tag: 'div[data-callout]',
        getAttrs: (el) => ({ variant: el.getAttribute('data-callout') || 'notice' }),
      },
    ];
  },

  renderHTML({ node }) {
    const variant = node.attrs.variant;
    return ['div', { 'data-callout': variant, class: `callout callout-${variant}` }, 0];
  },

  addCommands() {
    return {
      setCallout: (variant) => ({ commands }) =>
        commands.insertContent({ type: this.name, attrs: { variant }, content: [{ type: 'paragraph' }] }),
    };
  },
});

/* ─────────────────────────────────────────────────────────────
   Step-by-step (ol.steps > li.step)
   ───────────────────────────────────────────────────────────── */
export const Step = Node.create({
  name: 'step',
  content: 'block+',
  defining: true,

  parseHTML() {
    return [{ tag: 'li.step' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['li', { ...HTMLAttributes, class: 'step' }, 0];
  },
});

export const Steps = Node.create({
  name: 'steps',
  group: 'block',
  content: 'step+',
  defining: true,

  parseHTML() {
    return [{ tag: 'ol.steps' }];
  },

  renderHTML({ HTMLAttributes }) {
    return ['ol', { ...HTMLAttributes, class: 'steps' }, 0];
  },

  addCommands() {
    return {
      setSteps: () => ({ commands }) =>
        commands.insertContent({ type: this.name, content: [{ type: 'step', content: [{ type: 'paragraph' }] }] }),
    };
  },
});

/* ─────────────────────────────────────────────────────────────
   Pros & Cons (two-column)
   ───────────────────────────────────────────────────────────── */
export const Pros = Node.create({
  name: 'pros',
  content: 'block+',
  defining: true,
  parseHTML() { return [{ tag: 'div[data-proscons="pros"]' }]; },
  renderHTML({ HTMLAttributes }) {
    return ['div', { ...HTMLAttributes, 'data-proscons': 'pros', class: 'proscons-col proscons-pros' }, 0];
  },
});

export const Cons = Node.create({
  name: 'cons',
  content: 'block+',
  defining: true,
  parseHTML() { return [{ tag: 'div[data-proscons="cons"]' }]; },
  renderHTML({ HTMLAttributes }) {
    return ['div', { ...HTMLAttributes, 'data-proscons': 'cons', class: 'proscons-col proscons-cons' }, 0];
  },
});

export const ProsCons = Node.create({
  name: 'prosCons',
  group: 'block',
  content: 'pros cons',
  defining: true,
  parseHTML() { return [{ tag: 'div.proscons' }]; },
  renderHTML({ HTMLAttributes }) {
    return ['div', { ...HTMLAttributes, class: 'proscons' }, 0];
  },
  addCommands() {
    return {
      setProsCons: () => ({ commands }) =>
        commands.insertContent({
          type: this.name,
          content: [
            { type: 'pros', content: [{ type: 'paragraph', content: [{ type: 'text', text: 'Benefit or advantage' }] }] },
            { type: 'cons', content: [{ type: 'paragraph', content: [{ type: 'text', text: 'Drawback or limitation' }] }] },
          ],
        }),
    };
  },
});

/* ─────────────────────────────────────────────────────────────
   Product recommendation card (atom)
   ───────────────────────────────────────────────────────────── */
export const ProductCard = Node.create({
  name: 'productCard',
  group: 'block',
  atom: true,
  draggable: true,

  addAttributes() {
    return {
      slug: { default: null },
      name: { default: null },
      price: { default: null },
      image: { default: null },
    };
  },

  parseHTML() {
    return [
      {
        tag: 'div[data-product-rec]',
        getAttrs: (el) => ({
          slug: el.getAttribute('data-slug'),
          name: el.getAttribute('data-name'),
          price: el.getAttribute('data-price'),
          image: el.getAttribute('data-image'),
        }),
      },
    ];
  },

  renderHTML({ node }) {
    const { slug, name, price, image } = node.attrs;
    return [
      'div',
      { 'data-product-rec': '1', 'data-slug': slug, 'data-name': name, 'data-price': price, 'data-image': image, class: 'product-rec' },
      image ? ['img', { src: image, alt: name || '', loading: 'lazy' }] : ['div', { class: 'product-rec-img product-rec-img--placeholder' }],
      ['div', { class: 'product-rec-body' },
        ['a', { href: slug ? `/products/${slug}` : '/products', class: 'product-rec-name' }, name || 'Recommended product'],
        price ? ['span', { class: 'product-rec-price' }, price] : ['span'],
      ],
    ];
  },

  addCommands() {
    return {
      setProductCard: (attrs) => ({ commands }) => commands.insertContent({ type: this.name, attrs }),
    };
  },
});

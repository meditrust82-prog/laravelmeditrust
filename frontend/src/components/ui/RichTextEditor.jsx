import React, { useCallback, useImperativeHandle, forwardRef, useState, useEffect, useRef } from 'react';
import { useEditor, EditorContent } from '@tiptap/react';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Underline from '@tiptap/extension-underline';
import Placeholder from '@tiptap/extension-placeholder';
import { Table, TableRow, TableHeader, TableCell } from '@tiptap/extension-table';
import {
  FaPlus, FaImage, FaVideo, FaTable, FaExclamationTriangle, FaInfoCircle, FaBullhorn,
  FaListOl, FaCheckCircle, FaShoppingCart, FaSearch, FaTimes, FaChevronDown, FaHeading,
  FaListUl, FaQuoteLeft, FaParagraph, FaTrashAlt, FaColumns, FaRulerHorizontal, FaRulerVertical,
} from 'react-icons/fa';
import {
  FigureImage, VideoEmbed, Callout, Steps, Step, ProductCard, ProsCons, Pros, Cons,
} from './richText/extensions';

/* Convert a watch/share URL into an embeddable iframe src. */
const toEmbedUrl = (url) => {
  const yt = String(url).match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([\w-]{6,})/);
  if (yt) return `https://www.youtube.com/embed/${yt[1]}`;
  const vimeo = String(url).match(/vimeo\.com\/(\d+)/);
  if (vimeo) return `https://player.vimeo.com/video/${vimeo[1]}`;
  return url;
};

const formatPrice = (price) => {
  const n = Number(price);
  if (!Number.isFinite(n) || n <= 0) return null;
  return `NPR ${n.toLocaleString('en-IN')}`;
};

const ToolbarButton = ({ onClick, active, title, children, danger }) => (
  <button
    type="button"
    onMouseDown={(e) => { e.preventDefault(); onClick(); }}
    title={title}
    className={`p-1.5 rounded text-sm font-medium transition-colors ${
      active
        ? 'bg-primary-600 text-white'
        : danger
          ? 'text-gray-600 hover:bg-red-50 hover:text-red-600'
          : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'
    }`}
  >
    {children}
  </button>
);

const Divider = () => <div className="w-px h-5 bg-gray-300 mx-1" />;

const RichTextEditor = forwardRef(({
  value, onChange, placeholder = 'Write here...', uploadImage, products = [],
}, ref) => {
  const [blockMenuOpen, setBlockMenuOpen] = useState(false);
  const [menuMode, setMenuMode] = useState('blocks'); // 'blocks' | 'products'
  const [productQuery, setProductQuery] = useState('');
  const menuRef = useRef(null);

  const editor = useEditor({
    extensions: [
      StarterKit.configure({
        heading: { levels: [2, 3] },
        code: false,
        codeBlock: false,
      }),
      Underline,
      Link.configure({
        openOnClick: false,
        HTMLAttributes: { class: 'text-primary-600 underline cursor-pointer' },
      }),
      Placeholder.configure({ placeholder }),
      Table.configure({ resizable: true }),
      TableRow,
      TableHeader,
      TableCell,
      FigureImage,
      VideoEmbed,
      Callout,
      Steps,
      Step,
      ProductCard,
      ProsCons,
      Pros,
      Cons,
    ],
    content: value || '',
    onUpdate: ({ editor: ed }) => {
      onChange(ed.getHTML());
    },
  });

  useImperativeHandle(ref, () => ({
    focus: () => editor?.chain().focus().run(),
    insertHtml: (html) => { editor?.chain().focus().insertContent(html).run(); },
    setContent: (html) => { editor?.commands.setContent(html || ''); },
    insertLink: (href, text) => {
      const url = href.startsWith('http') || href.startsWith('/') ? href : `https://${href}`;
      editor?.chain().focus().insertContent(`<a href="${url}">${text || url}</a>`).run();
    },
  }), [editor]);

  /* Close menus on outside click. */
  useEffect(() => {
    const handler = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) {
        setBlockMenuOpen(false);
        setMenuMode('blocks');
      }
    };
    document.addEventListener('mousedown', handler);
    return () => document.removeEventListener('mousedown', handler);
  }, []);

  const setLink = useCallback(() => {
    if (!editor) return;
    const prev = editor.getAttributes('link').href || '';
    const url = window.prompt('Enter URL:', prev);
    if (url === null) return;
    if (url === '') {
      editor.chain().focus().extendMarkRange('link').unsetLink().run();
      return;
    }
    const href = url.startsWith('http') ? url : `https://${url}`;
    editor.chain().focus().extendMarkRange('link').setLink({ href }).run();
  }, [editor]);

  const closeMenu = () => { setBlockMenuOpen(false); setMenuMode('blocks'); setProductQuery(''); };

  const insertImageFromUrl = () => {
    const url = window.prompt('Image URL:');
    if (!url) return;
    const alt = window.prompt('Alt text (accessibility / image SEO):') || '';
    const caption = window.prompt('Caption (optional):') || '';
    editor?.chain().focus().setFigureImage({ src: url, alt, caption }).run();
    closeMenu();
  };

  const handleUpload = async (file) => {
    if (!uploadImage || !file) return;
    const url = await uploadImage(file);
    if (url) editor?.chain().focus().setFigureImage({ src: url, alt: '', caption: '' }).run();
    closeMenu();
  };

  const insertVideo = () => {
    const url = window.prompt('YouTube or Vimeo URL:');
    if (!url) return;
    editor?.chain().focus().setVideoEmbed(toEmbedUrl(url)).run();
    closeMenu();
  };

  const insertTable = () => {
    editor?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
    closeMenu();
  };

  const insertCallout = (variant) => { editor?.chain().focus().setCallout(variant).run(); closeMenu(); };
  const insertSteps = () => { editor?.chain().focus().setSteps().run(); closeMenu(); };

  const insertProduct = (p) => {
    const slug = p.slug || p.id;
    editor?.chain().focus().setProductCard({
      slug, name: p.name, price: formatPrice(p.price), image: p.image || (p.allImages?.[0] || null),
    }).run();
    closeMenu();
  };

  const filteredProducts = (products || []).filter((p) =>
    !productQuery || (p.name || '').toLowerCase().includes(productQuery.toLowerCase())
  ).slice(0, 25);

  if (!editor) return null;

  const inTable = editor.isActive('table');

  const BLOCK_ITEMS = [
    { label: 'Paragraph', icon: <FaParagraph />, run: () => { editor.chain().focus().setParagraph().run(); closeMenu(); } },
    { label: 'Heading 2', icon: <FaHeading />, run: () => { editor.chain().focus().setHeading({ level: 2 }).run(); closeMenu(); } },
    { label: 'Heading 3', icon: <FaHeading className="text-xs" />, run: () => { editor.chain().focus().setHeading({ level: 3 }).run(); closeMenu(); } },
    { label: 'Bullet List', icon: <FaListUl />, run: () => { editor.chain().focus().toggleBulletList().run(); closeMenu(); } },
    { label: 'Numbered List', icon: <FaListOl />, run: () => { editor.chain().focus().toggleOrderedList().run(); closeMenu(); } },
    { label: 'Quote', icon: <FaQuoteLeft />, run: () => { editor.chain().focus().toggleBlockquote().run(); closeMenu(); } },
    { label: 'Table (3×3)', icon: <FaTable />, run: insertTable },
    { label: 'Image', icon: <FaImage />, run: insertImageFromUrl },
    ...(uploadImage ? [{ label: 'Upload Image', icon: <FaImage />, run: () => { document.getElementById('rte-image-upload')?.click(); } }] : []),
    { label: 'Video / YouTube', icon: <FaVideo />, run: insertVideo },
    { label: 'Key Takeaway', icon: <FaCheckCircle className="text-green-500" />, run: () => insertCallout('key-takeaway') },
    { label: 'Warning', icon: <FaExclamationTriangle className="text-amber-500" />, run: () => insertCallout('warning') },
    { label: 'Notice', icon: <FaInfoCircle className="text-blue-500" />, run: () => insertCallout('notice') },
    { label: 'CTA', icon: <FaBullhorn className="text-primary-600" />, run: () => insertCallout('cta') },
    { label: 'Step-by-Step', icon: <FaListOl className="text-purple-500" />, run: insertSteps },
    { label: 'Pros & Cons', icon: <FaColumns className="text-amber-600" />, run: () => { editor.chain().focus().setProsCons().run(); closeMenu(); } },
    ...(products?.length ? [{ label: 'Product Recommendation', icon: <FaShoppingCart className="text-emerald-500" />, run: () => { setMenuMode('products'); } }] : []),
  ];

  return (
    <div className="border border-gray-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-transparent">
      {/* Hidden file input for image upload */}
      {uploadImage && (
        <input id="rte-image-upload" type="file" accept="image/*" className="hidden" onChange={(e) => { handleUpload(e.target.files[0]); e.target.value = ''; }} />
      )}

      {/* Toolbar */}
      <div className="flex flex-wrap items-center gap-0.5 px-2 py-1.5 border-b border-gray-200 bg-gray-50" ref={menuRef}>
        <ToolbarButton onClick={() => editor.chain().focus().toggleBold().run()} active={editor.isActive('bold')} title="Bold (Ctrl+B)">
          <span className="font-bold">B</span>
        </ToolbarButton>
        <ToolbarButton onClick={() => editor.chain().focus().toggleItalic().run()} active={editor.isActive('italic')} title="Italic (Ctrl+I)">
          <span className="italic">I</span>
        </ToolbarButton>
        <ToolbarButton onClick={() => editor.chain().focus().toggleUnderline().run()} active={editor.isActive('underline')} title="Underline (Ctrl+U)">
          <span className="underline">U</span>
        </ToolbarButton>

        <Divider />

        <ToolbarButton onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} active={editor.isActive('heading', { level: 2 })} title="Heading 2">H2</ToolbarButton>
        <ToolbarButton onClick={() => editor.chain().focus().toggleHeading({ level: 3 }).run()} active={editor.isActive('heading', { level: 3 })} title="Heading 3">H3</ToolbarButton>

        <Divider />

        <ToolbarButton onClick={() => editor.chain().focus().toggleBulletList().run()} active={editor.isActive('bulletList')} title="Bullet List">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <line x1="9" y1="6" x2="20" y2="6"/><line x1="9" y1="12" x2="20" y2="12"/><line x1="9" y1="18" x2="20" y2="18"/>
            <circle cx="4" cy="6" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"/>
          </svg>
        </ToolbarButton>
        <ToolbarButton onClick={() => editor.chain().focus().toggleOrderedList().run()} active={editor.isActive('orderedList')} title="Numbered List">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <line x1="10" y1="6" x2="21" y2="6"/><line x1="10" y1="12" x2="21" y2="12"/><line x1="10" y1="18" x2="21" y2="18"/>
            <path d="M4 6h1v4" strokeLinecap="round" strokeLinejoin="round"/><path d="M4 10h2" strokeLinecap="round"/><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1" strokeLinecap="round" strokeLinejoin="round"/>
          </svg>
        </ToolbarButton>

        <Divider />

        <ToolbarButton onClick={setLink} active={editor.isActive('link')} title="Insert / Edit Link">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71" strokeLinecap="round" strokeLinejoin="round"/>
          </svg>
        </ToolbarButton>
        {editor.isActive('link') && (
          <ToolbarButton onClick={() => editor.chain().focus().unsetLink().run()} active={false} title="Remove Link">
            <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
              <path d="M18.36 5.64a5 5 0 010 7.07l-3 3a5 5 0 01-7.07 0" strokeLinecap="round"/><line x1="2" y1="2" x2="22" y2="22"/>
            </svg>
          </ToolbarButton>
        )}

        <Divider />

        <ToolbarButton onClick={() => editor.chain().focus().toggleBlockquote().run()} active={editor.isActive('blockquote')} title="Blockquote">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179zm10 0C13.553 16.227 13 15 13 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.804.167 3.226 1.648 3.226 3.489a3.5 3.5 0 01-3.5 3.5c-1.073 0-2.099-.49-2.748-1.179z"/>
          </svg>
        </ToolbarButton>

        <Divider />

        {/* Block inserter */}
        <div className="relative">
          <button
            type="button"
            onMouseDown={(e) => { e.preventDefault(); setBlockMenuOpen((v) => !v); setMenuMode('blocks'); }}
            className="p-1.5 rounded text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 flex items-center gap-1"
            title="Insert block"
          >
            <FaPlus /> <span className="text-xs">Block</span> <FaChevronDown className="text-[9px]" />
          </button>

          {blockMenuOpen && (
            <div className="absolute left-0 top-full mt-1 z-50 w-64 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto">
              {menuMode === 'blocks' ? (
                BLOCK_ITEMS.map((item, i) => (
                  <button
                    key={i}
                    type="button"
                    onMouseDown={(e) => { e.preventDefault(); item.run(); }}
                    className="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-2.5"
                  >
                    <span className="w-4 flex justify-center">{item.icon}</span> {item.label}
                  </button>
                ))
              ) : (
                <div>
                  <div className="flex items-center gap-2 px-3 py-2 border-b border-gray-100 sticky top-0 bg-white">
                    <button type="button" onMouseDown={(e) => { e.preventDefault(); setMenuMode('blocks'); }} className="text-gray-400 hover:text-gray-600"><FaChevronDown className="rotate-90" /></button>
                    <FaShoppingCart className="text-emerald-500" />
                    <span className="text-sm font-semibold text-gray-700">Recommend a product</span>
                  </div>
                  <div className="px-2 py-2">
                    <div className="relative mb-2">
                      <FaSearch className="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs" />
                      <input
                        value={productQuery}
                        onChange={(e) => setProductQuery(e.target.value)}
                        placeholder="Search products…"
                        autoFocus
                        className="w-full pl-8 pr-2 py-1.5 border border-gray-200 rounded text-sm focus:outline-none focus:ring-2 focus:ring-primary-300"
                      />
                    </div>
                    <div className="space-y-0.5">
                      {filteredProducts.length === 0 && <p className="text-xs text-gray-400 px-1 py-2">No products found.</p>}
                      {filteredProducts.map((p) => (
                        <button
                          key={p.slug || p.id}
                          type="button"
                          onMouseDown={(e) => { e.preventDefault(); insertProduct(p); }}
                          className="w-full text-left px-2 py-2 rounded hover:bg-gray-50 flex items-center gap-2"
                        >
                          {p.image ? (
                            <img src={p.image} alt="" className="w-8 h-8 rounded object-cover bg-gray-100 shrink-0" />
                          ) : (
                            <span className="w-8 h-8 rounded bg-gray-100 shrink-0" />
                          )}
                          <span className="min-w-0">
                            <span className="block text-sm text-gray-800 line-clamp-1">{p.name}</span>
                            {p.price ? <span className="block text-xs text-gray-400">NPR {Number(p.price).toLocaleString('en-IN')}</span> : null}
                          </span>
                        </button>
                      ))}
                    </div>
                  </div>
                </div>
              )}
            </div>
          )}
        </div>

        <div className="flex-1" />

        <ToolbarButton onClick={() => editor.chain().focus().undo().run()} active={false} title="Undo">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M3 7v6h6" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M21 17a9 9 0 00-9-9 9 9 0 00-6 2.3L3 13" strokeLinecap="round" strokeLinejoin="round"/>
          </svg>
        </ToolbarButton>
        <ToolbarButton onClick={() => editor.chain().focus().redo().run()} active={false} title="Redo">
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
            <path d="M21 7v6h-6" strokeLinecap="round" strokeLinejoin="round"/>
            <path d="M3 17a9 9 0 019-9 9 9 0 016 2.3L21 13" strokeLinecap="round" strokeLinejoin="round"/>
          </svg>
        </ToolbarButton>
      </div>

      {/* Table controls */}
      {inTable && (
        <div className="flex flex-wrap items-center gap-0.5 px-2 py-1 border-b border-blue-100 bg-blue-50/60">
          <span className="text-[10px] font-bold uppercase text-blue-500 mr-1">Table</span>
          <ToolbarButton onClick={() => editor.chain().focus().addRowAfter().run()} title="Add row below"><FaRulerHorizontal /></ToolbarButton>
          <ToolbarButton onClick={() => editor.chain().focus().addColumnAfter().run()} title="Add column right"><FaRulerVertical /></ToolbarButton>
          <ToolbarButton onClick={() => editor.chain().focus().toggleHeaderRow().run()} active={editor.isActive('table')} title="Toggle header row"><FaHeading /></ToolbarButton>
          <ToolbarButton onClick={() => editor.chain().focus().deleteRow().run()} title="Delete row" danger><FaTrashAlt /></ToolbarButton>
          <ToolbarButton onClick={() => editor.chain().focus().deleteColumn().run()} title="Delete column" danger><FaColumns /></ToolbarButton>
          <ToolbarButton onClick={() => editor.chain().focus().deleteTable().run()} title="Delete table" danger><FaTable /></ToolbarButton>
        </div>
      )}

      {/* Editor area */}
      <EditorContent
        editor={editor}
        className="prose prose-sm max-w-none px-4 py-3 min-h-[120px] text-gray-800 focus:outline-none [&_.tiptap]:outline-none [&_.tiptap_p.is-editor-empty:first-child::before]:content-[attr(data-placeholder)] [&_.tiptap_p.is-editor-empty:first-child::before]:text-gray-400 [&_.tiptap_p.is-editor-empty:first-child::before]:pointer-events-none [&_.tiptap_p.is-editor-empty:first-child::before]:float-left [&_.tiptap_p.is-editor-empty:first-child::before]:h-0"
      />
    </div>
  );
});

RichTextEditor.displayName = 'RichTextEditor';

export default RichTextEditor;

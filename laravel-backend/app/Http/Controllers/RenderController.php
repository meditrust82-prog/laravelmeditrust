<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class RenderController extends Controller
{
    protected string $site = 'https://meditrustnepal.com';
    protected string $siteName = 'Meditrust Nepal';
    protected string $defaultDesc = "Meditrust Nepal — Nepal's trusted supplier of surgical instruments, ICU equipment, patient monitors, diagnostic devices and more. CE & ISO certified products, 24/7 support.";

    protected function escape(string $value = ''): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    protected function shell(array $props): string
    {
        $title = $this->escape($props['title'] ?? $this->siteName);
        $description = $this->escape($props['description'] ?? $this->defaultDesc);
        $canonical = $this->escape($props['canonical'] ?? $this->site);
        $image = $this->escape($props['image'] ?? ($this->site . '/logo.png'));
        $ogType = $this->escape($props['ogType'] ?? 'website');
        $ogTitle = !empty($props['ogTitle']) ? $this->escape($props['ogTitle']) : $title;
        $ogDesc = !empty($props['ogDesc']) ? $this->escape($props['ogDesc']) : $description;
        $ogImage = !empty($props['ogImage']) ? $this->escape($props['ogImage']) : $image;
        $body = $props['body'] ?? '';
        $schemas = collect($props['schemas'] ?? [])
            ->map(fn ($schema) => '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>')
            ->implode("\n  ");

        return "<!DOCTYPE html>
<html lang=\"en\">
<head>
  <meta charset=\"UTF-8\"/>
  <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"/>
  <title>{$title}</title>
  <meta name=\"description\" content=\"{$description}\"/>
  <link rel=\"canonical\" href=\"{$canonical}\"/>
  <link rel=\"alternate\" hreflang=\"en\" href=\"{$canonical}\"/>
  <link rel=\"alternate\" hreflang=\"ne\" href=\"{$canonical}\"/>
  <link rel=\"alternate\" hreflang=\"x-default\" href=\"{$canonical}\"/>
  <meta property=\"og:title\" content=\"{$ogTitle}\"/>
  <meta property=\"og:description\" content=\"{$ogDesc}\"/>
  <meta property=\"og:url\" content=\"{$canonical}\"/>
  <meta property=\"og:image\" content=\"{$ogImage}\"/>
  <meta property=\"og:image:secure_url\" content=\"{$ogImage}\"/>
  <meta property=\"og:image:type\" content=\"image/jpeg\"/>
  <meta property=\"og:image:alt\" content=\"{$ogTitle}\"/>
  <meta property=\"og:image:width\" content=\"1200\"/>
  <meta property=\"og:image:height\" content=\"630\"/>
  <meta property=\"og:type\" content=\"{$ogType}\"/>
  <meta property=\"og:site_name\" content=\"{$this->escape($this->siteName)}\"/>
  <meta property=\"og:locale\" content=\"en_US\"/>
  <meta property=\"og:locale:alternate\" content=\"ne_NP\"/>
  <meta name=\"twitter:card\" content=\"summary_large_image\"/>
  <meta name=\"twitter:title\" content=\"{$ogTitle}\"/>
  <meta name=\"twitter:description\" content=\"{$ogDesc}\"/>
  <meta name=\"twitter:image\" content=\"{$ogImage}\"/>
  <meta name=\"twitter:image:alt\" content=\"{$ogTitle}\"/>
  <meta name=\"robots\" content=\"index,follow,max-snippet:-1,max-image-preview:large,max-video-preview:-1\"/>
  <meta name=\"geo.region\" content=\"NP\"/>
  <meta name=\"geo.placename\" content=\"Kathmandu, Nepal\"/>
  {$schemas}
  </head>
  <body>
  {$body}
  </body>
  </html>";
    }

    protected function stripHtml(?string $html): string
    {
        $clean = strip_tags((string) $html);
        $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = preg_replace('/\s+/', ' ', $clean);
        return mb_substr(trim($clean), 0, 200);
    }

    protected function productImages(Product $product): array
    {
        $raw = $product->images;
        if (empty($raw) && !empty($product->image)) {
            $raw = [$product->image];
        }
        return collect($raw ?? [])
            ->map(fn ($image) => is_array($image) ? ($image['url'] ?? $image['path'] ?? null) : $image)
            ->filter(fn ($image) => is_string($image) && !empty($image))
            ->map(fn ($image) => str_starts_with($image, 'http') ? $image : ($this->site . '/' . ltrim($image, '/')))
            ->values()
            ->all();
    }

    protected function htmlResponse(string $html, int $status = 200)
    {
        return response($html, $status)->header('Content-Type', 'text/html');
    }

    public function render(Request $req)
    {
        if ($req->header('x-prerender-token') !== 'internal') {
            return response('Forbidden', 403);
        }

        $path = preg_replace('/[<>"\']/', '', (string) $req->query('path', '/products'));

        if (preg_match('~^/products/([^/?#]+)$~', $path, $m)) {
            $slugOrId = $m[1];
            $product = Product::where('slug', $slugOrId)
                ->orWhere('id', $slugOrId)
                ->first();

            if (!$product) {
                return $this->htmlResponse($this->shell([
                    'title' => 'Product Not Found | ' . $this->siteName,
                    'description' => $this->defaultDesc,
                    'canonical' => $this->site . '/products',
                ]), 404);
            }

            $slug = $product->slug ?: $product->id;
            $canonical = $this->site . '/products/' . $slug;
            $images = $this->productImages($product);
            $description = $product->meta_description
                ?: $this->stripHtml($product->description)
                ?: "Buy {$product->name} from Meditrust Nepal. CE & ISO certified, competitive pricing, 24/7 support.";
            $priceValidUntil = now()->addDays(30)->toDateString();
            $price = (float) $product->price;

            $productSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                '@id' => $canonical . '#product',
                'name' => $product->name,
                'description' => $description,
                'image' => $images ?: [$this->site . '/logo.png'],
                'sku' => 'MN-' . $slug,
                'brand' => $product->brand ? ['@type' => 'Brand', 'name' => $product->brand] : null,
                'category' => $product->category ?: 'Medical Equipment',
                'dateModified' => $product->updatedAt,
                'offers' => [
                    '@type' => 'Offer',
                    '@id' => $canonical . '#offer',
                    'url' => $canonical,
                    'priceCurrency' => 'NPR',
                    'price' => $price,
                    'priceValidUntil' => $priceValidUntil,
                    'availability' => ((int) $product->stock) > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'seller' => ['@type' => 'Organization', '@id' => $this->site . '/#organization', 'name' => $this->siteName],
                ],
            ];

            $breadcrumb = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => array_values(array_filter([
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $this->site],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Products', 'item' => $this->site . '/products'],
                    $product->category ? ['@type' => 'ListItem', 'position' => 3, 'name' => $product->category] : null,
                    ['@type' => 'ListItem', 'position' => $product->category ? 4 : 3, 'name' => $product->name],
                ])),
            ];

            $faq = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [
                    ['@type' => 'Question', 'name' => "Where can I buy {$product->name} in Nepal?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => "{$product->name} is available at Meditrust Nepal, Kathmandu. Order at meditrustnepal.com or call +977-9818100515."]],
                    ['@type' => 'Question', 'name' => "What is the price of {$product->name} in Nepal?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'The price of ' . $product->name . ' at Meditrust Nepal is NRS ' . ($price ? number_format($price) : 'competitive') . '. Contact us for institutional pricing.']],
                    ['@type' => 'Question', 'name' => "Is {$product->name} CE and ISO certified?", 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes, Meditrust Nepal supplies CE and ISO certified ' . ($product->category ?: 'medical equipment') . ' including ' . $product->name . '.']],
                ],
            ];

            $body = '<h1>' . $this->escape($product->name) . '</h1>'
                . '<p class="speakable">' . $this->escape($description) . '</p>'
                . ($product->category ? '<p>Category: ' . $this->escape($product->category) . '</p>' : '')
                . ($price ? '<p>Price: NRS ' . number_format($price) . '</p>' : '')
                . ($product->brand ? '<p>Brand: ' . $this->escape($product->brand) . '</p>' : '')
                . '<p><a href="' . $this->site . '/products">Back to all products</a></p>';
            $primaryImage = $images[0] ?? ($product->image ?: null);
            $ogImage = $product->og_image ?: $primaryImage;
            if ($ogImage) {
                if (str_contains($ogImage, 'res.cloudinary.com')) {
                    $ogImage = preg_replace('/\/upload\/(?:[a-zA-Z0-9_:,]+\/)?/', '/upload/f_jpg,q_auto,w_1200,h_630,c_pad,b_white/', $ogImage, 1);
                } elseif (!str_starts_with($ogImage, 'http')) {
                    $ogImage = $this->site . '/' . ltrim($ogImage, '/');
                }
            } else {
                $ogImage = $this->site . '/logo.png';
            }

            return $this->htmlResponse($this->shell([
                'title' => $product->meta_title ?: ($product->name . ' — Buy in Nepal | ' . $this->siteName),
                'description' => $description,
                'canonical' => $canonical,
                'image' => $images[0] ?? ($this->site . '/logo.png'),
                'ogType' => 'product',
                'ogTitle' => $product->og_title ?: null,
                'ogDesc' => $product->og_desc ?: null,
                'ogImage' => $ogImage,
                'schemas' => [$productSchema, $breadcrumb, $faq],
                'body' => $body,
            ]));
        }

        if ($path === '/' || $path === '/products') {
            $categorySeo = [
                'Surgical Instruments' => ['title' => 'Surgical Instruments Nepal — Buy CE & ISO Certified', 'desc' => 'Buy surgical instruments in Nepal from Meditrust Nepal. Laparoscopic tools, general surgery sets, orthopedic instruments. CE & ISO certified. Kathmandu delivery.'],
                'ICU Equipment' => ['title' => 'ICU Equipment Nepal — Ventilators, Patient Monitors, Infusion Pumps', 'desc' => 'Buy ICU equipment in Nepal — ventilators, patient monitors, infusion pumps, defibrillators. CE & ISO certified. Meditrust Nepal, Kathmandu.'],
                'Diagnostic Equipment' => ['title' => 'Diagnostic Equipment Nepal — ECG, Ultrasound, X-Ray Machines', 'desc' => 'Buy diagnostic equipment in Nepal — ECG machines, ultrasound, X-ray, pulse oximeters. CE certified. Meditrust Nepal, Kathmandu.'],
                'Hospital Furniture' => ['title' => 'Hospital Furniture Nepal — Beds, Stretchers, Wheelchairs', 'desc' => 'Buy hospital furniture in Nepal — hospital beds, stretchers, wheelchairs, IV stands. CE certified. Meditrust Nepal, Kathmandu.'],
                'Laboratory Equipment' => ['title' => 'Laboratory Equipment Nepal — Analysers, Microscopes, Centrifuges', 'desc' => 'Buy laboratory equipment in Nepal — biochemistry analysers, haematology analysers, microscopes, centrifuges. CE certified.'],
                'Sterilization Equipment' => ['title' => 'Sterilization Equipment Nepal — Autoclaves, UV Sterilizers', 'desc' => 'Buy sterilization equipment in Nepal — autoclaves, UV sterilizers, steam sterilizers. CE certified. Meditrust Nepal.'],
                'Imaging Equipment' => ['title' => 'Imaging Equipment Nepal — X-Ray, Ultrasound, C-arm', 'desc' => 'Medical imaging equipment supplier Nepal — digital X-ray, ultrasound, C-arm. CE certified. Meditrust Nepal, Kathmandu.'],
            ];
            $rawCategory = (string) $req->query('category', '');
            $products = Product::query()
                ->when($rawCategory !== '', fn ($query) => $query->whereRaw('LOWER(category) = ?', [strtolower($rawCategory)]))
                ->latest()
                ->limit(50)
                ->get();
            $canonical = $rawCategory !== '' ? $this->site . '/products?category=' . rawurlencode($rawCategory) : $this->site . '/products';
            $meta = $categorySeo[$rawCategory] ?? null;
            $title = $meta ? $meta['title'] . ' | ' . $this->siteName : 'Surgical & Medical Equipment Supplier Nepal | ' . $this->siteName;
            $description = $meta['desc'] ?? $this->defaultDesc;
            $itemList = [
                '@context' => 'https://schema.org',
                '@type' => 'ItemList',
                'name' => $rawCategory !== '' ? $rawCategory . ' — Nepal' : 'Medical Equipment Nepal',
                'url' => $canonical,
                'itemListElement' => $products->filter(fn ($p) => $p->slug)->values()->map(fn ($p, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'url' => $this->site . '/products/' . $p->slug,
                    'name' => $p->name,
                ])->all(),
            ];
            $body = '<h1 class="speakable">' . $this->escape($rawCategory ?: 'Medical Equipment Supplier Nepal') . '</h1>'
                . '<p>' . $this->escape($description) . '</p>'
                . '<ul>' . $products->filter(fn ($p) => $p->slug)->map(fn ($p) => '<li><a href="' . $this->site . '/products/' . $this->escape($p->slug) . '">' . $this->escape($p->name) . '</a></li>')->implode("\n") . '</ul>';

            return $this->htmlResponse($this->shell([
                'title' => $title,
                'description' => $description,
                'canonical' => $canonical,
                'schemas' => [$itemList],
                'body' => $body,
            ]));
        }

        $staticMeta = [
            '/about' => ['title' => 'About Us | ' . $this->siteName, 'desc' => "Learn about Meditrust Nepal — Kathmandu's leading medical equipment supplier since 2009."],
            '/contact' => ['title' => 'Contact Us | ' . $this->siteName, 'desc' => 'Contact Meditrust Nepal for medical equipment inquiries, quotations, and technical support.'],
            '/services' => ['title' => 'Services | ' . $this->siteName, 'desc' => 'Medical equipment installation, training, maintenance, and 24/7 technical support across Nepal.'],
            '/blog' => ['title' => 'Blog | ' . $this->siteName, 'desc' => 'Medical equipment insights, healthcare news, and product guides from Meditrust Nepal.'],
        ];
        if (isset($staticMeta[$path])) {
            $meta = $staticMeta[$path];
            return $this->htmlResponse($this->shell([
                'title' => $meta['title'],
                'description' => $meta['desc'],
                'canonical' => $this->site . $path,
                'body' => '<h1 class="speakable">' . $this->escape(trim(explode('|', $meta['title'])[0])) . '</h1><p>' . $this->escape($meta['desc']) . '</p>',
            ]));
        }

        return $this->htmlResponse($this->shell([
            'title' => $this->siteName . ' — Medical Equipment Supplier Nepal',
            'description' => $this->defaultDesc,
            'canonical' => $this->site . $path,
            'body' => '<h1 class="speakable">' . $this->siteName . '</h1><p>' . $this->defaultDesc . '</p>',
        ]));
    }

    public function front(Request $req)
    {
        $paths = [
            public_path('frontend-index.html'),
            base_path('../frontend/dist/index.html'),
            public_path('index.html'),
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return response()->file($path);
            }
        }

        return response($this->shell([
            'title' => $this->siteName,
            'description' => $this->defaultDesc,
            'canonical' => $this->site,
            'body' => '<div id="app"></div>',
        ]), 200)->header('Content-Type', 'text/html');
    }
}

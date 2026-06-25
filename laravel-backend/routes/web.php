<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\RenderController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\PageController;

Route::get('/api/health', fn () => response()->json(['status' => 'ok', 'version' => 'v1']));
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);
Route::get('/render', [RenderController::class, 'render']);
Route::get('/', function () {
    return redirect('/products');
});

Route::get('/home', [PageController::class, 'home']);
Route::get('/about', [PageController::class, 'about']);
Route::get('/contact', [PageController::class, 'contact']);
Route::get('/services', [PageController::class, 'services']);
Route::get('/blog', [PageController::class, 'blogList']);
Route::get('/blog/{slug}', [PageController::class, 'blogArticle']);
Route::get('/projects', [PageController::class, 'projects']);
Route::get('/compare', [PageController::class, 'compare']);
Route::get('/track/{trackingId}', [PageController::class, 'tracking']);
Route::get('/wishlist', [PageController::class, 'wishlist']);
Route::get('/bulk-inquiry', [PageController::class, 'bulkInquiry']);
Route::get('/kathmandu-medical-equipment-supplier', [PageController::class, 'kathmandu']);
Route::get('/category/{slug}', [PageController::class, 'category']);
Route::get('/my-quotes', [PageController::class, 'quoteStatus']);
Route::get('/brand/{slug}', [PageController::class, 'brand']);
Route::get('/hospital-equipment-nepal', [PageController::class, 'hospitalType']);
Route::get('/clinic-equipment-nepal', [PageController::class, 'hospitalType']);
Route::get('/diagnostic-center-equipment-nepal', [PageController::class, 'hospitalType']);
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy']);
Route::get('/terms-and-conditions', [PageController::class, 'termsConditions']);

Route::get('/products', [PageController::class, 'products']);
Route::get('/products/{idOrSlug}', [PageController::class, 'product']);
Route::get('/cart', [CartController::class, 'index']);

Route::get('/admin', function () {
    return redirect('/admin/dashboard');
});
Route::get('/admin/login', [PageController::class, 'adminLogin']);
Route::get('/admin/dashboard', [PageController::class, 'adminDashboard']);

Route::fallback([PageController::class, 'notFound']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AiController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\GbpController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ServicesSettingsController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProjectController;

$globalThrottle = env('APP_ENV') === 'local' ? '1000,15' : '200,15';
$loginThrottle = env('APP_ENV') === 'local' ? '1000,15' : '30,15';
$refreshThrottle = env('APP_ENV') === 'local' ? '1000,15' : '50,15';

Route::middleware("throttle:{$globalThrottle}")->group(function () use ($loginThrottle, $refreshThrottle) {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware("throttle:{$loginThrottle}");
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware("throttle:{$refreshThrottle}");
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('jwt');
    Route::get('auth/me', [AuthController::class, 'me'])->middleware('jwt');
    Route::put('auth/profile', [AuthController::class, 'updateProfile'])->middleware(['jwt', 'admin']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/export.csv', [ProductController::class, 'exportCsv'])->middleware(['jwt', 'admin']);
    Route::get('products/quotes-export.csv', [ProductController::class, 'exportQuotesCsv'])->middleware(['jwt', 'admin']);
    Route::post('products/import-csv', [ProductController::class, 'importCsv'])->middleware(['jwt', 'admin']);
    Route::get('products/{idOrSlug}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store'])->middleware(['jwt', 'admin']);
    Route::put('products/{id}', [ProductController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware(['jwt', 'admin']);

    Route::post('orders', [OrderController::class, 'store'])->middleware('throttle:20,15');
    Route::get('orders/my', [OrderController::class, 'myOrders'])->middleware('jwt');
    Route::get('orders', [OrderController::class, 'index'])->middleware(['jwt', 'admin']);
    Route::get('orders/{id}', [OrderController::class, 'show'])->middleware('jwt');
    Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->middleware(['jwt', 'admin']);
    Route::get('tracking/{id}', [OrderController::class, 'tracking']);

    Route::get('analytics', [AnalyticsController::class, 'index'])->middleware(['jwt', 'admin']);
    Route::get('analytics/dashboard', [AnalyticsController::class, 'index'])->middleware(['jwt', 'admin']);

    Route::post('ai/chat', [AiController::class, 'chat'])->middleware('throttle:20,1');
    Route::post('ai/recommend', [AiController::class, 'recommend'])->middleware('throttle:8,1');
    Route::post('ai/finder', [AiController::class, 'finder'])->middleware('throttle:8,1');
    Route::post('ai/blog-generate', [AiController::class, 'generateBlog'])->middleware(['jwt', 'admin', 'throttle:10,1']);

    Route::post('webhooks/khalti', [WebhookController::class, 'khalti'])->middleware('throttle:20,15');

    Route::get('gbp/connect', [GbpController::class, 'connect'])->middleware(['jwt', 'admin']);
    Route::get('gbp/callback', [GbpController::class, 'callback']);
    Route::delete('gbp/disconnect', [GbpController::class, 'disconnect'])->middleware(['jwt', 'admin']);
    Route::get('gbp/status', [GbpController::class, 'status'])->middleware(['jwt', 'admin']);
    Route::get('gbp/reviews', [GbpController::class, 'reviews']);
    Route::get('gbp/business-info', [GbpController::class, 'businessInfo']);
    Route::get('gbp/posts', [GbpController::class, 'posts'])->middleware(['jwt', 'admin']);
    Route::post('gbp/posts', [GbpController::class, 'posts'])->middleware(['jwt', 'admin']);

    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::post('testimonials', [TestimonialController::class, 'store'])->middleware(['jwt', 'admin']);
    Route::put('testimonials/{id}', [TestimonialController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy'])->middleware(['jwt', 'admin']);

    Route::get('banners', [BannerController::class, 'index']);
    Route::post('banners/{id}/click', [BannerController::class, 'click']);
    Route::get('banners/admin/all', [BannerController::class, 'adminAll'])->middleware(['jwt', 'admin']);
    Route::post('banners', [BannerController::class, 'store'])->middleware(['jwt', 'admin']);
    Route::put('banners/{id}', [BannerController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::delete('banners/{id}', [BannerController::class, 'destroy'])->middleware(['jwt', 'admin']);

    Route::post('quotes', [QuoteController::class, 'store'])->middleware('throttle:10,1');
    Route::get('quotes/my', [QuoteController::class, 'my'])->middleware('throttle:15,1');
    Route::get('quotes', [QuoteController::class, 'index'])->middleware(['jwt', 'admin']);
    Route::get('quotes/stats', [QuoteController::class, 'stats'])->middleware(['jwt', 'admin']);
    Route::patch('quotes/{id}', [QuoteController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::delete('quotes/{id}', [QuoteController::class, 'destroy'])->middleware(['jwt', 'admin']);

    Route::post('notify/newsletter', [NotifyController::class, 'newsletter'])->middleware('throttle:10,1');
    Route::post('notify/quote', [NotifyController::class, 'quote'])->middleware('throttle:10,1');
    Route::get('notify/subscribers', [NotifyController::class, 'subscribers'])->middleware(['jwt', 'admin']);
    Route::delete('notify/subscribers/{id}', [NotifyController::class, 'destroySubscriber'])->middleware(['jwt', 'admin']);

    Route::get('homepage', [HomepageController::class, 'index']);
    Route::put('homepage', [HomepageController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::get('services-settings', [ServicesSettingsController::class, 'index']);
    Route::put('services-settings', [ServicesSettingsController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::get('legal', [LegalController::class, 'index']);
    Route::put('legal', [LegalController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::get('about-settings', [AboutController::class, 'index']);
    Route::put('about-settings', [AboutController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::get('blogs', [BlogController::class, 'index']);
    Route::get('blogs/all', [BlogController::class, 'all'])->middleware(['jwt', 'admin']);
    Route::post('blogs/upload', [BlogController::class, 'upload'])->middleware(['jwt', 'admin']);
    Route::post('blogs/{id}/duplicate', [BlogController::class, 'duplicate'])->middleware(['jwt', 'admin']);
    Route::get('blogs/{slug}', [BlogController::class, 'show']);
    Route::post('blogs', [BlogController::class, 'store'])->middleware(['jwt', 'admin']);
    Route::put('blogs/{id}', [BlogController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::delete('blogs/{id}', [BlogController::class, 'destroy'])->middleware(['jwt', 'admin']);

    Route::get('projects', [ProjectController::class, 'index']);
    Route::post('projects', [ProjectController::class, 'store'])->middleware(['jwt', 'admin']);
    Route::put('projects/{id}', [ProjectController::class, 'update'])->middleware(['jwt', 'admin']);
    Route::delete('projects/{id}', [ProjectController::class, 'destroy'])->middleware(['jwt', 'admin']);

    Route::get('health', fn () => response()->json(['status' => 'ok', 'version' => 'v1']));

    // Ensure CORS preflight requests are handled for all API routes.
    Route::options('{any}', fn () => response()->noContent())->where('any', '.*');
});

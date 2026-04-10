<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\PromoController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SuperAdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SkinbaeController;
use App\Http\Controllers\DokuWebhookController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================================
// CUSTOMER ROUTES
// ========================================

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Skinbae.ID — brand & treatments (marketing site)
Route::prefix('skinbae')->name('skinbae.')->group(function () {
    Route::get('/', [SkinbaeController::class, 'home'])->name('home');
    Route::get('/services', [SkinbaeController::class, 'services'])->name('services');
    Route::get('/gallery', [SkinbaeController::class, 'gallery'])->name('gallery');
    Route::get('/contact', [SkinbaeController::class, 'contact'])->name('contact');
    Route::post('/contact', [SkinbaeController::class, 'contactStore'])
        ->middleware('throttle:5,1')
        ->name('contact.store');
});

// Product Catalog
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::post('/products/check-availability', [ProductController::class, 'checkAvailability'])
    ->middleware('throttle:60,1')
    ->name('products.check-availability');

// DOKU Webhook (no auth, no CSRF — called by DOKU servers)
Route::post('/doku/notification', [DokuWebhookController::class, 'handleNotification'])->name('doku.notification');

// Region Proxy API
Route::get('/api/regions/provinces', [RegionController::class, 'provinces'])->name('api.regions.provinces');
Route::get('/api/regions/cities/{province}', [RegionController::class, 'cities'])->name('api.regions.cities');
Route::get('/api/regions/districts/{city}', [RegionController::class, 'districts'])->name('api.regions.districts');
Route::get('/api/regions/villages/{district}', [RegionController::class, 'villages'])->name('api.regions.villages');

// Tracking
Route::get('/track', [\App\Http\Controllers\TrackingController::class, 'index'])->name('track.index');
Route::post('/track', [\App\Http\Controllers\TrackingController::class, 'search'])
    ->middleware('throttle:20,1')
    ->name('track.search');
Route::get('/track/{order_number}', [\App\Http\Controllers\TrackingController::class, 'show'])->name('track.show');

// Reviews
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Authenticated Customer Routes
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reviews.store');

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('/cart/toggle-all', [CartController::class, 'toggleAll'])->name('cart.toggle-all');
    Route::patch('/cart/{product}/toggle', [CartController::class, 'toggle'])->name('cart.toggle');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/step/{step}', [CheckoutController::class, 'show'])
        ->where('step', '[1-3]')
        ->name('checkout.step');
    Route::post('/checkout/step-1', [CheckoutController::class, 'storeStep1'])->name('checkout.step1');
    Route::post('/checkout/step-2', [CheckoutController::class, 'storeStep2'])->name('checkout.step2');
    Route::post('/checkout/step-3', [CheckoutController::class, 'storeStep3'])
        ->middleware('throttle:10,1')
        ->name('checkout.step3');
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])
        ->name('checkout.calculate-shipping');
    Route::post('/checkout/promo', [CheckoutController::class, 'applyPromo'])
        ->middleware('throttle:20,1')
        ->name('checkout.apply-promo');
    Route::post('/checkout/promo/clear', [CheckoutController::class, 'clearPromo'])->name('checkout.clear-promo');
    Route::get('/orders/{order_number}/confirmation', [CheckoutController::class, 'confirmation'])
        ->where('order_number', '[A-Za-z0-9\-]+')
        ->name('orders.confirmation');
    Route::get('/orders/{order_number}/status', [CheckoutController::class, 'status'])
        ->where('order_number', '[A-Za-z0-9\-]+')
        ->name('orders.status');

    Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order_number}', [\App\Http\Controllers\OrderController::class, 'show'])
        ->where('order_number', '[A-Za-z0-9\-]+')
        ->name('orders.show');
    Route::get('/orders/{order_number}/invoice', [\App\Http\Controllers\OrderController::class, 'invoice'])
        ->where('order_number', '[A-Za-z0-9\-]+')
        ->name('orders.invoice');
    Route::get('/orders/export', [\App\Http\Controllers\OrderController::class, 'export'])->name('orders.export');
    Route::get('/orders/poll', [\App\Http\Controllers\OrderController::class, 'poll'])->name('orders.poll');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // Profile
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// ========================================
// ADMIN ROUTES
// ========================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/tren', [AnalyticsController::class, 'trend'])->name('analytics.trend');

    Route::middleware('super.admin')->group(function () {
        Route::get('/super-dashboard', [SuperAdminDashboardController::class, 'index'])->name('super.dashboard');
        Route::resource('users', AdminUserController::class)->except(['show']);
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    });

    Route::middleware('operational.admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Product Management
        Route::resource('products', AdminProductController::class);
        Route::post('/products/{product}/toggle-status', [AdminProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::post('/products/{product}/toggle-featured', [AdminProductController::class, 'toggleFeatured'])->name('products.toggle-featured');

        // Brand Management
        Route::resource('brands', BrandController::class);

        // Category Management
        Route::resource('categories', CategoryController::class);

        // Stock Management
        Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
        Route::patch('/stocks/{product}', [StockController::class, 'update'])->name('stocks.update');
        Route::get('/stocks/low-stock', [StockController::class, 'lowStock'])->name('stocks.low-stock');

        // Promo Management
        Route::resource('promos', PromoController::class);

        // Order Management
        Route::get('/orders/export', [\App\Http\Controllers\Admin\OrderController::class, 'export'])->name('orders.export');
        Route::post('/orders/print-bulk', [\App\Http\Controllers\Admin\OrderController::class, 'printBulk'])->name('orders.print_bulk');
        Route::get('/orders/{order}/print', [\App\Http\Controllers\Admin\OrderController::class, 'print'])->name('orders.print');
        Route::get('/orders/{order}/invoice', [\App\Http\Controllers\Admin\OrderController::class, 'invoice'])->name('orders.invoice');
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->except(['create', 'store', 'destroy']);

        // Review Moderation
        Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [AdminReviewController::class, 'show'])->name('reviews.show');
        Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
        Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
    });
});

// ========================================
// AUTH ROUTES
// ========================================

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:auth-sensitive');
    
    // Socialite Routes
    Route::get('auth/{provider}', [\App\Http\Controllers\Auth\SocialiteController::class, 'redirect'])->name('socialite.redirect');
    Route::get('auth/{provider}/callback', [\App\Http\Controllers\Auth\SocialiteController::class, 'callback'])->name('socialite.callback');
});

// Password Reset (dibuka untuk guest maupun user yang sudah login)
Route::get('forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('forgot-password', [AuthController::class, 'sendResetLink'])
    ->middleware('throttle:auth-sensitive')
    ->name('password.email');
Route::get('reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('reset-password', [AuthController::class, 'resetPassword'])
    ->middleware('throttle:auth-sensitive')
    ->name('password.update');

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

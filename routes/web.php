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

// Product Catalog
Route::get('/catalog', [ProductController::class, 'index'])->name('catalog');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');
Route::post('/products/check-availability', [ProductController::class, 'checkAvailability'])->name('products.check-availability');

// Reviews
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');

// Authenticated Customer Routes
Route::middleware('auth')->group(function () {
    // Submit Review
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    
    // Wishlist/Favorites (optional - can be implemented later)
    // Route::resource('favorites', FavoriteController::class)->only(['index', 'store', 'destroy']);
});

// ========================================
// ADMIN ROUTES
// ========================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
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
    
    // Review Moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});

// ========================================
// AUTH ROUTES
// ========================================

use App\Http\Controllers\AuthController;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\CustomerAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Customer\AccountController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductViewController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Storefront Routes
|--------------------------------------------------------------------------
*/
// Media Storage Asset Handler (Supports non-symlink environments like FAT32/Hostinger)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);
    if (!file_exists($filePath)) {
        abort(404);
    }
    return response()->file($filePath, [
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('storage.media');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/api/search/suggestions', [ShopController::class, 'suggestions'])->name('search.suggestions');
Route::get('/product/{slug}', [ProductViewController::class, 'show'])->name('product.show');
Route::post('/product/{product}/review', [ProductViewController::class, 'storeReview'])->name('product.review');

// Shopping Cart & Drawer (AJAX & Standard)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/drawer', [CartController::class, 'drawerData'])->name('cart.drawer');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

// Checkout
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/order/{orderNumber}/confirmation', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
Route::get('/order/{orderNumber}/invoice', [CheckoutController::class, 'invoice'])->name('order.invoice');

// Wishlist
Route::post('/wishlist/toggle', [AccountController::class, 'toggleWishlist'])->name('wishlist.toggle');

/*
|--------------------------------------------------------------------------
| Customer Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [CustomerAuthController::class, 'register']);
});
Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Account Area (Requires auth:web)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:web')->prefix('account')->name('customer.')->group(function () {
    Route::get('/', [AccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [AccountController::class, 'orderDetail'])->name('order.detail');
    Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
    Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('address.store');
    Route::delete('/addresses/{address}', [AccountController::class, 'deleteAddress'])->name('address.delete');
    Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
    Route::get('/wishlist', [AccountController::class, 'wishlist'])->name('wishlist');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('admin.guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout')->middleware('admin.auth');

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Protected Routes (Requires admin.auth)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [DashboardController::class, 'index']);
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Products CRUD & Variant / Image Management
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
        Route::post('/products/{product}/image', [ProductController::class, 'uploadImage'])->name('products.image.upload');
        Route::delete('/products/image/{image}', [ProductController::class, 'deleteImage'])->name('products.image.delete');
        Route::post('/products/image/{image}/primary', [ProductController::class, 'setPrimaryImage'])->name('products.image.primary');
        Route::post('/products/{product}/variant', [ProductController::class, 'storeVariant'])->name('products.variant.store');
        Route::put('/products/variant/{variant}', [ProductController::class, 'updateVariant'])->name('products.variant.update');
        Route::delete('/products/variant/{variant}', [ProductController::class, 'destroyVariant'])->name('products.variant.delete');

        // Categories & Brands
        Route::resource('categories', CategoryController::class);
        Route::resource('brands', BrandController::class);

        // Inventory Management
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::put('/inventory/{inventory}', [InventoryController::class, 'update'])->name('inventory.update');

        // Orders Management
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
        Route::post('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status.update');

        // Customers
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

        // Marketing
        Route::resource('coupons', CouponController::class);
        Route::resource('banners', BannerController::class);

        // Settings & System
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\FulfillmentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StorefrontFeatureController;
use App\Http\Controllers\AdminBusinessController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\FacebookFeedController;
use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\ProfileController as SellerProfileController;
use App\Http\Controllers\Payment\SSLCommerzController;
use App\Http\Controllers\Payment\BkashController;
use App\Http\Controllers\Payment\NagadController;
use App\Http\Controllers\ReturnRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/shop');
});

Route::view('/deals', 'pages.deals')->name('deals');
Route::view('/categories', 'pages.categories')->name('categories');
Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::post('/contact', [StorefrontFeatureController::class, 'contactStore'])->name('contact.store');
Route::get('/compare', [StorefrontFeatureController::class, 'compare'])->name('compare');
Route::post('/compare/{product}', [StorefrontFeatureController::class, 'addToCompare'])->name('compare.add');
Route::delete('/compare', [StorefrontFeatureController::class, 'clearCompare'])->name('compare.clear');
Route::get('/search/suggestions', [StorefrontFeatureController::class, 'searchSuggestions'])->name('search.suggestions');

// SEO
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', fn () => response()->view('robots')->header('Content-Type', 'text/plain'));

// Facebook feed
Route::get('/facebook-feed.xml', [FacebookFeedController::class, 'feed'])->name('facebook.feed');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Product routes
    Route::resource('products', ProductController::class);

    Route::get('/orders', [CheckoutController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [CheckoutController::class, 'orderShow'])->name('orders.show');
    Route::get('/orders/{order}/tracking', [StorefrontFeatureController::class, 'tracking'])->name('orders.tracking');

    Route::get('/recently-viewed', [StorefrontFeatureController::class, 'recentlyViewed'])->name('recently-viewed');
    Route::get('/addresses', [StorefrontFeatureController::class, 'addresses'])->name('addresses.index');
    Route::post('/addresses', [StorefrontFeatureController::class, 'storeAddress'])->name('addresses.store');
    Route::delete('/addresses/{address}', [StorefrontFeatureController::class, 'deleteAddress'])->name('addresses.destroy');

    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    Route::post('/products/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Return requests
    Route::post('/orders/{order}/return', [ReturnRequestController::class, 'store'])->name('orders.return');
    Route::get('/returns', [ReturnRequestController::class, 'myReturns'])->name('returns.index');
});

// Sourcing mode toggle
Route::get('/sourcing/mode/{mode}', [App\Http\Controllers\SourcingController::class, 'setMode'])->name('sourcing.mode');

// Public shop routes
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [PublicProductController::class, 'index'])->name('index');
    Route::get('/product/{product}', [PublicProductController::class, 'show'])->name('product')->middleware('track.product.views');
});

// Cart routes (available to all users)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::patch('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
    Route::get('/validate', [CartController::class, 'validateCart'])->name('validate');
    Route::post('/coupon', [CartController::class, 'applyCoupon'])->name('coupon.apply');
    Route::delete('/coupon', [CartController::class, 'removeCoupon'])->name('coupon.remove');
});

// Checkout routes (available to all users)
Route::prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
});

// Payment gateway routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::prefix('sslcommerz')->name('sslcommerz.')->group(function () {
        Route::get('/init/{order}', [SSLCommerzController::class, 'init'])->name('init');
        Route::post('/success', [SSLCommerzController::class, 'success'])->name('success');
        Route::post('/fail', [SSLCommerzController::class, 'fail'])->name('fail');
        Route::post('/cancel', [SSLCommerzController::class, 'cancel'])->name('cancel');
        Route::post('/ipn', [SSLCommerzController::class, 'ipn'])->name('ipn');
    });

    Route::prefix('bkash')->name('bkash.')->group(function () {
        Route::get('/init/{order}', [BkashController::class, 'init'])->name('init');
        Route::get('/callback', [BkashController::class, 'callback'])->name('callback');
    });

    Route::prefix('nagad')->name('nagad.')->group(function () {
        Route::get('/init/{order}', [NagadController::class, 'init'])->name('init');
        Route::get('/callback', [NagadController::class, 'callback'])->name('callback');
    });
});

// COD OTP verification
Route::middleware('auth')->group(function () {
    Route::get('/orders/{order}/verify-otp', [CheckoutController::class, 'showOtpForm'])->name('orders.otp');
    Route::post('/orders/{order}/verify-otp', [CheckoutController::class, 'verifyOtp'])->name('orders.otp.verify');
    Route::post('/orders/{order}/resend-otp', [CheckoutController::class, 'resendOtp'])->name('orders.otp.resend');
});

Route::middleware(['auth', 'admin'])->prefix('admin-legacy')->name('admin-legacy.')->group(function () {
    Route::get('/', [CheckoutController::class, 'adminDashboard'])->name('dashboard');

    // Admin product management
    Route::resource('products', ProductController::class)->names([
        'index' => 'products.index',
        'create' => 'products.create',
        'store' => 'products.store',
        'show' => 'products.show',
        'edit' => 'products.edit',
        'update' => 'products.update',
        'destroy' => 'products.destroy',
    ]);

    // Admin order management
    Route::get('/orders', [CheckoutController::class, 'adminIndex'])->name('orders.index');
    Route::get('/orders/{order}', [CheckoutController::class, 'adminShow'])->name('orders.show');
    Route::patch('/orders/{order}/status', [CheckoutController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('/orders/{order}/invoice', [AdminBusinessController::class, 'invoice'])->name('orders.invoice');

    Route::get('/analytics', [AdminBusinessController::class, 'analytics'])->name('analytics');
    Route::get('/inventory', [AdminBusinessController::class, 'inventory'])->name('inventory');
    Route::get('/customers', [AdminBusinessController::class, 'customers'])->name('customers');
    Route::get('/expenses', [AdminBusinessController::class, 'expenses'])->name('expenses');
    Route::post('/expenses', [AdminBusinessController::class, 'storeExpense'])->name('expenses.store');
    Route::get('/notifications', [AdminBusinessController::class, 'notifications'])->name('notifications');
    Route::get('/reports', [AdminBusinessController::class, 'reports'])->name('reports');
    Route::get('/reports/{type}/export', [AdminBusinessController::class, 'export'])->name('reports.export');

    // Admin seller management
    Route::resource('sellers', SellerController::class)->names([
        'index' => 'sellers.index',
        'create' => 'sellers.create',
        'store' => 'sellers.store',
        'show' => 'sellers.show',
        'edit' => 'sellers.edit',
        'update' => 'sellers.update',
        'destroy' => 'sellers.destroy',
    ]);

    // Admin seller management - additional routes
    Route::post('/sellers/{seller}/regenerate-api-key', [SellerController::class, 'regenerateApiKey'])->name('sellers.regenerate-api-key');
    Route::patch('/sellers/{seller}/toggle-active', [SellerController::class, 'toggleActive'])->name('sellers.toggle-active');

    // Admin fulfillment management
    Route::get('/fulfillment', [FulfillmentController::class, 'index'])->name('fulfillment.index');
    Route::get('/fulfillment/{fulfillmentRequest}', [FulfillmentController::class, 'show'])->name('fulfillment.show');
    Route::patch('/fulfillment/{fulfillmentRequest}/status', [FulfillmentController::class, 'updateStatus'])->name('fulfillment.update-status');
    Route::post('/fulfillment/{fulfillmentRequest}/confirm', [FulfillmentController::class, 'confirm'])->name('fulfillment.confirm');
    Route::post('/fulfillment/{fulfillmentRequest}/mark-shipped', [FulfillmentController::class, 'markShipped'])->name('fulfillment.mark-shipped');
    Route::get('/fulfillment/{fulfillmentRequest}/export-csv', [FulfillmentController::class, 'exportCsv'])->name('fulfillment.export-csv');

    // Order fulfillment routes
    Route::get('/orders/{order}/fulfillment', [FulfillmentController::class, 'orderFulfillment'])->name('orders.fulfillment');

    // Seller approval routes
    Route::get('/sellers/pending', [SellerController::class, 'pendingApproval'])->name('sellers.pending');
    Route::post('/sellers/{seller}/approve', [SellerController::class, 'approve'])->name('sellers.approve');
    Route::post('/sellers/{seller}/reject', [SellerController::class, 'reject'])->name('sellers.reject');
    Route::get('/sellers/{seller}/documents', [SellerController::class, 'showDocuments'])->name('sellers.documents');

    // Admin seller suspension
    Route::post('/sellers/{seller}/suspend', [SellerController::class, 'suspend'])->name('sellers.suspend');
    Route::post('/sellers/{seller}/restore', [SellerController::class, 'restore'])->name('sellers.restore');

    // Admin return management
    Route::get('/returns', [ReturnRequestController::class, 'index'])->name('returns.index');
    Route::post('/returns/{returnRequest}/approve', [ReturnRequestController::class, 'approve'])->name('returns.approve');
    Route::post('/returns/{returnRequest}/reject', [ReturnRequestController::class, 'reject'])->name('returns.reject');
    Route::post('/returns/{returnRequest}/refund', [ReturnRequestController::class, 'refund'])->name('returns.refund');
});

// Seller authentication (guest)
Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('/register', [SellerAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerAuthController::class, 'register']);
    Route::get('/login', [SellerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SellerAuthController::class, 'login']);
});

// Seller portal (authenticated + seller role)
Route::middleware(['auth', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::post('/logout', [SellerAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [SellerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::resource('products', SellerProductController::class)->except(['show'])->names([
        'index' => 'products.index',
        'create' => 'products.create',
        'store' => 'products.store',
        'edit' => 'products.edit',
        'update' => 'products.update',
        'destroy' => 'products.destroy',
    ]);
    Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{fulfillmentRequest}', [SellerOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{fulfillmentRequest}/mark-shipped', [SellerOrderController::class, 'markShipped'])->name('orders.mark-shipped');
    Route::get('/profile', [SellerProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [SellerProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';

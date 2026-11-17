<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\Frontend\AccountController;
use Modules\Shop\Http\Controllers\Frontend\AuthController;
use Modules\Shop\Http\Controllers\Frontend\HomeController;
use Modules\Shop\Http\Controllers\Frontend\OrderPaymentController;

// Customer storefront (multiple_pasal's routes/web.php, theme "simple_theme"). Names: shop.front.*
// Loaded after the back office routes; the catch-all category / product URLs come last.

Route::name('shop.front.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/store', [HomeController::class, 'store'])->name('store');
    Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
    Route::get('/section/{section}', [HomeController::class, 'section'])->name('section');

    Route::post('/add-to-cart', [HomeController::class, 'addToCart'])->name('cart.add');
    Route::get('/remove-from-cart/{id}', [HomeController::class, 'removeFromCart'])->name('cart.remove');

    Route::post('/order', [HomeController::class, 'order'])->middleware('throttle:10,1')->name('order.store');
    Route::get('/order/{orderNumber}', [HomeController::class, 'orderDetail'])->name('order.show');
    Route::get('/order/{orderNumber}/pay/khalti', [OrderPaymentController::class, 'payKhalti'])->name('order.pay_khalti');
    Route::get('/order/{orderNumber}/pay/khalti/callback', [OrderPaymentController::class, 'khaltiCallback'])->name('order.pay_khalti.callback');

    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
    Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1')->name('register.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:shop_customer')->prefix('user')->name('account.')->group(function () {
        Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'update'])->name('update');
        Route::get('/order', [AccountController::class, 'orders'])->name('orders');
    });

    // /{category} and /{category}/{product}: never the back office or staff pages.
    $slug = '(?!admin$|staff$)[A-Za-z0-9-]+';
    Route::get('/{category}', [HomeController::class, 'category'])->where('category', $slug)->name('category');
    Route::get('/{category}/{product}', [HomeController::class, 'product'])->where(['category' => $slug, 'product' => '[A-Za-z0-9-]+'])->name('product');
});

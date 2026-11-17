<?php

use Modules\Restaurant\Http\Controllers\Admin\AuthController;
use Modules\Restaurant\Http\Controllers\Frontend\AboutController;
use Modules\Restaurant\Http\Controllers\Frontend\CheckoutController;
use Modules\Restaurant\Http\Controllers\Frontend\ContactController;
use Modules\Restaurant\Http\Controllers\Frontend\CustomerAccountController;
use Modules\Restaurant\Http\Controllers\Frontend\CustomerAuthController;
use Modules\Restaurant\Http\Controllers\Frontend\HomeController;
use Modules\Restaurant\Http\Controllers\Frontend\MenuController;
use Modules\Restaurant\Http\Controllers\Frontend\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('frontend.home.index');
Route::get('/about', [AboutController::class, 'index'])->name('frontend.about.index');
Route::get('/menu/{table?}', [MenuController::class, 'index'])->name('frontend.menu.index');
Route::get('/cart/items', [\Modules\Restaurant\Http\Controllers\Frontend\CartController::class, 'items'])->name('frontend.cart.items');
Route::get('/table', [TableController::class, 'index'])->name('frontend.table.index');
Route::post('/table/book', [TableController::class, 'book'])->name('frontend.table.book');
Route::get('/rooms/{room}', [TableController::class, 'showRoom'])->name('frontend.room.show');
Route::get('/tables/{table}', [TableController::class, 'showTable'])->name('frontend.table.show');
Route::get('/contact', [ContactController::class, 'index'])->name('frontend.contact.index');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('frontend.checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('frontend.checkout.store');
Route::get('/checkout/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('frontend.checkout.confirmation');

Route::middleware('guest:customer')->group(function () {
    Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('frontend.auth.show_login_form');
    Route::post('/login', [CustomerAuthController::class, 'login'])->name('frontend.auth.login');
    Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('frontend.auth.show_register_form');
    Route::post('/register', [CustomerAuthController::class, 'register'])->name('frontend.auth.register');
});
Route::post('/logout', [CustomerAuthController::class, 'logout'])
    ->middleware('auth:customer')
    ->name('frontend.auth.logout');

Route::middleware('auth:customer')->group(function () {
    Route::get('/account', [CustomerAccountController::class, 'index'])->name('frontend.account.index');
    Route::get('/account/orders/{order}', [CustomerAccountController::class, 'show'])->name('frontend.account.order');
});

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('admin.auth.show_login_form');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.auth.login');

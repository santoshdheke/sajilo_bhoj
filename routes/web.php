<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\CompanyRegistrationController;
use App\Http\Controllers\Frontend\AboutController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CustomerAccountController;
use App\Http\Controllers\Frontend\CustomerAuthController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\MenuController;
use App\Http\Controllers\Frontend\TableController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('frontend.home.index');
Route::get('/about', [AboutController::class, 'index'])->name('frontend.about.index');
Route::get('/menu/{table?}', [MenuController::class, 'index'])->name('frontend.menu.index');
Route::get('/cart/items', [\App\Http\Controllers\Frontend\CartController::class, 'items'])->name('frontend.cart.items');
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

Route::get('/get-started', [CompanyRegistrationController::class, 'create'])->name('register.create');
Route::post('/get-started', [CompanyRegistrationController::class, 'store'])->name('register.store');

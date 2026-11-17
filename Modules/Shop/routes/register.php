<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\ShopRegistrationController;

// Shop sign-up. Linked from the 410 "no workspace here" page, so it must work on a host
// that has no company yet. The owner's email is verified by a 6-digit code before the
// shop is created (multiple_pasal's company OTP flow).
Route::get('/shop/register', [ShopRegistrationController::class, 'create'])->name('shop.register.create');
Route::post('/shop/register', [ShopRegistrationController::class, 'store'])->middleware('throttle:10,1')->name('shop.register.store');
Route::post('/shop/register/send-otp', [ShopRegistrationController::class, 'sendOtp'])->middleware('throttle:5,1')->name('shop.register.send_otp');
Route::post('/shop/register/verify-otp', [ShopRegistrationController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('shop.register.verify_otp');

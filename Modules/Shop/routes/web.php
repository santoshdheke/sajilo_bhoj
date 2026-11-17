<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\Admin\AuthController;
use Modules\Shop\Http\Controllers\Admin\StaffController;

Route::get('/admin/login', [AuthController::class, 'showLoginForm'])->name('shop.admin.auth.show_login_form');
Route::post('/admin/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('shop.admin.auth.login');

// Invited staff choose their own password (link from the invite email).
Route::get('/staff/set-password/{token}', [StaffController::class, 'setPasswordForm'])->name('shop.staff.set_password');
Route::post('/staff/set-password/{token}', [StaffController::class, 'setPassword'])->name('shop.staff.set_password.submit');

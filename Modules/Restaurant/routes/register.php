<?php

use Illuminate\Support\Facades\Route;
use Modules\Restaurant\Http\Controllers\CompanyRegistrationController;

// Restaurant sign-up ("Create your workspace"). Linked from the 410 "no workspace here" page,
// so it must work on a host that has no company yet.
Route::get('/restaurant/register', [CompanyRegistrationController::class, 'create'])->name('restaurant.register.create');
Route::post('/restaurant/register', [CompanyRegistrationController::class, 'store'])->name('restaurant.register.store');
Route::permanentRedirect('/get-started', '/restaurant/register')->name('restaurant.register.legacy');

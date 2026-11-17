<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Auth\LoginController;

// Shared by every school person (admin, teacher, student). The login only matches users
// of this host's school; each role then lands on its own area.
Route::get('/', [LoginController::class, 'home'])->name('school.home');

Route::get('/login', [LoginController::class, 'create'])->name('school.login');
Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:5,1')->name('school.login.store');

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:school')->name('school.logout');

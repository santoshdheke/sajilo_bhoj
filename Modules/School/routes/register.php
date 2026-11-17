<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Auth\RegisterSchoolController;

// School sign-up. Linked from the 410 "no workspace here" page, so it must work on a host
// that has no company yet. The school's domain is the host being visited.
Route::get('/school/register', [RegisterSchoolController::class, 'create'])->name('school.register.create');
Route::post('/school/register', [RegisterSchoolController::class, 'store'])->middleware('throttle:5,1')->name('school.register.store');

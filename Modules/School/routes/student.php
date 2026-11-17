<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Student\DashboardController;

// No middleware/prefix here — RouteServiceProvider already wraps this file with
// ['web', ResolveSchool, 'auth:school', 'role student'], the /student prefix and "school.student." names.

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

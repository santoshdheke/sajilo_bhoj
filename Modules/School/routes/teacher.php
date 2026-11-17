<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Teacher\DashboardController;

// No middleware/prefix here — RouteServiceProvider already wraps this file with
// ['web', ResolveSchool, 'auth:school', 'role teacher'], the /teacher prefix and "school.teacher." names.

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

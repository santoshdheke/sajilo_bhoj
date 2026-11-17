<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Admin\ClassroomController;
use Modules\School\Http\Controllers\Admin\ClassTeacherController;
use Modules\School\Http\Controllers\Admin\DashboardController;
use Modules\School\Http\Controllers\Admin\SectionController;
use Modules\School\Http\Controllers\Admin\SubjectController;
use Modules\School\Http\Controllers\Admin\UserController;

// No middleware/prefix here — RouteServiceProvider already wraps this file with
// ['web', ResolveSchool, 'auth:school', 'role admin'], the /admin prefix and "school.admin." names.

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('users', UserController::class)->except(['show']);

Route::resource('classrooms', ClassroomController::class)->except(['show']);
Route::resource('sections', SectionController::class)->except(['show']);
Route::resource('subjects', SubjectController::class)->except(['show']);
Route::get('class-teachers', [ClassTeacherController::class, 'index'])->name('class-teachers.index');
Route::put('class-teachers/{section}', [ClassTeacherController::class, 'update'])->name('class-teachers.update');

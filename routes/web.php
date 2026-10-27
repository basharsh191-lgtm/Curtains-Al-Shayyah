<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\CurtainController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ShowroomController;
use App\Http\Middleware\TrackVisitor;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');
        Route::patch('/appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('appointments.update');
        Route::resource('curtains', CurtainController::class)->except('show');
        Route::patch('/curtains/{curtain}/toggle', [CurtainController::class, 'toggle'])->name('curtains.toggle');
        Route::resource('projects', ProjectController::class)->except('show');
        Route::patch('/projects/{project}/toggle-active', [ProjectController::class, 'toggleActive'])->name('projects.toggle-active');
        Route::post('/logout', [AdminAuthController::class, 'destroy'])->name('logout');
    });
});

Route::middleware(TrackVisitor::class)->group(function () {
    Route::get('/', [ShowroomController::class, 'home'])->name('home');
    Route::get('/products', [ShowroomController::class, 'products'])->name('products.index');
    Route::get('/products/{slug}', [ShowroomController::class, 'show'])->name('products.show');
    Route::get('/projects', [ShowroomController::class, 'projects'])->name('projects.index');
    Route::get('/appointments/create', [ShowroomController::class, 'createAppointment'])->name('appointments.create');
});

Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::patch('/appointments/{appointment}/manage', [AppointmentController::class, 'update'])->name('appointments.manage.update');
Route::delete('/appointments/{appointment}/manage', [AppointmentController::class, 'destroy'])->name('appointments.manage.destroy');

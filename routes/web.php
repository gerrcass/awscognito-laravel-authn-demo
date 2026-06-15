<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/admin/users', [UserController::class, 'index'])
        ->middleware('permission:users.manage')
        ->name('admin.users');

    Route::get('/patients', [PatientController::class, 'index'])
        ->middleware('permission:patients.view')
        ->name('patients');

    Route::get('/caja', [CajaController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('caja');
    Route::post('/caja/open', [CajaController::class, 'open'])
        ->middleware('permission:caja.open')
        ->name('caja.open');
    Route::post('/caja/close', [CajaController::class, 'close'])
        ->middleware('permission:caja.close')
        ->name('caja.close');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::get('/', function () {
    return redirect('/dashboard');
});

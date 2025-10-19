<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PoliController;
use App\Http\Controllers\Admin\DokterController;


Route::get('/', function () {
    return view('welcome');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);




Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('admin.dashboard');
        Route::resource('polis', PoliController::class);
        Route::resource('dokter', DokterController::class)->names('admin.dokter');
    });
});

Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', fn() => view('dokter.dashboard'))->name('dokter.dashboard');
    Route::resource('dokter', DokterController::class)->names('dokter');
});

Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', function() {
        return view('pasien.dashboard');
    })->name('pasien.dashboard');
});
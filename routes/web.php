<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\Pasien\PoliController as PasienPoliController;


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
        Route::resource('pasien', PasienController::class);
        Route::resource('obat', ObatController::class);
    });
});

Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', fn() => view('dokter.dashboard'))->name('dokter.dashboard');
    Route::resource('jadwal-periksa', JadwalPeriksaController::class)->names('dokter.jadwal-periksa');
    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])->name('dokter.periksa-pasien.index');
    Route::get('/periksa-pasien/{id}/create', [PeriksaPasienController::class, 'create'])->name('dokter.periksa-pasien.create');
    Route::post('/periksa-pasien/store',[PeriksaPasienController::class, 'store'])->name('dokter.periksa-pasien.store');
    Route::get('/periksa-pasien/riwayat', [PeriksaPasienController::class, 'riwayat'])->name('dokter.periksa-pasien.riwayat');
    Route::get('/periksa-pasien/{id}/riwayat', [PeriksaPasienController::class, 'show'])->name('dokter.periksa-pasien.show');
    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])->name('dokter.riwayat-pasien.index');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])->name('dokter.riwayat-pasien.show');
});

Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', function() {
        return view('pasien.dashboard');
    })->name('pasien.dashboard');
    Route::get('/daftar', [PasienPoliController::class, 'get'])->name('pasien.daftar');
    Route::post('/daftar', [PasienPoliController::class, 'submit'])->name('pasien.daftar.submit');
});
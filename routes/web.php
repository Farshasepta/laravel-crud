<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('portofolio');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang (level 1,2)
    Route::middleware('level:1,2')->group(function () {
        Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{id}', [BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // Mahasiswa (level 1,3)
    Route::middleware('level:1,3')->group(function () {
        Route::get('/mahasiswa/export/excel', [ExportController::class, 'mahasiswaExcel'])->name('mahasiswa.excel');
        Route::get('/mahasiswa/export/pdf', [ExportController::class, 'mahasiswaPdf'])->name('mahasiswa.pdf');
        Route::resource('mahasiswa', MahasiswaController::class);
    });

    // Pegawai (level 1,3)
    Route::middleware('level:1,3')->group(function () {
        Route::resource('pegawai', PegawaiController::class)->except(['show']);
    });

    // Email (level 1,3)
    Route::middleware('level:1,3')->group(function () {
        Route::get('/email', [EmailController::class, 'index'])->name('email.index');
        Route::post('/email', [EmailController::class, 'send'])->name('email.send');
    });

    // Akun (level 1 full; non-admin hanya edit diri)
    Route::middleware('level:1')->group(function () {
        Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
        Route::post('/akun', [AkunController::class, 'store'])->name('akun.store');
        Route::delete('/akun/{id}', [AkunController::class, 'destroy'])->name('akun.destroy');
    });
    Route::post('/akun/{id}', [AkunController::class, 'update'])->name('akun.update');
});

Route::get('/barcode', [BarcodeController::class, 'show'])->name('barcode');

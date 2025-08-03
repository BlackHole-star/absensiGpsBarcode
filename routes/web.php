<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RekapController;
use App\Http\Controllers\Admin\BarcodeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\User\ScanController;
use App\Http\Controllers\User\IzinController;
use App\Http\Controllers\User\AttendanceHistoryController;
use App\Http\Controllers\ProfileController;

// ========== AUTH ==========
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profil', [ProfileController::class, 'update'])->name('profile.update');

// Redirect root ke login
Route::get('/', fn () => redirect()->route('login'));

// ========== ADMIN ==========
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Rekap & Export
    Route::get('/rekap', [RekapController::class, 'rekap'])->name('rekap');
    Route::get('/export/excel', [ExportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [ExportController::class, 'exportPDF'])->name('export.pdf');

    // Barcode CRUD
    Route::resource('barcodes', BarcodeController::class)->except(['show']);
    Route::get('/barcodes/{id}/download', [BarcodeController::class, 'download'])->name('barcodes.download');
    Route::delete('/barcodes/{barcode}', [BarcodeController::class, 'destroy'])->name('barcodes.destroy');

    // Karyawan CRUD
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::post('/employees/{id}/reset-password', [EmployeeController::class, 'resetPassword'])->name('employees.resetPassword');
    Route::get('/employees/json', [EmployeeController::class, 'json'])->name('employees.json');
});

// ========== USER ==========
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::prefix('absen')->group(function () {
        // Absen Masuk & Pulang
        Route::get('scan', [ScanController::class, 'index'])->name('absen.scan');
        Route::post('store', [ScanController::class, 'store'])->name('absen.store');
        Route::post('checkout', [ScanController::class, 'checkout'])->name('absen.checkout');

        // Izin/Sakit
        Route::get('izin', [IzinController::class, 'form'])->name('absen.izin');
        Route::post('izin', [IzinController::class, 'submit'])->name('absen.izin.submit');

        // Riwayat Absensi
        Route::get('riwayat', [AttendanceHistoryController::class, 'index'])->name('absen.history');
        Route::get('riwayat/{date}', [AttendanceHistoryController::class, 'show'])->name('absen.history.detail');
    });
});


    // Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    // Halaman Scan Absen
    // Route::get('/scan', [UserAttendanceController::class, 'scanForm'])->name('absen.scan');

    // // Store absen masuk dan checkout
    // Route::post('/absen/store', [UserAttendanceController::class, 'store'])->name('absen.store');
    // Route::post('/absen/checkout', [UserAttendanceController::class, 'checkout'])->name('absen.checkout');

    // // Form dan submit Izin/Sakit
    // Route::get('/absen/izin', [UserAttendanceController::class, 'formIzin'])->name('absen.izin');
    // Route::post('/absen/izin', [UserAttendanceController::class, 'submitIzin'])->name('absen.izin.submit');

    // // Riwayat Absensi
    // Route::get('/attendance/history', [UserAttendanceController::class, 'history'])->name('attendance.history');
    // Route::get('/attendance/detail/{date}', [UserAttendanceController::class, 'showDetail'])->name('attendance.detail');
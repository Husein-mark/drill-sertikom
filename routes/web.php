<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\RegistrationManagerController as AdminRegistrationController;
use App\Http\Controllers\Admin\WorkshopController as AdminWorkshopController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\WorkshopCatalogController;
use Illuminate\Support\Facades\Route;

// Redirect root ke Katalog
Route::get('/', fn () => redirect()->route('catalog.index'));

// Katalog Workshop Publik
Route::get('/katalog', [WorkshopCatalogController::class, 'index'])->name('catalog.index');
Route::get('/workshop/{workshop:slug}', [WorkshopCatalogController::class, 'show'])->name('catalog.show');

// Otentikasi (Login, Register, Logout)
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Webhook Notifikasi Pembayaran Midtrans (Tanpa CSRF Token)
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])->name('midtrans.notification');

// Area Peserta (Memerlukan Login)
Route::middleware(['auth', 'role:peserta'])->group(function (): void {
    Route::get('/workshop/{workshop:slug}/daftar', [RegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/workshop/{workshop:slug}/daftar', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/tiket-saya', [RegistrationController::class, 'index'])->name('registrations.index');
});

// Area Pembayaran & Tiket Digital (Terproteksi Kepemilikan & Status Lunas)
Route::middleware('auth')->group(function (): void {
    Route::get('/pembayaran/{registration}', [RegistrationController::class, 'payment'])->name('registrations.payment');
    Route::post('/pembayaran/{registration}/simulasi', [PaymentController::class, 'simulateSuccess'])->name('payments.simulate');
    Route::get('/pembayaran/{registration}/cek-status', [PaymentController::class, 'checkStatus'])->name('payments.check');
    Route::get('/tiket/{registration}', [RegistrationController::class, 'ticket'])->name('registrations.ticket');
});

// Area Khusus Panitia (Middleware Auth + Role: Panitia)
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('admin.')->group(function (): void {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // CRUD Workshop
    Route::resource('workshops', AdminWorkshopController::class)->except(['show']);

    // Manajemen Pendaftar & Uji Perbandingan N+1 Query
    Route::get('/pendaftar', [AdminRegistrationController::class, 'index'])->name('registrations.index');
    Route::post('/pendaftar/{registration}/verifikasi', [AdminRegistrationController::class, 'manualVerify'])->name('registrations.verify');
    Route::post('/pendaftar/{registration}/batal', [AdminRegistrationController::class, 'cancel'])->name('registrations.cancel');
});

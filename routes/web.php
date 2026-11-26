<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\SuperadminController;
use App\Http\Controllers\PustakawanController;
use App\Http\Controllers\MemberController;

// Public & Auth Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Lupa Password & Verifikasi OTP Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
Route::get('/verify-otp', [ForgotPasswordController::class, 'showVerifyOtp'])->name('password.otp.show');
Route::post('/verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPassword'])->name('password.reset.show');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

// Superadmin Protected Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('dashboard');
    Route::get('/manajemen-akun', [SuperadminController::class, 'accounts'])->name('accounts');
    Route::post('/manajemen-akun', [SuperadminController::class, 'storeAccount'])->name('accounts.store');
    Route::put('/manajemen-akun/{id}', [SuperadminController::class, 'updateAccount'])->name('accounts.update');
    Route::patch('/manajemen-akun/{id}/toggle-suspend', [SuperadminController::class, 'toggleSuspend'])->name('accounts.toggle-suspend');
    Route::delete('/manajemen-akun/{id}', [SuperadminController::class, 'deleteAccount'])->name('accounts.delete');
});

// Pustakawan Protected Routes
Route::middleware(['auth', 'role:pustakawan'])->prefix('pustakawan')->name('pustakawan.')->group(function () {
    Route::get('/dashboard', [PustakawanController::class, 'dashboard'])->name('dashboard');
    
    // Buku
    Route::get('/buku', [PustakawanController::class, 'books'])->name('books');
    Route::post('/buku', [PustakawanController::class, 'storeBook'])->name('books.store');
    Route::put('/buku/{id}', [PustakawanController::class, 'updateBook'])->name('books.update');
    Route::delete('/buku/{id}', [PustakawanController::class, 'deleteBook'])->name('books.delete');
    
    // Peminjaman & Sirkulasi
    Route::get('/peminjaman', [PustakawanController::class, 'loans'])->name('loans');
    Route::post('/peminjaman', [PustakawanController::class, 'storeLoan'])->name('loans.store');
    Route::patch('/peminjaman/{id}/approve', [PustakawanController::class, 'approveOnlineLoan'])->name('loans.approve');
    Route::patch('/peminjaman/{id}/reject', [PustakawanController::class, 'rejectOnlineLoan'])->name('loans.reject');
    Route::patch('/peminjaman/{id}/handover', [PustakawanController::class, 'handOverBook'])->name('loans.handover');
    Route::patch('/peminjaman/{id}/return', [PustakawanController::class, 'returnBook'])->name('loans.return');
    
    // Anggota
    Route::get('/anggota', [PustakawanController::class, 'members'])->name('members');
});

// Member Protected Routes
Route::middleware(['auth', 'role:member'])->prefix('member')->name('member.')->group(function () {
    // 1. Beranda
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');

    // 2. Daftar Koleksi
    Route::get('/koleksi', [MemberController::class, 'koleksi'])->name('koleksi');
    Route::get('/buku/{id}', [MemberController::class, 'detailBuku'])->name('buku.detail');

    // 3. Buku Favorit
    Route::get('/favorit', [MemberController::class, 'favorit'])->name('favorit');
    Route::post('/favorit/toggle/{id}', [MemberController::class, 'toggleFavorite'])->name('favorit.toggle');

    // 4. Keranjang Peminjaman
    Route::get('/keranjang', [MemberController::class, 'keranjang'])->name('keranjang');
    Route::post('/keranjang/add', [MemberController::class, 'addToCart'])->name('keranjang.add');
    Route::delete('/keranjang/remove/{id}', [MemberController::class, 'removeFromCart'])->name('keranjang.remove');
    Route::post('/keranjang/checkout', [MemberController::class, 'checkoutCart'])->name('keranjang.checkout');

    // 5. Aktifitas (Riwayat Peminjaman & Status)
    Route::get('/aktifitas', [MemberController::class, 'aktifitas'])->name('aktifitas');

    // Legacy Support for old pinjam-online
    Route::get('/pinjam-online', function () {
        return redirect()->route('member.koleksi');
    })->name('pinjam-online');
    Route::post('/pinjam-online', [MemberController::class, 'requestLoan'])->name('pinjam-online.request');

    // 6. Akun & Password
    Route::get('/akun', [MemberController::class, 'akun'])->name('akun');
    Route::put('/akun/profile', [MemberController::class, 'updateProfile'])->name('akun.profile');
    Route::put('/akun/password', [MemberController::class, 'updatePassword'])->name('akun.password');
});

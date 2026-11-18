<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\DashboardController;       
use App\Http\Controllers\KontakController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ==========================================
// HALAMAN WELCOME
// ==========================================
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


// ==========================================
// HALAMAN DASHBOARD
// ==========================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ==========================================
// ROUTE YANG MEMERLUKAN LOGIN
// ==========================================
Route::middleware('auth')->group(function () {

    // ======================================
    // AJAX LIVE SEARCH BUKU
    // ======================================
    Route::get('/books/search', [BookController::class, 'search'])
        ->name('books.search');

    // AJAX CEK KODE BUKU REALTIME
    Route::get('/books/cek-kode', [BookController::class, 'cekKode'])
        ->name('books.cek-kode');


    // ======================================
    // AJAX LIVE SEARCH ANGGOTA
    // ======================================
    Route::get('/members/search', [MemberController::class, 'search'])
        ->name('members.search');

    // AJAX CEK NIS/NIM REALTIME
    Route::get('/members/cek-nis', [MemberController::class, 'cekNis'])
        ->name('members.cek-nis');


    // ======================================
    // CRUD DATA BUKU
    // ======================================
    Route::resource('books', BookController::class);


    // ======================================
    // CRUD DATA ANGGOTA
    // ======================================
    Route::resource('members', MemberController::class);


    // ======================================
    // DATA PEMINJAMAN
    // ======================================
    Route::resource('loans', LoanController::class)
        ->only(['index', 'create', 'store']);

    // ======================================
    // PENGEMBALIAN BUKU
    // ======================================
    Route::patch('/loans/{loan}/kembalikan', [
        LoanController::class,
        'kembalikan'
    ])->name('loans.kembalikan');


    // ======================================
    // PROFILE
    // ======================================
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});


// ==========================================
// HALAMAN KONTAK
// ==========================================
Route::get('/kontak', [KontakController::class, 'index'])
    ->name('kontak');


// ==========================================
// AUTHENTICATION
// ==========================================
require __DIR__.'/auth.php';

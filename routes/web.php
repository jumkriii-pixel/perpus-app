<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\KontakController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// HALAMAN WELCOME
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// HALAMAN DASHBOARD
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ROUTE YANG MEMERLUKAN LOGIN
Route::middleware('auth')->group(function () {

    // AJAX LIVE SEARCH BUKU
    Route::get('/books/search', [BookController::class, 'search'])
        ->name('books.search');

    // AJAX CEK KODE BUKU REALTIME
    Route::get('/books/cek-kode', [BookController::class, 'cekKode'])
        ->name('books.cek-kode');

    // AJAX LIVE SEARCH ANGGOTA
    Route::get('/members/search', [MemberController::class, 'search'])
        ->name('members.search');

    // AJAX CEK NIS/NIM REALTIME
    Route::get('/members/cek-nis', [MemberController::class, 'cekNis'])
        ->name('members.cek-nis');

    // CRUD BUKU
    Route::resource('books', BookController::class);

    // CRUD ANGGOTA
    Route::resource('members', MemberController::class);

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// HALAMAN KONTAK
Route::get('/kontak', [KontakController::class, 'index']);

// AUTHENTICATION
require __DIR__.'/auth.php';

<?php

use App\Http\Middleware\AdminOnly;
use Illuminate\Support\Facades\Route;

Route::view('/', 'beranda')->name('beranda');
Route::view('/layanan', 'layanan')->name('layanan');
Route::view('/login', 'login')->name('login');
Route::view('/register', 'register')->name('register');
Route::view('/pesanan', 'pesanan')->name('pesanan');
Route::view('/riwayat', 'riwayat')->name('riwayat');

// Halaman admin: dicek di SERVER (cookie admin_token + role admin)
Route::prefix('admin')->middleware(AdminOnly::class)->group(function () {
    Route::view('/', 'admin.dashboard');
    Route::view('/pesanan', 'admin.pesanan');
    Route::view('/pesanan/tambah', 'admin.pesanan-tambah');
    Route::view('/pesanan/{id}/ubah', 'admin.pesanan-ubah');
    Route::view('/pengguna', 'admin.pengguna');
    Route::view('/pengguna/tambah', 'admin.pengguna-form');
    Route::view('/pengguna/{id}/ubah', 'admin.pengguna-form');
    Route::view('/layanan', 'admin.layanan');
    Route::view('/layanan/tambah', 'admin.layanan-form');
    Route::view('/layanan/{id}/ubah', 'admin.layanan-form');
    Route::view('/jenis-layanan', 'admin.jenis-layanan');
    Route::view('/laporan', 'admin.laporan');
});

// SEMENTARA: untuk diagnosa, hapus setelah login admin beres
Route::get('/debug-admin', function () {
    $plain = $_COOKIE['admin_token'] ?? null;
    $token = $plain ? \Laravel\Sanctum\PersonalAccessToken::findToken($plain) : null;
    return [
        'cookie_ada'  => (bool) $plain,
        'token_valid' => (bool) $token,
        'role'        => $token?->tokenable?->role,
    ];
});
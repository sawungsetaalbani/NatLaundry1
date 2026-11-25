<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\JenisLayananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\DetailPesananController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;

/*
|--------------------------------------------------------------------------
| AUTH (login & register khusus admin / pengguna sistem)
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| PUBLIK (tanpa login)
|--------------------------------------------------------------------------
*/
Route::get('/layanan', [LayananController::class, 'index']);
Route::get('/layanan/{id}', [LayananController::class, 'show']);
Route::get('/jenis-layanan', [JenisLayananController::class, 'index']);

// cek pesanan lewat kode (form Redeem di beranda)
Route::get('/pesanan/kode/{kode}', [PesananController::class, 'byKode']);
Route::get('/pesanan/kode/{kode}/export-pdf', [PesananController::class, 'exportPdfByKode']);
Route::get('/pesanan/kode/{kode}/riwayat', [PesananController::class, 'riwayatByKode']);
Route::get('/pesanan/kode/{kode}/riwayat/export-pdf', [PesananController::class, 'exportRiwayatPdfByKode']);

/*
|--------------------------------------------------------------------------
| ADMIN (wajib login + role admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::apiResource('pengguna', PenggunaController::class)
            ->except(['create', 'edit']);

        Route::apiResource('jenis-layanan', JenisLayananController::class)
            ->except(['create', 'edit']);

        Route::apiResource('layanan', LayananController::class)
            ->except(['create', 'edit']);

        Route::apiResource('pesanan', PesananController::class)
            ->except(['create', 'edit']);

        Route::apiResource('detail-pesanan', DetailPesananController::class)
            ->except(['create', 'edit']);

        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/dashboard/grafik-penjualan', [DashboardController::class, 'grafikPenjualan']);
        Route::get('/dashboard/grafik-pelanggan', [DashboardController::class, 'grafikPelanggan']);
        Route::get('/dashboard/pesanan', [DashboardController::class, 'pesananDiproses']);

        Route::get('/laporan', [LaporanController::class, 'index']);
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf']);
    });
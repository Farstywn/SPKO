<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\SpkoController;
use Illuminate\Support\Facades\Route;

// Dashboard Utama ERP
Route::get('/', [MasterDataController::class, 'dashboard'])->name('dashboard');

// Modul Master Data
Route::prefix('master')->name('master.')->group(function () {
    Route::get('employee', [MasterDataController::class, 'employees'])->name('employee');
    Route::get('product', [MasterDataController::class, 'products'])->name('product');
});

// Modul Transaksi SPKO & Nota Terima Kerja (NTHKO)
Route::get('spko/suggest-number', [SpkoController::class, 'getSuggestNumber'])->name('spko.suggest_number');
Route::get('spko/{id}/print', [SpkoController::class, 'print'])->name('spko.print');
Route::resource('spko', SpkoController::class);
Route::get('nthko', [MasterDataController::class, 'nthko'])->name('nthko.index');

// Modul Informasi Harian SPKO & NTHKO (Raw Query - Soal 3)
Route::get('reports/daily-spko-nthko', [DailyReportController::class, 'index'])->name('reports.daily');


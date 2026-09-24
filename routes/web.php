<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\SpkoController;
use Illuminate\Support\Facades\Route;

// Redirect root ke modul SPKO
Route::redirect('/', '/spko');

// Modul Transaksi SPKO & Nota Terima Kerja (Soal 2)
Route::get('spko/{id}/print', [SpkoController::class, 'print'])->name('spko.print');
Route::resource('spko', SpkoController::class);

// Modul Informasi Harian SPKO & NTHKO (Raw Query - Soal 3)
Route::get('reports/daily-spko-nthko', [DailyReportController::class, 'index'])->name('reports.daily');

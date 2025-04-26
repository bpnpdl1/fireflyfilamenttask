<?php

use App\Http\Controllers\MonthlyReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/monthly-report/{month}', [MonthlyReportController::class, 'downloadReport'])
    ->name('monthly-report.download')
    ->middleware('auth');

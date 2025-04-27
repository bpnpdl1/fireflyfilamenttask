<?php

use App\Http\Controllers\MonthlyReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return to_route('filament.user.auth.login');
});

Route::get('/monthly-report/{month}', [MonthlyReportController::class, 'downloadReport'])
    ->name('monthly-report.download')
    ->middleware('auth');

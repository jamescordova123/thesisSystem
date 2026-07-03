<?php

use App\Http\Controllers\Cashier\DashboardController;
use App\Http\Controllers\Cashier\ExportController;
use App\Http\Controllers\Cashier\LedgerController;
use App\Http\Controllers\Cashier\NotificationController;
use App\Http\Controllers\Cashier\PaymentController;
use App\Http\Controllers\Cashier\ReportController;
use App\Http\Controllers\Cashier\SearchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'cashier'])
    ->prefix('cashier')
    ->name('cashier.')
    ->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('search', SearchController::class)->name('search');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{user}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('payments/{user}', [PaymentController::class, 'store'])->name('payments.store');
        Route::put('payments/{user}/{payment}', [PaymentController::class, 'update'])->name('payments.update');

        Route::get('ledger/{user}', [LedgerController::class, 'show'])->name('ledger.show');

        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications', [NotificationController::class, 'store'])->name('notifications.store');

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        Route::get('exports/daily-collection', [ExportController::class, 'dailyCollection'])->name('exports.daily-collection');
        Route::get('exports/monthly-collection', [ExportController::class, 'monthlyCollection'])->name('exports.monthly-collection');
        Route::get('exports/outstanding-balance', [ExportController::class, 'outstandingBalance'])->name('exports.outstanding-balance');
        Route::get('exports/fully-paid', [ExportController::class, 'fullyPaid'])->name('exports.fully-paid');
        Route::get('exports/partial-payment', [ExportController::class, 'partialPayment'])->name('exports.partial-payment');
        Route::get('exports/no-payment', [ExportController::class, 'noPayment'])->name('exports.no-payment');
        Route::get('exports/ledger/{user}', [ExportController::class, 'ledger'])->name('exports.ledger');
        Route::get('exports/receipt/{payment}', [ExportController::class, 'receipt'])->name('exports.receipt');
    });

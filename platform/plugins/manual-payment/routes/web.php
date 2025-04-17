<?php

use Illuminate\Support\Facades\Route;
use Botble\ManualPayment\Http\Controllers\ManualPaymentController;
use Botble\Base\Facades\BaseHelper;

Route::group(['middleware' => ['web', 'core']], function () {

    // Admin routes for manual payments
    Route::group([
        'prefix' => BaseHelper::getAdminPrefix(),
        'middleware' => 'auth',
        'as' => 'manual-payment.',
    ], function () {
        Route::get('manual-payments', [ManualPaymentController::class, 'index'])->name('index');
        Route::get('manual-payments/{id}', [ManualPaymentController::class, 'show'])->name('show');
    });

    // Frontend checkout form + process
    Route::group(['as' => 'manual-payment.'], function () {
        Route::get('manual-payment/checkout/{order}', [ManualPaymentController::class, 'showForm'])
            ->name('checkout.form');

        Route::post('manual-payment/checkout/{order}', [ManualPaymentController::class, 'processForm'])
            ->name('checkout.process');
    });

    // Optional submission route (unused unless needed elsewhere)
    Route::post('manual-payment/submit', [ManualPaymentController::class, 'store'])
        ->name('submit');
});

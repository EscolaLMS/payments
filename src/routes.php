<?php

use EscolaLms\Payments\Http\Controllers\Admin\PaymentsController as PaymentsAdminController;
use EscolaLms\Payments\Http\Controllers\CallbackController;
use EscolaLms\Payments\Http\Controllers\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::any('/payments-gateway-callback/{payment}', [CallbackController::class, 'index'])->name('payments-gateway-callback');

    Route::group(['prefix' => 'admin/payments', 'middleware' => ['auth:api']], function () {
        Route::get('/{payment}', [PaymentsAdminController::class, 'show']);
        Route::get('/', [PaymentsAdminController::class, 'search']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/{payment}', [PaymentsController::class, 'show']);
        Route::get('/', [PaymentsController::class, 'search']);
    });
});

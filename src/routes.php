<?php

use EscolaLms\Payments\Http\Controllers\Admin\PaymentsController as PaymentsAdminController;
use EscolaLms\Payments\Http\Controllers\GatewayController;
use EscolaLms\Payments\Http\Controllers\PaymentsController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
    Route::get('/payments-gateways', [GatewayController::class, 'index']);
    Route::any('/payments-gateways/callback/{payment}', [GatewayController::class, 'callback'])->name('payments-gateway-callback');

    Route::group(['prefix' => 'admin/payments', 'middleware' => ['auth:api']], function () {
        Route::get('/{payment}', [PaymentsAdminController::class, 'show']);
        Route::get('/', [PaymentsAdminController::class, 'search']);
    });

    Route::prefix('payments')->group(function () {
        Route::get('/{payment}', [PaymentsController::class, 'show']);
        Route::get('/', [PaymentsController::class, 'search']);
    });
});

<?php

use Illuminate\Support\Facades\Route;

use EscolaLms\Payments\Http\Controllers\PaymentsApiController;

Route::group(['prefix' => 'api/payments'], function () {
    Route::post('/gateway', [PaymentsApiController::class, 'gateway']);
    Route::post('/transaction', [PaymentsApiController::class, 'registerTransaction']);
});

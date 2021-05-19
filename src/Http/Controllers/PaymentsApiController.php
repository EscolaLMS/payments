<?php

namespace EscolaLms\Payments\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Payments\Http\Controllers\Contracts\PaymentsApiContract;
use EscolaLms\Payments\Http\Requests\GatewayRequest;
use EscolaLms\Payments\Http\Requests\TransactionRegistrationRequest;
use Illuminate\Http\JsonResponse;

class PaymentsApiController extends EscolaLmsBaseController implements PaymentsApiContract
{
    public function gateway(GatewayRequest $request): JsonResponse {
        return response()->json('Not implemented', 404);
    }

    public function registerTransaction(TransactionRegistrationRequest $request): JsonResponse {
        return response()->json('Not implemented', 404);
    }
}

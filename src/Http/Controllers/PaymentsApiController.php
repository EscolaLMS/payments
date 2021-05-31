<?php

namespace EscolaLms\Payments\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Payments\Http\Controllers\Contracts\PaymentsApiContract;
use EscolaLms\Payments\Http\Requests\GatewayRequest;
use EscolaLms\Payments\Http\Requests\TransactionRegistrationRequest;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use Illuminate\Http\JsonResponse;

class PaymentsApiController extends EscolaLmsBaseController implements PaymentsApiContract
{
    private PaymentsServiceContract $service;

    public function __construct(PaymentsServiceContract $service)
    {
        $this->service = $service;
    }

    public function gateway(GatewayRequest $request): JsonResponse {
        return response()->json('Not implemented', 404);
    }

    public function registerTransaction(TransactionRegistrationRequest $request): JsonResponse {
        $registration = $this->service->registerTransaction(
            $request->getParamAmount(),
            $request->getParamCurrency(),
            $request->getParamDescription()
        );
        return response()->json($registration->toArray(), 200);
    }
}

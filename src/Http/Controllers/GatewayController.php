<?php

namespace EscolaLms\Payments\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GatewayController extends EscolaLmsBaseController
{
    private PaymentsServiceContract $paymentService;

    public function __construct(PaymentsServiceContract $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request)
    {
        return $this->sendResponse($this->paymentService->listGatewaysWithRequiredParameters(), __('List of payment gateways with required parameters'));
    }

    public function callback(Request $request)
    {
        $payment = $this->paymentService->findPayment((int) $request->route('payment'));
        if (is_null($payment)) {
            Log::error(__('Callback called for undefined payment :id', ['id' => $request->route('payment')]));
            return $this->sendError(__('Payment not found'), 404);
        }
        $this->paymentService->processPayment($payment)->callback($request);
        return $this->sendSuccess('OK');
    }
}

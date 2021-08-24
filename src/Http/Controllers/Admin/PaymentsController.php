<?php

namespace EscolaLms\Payments\Http\Controllers\Admin;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Payments\Dtos\PaymentFilterCriteriaDto;
use EscolaLms\Payments\Facades\Payments;
use EscolaLms\Payments\Http\Controllers\Admin\Swagger\PaymentsSwagger;
use EscolaLms\Payments\Http\Requests\Admin\PaymentsSearchAdminRequest;
use EscolaLms\Payments\Http\Requests\PaymentShowRequest;
use EscolaLms\Payments\Http\Resources\PaymentCollection;
use EscolaLms\Payments\Http\Resources\PaymentResource;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\JsonResponse;

class PaymentsController extends EscolaLmsBaseController implements PaymentsSwagger
{
    public function search(PaymentsSearchAdminRequest $request): JsonResponse
    {
        $paymentFilterDto = PaymentFilterCriteriaDto::instantiateFromRequest($request);
        $orderDto = OrderDto::instantiateFromRequest($request);
        return $this->sendResponse(
            PaymentCollection::make(Payments::searchPayments($paymentFilterDto, $orderDto))->toArray($request),
            __("Search payments results")
        );
    }

    public function show(PaymentShowRequest $request, Payment $payment): JsonResponse
    {
        return $this->sendresponse(
            PaymentResource::make($request->getPayment())->toArray($request),
            __("Payment details")
        );
    }
}

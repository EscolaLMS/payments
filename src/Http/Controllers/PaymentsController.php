<?php

namespace EscolaLms\Payments\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Dtos\PaginationDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Payments\Dtos\PaymentFilterCriteriaDto;
use EscolaLms\Payments\Facades\Payments;
use EscolaLms\Payments\Http\Controllers\Swagger\PaymentsSwagger;
use EscolaLms\Payments\Http\Requests\PaymentShowRequest;
use EscolaLms\Payments\Http\Requests\PaymentsSearchRequest;
use EscolaLms\Payments\Http\Responses\PaymentListResponse;
use EscolaLms\Payments\Http\Responses\PaymentResponse;
use EscolaLms\Payments\Models\Payment;

class PaymentsController extends EscolaLmsBaseController implements PaymentsSwagger
{
    public function search(PaymentsSearchRequest $request): PaymentListResponse
    {
        $paymentFilterDto = PaymentFilterCriteriaDto::instantiateFromRequest($request);
        $orderDto = OrderDto::instantiateFromRequest($request);
        $paginationDto = PaginationDto::instantiateFromRequest($request);
        return new PaymentListResponse(Payments::searchPayments(
            $paymentFilterDto,
            $orderDto,
            $paginationDto
        ));
    }

    public function show(PaymentShowRequest $request, Payment $payment): PaymentResponse
    {
        return new PaymentResponse($request->getPayment());
    }
}

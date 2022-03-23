<?php

namespace EscolaLms\Payments\Gateway\Drivers\Contracts;

use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;

interface GatewayDriverContract
{
    public function purchase(Payment $payment, array $parameters = []): ResponseInterface;
    public function callback(Request $request, array $parameters = []): CallbackResponse;

    public static function requiredParameters(): array;

    public function throwExceptionForResponse(ResponseInterface $response): void;
    public function throwExceptionIfMissingParameters(array $parameters): void;
}

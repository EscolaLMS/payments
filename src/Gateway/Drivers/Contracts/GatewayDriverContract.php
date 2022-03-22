<?php

namespace EscolaLms\Payments\Gateway\Drivers\Contracts;

use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;

interface GatewayDriverContract
{
    public function purchase(PaymentDto $dto, array $parameters = []): ResponseInterface;
    public function callback(Request $request, array $parameters = []): CallbackResponse;

    public function requiredParameters(): array;

    public function throwExceptionForResponse(ResponseInterface $response): void;
    public function throwExceptionIfMissingParameters(array $parameters): void;
}

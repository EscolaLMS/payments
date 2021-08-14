<?php

namespace EscolaLms\Payments\Gateway\Drivers\Contracts;

use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use EscolaLms\Payments\Dtos\PaymentDto;
use Omnipay\Common\Message\ResponseInterface;

interface GatewayDriverContract
{
    public function purchase(PaymentDto $payment, PaymentMethodContract $method): ResponseInterface;
    public function throwExceptionForResponse(ResponseInterface $response): void;
}

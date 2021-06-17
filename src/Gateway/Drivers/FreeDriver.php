<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use EscolaLms\Payments\Gateway\Responses\NoneGatewayResponse;
use Omnipay\Common\Message\ResponseInterface;

class FreeDriver extends AbstractDriver implements GatewayDriverContract
{
    public function purchase(PaymentDto $payment, PaymentMethodContract $method): ResponseInterface
    {
        return new NoneGatewayResponse();
    }
}

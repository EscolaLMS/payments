<?php

namespace EscolaLms\Payments\Facades\Fakes;

use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Gateway\Responses\NoneGatewayResponse;
use Omnipay\Common\Message\ResponseInterface;

class PaymentGatewayFake
{
    public function purchase(PaymentDto $dto, array $parameters = []): ResponseInterface
    {
        return new NoneGatewayResponse();
    }

    public function requiredParameters(): array
    {
        return [];
    }
}

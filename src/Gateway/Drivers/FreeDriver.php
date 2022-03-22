<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Responses\CallbackResponse;
use EscolaLms\Payments\Gateway\Responses\NoneGatewayResponse;
use Illuminate\Http\Request;
use Omnipay\Common\Message\ResponseInterface;

class FreeDriver extends AbstractDriver implements GatewayDriverContract
{
    public function purchase(PaymentDto $dto, array $parameters = []): ResponseInterface
    {
        return new NoneGatewayResponse();
    }

    public function callback(Request $request, array $parameters = []): CallbackResponse
    {
        return new CallbackResponse();
    }

    public function requiredParameters(): array
    {
        return [];
    }
}

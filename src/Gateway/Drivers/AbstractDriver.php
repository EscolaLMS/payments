<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Exceptions\PaymentException;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use Omnipay\Common\Message\ResponseInterface;

abstract class AbstractDriver implements GatewayDriverContract
{
    protected PaymentsConfig $config;

    public function __construct(PaymentsConfig $config)
    {
        $this->config = $config;
    }

    public function throwExceptionForResponse(ResponseInterface $response): void
    {
        throw new PaymentException('[' . $response->getCode() . '] '  . $response->getMessage());
    }
}

<?php

namespace EscolaLms\Payments\Gateway;

use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\FreeDriver;
use EscolaLms\Payments\Gateway\Drivers\StripeDriver;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Manager;

class GatewayManager extends Manager
{
    private PaymentsConfig $paymentsConfig;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->paymentsConfig = new PaymentsConfig($this->config['escolalms_payments']);
    }

    public function getPaymentsConfig(): PaymentsConfig
    {
        return $this->paymentsConfig;
    }

    public function getDefaultDriver(): string
    {
        return $this->paymentsConfig->getDefaultGateway();
    }

    public function createFreeDriver(): FreeDriver
    {
        return new FreeDriver($this->paymentsConfig);
    }

    public function createStripeDriver(): StripeDriver
    {
        return new StripeDriver($this->paymentsConfig);
    }
}

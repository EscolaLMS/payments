<?php

namespace EscolaLms\Payments\Facades;

use Closure;
use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use Illuminate\Support\Facades\Facade;
use Omnipay\Common\Message\ResponseInterface;

/**
 * @method static GatewayDriverContract driver(?string $driver)
 * @method static ResponseInterface purchase(PaymentDto $payment, PaymentMethodContract $method)
 * @method static PaymentsConfig getPaymentsConfig()
 * @method static GatewayManager extend($driver, Closure $callback)
 * 
 * @see \EscolaLms\Payments\Gateway\GatewayManager
 */
class PaymentGateway extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'payment-gateway';
    }
}

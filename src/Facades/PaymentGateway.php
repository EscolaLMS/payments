<?php

namespace EscolaLms\Payments\Facades;

use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Facades\Fakes\PaymentGatewayFake;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\Drivers\Contracts\IntentDriverContract;
use Illuminate\Support\Facades\Facade;
use Omnipay\Common\Message\ResponseInterface;

/**
 * @method static GatewayDriverContract|IntentDriverContract driver(?string $driver)
 * @method static ResponseInterface purchase(PaymentDto $dto, array $parameters = [])
 * @method static array requiredParameters()
 * @method static PaymentsConfig getPaymentsConfig()
 *
 * @see \EscolaLms\Payments\Gateway\GatewayManager
 */
class PaymentGateway extends Facade
{
    public static function fake()
    {
        static::swap($fake = app(PaymentGatewayFake::class));

        return $fake;
    }

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

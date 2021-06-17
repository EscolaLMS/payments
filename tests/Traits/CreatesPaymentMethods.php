<?php

namespace EscolaLms\Payments\Tests\Traits;

use Illuminate\Support\Carbon;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;

trait CreatesPaymentMethods
{
    protected function getGateway()
    {
        $this->gateway = Omnipay::create('Stripe\PaymentIntents');
        $this->gateway->setApiKey('sk_test_51I6fE0FHAZ5Pnnlr2l21VJwGXrsnsUzUZ4om4l6fJmjriJ1ScZEBRzwFFw6stsn1h30ldDnpMfs1Gw7uE9N2uVGH00PcJYHZJ0');
        return $this->gateway;
    }

    protected function getPaymentMethodId(): string
    {
        $card = $this->getGateway()->createCard([
            'card' => new CreditCard([
                'number' => 4242424242424242,
                'expiryMonth' => 12,
                'expiryYear' => Carbon::now()->addYear()->format('Y'),
                'cvv' => 123,
            ])
        ])->send()->getData();

        return $card['id'];
    }
}

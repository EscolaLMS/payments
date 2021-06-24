<?php

namespace EscolaLms\Payments\Tests\Traits;

use Illuminate\Support\Carbon;
use Omnipay\Common\CreditCard;
use Omnipay\Omnipay;
use Payments;

trait CreatesPaymentMethods
{
    protected function getGateway()
    {
        $this->gateway = Omnipay::create('Stripe\PaymentIntents');
        $this->gateway->setApiKey(Payments::getPaymentsConfig()->getStripeApiKey());
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

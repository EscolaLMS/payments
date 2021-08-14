<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Exceptions\CardDeclined;
use EscolaLms\Payments\Exceptions\ExpiredCard;
use EscolaLms\Payments\Exceptions\IncorrectCvc;
use EscolaLms\Payments\Exceptions\ProcessingError;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;
use Omnipay\Stripe\PaymentIntentsGateway;

class StripeDriver extends AbstractDriver implements GatewayDriverContract
{
    /** @var PaymentIntentsGateway $gateway */
    private GatewayInterface $gateway;

    public function __construct(PaymentsConfig $config)
    {
        $this->config = $config;

        $gateway = Omnipay::create('Stripe\PaymentIntents');
        assert($gateway instanceof PaymentIntentsGateway);
        $this->gateway = $gateway;
        $this->gateway->setApiKey($this->config->getStripeApiKey());
    }

    public function purchase(PaymentDto $payment, PaymentMethodContract $method): ResponseInterface
    {
        return $this->gateway->purchase([
            'amount' => $payment->getAmount(),
            'currency' => (string) ($payment->getCurrency() ?? $this->config->getDefaultCurrency()),
            'description' => $payment->getDescription(),
            'returnUrl' => $this->config->getRedirectUrl(),
            'paymentMethod' => $method->getPaymentMethodId(),
            'confirm' => true,
            'metadata' => [
                'order_id' => $payment->getOrderId()
            ]
        ])->send();
    }

    public function throwExceptionForResponse(ResponseInterface $response): void
    {
        switch ($response->getCode()) {
            case 'card_declined':
                throw new CardDeclined($response->getMessage());
            case 'expired_card':
                throw new ExpiredCard($response->getMessage());
            case 'incorrect_cvc':
                throw new IncorrectCvc($response->getMessage());
            case 'processing_error':
                throw new ProcessingError($response->getMessage());
            default:
                parent::throwExceptionForResponse($response);
        };
    }
}

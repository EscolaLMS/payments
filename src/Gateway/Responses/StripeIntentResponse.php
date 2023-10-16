<?php

namespace EscolaLms\Payments\Gateway\Responses;

use EscolaLms\Payments\Gateway\Requests\NoneGatewayRequest;
use EscolaLms\Payments\Gateway\Responses\Contracts\IntentResponseInterface;
use Stripe\PaymentIntent;

class StripeIntentResponse implements IntentResponseInterface
{
    public function __construct(protected ?PaymentIntent $intent = null)
    {

    }

    public function getData(): PaymentIntent
    {
        return $this->intent;
    }

    public function getRequest(): NoneGatewayRequest
    {
        return new NoneGatewayRequest();
    }

    public function isSuccessful(): bool
    {
        return true;
    }

    public function isRedirect(): bool
    {
        return false;
    }

    public function isCancelled(): bool
    {
        return false;
    }

    public function getMessage(): string
    {
        return '';
    }

    public function getCode(): string
    {
        return '0';
    }

    public function getTransactionReference(): ?string
    {
        return $this->intent->client_secret;
    }
}

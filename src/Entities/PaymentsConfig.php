<?php

namespace EscolaLms\Payments\Entities;

use EscolaLms\Payments\Enums\Currency;

class PaymentsConfig
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getDefaultGateway(): string
    {
        return $this->config['default_gateway'];
    }

    public function getDefaultCurrency(): Currency
    {
        if (Currency::isValidValue($this->config['default_currency'])) {
            return Currency::fromValue($this->config['default_currency']);
        }
        return Currency::USD();
    }

    public function getStripeSecretKey(): string
    {
        return $this->config['drivers']['stripe']['secret_key'];
    }

    public function getStripePublishableKey(): string
    {
        return $this->config['drivers']['stripe']['publishable_key'];
    }

    public function getStripeAllowedPaymentMethodTypes(): array
    {
        return $this->config['drivers']['stripe']['allowed_payment_method_types'] ?? ['card', 'p24'];
    }

    public function getPrzelewy24Live(): bool {
        return $this->config['drivers']['przelewy24']['live'];
    }

    public function getPrzelewy24MerchantId(): string
    {
        return $this->config['drivers']['przelewy24']['merchant_id'];
    }

    public function getPrzelewy24PosId(): string
    {
        return $this->config['drivers']['przelewy24']['pos_id'] ?? $this->getPrzelewy24MerchantId();
    }

    public function getPrzelewy24ApiKey(): string
    {
        return $this->config['drivers']['przelewy24']['api_key'];
    }

    public function getPrzelewy24Crc(): string
    {
        return $this->config['drivers']['przelewy24']['crc'];
    }

    public function shouldThrowOnPaymentError(): bool
    {
        return true;
    }
}

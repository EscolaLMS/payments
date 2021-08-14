<?php

namespace EscolaLms\Payments\Entities;

use EscolaLms\Payments\Contracts\Billable;
use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Models\Payment;

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

    public function getStripeApiKey(): string
    {
        return $this->config['drivers']['stripe']['key'];
    }

    public function getStripePublishableKey(): string
    {
        return $this->config['drivers']['stripe']['publishable_key'];
    }

    public function getRedirectUrl(): string
    {
        return url($this->config['url_redirect'] ?? '/');
    }

    public function getPaymentModel(): string
    {
        return $this->config['payment_model'] ?? Payment::class;
    }

    public function getFallbackBillableModel(): string
    {
        return $this->config['fallback_billable_model'] ?? Billable::class;
    }

    public function shouldThrowOnPaymentError(): bool
    {
        return true;
    }
}

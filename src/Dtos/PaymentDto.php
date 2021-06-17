<?php

namespace EscolaLms\Payments\Dtos;

use EscolaLms\Core\Dtos\Contracts\DtoContract;
use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Models\Payment;
use Money\Money;

class PaymentDto implements DtoContract
{
    private int $amount;
    private ?Currency $currency;
    private string $description;
    private ?string $orderId;

    public function __construct(
        int $amount,
        ?Currency $currency,
        string $description,
        ?string $orderId = null
    ) {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->orderId = $orderId;
    }

    public static function instantiateFromPayment(Payment $payment): self
    {
        return new self(
            $payment->amount,
            $payment->currency,
            $payment->description,
            $payment->order_id,
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function toArray(): array
    {
        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'orderId' => $this->getOrderId()
        ];
    }
}

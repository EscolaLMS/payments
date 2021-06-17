<?php

namespace EscolaLms\Payments\Tests\Mocks;

use EscolaLms\Payments\Concerns\Payable as ConcernsPayable;
use EscolaLms\Payments\Contracts\Billable;
use EscolaLms\Payments\Contracts\Payable as ContractsPayable;
use EscolaLms\Payments\Enums\Currency;
use Illuminate\Support\Collection;

class Payable implements ContractsPayable
{
    use ConcernsPayable;

    private ?Billable $billable = null;
    private ?Currency $currency;
    private ?string $order_id;
    private Collection $payments;
    private int $amount;
    private string $description;

    public function __construct(int $amount, ?Currency $currency, string $description = '', ?string $order_id = null)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->description = $description;
        $this->order_id = $order_id;
        $this->payments = new Collection();
    }

    public function getPaymentAmount(): int
    {
        return $this->amount;
    }

    public function getPaymentCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function getPaymentDescription(): string
    {
        return $this->description;
    }

    public function getPaymentOrderId(): ?string
    {
        return $this->order_id;
    }

    public function getBillable(): ?Billable
    {
        return $this->billable;
    }

    public function setBillable(?Billable $billable): self
    {
        $this->billable = $billable;
        return $this;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function setPayments(Collection $payments): self
    {
        $this->payments = $payments;
        return $this;
    }
}

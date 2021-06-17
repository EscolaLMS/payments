<?php

namespace EscolaLms\Payments\Entities;

use EscolaLms\Payments\Contracts\Billable;
use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Events\PaymentCancelled;
use EscolaLms\Payments\Events\PaymentPaid;
use EscolaLms\Payments\Exceptions\RedirectException;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use EscolaLms\Payments\Models\Payment;
use Omnipay\Common\Message\ResponseInterface;
use EscolaLms\Payments\Facades\PaymentGateway;
use Illuminate\Database\Eloquent\Model;
use Omnipay\Common\Message\RedirectResponseInterface;
use RuntimeException;

class PaymentProcessor
{
    private Payment $payment;
    private ?string $driver = null;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function setAmount(int $amount): self
    {
        $this->payment->amount = $amount;
        return $this;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->payment->currency = $currency ?? PaymentGateway::getPaymentsConfig()->getDefaultCurrency();
        return $this;
    }

    public function setBillable(Billable $billable): self
    {
        if ($billable instanceof Model) {
            $this->payment->billable()->associate($billable);
        }
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->payment->description = $description;
        return $this;
    }

    public function setOrderId(?string $orderId): self
    {
        $this->payment->order_id = $orderId;
        return $this;
    }

    public function savePayment(): self
    {
        $this->payment->save();
        return $this;
    }

    /**
     * Pay for payment that is being processed
     * 
     * @throws RedirectException|RuntimeException
     */
    public function purchase(PaymentMethodContract $method): void
    {
        $this->savePayment();
        $dto = PaymentDto::instantiateFromPayment($this->payment);
        $response = $this->getPaymentDriver()->purchase($dto, $method);

        if ($response->isSuccessful()) {
            $this->setSuccessful($response);
        } elseif ($response->isRedirect()) {
            assert($response instanceof RedirectResponseInterface);
            throw new RedirectException($response);
        } elseif ($response->isCancelled()) {
            $this->setCancelled($response);
        } else {
            throw new RuntimeException(__("Payment failed: :reason",  ['reason' => $response->getMessage()]));
        }
    }

    private function setSuccessful(ResponseInterface $response): void
    {
        $this->setPaymentStatus(PaymentStatus::PAID());
        event(new PaymentPaid($this->payment));
    }

    private function setCancelled(ResponseInterface $response): void
    {
        $this->setPaymentStatus(PaymentStatus::CANCELLED());
        event(new PaymentCancelled($this->payment));
    }

    private function setPaymentStatus(PaymentStatus $status): bool
    {
        $this->payment->status = $status;
        return $this->payment->save();
    }

    public function setPaymentDriver(?string $driver): self
    {
        $this->driver = $driver;
        return $this;
    }

    protected function getPaymentDriver(): GatewayDriverContract
    {
        if ($this->payment->amount === 0) {
            $driver = 'free';
        } else {
            $driver = $this->driver;
        }

        return PaymentGateway::driver($driver);
    }
}

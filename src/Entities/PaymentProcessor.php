<?php

namespace EscolaLms\Payments\Entities;

use EscolaLms\Payments\Contracts\Billable;
use EscolaLms\Payments\Dtos\Contracts\PaymentMethodContract;
use EscolaLms\Payments\Dtos\PaymentDto;
use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Events\EscolaLmsPaymentCancelledTemplateEvent;
use EscolaLms\Payments\Events\EscolaLmsPaymentFailedTemplateEvent;
use EscolaLms\Payments\Events\EscolaLmsPaymentSuccessTemplateEvent;
use EscolaLms\Payments\Exceptions\PaymentException;
use EscolaLms\Payments\Exceptions\RedirectException;
use EscolaLms\Payments\Facades\PaymentGateway;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\ResponseInterface;

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
     * @throws RedirectException|PaymentException
     */
    public function purchase(PaymentMethodContract $method): self
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
            $this->setError($response);
            if (PaymentGateway::getPaymentsConfig()->shouldThrowOnPaymentError()) {
                $this->getPaymentDriver()->throwExceptionForResponse($response);
            }
        }

        return $this;
    }

    private function setSuccessful(ResponseInterface $response): void
    {
        $this->setPaymentStatus(PaymentStatus::PAID());
        event(new EscolaLmsPaymentSuccessTemplateEvent($this->payment->billable, $this->payment));
    }

    private function setCancelled(ResponseInterface $response): void
    {
        $this->setPaymentStatus(PaymentStatus::CANCELLED());
        event(new EscolaLmsPaymentCancelledTemplateEvent($this->payment->billable, $this->payment));
    }

    private function setError(ResponseInterface $response): void
    {
        $this->setPaymentStatus(PaymentStatus::FAILED());
        event(new EscolaLmsPaymentFailedTemplateEvent($this->payment->billable, $this->payment, $response->getCode(), $response->getMessage()));
    }

    public function isNew(): bool
    {
        return $this->getPayment()->status->is(PaymentStatus::NEW);
    }

    public function isSuccessful(): bool
    {
        return $this->getPayment()->status->is(PaymentStatus::PAID);
    }

    public function isCancelled(): bool
    {
        return $this->getPayment()->status->is(PaymentStatus::CANCELLED);
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

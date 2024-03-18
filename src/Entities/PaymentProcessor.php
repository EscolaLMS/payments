<?php

namespace EscolaLms\Payments\Entities;

use EscolaLms\Core\Models\User;
use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Events\PaymentCancelled;
use EscolaLms\Payments\Events\PaymentFailed;
use EscolaLms\Payments\Events\PaymentRequiresRedirect;
use EscolaLms\Payments\Events\PaymentSuccess;
use EscolaLms\Payments\Facades\PaymentGateway;
use EscolaLms\Payments\Facades\Payments;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Common\Message\RedirectResponseInterface;

class PaymentProcessor
{
    private Payment $payment;

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

    public function setUser(User $user): self
    {
        $this->payment->user()->associate($user);
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

    public function setGatewayOrderId(?string $gatewayOrderId): self
    {
        $this->payment->gateway_order_id = $gatewayOrderId;
        return $this;
    }

    public function setPaymentDriverName(?string $driver): self
    {
        $this->payment->driver = $driver ?? PaymentGateway::getDefaultDriver();
        return $this;
    }

    public function getPaymentDriverName(): string
    {
        if ($this->payment->amount === 0) {
            return 'free';
        }
        return $this->payment->driver ?? PaymentGateway::getDefaultDriver();
    }

    public function savePayment(): self
    {
        $this->payment->save();
        return $this;
    }

    public function purchase(array $parameters = []): self
    {
        // stripe throw error unsupported !!
        // todo $paramters['subs'], $paramters['trial'], $paramters['recursive'] itp



        $driver = $parameters['gateway'] ?? null;
        if (!is_null($driver) && Payments::isDriverEnabled($driver)) {
            $this->setPaymentDriverName($driver);
        } else {
            $this->setPaymentDriverName($this->getPaymentDriverName());
        }

        $this->savePayment();

        $response = $this->getPaymentDriver()->purchase($this->payment, $parameters);
        if ($response->isSuccessful()) {
            $this->setSuccessful();
        } elseif ($response->isRedirect()) {
            assert($response instanceof RedirectResponseInterface);
            $this->setRedirect($response->getRedirectUrl());
        } elseif ($response->isCancelled()) {
            $this->setCancelled();
        } else {
            $this->setError($response->getMessage(), $response->getCode());
            if (PaymentGateway::getPaymentsConfig()->shouldThrowOnPaymentError()) {
                $this->getPaymentDriver()->throwExceptionForResponse($response);
            }
        }

        return $this;
    }

    public function callback(Request $request): self
    {
        $callbackResponse = $this->getPaymentDriver()->callback($request);

        $this->clearRedirect();
        $this->setGatewayOrderId($callbackResponse->getGatewayOrderId());

        if ($callbackResponse->getSuccess()) {
            $this->setSuccessful();
        } else {
            $this->setError($callbackResponse->getError());
        }

        return $this;
    }

    private function setSuccessful(): void
    {
        $this->setPaymentStatus(PaymentStatus::PAID());
        event(new PaymentSuccess($this->payment->user, $this->payment));
    }

    private function clearRedirect(): void
    {
        $this->payment->redirect_url = null;
    }

    private function setRedirect(string $redirect_url): void
    {
        $this->payment->redirect_url = $redirect_url;
        $this->setPaymentStatus(PaymentStatus::REQUIRES_REDIRECT());
    }

    private function setCancelled(): void
    {
        $this->setPaymentStatus(PaymentStatus::CANCELLED());
        event(new PaymentCancelled($this->payment->user, $this->payment));
    }

    private function setError(string $message, string $code = '0'): void
    {
        $this->setPaymentStatus(PaymentStatus::FAILED());
        event(new PaymentFailed($this->payment->user, $this->payment, $code, $message));
    }

    public function isNew(): bool
    {
        return $this->getPayment()->status->is(PaymentStatus::NEW);
    }

    public function isSuccessful(): bool
    {
        return $this->getPayment()->status->is(PaymentStatus::PAID);
    }

    public function isRedirect(): bool
    {
        return $this->getPayment()->status->is(PaymentStatus::REQUIRES_REDIRECT());
    }

    public function getRedirectUrl(): string
    {
        return $this->getPayment()->redirect_url;
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

    protected function getPaymentDriver(): GatewayDriverContract
    {
        return PaymentGateway::driver($this->getPaymentDriverName());
    }
}

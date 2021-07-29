<?php

namespace EscolaLms\Payments\Events;

use EscolaLms\Payments\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentError
{
    use Dispatchable, SerializesModels;

    private Payment $payment;
    private ?string $code;
    private ?string $message;

    public function __construct(Payment $payment, ?string $code = null, ?string $message = null)
    {
        $this->payment = $payment;
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * Get the value of code
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Get the value of message
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }
}

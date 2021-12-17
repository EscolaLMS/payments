<?php

namespace EscolaLms\Payments\Events;

use EscolaLms\Payments\Models\Payment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class EscolaLmsPaymentTemplateEvent
{
    use Dispatchable, SerializesModels;

    private Payment $payment;
    private Authenticatable $user;

    public function __construct(Authenticatable $user, Payment $payment)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function getUser(): Authenticatable
    {
        return $this->user;
    }
}

<?php

namespace EscolaLms\Payments\Events;

use EscolaLms\Payments\Models\Payment as PaymentModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class Payment
{
    use Dispatchable, SerializesModels;

    private PaymentModel $payment;
    private Authenticatable $user;

    public function __construct(Authenticatable $user, PaymentModel $payment)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    /**
     * @return Payment
     */
    public function getPayment(): PaymentModel
    {
        return $this->payment;
    }

    public function getUser(): Authenticatable
    {
        return $this->user;
    }
}

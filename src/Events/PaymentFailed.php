<?php

namespace EscolaLms\Payments\Events;

use EscolaLms\Payments\Models\Payment as PaymentModel;
use Illuminate\Contracts\Auth\Authenticatable;

class PaymentFailed extends Payment
{
    private ?string $code;
    private ?string $message;

    public function __construct(Authenticatable $user, PaymentModel $payment, ?string $code = null, ?string $message = null)
    {
        $this->code = $code;
        $this->message = $message;
        parent::__construct($user, $payment);
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

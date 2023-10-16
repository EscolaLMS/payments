<?php

namespace EscolaLms\Payments\Dtos;

use EscolaLms\Payments\Models\Payment;
use Stripe\Event;

class WebHookDto
{
    public function __construct(public Payment $payment, public Event $event)
    {
    }
}

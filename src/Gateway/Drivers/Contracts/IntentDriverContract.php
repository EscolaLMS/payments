<?php

namespace EscolaLms\Payments\Gateway\Drivers\Contracts;

use EscolaLms\Payments\Dtos\WebHookDto;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Http\Request;

interface IntentDriverContract
{
    public function purchase(Payment $payment, array $parameters = []): string;

    public function getPaymentWithEventFromWebhook(Request $request): WebHookDto;
}

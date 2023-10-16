<?php

namespace EscolaLms\Payments\Gateway\Drivers\Contracts;

use EscolaLms\Payments\Dtos\WebHookDto;
use Illuminate\Http\Request;

interface WebHookDriverContract
{
    public function getWebhookData(Request $request): WebHookDto;
}

<?php

namespace EscolaLms\Payments\Facades\Fakes;

use EscolaLms\Payments\Gateway\GatewayManager;
use EscolaLms\Payments\Facades\Fakes\FakeDriver;

class PaymentGatewayFake extends GatewayManager
{
    public function driver($driver = null)
    {
        return new FakeDriver($this->paymentsConfig);
    }
}

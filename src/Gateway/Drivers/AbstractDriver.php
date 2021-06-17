<?php

namespace EscolaLms\Payments\Gateway\Drivers;

use EscolaLms\Payments\Entities\PaymentsConfig;

abstract class AbstractDriver
{
    protected PaymentsConfig $config;

    public function __construct(PaymentsConfig $config)
    {
        $this->config = $config;
    }
}

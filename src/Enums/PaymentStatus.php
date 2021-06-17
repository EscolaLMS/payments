<?php

namespace EscolaLms\Payments\Enums;

use EscolaLms\Core\Enums\BasicEnum;

class PaymentStatus extends BasicEnum
{
    public const NEW        = 'new';
    public const PAID       = 'paid';
    public const CANCELLED  = 'cancelled';
}

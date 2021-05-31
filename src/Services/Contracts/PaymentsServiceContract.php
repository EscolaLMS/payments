<?php

namespace EscolaLms\Payments\Services\Contracts;

use EscolaLms\Payments\Models\TransactionRegistration;


interface PaymentsServiceContract {
    public function registerTransaction(int $amount, string $currency, string $description): TransactionRegistration;
}

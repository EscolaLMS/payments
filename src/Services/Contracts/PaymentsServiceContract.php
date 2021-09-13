<?php

namespace EscolaLms\Payments\Services\Contracts;

use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Payments\Contracts\Payable;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Models\Payment;
use EscolaLms\Payments\Entities\PaymentProcessor;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PaymentsServiceContract
{
    public function getPaymentsConfig(): PaymentsConfig;
    public function processPayable(Payable $payable): PaymentProcessor;
    public function processPayment(Payment $payment): PaymentProcessor;
    public function searchPayments(CriteriaDto $criteriaDto, OrderDto $orderDto): LengthAwarePaginator;
    public function listPaymentsForBillable(int $billable_id, ?string $billable_type): Collection;
}
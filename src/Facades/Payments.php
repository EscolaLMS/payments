<?php

namespace EscolaLms\Payments\Facades;

use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Dtos\PaginationDto;
use EscolaLms\Payments\Contracts\Payable;
use EscolaLms\Payments\Entities\PaymentProcessor;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PaymentsConfig getPaymentsConfig()
 * @method static PaymentProcessor processPayable(Payable $payable)
 * @method static PaymentProcessor processPayment(Payment $payment)
 * @method static Collection searchPayments(CriteriaDto $criteriaDto, OrderDto $orderDto, PaginationDto $paginationDto)
 * @method static Collection listPaymentsForBillable(int $billable_id, string|null $billable_type = null)
 *
 * @see \EscolaLms\Payments\Services\PaymentsService
 */
class Payments extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'payments';
    }
}

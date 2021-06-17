<?php

namespace EscolaLms\Payments\Services;

use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Dtos\PaginationDto;
use EscolaLms\Payments\Contracts\Payable;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Facades\PaymentGateway;
use EscolaLms\Payments\Models\Payment;
use EscolaLms\Payments\Repositories\Contracts\PaymentsRepositoryContract;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use EscolaLms\Payments\Entities\PaymentProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PaymentsService implements PaymentsServiceContract
{
    private function repository(): PaymentsRepositoryContract
    {
        return app(PaymentsRepositoryContract::class);
    }

    public function getPaymentsConfig(): PaymentsConfig
    {
        return PaymentGateway::getPaymentsConfig();
    }

    public function listPaymentsForBillable(int $billable_id, ?string $billable_type = null): Collection
    {
        $query = $this->repository()->allQuery()->where('billable_id', $billable_id);
        if ($billable_type) {
            $query->where('billable_type', $billable_type);
        }
        return $query->get();
    }

    public function searchPayments(
        CriteriaDto $criteriaDto,
        OrderDto $orderDto,
        PaginationDto $paginationDto
    ): Collection {
        return $this->repository()->searchOrderAndPaginate($criteriaDto, $orderDto, $paginationDto)->get();
    }

    public function processPayable(Payable $payable): PaymentProcessor
    {
        /** @var Payment $payment */
        $payment = $this->repository()->create([
            'amount' => $payable->getPaymentAmount(),
            'currency' => $payable->getPaymentCurrency() ?? $this->getPaymentsConfig()->getDefaultCurrency(),
            'description' => $payable->getPaymentDescription(),
            'order_id' => $payable->getPaymentOrderId()
        ]);
        if ($payable instanceof Model) {
            $payment->payable()->associate($payable);
        }
        if ($payable->getBillable() instanceof Model) {
            $payment->billable()->associate($payable->getBillable());
        }
        $payment->save();
        return new PaymentProcessor($payment->refresh());
    }

    public function processPayment(Payment $payment): PaymentProcessor
    {
        return new PaymentProcessor($payment);
    }
}

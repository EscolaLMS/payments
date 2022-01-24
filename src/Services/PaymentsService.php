<?php

namespace EscolaLms\Payments\Services;

use EscolaLms\Core\Dtos\CriteriaDto;
use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Payments\Contracts\Payable;
use EscolaLms\Payments\Entities\PaymentsConfig;
use EscolaLms\Payments\Events\PaymentRegistered;
use EscolaLms\Payments\Facades\PaymentGateway;
use EscolaLms\Payments\Models\Payment;
use EscolaLms\Payments\Repositories\Contracts\PaymentsRepositoryContract;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use EscolaLms\Payments\Entities\PaymentProcessor;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function searchPayments(CriteriaDto $criteriaDto, OrderDto $orderDto): LengthAwarePaginator
    {
        return $this->repository()->searchAndOrder($criteriaDto, $orderDto)->paginate(request()->input('per_page', 15));
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

        // Payment starts here, maybe this event fits here
        $this->dispatchRegisterPaymentEvent($payable->getBillable(), $payment);
        return new PaymentProcessor($payment->refresh());
    }

    public function processPayment(Payment $payment): PaymentProcessor
    {
        return new PaymentProcessor($payment);
    }

    public function dispatchRegisterPaymentEvent(Authenticatable $user, Payment $payment)
    {
        event(new PaymentRegistered($user, $payment));
    }
}

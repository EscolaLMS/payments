<?php

namespace EscolaLms\Payments\Services;

use EscolaLms\Payments\Models\TransactionRegistration;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use Illuminate\Support\Facades\Auth;


class PaymentsService implements PaymentsServiceContract
{
    public function registerTransaction(int $amount, string $currency, string $description): TransactionRegistration
    {
        $registration = TransactionRegistration::factory()->newModel([
            'amount' => $amount,
            'currency' => $currency,
            'description' => $description,
            'buyer_id' => Auth::user()->id,
        ]);
        $ok = $registration->save();
        if (!$ok) {
            throw new \Exception(
                sprintf(
                    "Could not register new transaction with amount %d, currency %s, description %s, buyer_id %d",
                    $amount, $currency, $description, Auth::user()->id
                )
            );
        }
        return $registration;
    }
}

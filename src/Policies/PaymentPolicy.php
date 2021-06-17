<?php

namespace EscolaLms\Payments\Policies;

use EscolaLms\Core\Models\User;
use EscolaLms\Payments\Contracts\Billable;
use EscolaLms\Payments\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PaymentPolicy
{
    use HandlesAuthorization;

    /**
     * @param User|Billable $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view($user, Payment $payment)
    {
        if ($user->hasRole('admin') || $user->can('view payment')) {
            return true;
        }

        $billable = $payment->billable;
        $classname = get_class($billable);

        if ($user->getKey() === $billable->getKey() && $user instanceof $classname) {
            return true;
        };

        return false;
    }
}

<?php

namespace EscolaLms\Payments\Tests\Traits;

use EscolaLms\Core\Enums\UserRole;
use EscolaLms\Payments\Facades\Payments;
use Illuminate\Support\Str;

trait CreatesBillable
{
    public function createBillableStudent()
    {
        $model = Payments::getPaymentsConfig()->getFallbackBillableModel();
        $billable = new $model([
            'first_name' => Str::random(5),
            'last_name' => Str::random(5),
        ]);
        $billable->assignRole(UserRole::STUDENT);
        $billable->save();
        return $billable;
    }
}

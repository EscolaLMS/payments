<?php

namespace EscolaLms\Payments;

use Illuminate\Support\ServiceProvider;

class EscolaLmsPaymentsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}

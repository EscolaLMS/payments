<?php

namespace EscolaLms\Payments;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;


class EscolaLmsPaymentsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->mergeConfigFrom(
            __DIR__ . '/config.php',
            'escolalms.payments'
        );
    }

    public function register()
    {
        parent::register();

        $this->app->singleton(
            StripeClient::class,
            fn($app) => dd(get_class($app))
            #$app => new StripeClient(config('escolalms.payments.stripe.secret_key'))
        );
    }

    public function validateConfig()
    {
        dd(get_func_args());
    }
}

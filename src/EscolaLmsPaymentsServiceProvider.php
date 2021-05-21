<?php

namespace EscolaLms\Payments;

use EscolaLms\Core\Providers\Injectable;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use EscolaLms\Payments\Services\PaymentsService;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;


class EscolaLmsPaymentsServiceProvider extends ServiceProvider
{
    use Injectable;

    private const CONTRACTS = [
        PaymentsServiceContract::class => PaymentsService::class,
    ];

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadConfig();
        $this->loadMigrations();
        $this->loadSeeds();
    }

    public function register()
    {
        parent::register();

        $this->injectContract(self::CONTRACTS);

        $this->app->singleton(
            StripeClient::class,
            fn($app) => new StripeClient([
                'api_base' => [$app['config']->get('escolalms.payments.stripe.api_base')],
                'api_key' => [$app['config']->get('escolalms.payments.stripe.secret_key')],
            ]),
        );

        $this->app->singleton(Alcohol\ISO4217::class);
    }

    private function loadConfig()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('escolalms/payments.php')
        ], 'escolalms');

        $this->mergeConfigFrom(
            __DIR__ . '/config.php',
            'escolalms.payments'
        );
    }

    private function loadMigrations(): void
    {
        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'escolalms');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    private function loadSeeds(): void
    {
        $this->publishes([
            __DIR__ . '/../database/seeders/AssignPermissions.php' => database_path('seeders/AssignPermissions.php'),
        ], 'payments-seeds');
    }
}

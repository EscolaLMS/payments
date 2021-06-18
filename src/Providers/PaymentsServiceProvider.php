<?php

namespace EscolaLms\Payments\Providers;

use EscolaLms\Core\Providers\Injectable;
use EscolaLms\Payments\Gateway\Drivers\Contracts\GatewayDriverContract;
use EscolaLms\Payments\Gateway\GatewayManager;
use EscolaLms\Payments\Repositories\Contracts\PaymentsRepositoryContract;
use EscolaLms\Payments\Repositories\PaymentsRepository;
use EscolaLms\Payments\Services\Contracts\PaymentsServiceContract;
use EscolaLms\Payments\Services\PaymentsService;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class PaymentsServiceProvider extends ServiceProvider
{
    use Injectable;

    private const CONTRACTS = [
        PaymentsRepositoryContract::class => PaymentsRepository::class,
        PaymentsServiceContract::class => PaymentsService::class
    ];

    private function injectContract(array $contracts): void
    {
        foreach ($contracts as $abstract => $concrete) {
            $this->app->singleton($abstract, $concrete);
        }
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/payments.php', 'payments');

        $this->injectContract(self::CONTRACTS);

        $this->app->singleton('payments', function ($app) {
            return app(PaymentsServiceContract::class);
        });
        $this->app->singleton('payment-gateway', function ($app) {
            return new GatewayManager($app);
        });
        $this->app->singleton('payment-gateway.driver', function ($app) {
            return $app['payment-gateway']->driver();
        });
        $this->app->alias('payment-gateway.driver', GatewayDriverContract::class);

        $this->app->register(AuthServiceProvider::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'payments',
            'payment-gateway'
        ];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../../config/payments.php' => config_path('payments.php'),
        ], 'payments.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../../resources/views' => base_path('resources/views/vendor/escolalms/'),
        ], 'payments.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../../resources/assets' => public_path('vendor/escolalms/'),
        ], 'payments.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../../resources/lang' => resource_path('lang/vendor/escolalms/'),
        ], 'payments.views');*/

        // Publishing the database migrations.
        $this->publishes([
            __DIR__ . '/../../database/migrations' => $this->app->databasePath('migrations'),
        ], 'payments.migrations');

        // Registering package commands.
        // $this->commands([]);
    }
}

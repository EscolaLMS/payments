<?php

namespace EscolaLms\Payments\Providers;

use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Settings\EscolaLmsSettingsServiceProvider;
use EscolaLms\Settings\Facades\AdministrableConfig;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{

    public function register()
    {
        if (class_exists(\EscolaLms\Settings\EscolaLmsSettingsServiceProvider::class) && class_exists(\EscolaLms\Settings\Facades\AdministrableConfig::class)) {
            if (!$this->app->getProviders(EscolaLmsSettingsServiceProvider::class)) {
                $this->app->register(EscolaLmsSettingsServiceProvider::class);
            }
            AdministrableConfig::registerConfig('escolalms_payments.drivers.stripe.key', ['required', 'array']);
            AdministrableConfig::registerConfig('escolalms_payments.drivers.stripe.publishable_key', ['required', 'array']);

            AdministrableConfig::registerConfig('escolalms_payments.default_gateway', ['required', 'string', 'in:Free,Stripe']);
            AdministrableConfig::registerConfig('escolalms_payments.default_currency', ['required', 'string', 'in:' . implode(',', Currency::getValues())]);
        }
    }
}
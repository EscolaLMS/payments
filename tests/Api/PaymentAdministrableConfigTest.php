<?php

namespace EscolaLms\Payments\Tests\APIs;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Payments\Tests\TestCase;
use EscolaLms\Settings\Database\Seeders\PermissionTableSeeder as SettingsPermissionSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;

class PaymentAdministrableConfigTest extends TestCase
{
    use CreatesUsers;
    use DatabaseTransactions;

    /**
     * @test
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(\EscolaLms\Settings\EscolaLmsSettingsServiceProvider::class)) {
            $this->markTestSkipped(
                'Escolalms/Settings is not installed'
            );
        }

        $this->seed(SettingsPermissionSeeder::class);

        $this->user = config('auth.providers.users.model')::factory()->create();
        $this->user->guard_name = 'api';
        $this->user->assignRole('admin');

        Config::set('escola_settings.use_database', true);
        Config::set('escolalms_payments.drivers.stripe.key', 'key_value');
        Config::set('escolalms_payments.drivers.stripe.publishable_key', 'publishable_key_value');
    }

    public function test_payment_administrable_config()
    {
        $this->response = $this->actingAs($this->user, 'api')->json(
            'GET',
            '/api/config',
        );
        $this->response->assertOk();
        $this->response->assertJsonFragment([
            'escolalms_payments' => [
                'drivers' => [
                    'stripe' => [
                        'key' => 'key_value',
                        'publishable_key' => 'publishable_key_value'
                    ]
                ]
            ]
        ]);
    }
}

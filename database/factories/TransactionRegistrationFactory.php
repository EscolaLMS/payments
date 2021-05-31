<?php
namespace Database\Factories\EscolaLms\Payments\Models;

use Database\Factories\EscolaLms\Core\Models\UserFactory;
use EscolaLms\Payments\Models\TransactionRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Alcohol\ISO4217;

class TransactionRegistrationFactory extends Factory
{
    protected $model = TransactionRegistration::class;

    public function definition()
    {
        $randomCurrency = fn(ISO4217 $iso) =>
            $iso->getAll()[$this->faker->randomKey($iso->getAll())]['alpha3'];

        return [
            'amount' => $this->faker->randomNumber(),
            'currency' => app()->call($randomCurrency),
            'description' => $this->faker->paragraph(1),
            'buyer_id' => UserFactory::new(),
        ];
    }
}

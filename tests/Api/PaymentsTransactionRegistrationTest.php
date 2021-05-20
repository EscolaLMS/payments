<?php

namespace EscolaLms\Payments\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;

class PaymentsTransactionRegistrationTest extends \EscolaLms\Payments\Tests\TestCase
{
    use CreatesUsers;

    public function testStudentCanRegisterPayment() {
        $this->response = $this->actingAs($this->makeStudent())
            ->json(
                'POST',
                '/api/payments/transaction',
                ['amount'=>15,'currency'=>'PLN','description'=>'Payment for the course XYZ']
            )
        ;
        $this->response->assertOk();
        //@todo login as student
        //@todo register payment through api
        //@todo check if the order details had been stored in the system
    }

    public function testStudentCanRegisterPaymentWithPersistedDetails() {
        //@todo login as student
        //@todo persist payment details internally
        //@todo register payment through api with associated details
        //@todo check if the order details had been stored in the system
    }
}

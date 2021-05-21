<?php

namespace EscolaLms\Payments\Tests\Api;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\Payments\Models\TransactionRegistration;

class PaymentsTransactionRegistrationTest extends \EscolaLms\Payments\Tests\TestCase
{
    use CreatesUsers;

    public function testStudentCanRegisterPayment() {
        $registration = TransactionRegistration::factory()->makeOne();
        $user = $this->makeStudent();
        $registration->buyer_id = $user->id;

        $this->response = $this->actingAs($user)
            ->json(
                'POST',
                '/api/payments/transaction',
                [
                    'amount'=>$registration->amount,
                    'currency'=>$registration->currency,
                    'description'=>$registration->description,
                ]
            )
        ;
        $this->response->assertOk();
        $this->response->assertJsonFragment([
            'amount'=>$registration->amount,
            'currency'=>$registration->currency,
            'description'=>$registration->description,
            'buyer_id'=>$user->id,
        ]);
        $id = $this->response->json('id');
        $this->assertIsInt($id);

        $stored = TransactionRegistration::factory()->make()->newQuery()->where('id',$id)->first();
        $this->assertEquals($registration->toArray(), collect($stored->toArray())->except('id')->all());
    }

    public function testStudentCanRegisterPaymentWithPersistedDetails() {
        //@todo login as student
        //@todo persist payment details internally
        //@todo register payment through api with associated details
        //@todo check if the order details had been stored in the system
    }
}

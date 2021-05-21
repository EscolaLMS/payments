<?php

namespace EscolaLms\Payments\Tests\Api;

class PaymentsTransactionPerformTest extends \EscolaLms\Payments\Tests\TestCase
{
    public function testStudentCanPerformRegisteredPayment() {
        $user = $this->makeStudent();
        $registration = Transaction::factory()->makeOne();
        $registration->buyer_id = $user->id;

        //@todo login as student
        //@todo register students payment internally
        //@todo perform a payment through the api
    }

    public function testStudentCannotPerformRegisteredPaymentOfOthers() {
        //@todo login as student
        //@todo register other students payment internally
        //@todo perform a payment through the api
    }
}

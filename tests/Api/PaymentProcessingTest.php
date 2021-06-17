<?php

namespace EscolaLms\Payments\Tests\Api;

use EscolaLms\Payments\Dtos\PaymentMethodDto;
use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Tests\Mocks\Payable;
use EscolaLms\Payments\Tests\Traits\CreatesBillable;
use EscolaLms\Payments\Tests\Traits\CreatesPaymentMethods;

class PaymentProcessingTest extends \EscolaLms\Payments\Tests\TestCase
{
    use CreatesPaymentMethods;
    use CreatesBillable;

    public function testPayableCanBecomePayment()
    {
        $billable = $this->createBillableStudent();
        $payable = new Payable(1000, Currency::USD(), 'asdf', 1337);
        $payable->setBillable($billable);

        $processor = $payable->process();
        $payment = $processor->getPayment();

        $this->assertEquals($payable->getPaymentAmount(), $payment->amount);
        $this->assertEquals($payable->getPaymentDescription(), $payment->description);
        $this->assertEquals($payable->getPaymentOrderId(), $payment->order_id);
        $this->assertEquals($payable->getPaymentCurrency(), $payment->currency);
        $this->assertEquals($payable->getBillable()->getKey(), $payment->billable->getKey());
    }

    public function testPayableCanBecomePaymentAndBePaid()
    {
        $billable = $this->createBillableStudent();
        $payable = new Payable(1000, Currency::USD(), 'asdf', 1337);
        $payable->setBillable($billable);

        $processor = $payable->process();
        $payment = $processor->getPayment();
        $this->assertEquals(PaymentStatus::NEW(), $payment->status);

        $paymentMethodId = $this->getPaymentMethodId();
        $paymentMethodDto = new PaymentMethodDto($paymentMethodId);

        $processor->purchase($paymentMethodDto);
        $payment->refresh();

        $this->assertEquals(PaymentStatus::PAID(), $payment->status);

        $response = $this->actingAs($billable)->json('GET', 'api/payments/');
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $payment->getKey()
        ]);
    }
}

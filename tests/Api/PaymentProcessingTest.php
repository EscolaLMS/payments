<?php

namespace EscolaLms\Payments\Tests\Api;

use EscolaLms\Payments\Enums\Currency;
use EscolaLms\Payments\Enums\PaymentStatus;
use EscolaLms\Payments\Events\PaymentFailed;
use EscolaLms\Payments\Events\PaymentRegistered;
use EscolaLms\Payments\Exceptions\CardDeclined;
use EscolaLms\Payments\Exceptions\ExpiredCard;
use EscolaLms\Payments\Exceptions\IncorrectCvc;
use EscolaLms\Payments\Exceptions\PaymentException;
use EscolaLms\Payments\Exceptions\ProcessingError;
use EscolaLms\Payments\Tests\Mocks\Payable;
use EscolaLms\Payments\Tests\Traits\CreatesBillable;
use EscolaLms\Payments\Tests\Traits\CreatesPaymentMethods;
use Illuminate\Support\Facades\Event;

class PaymentProcessingTest extends \EscolaLms\Payments\Tests\TestCase
{
    use CreatesPaymentMethods;
    use CreatesBillable;

    public function testPayableCanBecomePayment()
    {
        Event::fake([PaymentRegistered::class]);
        $billable = $this->createBillableStudent();
        $payable = new Payable(1000, Currency::USD(), 'asdf', 1337);
        $payable->setUser($billable);

        $processor = $payable->process();
        $payment = $processor->getPayment();
        Event::assertDispatched(PaymentRegistered::class);
        $this->assertEquals($payable->getPaymentAmount(), $payment->amount);
        $this->assertEquals($payable->getPaymentDescription(), $payment->description);
        $this->assertEquals($payable->getPaymentOrderId(), $payment->order_id);
        $this->assertEquals($payable->getPaymentCurrency(), $payment->currency);
        $this->assertEquals($payable->getUser()->getKey(), $payment->user->getKey());
    }

    public function testPayableCanBecomePaymentAndBePaid()
    {
        Event::fake([PaymentRegistered::class]);
        $billable = $this->createBillableStudent();
        $payable = new Payable(1000, Currency::USD(), 'asdf', 1337);
        $payable->setUser($billable);

        $processor = $payable->process();
        Event::assertDispatched(PaymentRegistered::class);
        $payment = $processor->getPayment();
        $this->assertEquals(PaymentStatus::NEW(), $payment->status);

        $paymentMethodId = $this->getPaymentMethodId();

        $processor->purchase(['paymentMethod' => $paymentMethodId, 'returnUrl' => url('/')]);
        $payment->refresh();

        $this->assertEquals(PaymentStatus::PAID(), $payment->status);

        $response = $this->actingAs($billable)->json('GET', 'api/payments/');
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $payment->getKey()
        ]);
    }

    public function testPaymentShouldFailAndThrowException()
    {
        Event::fake();
        $billable = $this->createBillableStudent();
        $payable = new Payable(1000, Currency::USD(), 'asdf', 1337);
        $payable->setUser($billable);

        $wrongCardNumbers = [
            '4000000000000002' => CardDeclined::class,
            '4000000000000069' => ExpiredCard::class,
            '4000000000000101' => IncorrectCvc::class,
            '4000000000000119' => ProcessingError::class,
            '4000000000000127' => IncorrectCvc::class,
            '4000000000009979' => CardDeclined::class,
            '4000000000009987' => CardDeclined::class,
            '4000000000009995' => CardDeclined::class,
        ];

        foreach ($wrongCardNumbers as $wrongCardNumber => $expectedException) {
            $processor = $payable->process();
            $payment = $processor->getPayment();
            $this->assertEquals(PaymentStatus::NEW(), $payment->status);
            $paymentMethodId = $this->getPaymentMethodId($wrongCardNumber);
            try {
                $processor->purchase(['paymentMethod' => $paymentMethodId, 'returnUrl' => url('/')]);
            } catch (PaymentException $ex) {
                $this->assertInstanceOf($expectedException, $ex);
            }
            $payment->refresh();
            $this->assertEquals(PaymentStatus::FAILED(), $payment->status);
            Event::assertDispatched(PaymentFailed::class);
        }
    }
}
